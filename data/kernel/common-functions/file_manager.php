<?php

class file_manager {
	private $rootPath;
	private $currentSpace;
	private $currentPath;
	private $additionalPath;
	private $currentFileName;
	private $fileHandle;
	private $customerDbName;
	private $coreUserID;

	function __construct() {
		$this->rootPath = "/usr/local/www/apache22/data-hidden/";
		$this->currentSpace = "";
		$this->currentPath = $this->rootPath;
		$this->additionalPath = "";
		$this->currentFileName = "";
		$this->fileHandle = null;
		$this->customerDbName = session_control::getSessionInfo("db_name");
		$this->coreUserID = session_control::getSessionInfo("id");
	}

	public function globalSpace() {
		$this->currentSpace = "GLOBAL";
		$this->currentPath = $this->rootPath."GLOBAL/";
		return $this;
	}

	public function customerSpace($overrideCustomer="") {
		$dbName = $overrideCustomer!="" ? $overrideCustomer : $this->customerDbName;
		$this->currentSpace = "CUSTOMER";
		$this->currentPath = $this->rootPath."CUSTOMER/".$dbName."/";
		if(!file_exists($this->currentPath)) {
			mkdir($this->currentPath, 0777);
			chmod($this->currentPath, 0777);
			mkdir($this->currentPath."TEMPLATE/", 0777);
			chmod($this->currentPath."TEMPLATE/", 0777);
			mkdir($this->currentPath."USER/", 0777);
			chmod($this->currentPath."USER/", 0777);
		}
		return $this;
	}

	public function userSpace($overrideCustomer="",$overrideUser="") {
		$dbName = $overrideCustomer!="" ? $overrideCustomer : $this->customerDbName;
		$coreUserID = $overrideUser!="" ? $overrideUser : $this->coreUserID;
		$this->currentSpace = "USER";
		$this->currentPath = $this->rootPath."CUSTOMER/".$dbName."/USER/".$coreUserID."/";
		if(!file_exists($this->rootPath."CUSTOMER/".$dbName)) {
			mkdir($this->rootPath."CUSTOMER/".$dbName, 0777);
			chmod($this->rootPath."CUSTOMER/".$dbName, 0777);
			mkdir($this->rootPath."CUSTOMER/".$dbName."/TEMPLATE/", 0777);
			chmod($this->rootPath."CUSTOMER/".$dbName."/TEMPLATE/", 0777);
			mkdir($this->rootPath."CUSTOMER/".$dbName."/USER/", 0777);
			chmod($this->rootPath."CUSTOMER/".$dbName."/USER/", 0777);
		}
		if(!file_exists($this->rootPath."CUSTOMER/".$dbName."/USER/")) {
			mkdir($this->rootPath."CUSTOMER/".$dbName."/USER/", 0777);
			chmod($this->rootPath."CUSTOMER/".$dbName."/USER/", 0777);
		}
		if(!file_exists($this->currentPath)) {
			mkdir($this->currentPath, 0777);
			chmod($this->currentPath, 0777);
			mkdir($this->currentPath."TMP/", 0777);
			chmod($this->currentPath."TMP/", 0777);
		}
		return $this;
	}

	public function cloneCustomer($newCustomer) {
		if($this->currentSpace != "CUSTOMER") return false;
		$sourcePath = $this->currentPath;
		$destinationPath = $this->rootPath."CUSTOMER/".$newCustomer."/";
		$this->recursive_copy($sourcePath, $destinationPath);
	}

	public function deleteUser() {
		if($this->currentSpace != "USER") return false;
		if($this->currentPath == "" || $this->currentPath == $this->rootPath) return false;
		if(!preg_match('/CUSTOMER\/[_0-9a-zA-Z]{1,40}\/USER\/[0-9]{1,10}\//', $this->currentPath)) return false;
		return $this->recursive_delete($this->currentPath);
	}

	public function getFullPath() {
		return $this->currentPath.$this->additionalPath;
	}

	public function setPath($pathName) {
		if(!preg_match("/^[-\/_.0-9a-zA-Z]{1,250}$/", $pathName)) return false;
		if(substr($pathName, -1)!="/") $pathName .= "/"; // fehlende '/' am Ende muessen hinzugefuegt werden
		if(substr($pathName, 0, 1)=="/") $pathName = substr($pathName, 1); // fuehrende '/' muessen geloescht werden
		$this->additionalPath = $pathName;
		return $this;
	}

	public function getPath() {
		return $this->additionalPath;
	}

	public function getFile() {
		return $this->currentFileName;
	}

	public function setFile($fileName) {
		if(!preg_match("/^[-_.0-9a-zA-Z]{1,250}$/", $fileName)) return false;
		$this->currentFileName = $fileName;
		return $this;
	}

	public function exists($fileName) {
		return file_exists($this->currentPath.$this->additionalPath.$this->currentFileName);
	}

	public function putContents($content) {
		if($this->additionalPath=="") return false;
		if($this->currentFileName=="") return false;
		if(!file_exists($this->currentPath.$this->additionalPath)) return false;
		$ret = file_put_contents($this->currentPath.$this->additionalPath.$this->currentFileName, $content);
		chmod($this->currentPath.$this->additionalPath.$this->currentFileName, 0666);
		return $ret;
	}

	public function getContents() {
		if($this->additionalPath=="") return false;
		if($this->currentFileName=="") return false;
		if(!file_exists($this->currentPath.$this->additionalPath)) return false;
		return file_get_contents($this->currentPath.$this->additionalPath.$this->currentFileName);
	}

	public function fopen($mode) {
		if($this->fileHandle != null) return false;
		if($this->additionalPath=="") return false;
		if($this->currentFileName=="") return false;
		if(!file_exists($this->currentPath.$this->additionalPath)) return false;
		$this->fileHandle = fopen($this->currentPath.$this->additionalPath.$this->currentFileName, $mode);
		return $this->fileHandle;
	}

	public function fclose() {
		if($this->fileHandle == null) return false;
		$ret = fclose($this->fileHandle);
		if($this->currentFileName!="" && file_exists($this->currentPath.$this->additionalPath.$this->currentFileName)) chmod($this->currentPath.$this->additionalPath.$this->currentFileName, 0666);
		$this->fileHandle == null;
		return $ret;
	}

	public function size() {
		if($this->additionalPath=="") return false;
		if($this->currentFileName=="") return false;
		if(!file_exists($this->currentPath.$this->additionalPath.$this->currentFileName)) return false;
		return filesize($this->currentPath.$this->additionalPath.$this->currentFileName);
	}

	public function readfile() {
		if($this->additionalPath=="") return false;
		if($this->currentFileName=="") return false;
		if(!file_exists($this->currentPath.$this->additionalPath.$this->currentFileName)) return false;
		return readfile($this->currentPath.$this->additionalPath.$this->currentFileName);
	}

	public function renameFile($newName) {
		if(!preg_match("/^[-_.0-9a-zA-Z]{1,250}$/", $newName)) return false;
		if($this->currentFileName=="") return false;
		return @rename($this->currentPath.$this->additionalPath.$this->currentFileName, $this->currentPath.$this->additionalPath.$newName);
	}

	public function renameDir($newName) {
		if(!preg_match("/^[-_.0-9a-zA-Z]{1,250}$/", $newName)) return false;
		if($this->additionalPath=="") return false;
		$arr = explode("/", $this->additionalPath);
		if(count($arr)<2) return false;
		$arr[count($arr)-2] = $newName;
		$newPath = implode("/",$arr);
		return @rename($this->currentPath.$this->additionalPath, $this->currentPath.$newPath);
	}

	public function deleteFile() {
		if($this->currentFileName=="") return false;
		return @unlink($this->currentPath.$this->additionalPath.$this->currentFileName);
	}

	public function moveTo($destDir) {
		if($this->additionalPath=="" || $destDir=="") return false;
		if(!preg_match("/^[-\/_.0-9a-zA-Z]{1,250}$/", $destDir)) return false;
		if(substr($destDir, -1)!="/") $destDir .= "/"; // fehlende '/' am Ende muessen hinzugefuegt werden
		if(substr($destDir, 0, 1)=="/") $destDir = substr($destDir, 1); // fuehrende '/' muessen geloescht werden
		if(!file_exists($this->currentPath.$this->additionalPath)) return false;

		if($this->currentFileName=="") {
			//directory copy mode
			$this->recursive_copy($this->currentPath.$this->additionalPath, $this->currentPath.$destDir);
			return $this->recursive_delete($this->currentPath.$this->additionalPath);
		}else{
			//file copy mode
			if(!file_exists($this->currentPath.$destDir)) return false;
			return @rename($this->currentPath.$this->additionalPath.$this->currentFileName, $this->currentPath.$destDir.$this->currentFileName);
		}
	}

	public function pathinfo() {
		if($this->currentFileName=="") return false;
		$pp = pathinfo($this->currentPath.$this->additionalPath.$this->currentFileName);
		$pp['dirname'] = $this->additionalPath;
		return $pp;
	}

	public function makeDir() {
		if($this->additionalPath=="") return false;
		if(!file_exists($this->currentPath.$this->additionalPath)) {
			$arrDir = explode("/",$this->additionalPath);
			$wholePath = $this->currentPath;
			foreach($arrDir as $curDir) {
				if($curDir!="") {
					$wholePath .= $curDir."/";
					if(!file_exists($wholePath)) {
						mkdir($wholePath, 0777); //0777 scheint gar nichts zu bringen... daher ist die naechste Zeile mit chmod noetig!
						chmod($wholePath, 0777);
					}
				}
			}
		}
		return true;
	}

	public function listDir($sort=0) {
		if($this->additionalPath=="") return false;
		if(!file_exists($this->currentPath.$this->additionalPath)) return false;

		$dirList = scandir($this->currentPath.$this->additionalPath,$sort);
		array_splice($dirList, array_search('.',$dirList), 1);
		array_splice($dirList, array_search('..',$dirList), 1);
//		$dirList = array_diff($dirList, array('.','..')); //array ist nach array_diff u.U. nicht mehr 0-basiert!

		return $dirList;
	}


	public function deleteDir() {
		if($this->additionalPath=="") return false;
		return @$this->recursive_delete($this->currentPath.$this->additionalPath);
	}

	public function getTemplatePath($pluginName,$fileName) {
		$templatePath = "";
		if($this->currentSpace != "USER" && $this->currentSpace != "CUSTOMER" && $this->currentSpace != "GLOBAL") {
			$this->userSpace();
			if(file_exists($this->currentPath."TEMPLATE/".$pluginName."/".$fileName)) $templatePath = "TEMPLATE/".$pluginName."/";
			else{
				$this->customerSpace();
				if(file_exists($this->currentPath."TEMPLATE/".$pluginName."/".$fileName)) $templatePath = "TEMPLATE/".$pluginName."/";
				else{
					$this->globalSpace();
					if(file_exists($this->currentPath."TEMPLATE/".$pluginName."/".$fileName)) $templatePath = "TEMPLATE/".$pluginName."/";
				}
			}
		}else{
			if(file_exists($this->currentPath."TEMPLATE/".$pluginName."/".$fileName)) $templatePath = "TEMPLATE/".$pluginName."/";
		}

		if($templatePath == "") return false;
		else{
			$this->additionalPath = $templatePath;
			$this->currentFileName = $fileName;
			return $this->currentPath.$this->additionalPath.$this->currentFileName;
		}
	}

	public function setTmpDir($token) {
		if(!preg_match("/^[0-9a-zA-Z]{32,32}$/", $token)) return false;
		$this->userSpace();
		if(file_exists($this->currentPath."TMP/".$token)) {
			$this->additionalPath = "TMP/".$token."/";
			return true;
		}else{
			return false;
		}
	}

	public function createTmpDir() {
		if($this->currentSpace != "USER") $this->userSpace();

		//Ggf. alte tmp-Unterverzeichnisse > 24h loeschen
				// ---> unix-timestamp = filectime($filename)  VERGLEICHEN MIT  time()
		//neuer Verzeichnisname ermitteln
		do{
			$newTmpDirName = md5(time().$this->currentPath);
		}while(file_exists($this->currentPath."TMP/".$newTmpDirName));
		$this->additionalPath = "TMP/".$newTmpDirName."/";
		//Verzeichnisname anlegen
		mkdir($this->currentPath.$this->additionalPath, 0777);
		chmod($this->currentPath.$this->additionalPath, 0777);
		$this->currentSpace = "USER_TMP";
		return $newTmpDirName;
	}

	private function recursive_copy($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		chmod($dst, 0777);
		while(false !== ( $file = readdir($dir)) ) {
			if(( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src.'/'.$file) ) $this->recursive_copy($src.'/'.$file, $dst.'/'.$file);
				else{
					copy($src.'/'.$file, $dst.'/'.$file);
					chmod($dst.'/'.$file, 0777);
				}
			}
		}
		closedir($dir);
	} 

	private function recursive_delete($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->recursive_delete("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}
?>
