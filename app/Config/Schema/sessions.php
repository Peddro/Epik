<?php
/**
 * This is Sessions Schema file
 *
 * Use it to configure database for Sessions
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

/**
 * Using the Schema command line utility
 * cake schema run create Sessions
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config.Schema
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class SessionsSchema extends CakeSchema {

	/**
	 * @var string
	 */
	public $name = 'Sessions';

	/**
	 * Before
	 * @param array $event
	 * @return bool
	 */
	public function before($event = array()) {
		return true;
	}

	/**
	 * Before
	 * @param array $event
	 */
	public function after($event = array()) {}

	/**
	 * @var array
	 */
	public $cake_sessions = array(
		'id' => array('type' => 'string', 'null' => false, 'key' => 'primary'),
		'data' => array('type' => 'text', 'null' => true, 'default' => null),
		'expires' => array('type' => 'integer', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

}
