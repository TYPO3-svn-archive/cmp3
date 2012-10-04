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
 * @subpackage Job
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Job;



/**
 * The queue handles jobs
 *
 * This variant does local running of jobs.
 * Another variant might pass jobs to a daemon for asynchronous processing.
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Job
 * @package    CMP3
 */
class Queue {


	/**
	 *
	 * @var Job[]
	 */
	protected static $objJobArray = array();



	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
	}


	/**
	 * Creates a job
	 *
	 * @param string $strJobName The name of the hob that should be rendered. Has to be defined in the config of course.
	 * @param \Cmp3\Config\ConfigInterface $objConfig The configuration not only for the job but also for sources and transformations
	 * @return Job
	 */
	public function CreateJob($strJobName, \Cmp3\Config\ConfigInterface $objConfig)
	{
		$objJob = new \Cmp3\Job\Job($strJobName, $objConfig);

		$objJob->ID = $this->CreateJobID();

		$objLog = new \Cmp3\Log\Logger($objLogWriter);
		$objLog->SetJobID($objJob->ID);
		$objLogWriter = new \Zend_Log_Writer_Stream(\Cmp3\Cmp3::$LogPath . $objJob->ID . '.log');
		$objLog->AddWriter($objLogWriter, 'Stream');
		$objLogWriter = new \Cmp3\Log\LogWriter_Memory();
		$objLog->AddWriter($objLogWriter, 'Memory');

		$objJob->Logger = $objLog;

		self::$objJobArray[$objJob->ID] = $objJob;

		return $objJob;
	}


	/**
	 * This can be used to add a job which is not created by CreateJob()
	 * @todo we should use an interface for that
	 *
	 * @param Job $objJob
	 * @return string JobID
	 */
	public function Add(Job $objJob)
	{
		if (!$objJob->ID) {
			$objJob->ID = $this->CreateJobID();
		}

/*
    $logger = Zend_Log::factory(array(
        array(
            'writerName'   => 'Stream',
            'writerParams' => array(
                'stream'   => '/tmp/zend.log',
            ),
            'filterName'   => 'Priority',
            'filterParams' => array(
                'priority' => Zend_Log::WARN,
            ),
        ),
        array(
            'writerName'   => 'Firebug',
            'filterName'   => 'Priority',
            'filterParams' => array(
                'priority' => Zend_Log::INFO,
            ),
        ),
    ));
 */

		if (!$objLog->objLogger) {
			$objLogWriter = new \Zend_Log_Writer_Stream(\Cmp3\Cmp3::$LogPath . $objJob->ID . '.log');
			$objLog = new \Cmp3\Log\Logger($objLogWriter);
			$objLog->SetJobID($objJob->ID);

			$objJob->Logger = $objLog;
		}

		self::$objJobArray[$objJob->ID] = $objJob;

		return $objJob->ID;
	}


	/**
	 *
	 * @param string|Job $Job Job object or job ID
	 */
	public function RunJob($Job)
	{
		if ($Job instanceof Job) {
			$objJob = $Job;
		} else {
			$objJob = self::$objJobArray[$Job];
		}
		$objJob->Run();
	}


	/**
	 *
	 * @param string|Job $Job Job object or job ID
	 * @return Job
	 */
	public function GetJob($Job)
	{
		if ($Job instanceof Job) {
			$objJob = $Job;
		} else {
			$objJob = self::$objJobArray[$Job];
		}
		return $objJob;
	}


	/**
	 *
	 * @param string|Job $Job Job object or job ID
	 * @return boolean
	 */
	public function isJobFinished($Job)
	{
		if ($Job instanceof Job) {
			$objJob = $Job;
		} else {
			$objJob = self::$objJobArray[$Job];
		}
		return $objJob->isFinished;
	}


	/**
	 *
	 * @param string|Job $Job Job object or job ID
	 * @return Result
	 */
	public function GetResult($Job)
	{
		if ($Job instanceof Job) {
			$objJob = $Job;
		} else {
			$objJob = self::$objJobArray[$Job];
		}
		return $objJob->GetResult();
	}


	/**
	 * @return string
	 */
	protected function CreateJobID()
	{
		return number_format(microtime(true), 2, '', '');
	}

}


