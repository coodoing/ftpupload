<?php

require_once 'Config.php';
require_once 'FileDiff.php';

public class FtpServer {

	private $host;
	private $link;
	public $link_time;
	private $err_code = 0;	
	public $mode = FTP_BINARY;//transfer mode => {ascii:FTP_ASCII, binary:FTP_BINARY}
	public function __construct(){
		
	}
	
	/**
	* connect ftp server
	* @param string $host(server address)
	* @param string $username　　　
	* @param string $password　　　
	* @param integer $port(21)　　　　   
	* @param boolean $pasv      
	* @param boolean $ssl
	* @param integer $timeout
	*/
	public function connect($host, $username = '', $password = '', $port = '21', $pasv = true, $ssl = false, $timeout = 10) {
		$start = time();
		$this->host = $host;
		if ($ssl) {
			if (!$this->link = ftp_ssl_connect($host, $port, $timeout)) {
				$this->err_code = 1;
				return false;
			}
		} else {
			if (!$this->link = ftp_connect($host, $port, $timeout)) {
				$this->err_code = 1;
				return false;
			}
		}
		if (ftp_login($this->link, $username, $password)) {
			if ($pasv)
				ftp_pasv($this->link, true);			
			$this->link_time = time() - $start;
			return true;
		} else {
			$this->err_code = 1;
			return false;
		}
		register_shutdown_function(array (
			& $this,
			'close'
		));
	}
	
	/**
	* make dirs on ftp server
	* @param string $dirs 
	*/
	public function mkdir($dirs) {
		if (!$this->link) {
			$this->err_code = 2;
			return false;
		}
		$url = str_replace('', '/', $dirs);
		$dirname = explode('/', $url);
		$path = '/';		
		foreach ($dirname as $v) {
			if ($v && !$this->chdir($path . $v)) {
				if ($path){
					$this->chdir($path);
				}					
				if(!file_exists($v)){
					$result = ftp_mkdir($this->link, $v);
				}					
			}
			if ($v)
				$path .= $v . '/';			
		}			
		return true;
	}
	
	/**
	* remove dirs on ftp server
	* @param string $dirname  
	* @param boolean $enforce 
	*/
	public function rmdir($dirname, $enforce = false) {
		if (!$this->link) {
			$this->err_code = 2;
			return false;
		}
		$list = $this->nlist($dirname);
		if ($list && $enforce) {
			$this->chdir($dirname);
			foreach ($list as $v) {
				$this->f_delete($v);
			}
		}
		elseif ($list && !$enforce) {
			$this->err_code = 3;
			return false;
		}
		@ ftp_rmdir($this->link, $dirname);
		return true;
	}
		
	/**
	* delete server file
	* @param string $filename
	*/
	protected function f_delete($filename) {
		if (!$this->link) {
			$this->err_code = 2;
			return false;
		}
		if (@ ftp_delete($this->link, $filename)) {
			return true;
		} else {
			$this->err_code = 4;
			return false;
		}
	}	
	
	/**
	* delete files on ftp server 
	*/
	protected function delfiles($files,$enforce=true){
		if (!$this->link) {
			$this->err_code = 2;
			return false;
		}		
		if ($files && $enforce) {
			foreach ($files as $v) {
				$this->f_delete($v);
			}
		}
		elseif ($files && !$enforce) {
			$this->err_code = 3;
			return false;
		}
		return true;
	}

	/**
	* delete diff files between the new and old which include the upload files' list
	* @param string $newFtpFiles 
	*/
	public function del_diff_files($newFtpFiles){
		$diff = new FileDiff();		
		$diff_path = $info['diff_path'];
		$newfile = $diff_path.'-'.date("Y-m-d");
		if(!empty($newFtpFiles))
			$diff->write($newfile,$newFtpFiles);
		$oldfile = $diff_path.'-'.date("Y-m-d",strtotime("-1 day"));	
		$difffiles = $diff->diff($oldfile,$newfile);
		$this->delfiles($difffiles);
	}
	
	/**
	* upload files to ftp server
	* @param string $remote
	* @param string $local
	*/
	public function upload($remote, $local) {	
		$az2z = ftp_raw( $this->link , 'site az2z' ); // ftp_exec
		if(!empty($az2z)){
			if (!$this->link) {
				$this->err_code = 2;
				return false;
			}
			$dirname = pathinfo($remote, PATHINFO_DIRNAME);
			if(!is_dir($dirname) && !file_exists($dirname)){ 
				$this->mkdir($dirname);
			}		
			if (ftp_put($this->link, $remote, $local, $this->mode)) {
				echo "Uploaded $local to $this->host as $remote successful. <br> ";
				return true;
			} else {
				echo 'Upload failed. <br>';
				$this->err_code = 7;
				return false;
			}
		}else{
			$this->err_code = 8;
			return false;
		}
	}

	/**
	* ls cmd on ftp server
	* @param string $dirname
	*/
	public function nlist($dirname) {
		if (!$this->link) {
			$this->err_code = 2;
			return false;
		}
		if ($list = @ ftp_nlist($this->link, $dirname)) {
			return $list;
		} else {
			$this->err_code = 5;
			return false;
		}
	}
	
	/**
	* change dir on ftp server
	* @param string $dirname
	*/
	protected function chdir($dirname) {
		if (!$this->link) {
			$this->err_code = 2;
			return false;
		}
		if (@ ftp_chdir($this->link, $dirname)) {
			return true;
		} else {
			$this->err_code = 6;
			return false;
		}
	}
	
	/**
	* get error code
	*/
	public function get_error() {
		if (!$this->err_code)
			return false;
		$err_msg = array (
			'1' => 'Server can not connect',
			'2' => 'Not connect to server',
			'3' => 'Can not delete non-empty folder',
			'4' => 'Can not delete file',
			'5' => 'Can not get file list',
			'6' => 'Can not change the current directory on the server',
			'7' => 'Can not upload files',
			'8' => 'Set az2z error',
		);
		return $err_msg[$this->err_code];
	}

	/**
	* close ftp connection
	*/
	public function close() {
		return @ ftp_close($this->link);
	}
}