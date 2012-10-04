<?php
/**
 * Cross Media Publishing - CMP3
 * www.cross-media.net
 *
 * LICENSE
 *
 * This source file is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * @subpackage System
 * @package    CMP3
 * @copyright  Copyright (c) 2009 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3;



/**
 * Registry
 *
 * Should be used static only:
 *
 * \Cmp3\Registry::isRegistered('\Next\User:Property:Profile')
 * \Cmp3\Registry::Set('DownloadLogging', 'tx_nawdownloadlog_LogEntry')
 *
 * The Registry can be instanciated with a singleton but this is useful if the iterater needs to be used only
 *
 *
 * @author Rene Fritz <r.fritz@bitmotion.de>
 * @author SoBeNoFear <ianunruh@gmail.com>
 * @subpackage Core
 * @package    CMP3
 */
class Registry implements \ArrayAccess, \Iterator {


	/**
	 * Array of objects
	 * @var array
	 */
	protected static $_registry = array ();


	/**
	 * Protected magic methods
	 *
	 * These magic methods are protected to prevent cloning,
	 * serializing, and constructing the registry, which would
	 * break the singleton pattern
	 *
	 * @return void
	 */
	protected function __construct()
	{
	}
	public function __clone() {
	    throw new Exception("Because of the nature of a singleton, method is disallowed: ".__METHOD__);
	}
	public function __sleep() {
	    throw new Exception("Because of the nature of a singleton, method is disallowed: ".__METHOD__);
	}
	public function __wakeUp() {
	    throw new Exception("Because of the nature of a singleton, method is disallowed: ".__METHOD__);
	}


	/**
	 * Retrieve or construct instance of the registry
	 * @return object
	 */
	public static function Singleton()
	{
		/**
		 * Singleton instance of registry
		 * @var object
		 */
		static $__instance = null;

		if (! is_object ( $__instance )) {
			$me = __CLASS__;
			$__instance = new $me ( );
		}
		return $__instance;
	}


	/**
	 * Retrieve or construct instance of the registry
	 * @return object
	 */
	public static function GetInstance()
	{
		return self::Singleton();
	}




	/******************************
	 *
	 * methods
	 *
	 ******************************/


	/**
	 * Retrieve pre-existing registry value
	 *
	 *
	 * @param string $alias Object alias or abstract to retrieve
	 * @return mixed|null Object on success; null on failure
	 */
	public function Get($alias)
	{
		if (array_key_exists ( $alias, self::$_registry )) {
			return self::$_registry [$alias];
		}
		return null;
	}


	/**
	 * Set object to registry alias
	 *
	 * @param string|array $alias Abstract to bind/alias to set/array of assoc objects
	 * @param mixed $object [optional] Concrete/object to set
	 * @return void
	 */
	public function Set($alias, $object = '')
	{
		self::$_registry [$alias] = $object;
	}


	/**
	 * Check for existing object
	 *
	 * @param string $alias Object to check
	 * @return bool Whether it exists or not
	 */
	public function isRegistered($alias)
	{
		return array_key_exists ( $alias, self::$_registry );
	}


	/**
	 * Unset object/unbind abstract
	 *
	 * @param string $alias Abstact to unset
	 * @return bool Whether the object existed or not to begin with
	 */
	public function Unregister($alias)
	{
		if (array_key_exists ( $alias, self::$_registry )) {
			unset ( self::$_registry [$alias] );
			return true;
		}
		return false;
	}


	/**
	 * @see Registry::Get()
	 */
	public function __get($alias)
	{
		return self::Get ( $alias );
	}


	/**
	 * @see Registry::Set()
	 */
	public function __set($alias, $object)
	{
		return self::Set ( $alias, $object );
	}


	/**
	 * @see Registry::isRegistered()
	 */
	public function __isset($alias)
	{
		return self::isRegistered ( $alias );
	}


	/**
	 * @see Registry::Unregister()
	 */
	public function __unset($alias)
	{
		return self::Unregister ( $alias );
	}





	/*********************
	 *
	 * Iterator
	 *
	 *********************/


	/**
	 * @see Registry::Get()
	 */
	public function offsetGet($alias)
	{
		return self::Get ( $alias );
	}


	/**
	 * @see Registry::Set()
	 */
	public function offsetSet($alias, $object)
	{
		return self::Set ( $alias, $object );
	}


	/**
	 * @see Registry::isRegistered()
	 */
	public function offsetExists($alias)
	{
		return self::isRegistered ( $alias );
	}


	/**
	 * @see Registry::Unregister()
	 */
	public function offsetUnset($alias)
	{
		return self::Unregister ( $alias );
	}


	/**
	 * Methods that allow cycling through the objects array
	 *
	 * Declared to satisfy the Iterator interface
	 * Allows for procedures like this:
	 * <code>
	 * 	foreach(Registry::singleton() as $a=>$o){
	 * 		## do something with this registry object
	 *	}
	 * </code>
	 */
	public function current() {
		return current ( self::$_registry );
	}

	public function key() {
		return key ( self::$_registry );
	}

	public function next() {
		return next ( self::$_registry );
	}

	public function rewind() {
		return reset ( self::$_registry );
	}

	public function valid() {
		return ( bool ) $this->current ();
	}

}




