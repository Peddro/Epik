<?php
/**
 * Imports
 */
App::uses('Component', 'Controller');

App::import('Vendor', 'curl');

/**
 * Files Component
 *
 * @package app.Controller.Component
 * @author Bruno Sampaio
 */
class FilesComponent extends Component {
	
	/**
	 * Create File
	 *
	 * @param string $path - the file path.
	 * @param string $content - the file content.
	 * @param int $mode - the permissions mode.
	 */
	public function create($path, $content, $mode=493) {
		$dataFile = new File($path, true, $mode);
		$dataFile->write($content);
		$dataFile->close();
	}
	
	/**
	 * Get File Name without Extension
	 * 
	 * @param string $filename - the file name with extension.
	 * @return string - the file name without extension
	 */
	public function getName($filename) {
		return basename($filename, '.'.pathinfo($filename, PATHINFO_EXTENSION));
	}
	
	/**
	 * Get Files inside Folder
	 * 
	 * @param string $folder - the folder name.
	 * @param string $url - the url to the folder.
	 * @param string $type - the files type.
	 * @param array $list - the list where to load the files.
	 * @param array $match - the list of file names to be matched.
	 */
	public function getFolderFiles($folder, $url, $type, &$list, &$match=false) {
		$dir = new Folder($folder);
		$files = $dir->read(false, true)[1];
		foreach($files as $file) {
			$name = $this->getName($file);
			if(!$match || (is_array($match) && (isset($match[$name]) || in_array($name, $match)))) {
				$list[$name] = array('type' => $type, 'url' => $url.$file);
			}
		}
	}
	
	/**
	 * Download File
	 * 
	 * Downloads a file from an external system.
	 * @param string $folder - folder to store the file.
	 * @param string $filename - file name to be used.
	 * @param array $file - file object.
	 * @return array
	 */
	public function download($folder, $filename, $file) {
		
		$found = false;
		$types = Configure::read('Files.types');
		$current = current($types);
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		
		// Search for file type using its extension
		do {
			$found = in_array($ext, $current);
			if(!$found) next($types);
			
		} while(!$found && ($current = current($types)));
		
		// If file type was found
		if($found) {
			$file['type'] = key($types);
			$maximumSize = Configure::read('Files.size.'.$file['type']);
			
			// If file size is ok
			if($file['size'] < $maximumSize) {
				
				// check if file already exists and if yes delete it
				$this->delete($folder, $filename, false);
				
				$file['name'] = $filename.'.'.$ext;
				$file['path'] = $folder.DS.$file['name'];
				
				// Download file
				$fp = fopen($file['path'], 'w');
				$ch = curl_init($file['url']);
				curl_setopt($ch, CURLOPT_FILE, $fp);
				$data = curl_exec($ch);
				curl_close($ch);
				fclose($fp);
			}
			else {
				$file['error'] = __('error-file-size', $maximumSize/1024);
			}
		}
		else {
			$file['error'] = __('error-file-unknown', $file['type']);
		}
		return $file;
	}
	
	
    /**
	 * Upload File
	 * 
	 * Stores a file uploaded by the user.
	 * @param string $folder - folder to store the file.
	 * @param string $filename - file name to be used.
	 * @param array $file - file object.
	 * @return array
	 */
	public function upload($folder, $filename, $file) {
		// setup dir names absolute and relative
		$folder = $this->getPath($folder, '');
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		
		// create the folder if it does not exists
		if(!is_dir($folder)) {
			mkdir($folder);
		}
		
		// get file types
		$types = explode('/', $file['type']);
		
		// maximum size for this file type
		$maximumSize = Configure::read('Files.size.'.$types[0]);
		
		// list of permitted file types
		$permittedTypes = Configure::read('Files.types.'.$types[0]);
		
		if(count($types) == 2 && !empty($maximumSize) && !empty($permittedTypes)) {
			
			if($file['size'] <= $maximumSize) {
				
				// if file type ok upload the file
				if(in_array($types[1], $permittedTypes)) {

					if(!$file['error']) {

						// check if file already exists and if yes delete it
						$this->delete($folder, $filename, false);

						// create unique filename and upload file
						$filename = $filename.'.'.$ext;
						$full_url = $folder.$filename;

						$success = move_uploaded_file($file['tmp_name'], $full_url);

						// if upload was successful
						if($success) {
							// save the url of the file
							$result['name'] = $filename;
						}
						else {
							$result['error'] = __('error-while-uploading', $filename);
						}
					}
					else {
						// an error occured
						$result['error'] = __('error-while-uploading', $filename);
					}
				} 
				else if($file['error'] == 4) {
					// no file was selected for upload
					$result['nofile'] = __('error-file-empty');
				} 
				else {
					// unacceptable file type
					$result['error'] = __('error-file-type', implode(', ', $permittedTypes));
				}
			}
			else {
				$result['error'] = __('error-file-size', $maximumSize/1024);
			}
		}
		else {
			$result['error'] = __('error-file-unknown', $types[1]);
		}
		
		return $result;
	}
	
	
	/**
	 * Rename File
	 * 
	 * Renames a file in the file system.
	 * @param string $folder - folder where the files are located.
	 * @param string $oldName - current name of the file.
	 * @param string $newName - new name for the file.
	 */
	public function rename($folder, $oldName, $newName) {
		if($oldName) {
			$oldFile = $this->getPath($folder, $oldName);
			$newFile = $this->getPath($folder, $newName).'.'.pathinfo($oldName, PATHINFO_EXTENSION);
			rename($oldFile, $newFile);
		}
	}
	
	
	/**
	 * Move File
	 *
	 * Moves a file from $oldPath to $newPath.
	 * @param string $oldPath - old path to the file.
	 * @param string $newPath - new path to the file.
	 * @return bool
	 */
	public function move($oldPath, $newPath) {
		if(copy($oldPath, $newPath)) {
			unlink($oldPath);
			return true;
		}
		else return false;
	}
	
	
	/**
	 * Delete File
	 * 
	 * Deletes a file from the file system.
	 * @param string $folder - the folder name or complete path to folder.
	 * @param string $filename - the file name.
	 * @param bool $ext - if extension is part of $filename.
	 */
	public function delete($folder, $filename, $ext=true) {
		if($filename) {
			$path = $this->getPath($folder, $filename);
			
			if($ext) {
				if(file_exists($path)) {
					unlink($path);
				}
			}
			else {
				foreach (glob($path.'.*') as $file) {
					unlink($file);
				}
			}
		}
	}
	
	
	/**
	 * Get File Path
	 *
	 * Gets a file path given a folder and a file name.
	 * @param string $folder - the folder name or complete path to folder.
	 * @param string $file - the file name.
	 */
	private function getPath($folder, $file) {
		if(substr($folder, 0, 1) == '/') {
			$len = strlen($folder);
			if(substr($folder, $len-1, 1) != '/') {
				$folder.= DS;
			}
			return $folder.$file;
		}
		else {
			return WWW_ROOT.$folder.DS.$file;
		}
	}
}