<?php
ini_set("max_execution_time", "0");
chdir(dirname(__FILE__));

require_once 'Config.php';
require_once 'FTPServer.php';
require_once 'FTPClient.php';

$server = new FTPServer();
$client = new FTPClient();
$server->connect($info['ftp_server'],$info['ftp_uname'],$info['ftp_pwd']);

$dirs = $client->get_dirs();
foreach($dirs as $dir){
	$files = $client->get_files($dir);		
	//$newFtpFilesArray = array(); 
	foreach($files as $f){					
		$pathinfo = pathinfo($f);
		$remote_file = $info['remote_path'].$pathinfo[basename];		
		$server->upload($remote_file,$f);			
		//array_push($newFtpFilesArray ,$remote_file);
	}

	////////////////////////////////////////////////////////////
	//
	// delete diff files on ftp server and delete uploaded files on ftp client
	//
	////////////////////////////////////////////////////////////
	//$newFtpFiles = implode(',',$newFtpFilesArray);	
	//$server->del_diff_files($newFtpFiles);	
	//$client->del_files($files);
}
$server->close();