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
 * @subpackage Log
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Log;

#TODO merge with zend_log

/**
 * General logging for jobs
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Log
 * @package    CMP3
 */
class Logger extends \Zend_Log {

/*
	const EMERG   = 0;  // Emergency: system is unusable
	const ALERT   = 1;  // Alert: action must be taken immediately
	const CRIT    = 2;  // Critical: critical conditions
	const ERR     = 3;  // Error: error conditions
	const WARN    = 4;  // Warning: warning conditions
	const NOTICE  = 5;  // Notice: normal but significant condition
	const INFO    = 6;  // Informational: informational messages
	const DEBUG   = 7;  // Debug: debug messages
*/


	protected $strJobID;




	/**
	 * Set's the job id which might be needed by some writes. Will be set automatically in the constructor.
	 *
	 * @param strJobID
	 */
	public function SetJobID ($strJobID)
	{
		$this->strJobID = $strJobID;
	}


    /**
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param  \Zend_Log_Writer_Abstract $writer
     * @param string $strName
     * @return void
     */
    public function AddWriter(\Zend_Log_Writer_Abstract $writer, $strName=null)
    {
    	if ($strName) {
        	$this->_writers[$strName] = $writer;
    	} else {
    		$this->_writers[] = $writer;
    	}
    }


    /**
     * Add a writer.  A writer is responsible for taking a log
     * message and writing it out to storage.
     *
     * @param string $strName
     * @return \Zend_Log_Writer_Abstract
     */
    public function GetWriter($strName)
    {
    	return $this->_writers[$strName];
    }


	/**
	 * Log a message at a priority
	 *
	 * @param  string   $message   Message to log
	 * @param  integer  $priority  Priority of message
	 * @return void
	 * @throws Zend_Log_Exception
	 */
	public function Log($message, $priority=self::INFO)
	{
		$this->_extras = array();
		$this->_extras['ID'] = $this->strJobID;

		return parent::log((string)$message, $priority);
	}


	/**
	 * Log a message at a priority and add some data
	 *
	 * @param  string   $message   Message to log
	 * @param  mixed   	 $data      Any data
	 * @param  integer  $priority  Priority of message
	 * @return void
	 * @throws Zend_Log_Exception
	 */
	public function LogData($message, $data, $priority=self::INFO)
	{
		$this->_extras['data'] = $data;
		$this->_extras['ID'] = $this->strJobID;

		return parent::log($message, $priority);
	}
}






