<?php
/**
 * Exception
 */

/**
 * Security Breach Exception
 *
 * This exception is thrown when a security breach occurs.
 *
 * @package app.Lib.Error.Exception
 * @author Bruno Sampaio
 */
class SecurityBreachException extends CakeException {
	
	/**
	 * Constructor
	 *
	 * @param string $message - the error message (ignored).
	 * @param int $code - the error code.
	 */
	public function __construct($message = null, $code = 400) {
		$this->_attributes['type'] = isset($message['type'])? $message['type'] : null;
		parent::__construct(__('Security Breach'), $code);
	}
	
	
	/**
	 * Type of security breach.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_attributes['type'];
	}
	
}