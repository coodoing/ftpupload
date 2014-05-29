<?php

public class FileRec{
	
	private $path;
	public function __construct($p){
		if(file_exists($p))
			$this->path = $p;
	}
	
	/**
	 * get all files under the input dirs 
	 * @param RecursiveDirectoryIterator $dir 
	 * @return array $files
	 */
	protected function get_files($dir) {
	    $files = array(); 
	    for (; $dir->valid(); $dir->next()) {
	        if ($dir->isDir() && !$dir->isDot()) {
	            if ($dir->haschildren()) {
	                $files = array_merge($files, $this->get_files($dir->getChildren()));
	            }           
	        }else if($dir->isFile()){
	            $files[] = $dir->getPathName();
	        }
	    }
	    return $files;//array('dirs'=>$dirs,'files'=>$files,);
	    
	}
	
	
	/**
	* get all directories under input dir
	*/
	protected function get_dirs($dir){
	    $dirs = array();
	    for (; $dir->valid(); $dir->next()) {
	        if ($dir->isDir() && !$dir->isDot()) {
	            if ($dir->haschildren()) {
	                $dirs = array_merge($dirs, $this->get_dirs($dir->getChildren()));
	            }   
				$dirs[] = $dir->getPathName();                        
	        }
	    }
	    return $dirs;
	}
	
	/**
	* get files under the input dir
	*/
	protected function get_cur_files($dir){
		$files = array(); 
	    for (; $dir->valid(); $dir->next()) {
	        if ($dir->isDir() && !$dir->isDot()) {
	            if ($dir->haschildren()) {
	               //$files = array_merge($files, get_files($dir->getChildren()));
	            }           
	        }else if($dir->isFile()){
	            $files[] = $dir->getPathName();
	        }
	    }
	    return $files;
	}
	
	public function getFiles(){
		$paths = new RecursiveDirectoryIterator($this->path);
		$dirs = $this->get_files($paths);
		return $dirs;
	}
	
	public function getCurrentFiles(){
		$paths = new RecursiveDirectoryIterator($this->path);
		$dirs = $this->get_cur_files($paths);
		return $dirs;
	}
	
	public function getDirs(){
		$paths = new RecursiveDirectoryIterator($this->path);
		$dirs = $this->get_dirs($paths);
		return $dirs;
	}
}
