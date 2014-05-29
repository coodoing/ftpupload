ftpupload
=========

`ftpupload` is just an automated tool to upload files or applications to ftp server. 

## Examples

### 1   **Modify the `Config.php` file**
```
$info = array(		
		// ftp server
		'ftp_server'=>'localhost',
		// ftp username
		'ftp_uname'=>'anonymous',
		// ftp password
		'ftp_pwd'=>'******',

		// remote path to upload to
		'remote_path'=>'/ftpserver/upload/',
		// local path to upload
		'local_path'=>'/local/upload/',
		// files or dirs to ignore
		'ignore'=>array('.git','.svn'),
		// is allowed to delete file on ftp server
		'allow_del'=>'true',
		// tmp file
		'temp'=>'/tmp/',
		// path to store file which include upload files list
		'diff_path'=>'/tmp/diff/',

	);


```
### 2 **Run the script**

```
php Upload.php 

or

sh ftpupload.sh

```
