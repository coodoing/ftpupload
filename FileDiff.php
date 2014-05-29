<?php

public class FileDiff{	
	public function __construct(){
			
	}

	protected function read($oldfile){
		$array = array();
		if(file_exists($oldfile)){
			$fp = fopen($oldfile,'rb');		
			while(!feof($fp)){		
		         $info = fgets($fp,1024);		
		         $array[] = $info;
			}		
			fclose($fp);
		}
		return $array;
	}

	public function write($file,$content){
		$fp = fopen($file,'wb');
		fwrite($fp,$content,strlen($content));
		fclose($fp);
	}
		
	/**
	 * compare two differenct upload file list
	 * */
	public function diff($pre,$now){
		$old = $this->read($pre);
		$new = $this->read($now);
		$result = array();
		
		if($old){
			foreach($old as $o){
				if(!in_array($o,$new)){
					$o = str_replace(array("/r/n", "/r", "/n","\n","\r\n"), "", $o); 
					$result[] = $o;
				}
			}
		}
		return $result;
	}
	
}