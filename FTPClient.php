<?php

require_once 'Config.php';
require_once 'FileRec.php';

public class FTPClient{
	
	public function __construct(){

	}

	/**
	* delete local file
	* @param string $filename
	*/
	protected function del_file($filename) {
		if(file_exists($filename))
			unlink($filename);
	}

	/**
	* delete files from ftp client
	*/
	public function del_files($files){
		foreach ($files as $v) {
			$this->del_file($v);
		}
	}

	public function get_dirs(){
		$filerec = new FileRec($info['local_path']);
		return $filerec->getDirs();
	}

	public function get_files($dir){
		$filerec = new FileRec($dir);
		return $filerec->getCurrentFiles();
	}
}