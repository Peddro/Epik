<?php
/**
 * Exception
 */

/**
 * Invalid Request Exception
 *
 * This exception is thrown when an Ajax action is invoked directly by a user or vice-versa.
 *
 * @package app.Lib.Error.Exception
 * @author Bruno Sampaio
 */
class InvalidRequestException extends CakeException {
	
	/**
	 * Constructor
	 *
	 * @param string $message - the error message (ignored).
	 * @param int $code - the error code.
	 */
	public function __construct($message = null, $code = 404) {
		parent::__construct(__('Invalid Request'), $code);
	}
}