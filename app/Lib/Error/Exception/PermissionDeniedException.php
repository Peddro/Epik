<?php
/**
 * Exception
 */

/**
 * Permission Denied Exception
 *
 * This exception is thrown when a user tries to access contents that do not belong to him.
 *
 * @package app.Lib.Error.Exception
 * @author Bruno Sampaio
 */
class PermissionDeniedException extends CakeException {
	
	/**
	 * Constructor
	 *
	 * @param string $message - the error message (ignored).
	 * @param int $code - the error code.
	 */
	public function __construct($message = null, $code = 400) {
		parent::__construct(__('Permission Denied'), $code);
	}
}