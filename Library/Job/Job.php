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
 * A job has two functions:
 * 1. It holds all information which are needed to do the job. This might be configuration and/or other data
 * 2. Do the actual processing in the Run method.
 *
 * @property-read  mixed $ID Uniq ID for this job
 * @property-read  boolean $isFinished Flag that indicates if the job is finished or not
 * @property-read  boolean $hasErrors Flag that indicates if errors occured while processing
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Job
 * @package    CMP3
 */
interface JobInterface {

	/**
	 * Runs this job
	 * @return void
	 */
	public function Run();
}



/**
 * {@inheritdoc}
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Job
 * @package    CMP3
 *
 * @property \Cmp3\Log\Logger $Logger
 */
class Job implements JobInterface {

	/**
	 *  The name of the hob that should be rendered. Has to be defined in the config of course.
	 * @var string
	 */
	public $strJobName;

	/**
	 * The configuration not only for the job but also for sources and transformations
	 * @var \Cmp3\Config\ConfigResource
	 */
	protected $objConfig;

	protected $objSourcesListConfig;
	protected $objTransformationListConfig;

	/**
	 * Logger object
	 *
	 * @var \Cmp3\Log\Logger
	 */
	protected $objLogger;

	/**
	 *
	 * @var Task[]
	 */
	public $TaskArray;

	/**
	 *
	 * @var array
	 */
	protected $DataArray;

	/**
	 *
	 * @var array
	 */
	protected $ObjectArray;

	/**
	 * Result object
	 * @var Result
	 */
	protected $objResult;

	/**
	 * Uniq ID for this job
	 * @var string
	 */
	public $ID;

	/**
	 * Timestamp set when job is started
	 * @var integer
	 */
	public $Time;

	/**
	 * Flag that indicates if the job is finished or not
	 * @var boolean
	 */
	public $isFinished = false;

	/**
	 * Flag that indicates if errors occured while processing
	 * @var boolean
	 */
	public $hasErrors = false;

	/**
	 * might be set by unit tests
	 *
	 * @var boolean
	 */
	public static $throwExceptions = false;


	/**
	 * Constructor
	 *
	 * @param string $strJobName The name of the hob that should be rendered. Has to be defined in the config of course.
	 * @param \Cmp3\Config\ConfigInterface $objConfig The configuration not only for the job but also for sources and transformations
	 */
	public function __construct($strJobName, \Cmp3\Config\ConfigInterface $objConfig)
	{
		$this->strJobName = $strJobName;
		$this->objConfig = new \Cmp3\Config\DataDecorator($objConfig, $this);
		$this->objSourcesListConfig = $this->objConfig->GetProxy('source.');
		$this->objTransformationListConfig = $this->objConfig->GetProxy('transformation.');

		// we use that as result object in case of errors
		$this->objResult = new Result(null);
	}


	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 * @throws \Cmp3\UndefinedGetPropertyException
	 */
	public function __get($strName)
	{
		switch ($strName) {

			case 'Logger':
				return $this->objLogger;

			default:
				throw new \Cmp3\UndefinedGetPropertyException($strName);
		}
	}


	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName with $mixValue
	 *
	 * @param string $strName Name of the property to get
	 * @param string $mixValue Value of the property to set
	 * @return mixed
	 * @throws \Cmp3\UndefinedSetPropertyException
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'Logger':
				return $this->objLogger = $mixValue;

			default:
				throw new \Cmp3\UndefinedSetPropertyException($strName);
		}
	}


	/**
	 * Add a task to the jobs task array
	 *
	 * @param Task $objTask
	 */
	public function AddTask(Task $objTask)
	{
		$objTask->Job = $this;
		$this->TaskArray[] = $objTask;
	}


	/**
	 * Add data to the job which might be used by processors
	 *
	 * @param string $strName
	 * @param mixed $mixData
	 */
	public function AddData($strName, $mixData)
	{
		$this->DataArray[$strName] = $mixData;
	}


	/**
	 * Get data by it's name
	 *
	 * @param string $strName
	 */
	public function GetData($strName)
	{
		return $this->DataArray[$strName];
	}


	/**
	 * Add an object to the job which might be used somewhere in the configuration with OBJ:somename
	 *
	 * @param string $strName
	 * @param object $objMixed
	 */
	public function AddObject($strName, $objMixed)
	{
		$this->ObjectArray[$strName] = $objMixed;
	}


	/**
	 * Get an object by it's name
	 *
	 * @param string $strName
	 */
	public function GetObject($strName)
	{
		return $this->ObjectArray[$strName];
	}


	/**
	 * Runs this job
	 *
	 * @throws \Cmp3\Job\Exception
	 */
	public function Run()
	{
		$this->Time = time();

		$objTimeTravel = new  \Cmp3\Tools\TimeTravel();

		$this->objLogger->Info(__CLASS__ . " Start job '{$this->strJobName}' with ID {$this->ID}");
		$this->objLogger->Info(__CLASS__ . " Default language: " . \Cmp3\Cmp3::$DefaultLanguage);

		try {

			// tasks not yet created so we're going to do this right now
			if (!$this->TaskArray) {
				$this->Prepare();
			}

			// something went wrong, propably missing config
			if (!$this->TaskArray) {
				throw new Exception('No tasks defined for this job');
			}

			// let's run the tasks
			foreach($this->TaskArray as $objTask) {

				/* @var $objTask Task */
				$objTask->Run();
			}

			// compose processing - eg. merge PDF
			$objResultArray = array();
			foreach($this->TaskArray as $objTask) {

				/* @var $objTask Task */
				$objResultArray[] = $objTask->Result;
			}

			if ($this->objConfig->hasProperty('job.' . $this->strJobName . '.compose.')) {
#FIXME we're getting here even if no compose is defined - wrong Config class used?
				$this->objLogger->Info("Composing is defined for this job");

				$objConfig = $this->objConfig->GetProxy('job.' . $this->strJobName . '.compose');
				$objConfig->SetValue('debug', $this->objConfig->isEnabled('debug'));
				$objResultArray = $this->ComposeResult($objResultArray, $objConfig);
			} else {
				$this->objLogger->Info("Composing is not defined for this job");
			}


			if (count($objResultArray) > 1) {
				throw new Exception('We have too many results (' . count($objResultArray) . '). Just one was expected.');
			}

			$this->objResult = reset($objResultArray);

			if (!($this->objResult instanceof ResultInterface)) {
				throw new Exception('Something went wrong, result is not of type ResultInterface: ' . (is_object($this->objResult) ? get_class($this->objResult) : gettype($this->objResult)));
			}


		} catch (\Exception $e) {

			$this->hasErrors = true;

			$this->objLogger->Info(__CLASS__ . " Job failed");
			$this->objLogger->Info(__CLASS__ . " Job runtime: " . $objTimeTravel->GetRuntime());

			$this->objResult->ErrorMessage = $e->getMessage();

			$this->objLogger->Log($e->getMessage(), \Cmp3\Log\Logger::EMERG);
			$this->objLogger->Debug($this->MakePrettyException($e));

			if (self::$throwExceptions) {
				throw $e;
			}
		}

		if (!$this->hasErrors) {
			$this->objLogger->Info(__CLASS__ . " Job finished");
			$this->objLogger->Info(__CLASS__ . " Job runtime: " . $objTimeTravel->GetRuntime());
		}

		$this->objResult->hasErrors = $this->hasErrors;
		if ($objLog = $this->objLogger->GetWriter('Memory')) {
			$this->objResult->Log = str_replace("\n\n", "\n", (string)$objLog->GetLog());
		}

		$this->isFinished = true;
	}


	/**
	 * This returns the result object which should provide all needed data to access the result of the job
	 * or it provides the result itself.
	 *
	 * @return Result
	 */
	public function GetResult()
	{
		return $this->objResult;
	}


	/**
	 * Compose result
	 *
	 * @param \Cmp3\Job\Result[] $objResultArray
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 */
	protected function ComposeResult($objResultArray, $objConfig)
	{
		$objResultProcessing = null;


		if ($objConfig) {
			$objResultProcessing = \Cmp3\Composer\ComposerProcessing::Factory($objConfig, $this, $this->objLogger);
		}

		if ($objResultProcessing) {

			$this->objLogger->Info("Start compose processing");

			$objResultArray = $objResultProcessing->Process($objResultArray);
		}

		return $objResultArray;
	}


	/**
	 * prepares a job defined by the given configuration
	 * this uses config data to creates tasks
	 *
	 * @throws \Cmp3\Exception
	 * @throws Exception
	 * @return void
	 */
	public function Prepare()
	{
		$objJobConfig = $this->objConfig->GetProxy('job.' . $this->strJobName . '.');

		// collect sources for the job

		/* @var $objSourceArray Source_Interface[] */
		$objTaskArray = array();

		$objConfigIterator = new \Cmp3\Config\ConfigIterator ($objJobConfig, 'parts.');
		foreach($objConfigIterator as $key => $objConfig) {


			if ($objConfig->isEnabled('enabled', true)) {
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Create task for key: ' . $key);
				$objTask = $this->CreateTaskFromConfig($objConfig);
				$this->AddTask($objTask);

			} else {
				if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Do not create task for key: ' . $key . ' Is disabled.');
			}
		}
	}


	/**
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @return Task
	 */
	public function CreateTaskFromConfig($objConfig)
	{
		$objTask = new Task();

		$objTask->Config = $objConfig;

		// create scource object
		$objTask->Source = $this->CreateTaskSourceFromConfig($objConfig);

		// create transformation object
		$objTask->Transformation = $this->CreateTaskTransformationFromConfig($objConfig);

		return $objTask;
	}


	/**
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @return \Cmp3\Source\SourceInterface
	 * @throws Exception
	 */
	public function CreateTaskSourceFromConfig($objConfig)
	{
		if (!$objConfig->hasProperty('source')) {
			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No \'source\' is defined for this task. This might be an error but could be intended too.');
			return false;
		}

		$strSourceName = $objConfig->GetValue('source');

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Source '$strSourceName' should be used");
#TODO add this in CreateTaskTransformationFromConfig too
		list($strMagic, $strObjectName) = explode(':', $strSourceName);
		if ($strMagic=='OBJ' AND $strObjectName) {
			$objSource = $this->GetObject($strObjectName);
			if (!is_object($objSource) OR !($objSource instanceof \Cmp3\Source\SourceInterface)) {
				throw new Exception("The object '$strObjectName' which is configured isn't available. Bug in application?");
			}

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Use object source with name: ' . $strObjectName);

		} else {


			$strSourceClass = $this->objSourcesListConfig->GetValue($strSourceName);
			if (!class_exists($strSourceClass)) {
				throw new Exception("Could not find source class: '$strSourceClass' for source name '$strSourceName'");
			}
			$objSourceConfig = $this->objSourcesListConfig->GetProxy($strSourceName.'.');
			$objSourceConfig->SetValue('debug', $this->objConfig->isEnabled('debug'));

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Create source: ' . $strSourceClass);

			$objProperties = array();
			$objProperties['Logger'] = $this->objLogger;
			$objProperties['Config'] = $objSourceConfig;
			$objProperties['Job'] = $this;

			$objSource = new $strSourceClass($objProperties);
		}

		return $objSource;
	}


	/**
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 * @return \Cmp3\Transformation\TransformerInterface
	 * @throws Exception
	 */
	public function CreateTaskTransformationFromConfig($objConfig)
	{
		$objTransformation = null;

		// create transformation object
		if ($strTransformationName = $objConfig->GetValue('transformation')) {

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . " Transformation '$strTransformationName' should be used");

			$strTransformationClass = $this->objTransformationListConfig->GetValue($strTransformationName);
			if (!class_exists($strTransformationClass)) {
				throw new Exception('Could not find transformation class: '.$strTransformationClass);
			}
			$objTransformationConfig = $this->objTransformationListConfig->GetProxy($strTransformationName.'.');
			$objTransformationConfig->SetValue('debug', $this->objConfig->isEnabled('debug'));

			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Create transformation: ' . $strTransformationClass);

			$objProperties = array();
			$objProperties['Logger'] = $this->objLogger;
			$objProperties['Config'] = $objTransformationConfig;
			#TODO not yet needed $objProperties['Job'] = $this;

			$objTransformation = new $strTransformationClass($objProperties);
		}

		return $objTransformation;
	}




	/**
	 *
	 * @param \Exception $e
	 */
	protected function MakePrettyException(\Exception $e)
	{
		$trace = $e->getTrace();

		$result = 'Exception: "';
		$result .= $e->getMessage();
		$result .= '" @ ';
		if($trace[0]['class'] != '') {
			$result .= $trace[0]['class'];
			$result .= '->';
		}
		$result .= $trace[0]['function'];
		$result .= "();\n";

		return $result;
	}
}


