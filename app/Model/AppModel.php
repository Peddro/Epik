<?php
/**
 * Imports
 */
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package app.Model
 * @author Bruno Sampaio
 */
class AppModel extends Model {
	
	/**
	 * Get an image path for a certain type of item.
	 *
	 * @param array $data - the item data.
	 * @param string $type - the item type (Model Name).
	 * @param string $mainFolder - where to search for the file.
	 * @param string $filename - the filename without extension.
	 * @param string $field - the field that determines if this item has an image.
	 * @return string - the image file path.
	 */
	protected function getImagePath($data, $type, $mainFolder, $filename, $field) {
		$type = lcfirst($type);
		
		if($data[$field]) {
			
			foreach(Configure::read('Files.types.image') as $ext) {
				if(file_exists($mainFolder.$filename.'.'.$ext)) {
					return $filename.'.'.$ext;
				}
			}
		}
		return Configure::read("Default.$type.img");
	}
	
	
	/**
	 * Checks if an item was imported from an external source.
	 * For that it must contain the attributes lms_id, lms_url, and external_id.
	 *
	 * @param array $data - the item data.
	 * @return bool
	 */
	protected function wasImported($data) {
		return isset($data['lms_id']) && $data['lms_id'] && isset($data['lms_url']) && $data['lms_url'] && isset($data['external_id']) && $data['external_id'];
	}
	

	/**
	 * Checks if a string starts with a certain string of characters.
	 *
	 * @param string $haystack - is the full string.
	 * @param string $needle - is the string to check against.
	 * @return bool
	 */
	protected function startsWith($haystack, $needle) {
	    $length = strlen($needle);
	    return (substr($haystack, 0, $length) === $needle);
	}


	/**
	 * Checks if a string ends with a certain string of characters.
	 *
	 * @param string $haystack - is the full string.
	 * @param string $needle - is the string to check against.
	 * @return bool
	 */
	protected function endsWith($haystack, $needle) {
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }

	    return (substr($haystack, -$length) === $needle);
	}

}
