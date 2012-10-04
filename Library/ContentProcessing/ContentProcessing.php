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
 * @subpackage ContentProcessing
 * @package    CMP3
 * @copyright  Copyright (c) 2008 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\ContentProcessing;


/**
 * Performs content processing using one or more processors
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class ContentProcessing  extends \Cmp3\BaseLogger {

	/**
	 * Logger object
	 *
	 * @var \Cmp3\Log\Logger
	 */
	protected $objLogger;


	/**
	 *
	 * @var ProcessorInterface[]
	 */
	protected $processors = array();


	/**
	 * Creates an content processor using the TypoScript style setup as parameter
	 *
	 * Example:
	 * 10 = Replace
	 * 10.str_replace.1.search = Google
	 * 10.str_replace.1.replace = Beagle
	 * 20 = HtmlToText
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig processing configuration
	 * @param null|\Cmp3\Job\Job $objJob
	 * @param null|\Cmp3\Log\Logger $objLog Logger object
	 * @throws Exception
	 * @return ContentProcessing
	 */
	public static function Factory ($objConfig, $objJob = null, $objLog = null)
	{
		$objProperties = array();
		$objProperties['Logger'] = $objLog;
		$objContentProcessing = new ContentProcessing($objProperties);
		if ($arrProperties = $objConfig->GetProperties('')) {

			// this sorting is ok because we ignore keys like '10.' anyway
			ksort($arrProperties);
			foreach ($arrProperties as $key => $property) {

				if (is_array($property)) continue;

				if (class_exists($property)) {
					$strClass = $property;

					$reflection = new \ReflectionClass($strClass);

					// full namespace needed because of autoloader
					if ($reflection->implementsInterface('\Cmp3\ContentProcessing\ProcessorInterface')) {

						$objConfigProcessor = null;
						if ($objConfig->hasProperty($key.'.')) { #  $objConfig[$key.'.']
							$objConfigProcessor = $objConfig->GetProxy($key.'.');
						} else {
							// dummy
							$objConfigProcessor = new \Cmp3\Config\ArrayData();
						}

						$objProperties = array();
						$objProperties['Logger'] = $objLog;
						$objProperties['Config'] = $objConfigProcessor;
						if ($strDataKey = $objConfigProcessor->getValue('useData')) {
							if (!$objJob) {
								throw new Exception('useData is used on configuration but the needed job to get the data from is not available');
							}
							$objProperties['Data'] = $objJob->GetData($strDataKey);
						}

						$objProcessor = new $strClass($objProperties);

					} else {
						throw new Exception($strClass . ' is not of type ProcessorInterface!');
					}

					$objContentProcessing->AddProcessor($objProcessor, $key);

				} else {
					throw new Exception("Can't find processor class: " . $property);
				}
			}
		}
		return $objContentProcessing;
	}

	/***************************************
	 *
	 *   processor setup
	 *
	 ***************************************/


	/**
	 * Adds a content processor
	 *
	 * @param \Cmp3\ContentProcessing\Abstract|\Cmp3\ContentProcessing\ProcessorInterface $processorObject
	 * @param string|null $strName Processor identifier, default is class name of $processorObject
	 * @throws Exception
	 * @return void
	 */
	public function AddProcessor (ProcessorInterface $processorObject, $strName=null)
	{
		if (!($processorObject instanceof ProcessorInterface)) {
			throw new Exception ('Argument 1 passed to ContentProcessor::AddProcessor() must be an instance of ProcessorInterface');
		}

		$strName = $strName ? $strName : get_class($processorObject);
		$this->processors[$strName] = $processorObject;
	}


	/**
	 * Returns content processor object
	 * alias for GetProcessor()
	 *
	 * @param string $strName Processor identifier
	 * @return \Cmp3\ContentProcessing\ProcessorInterface
	 */
	public function Processor ($strName)
	{
		return $this->GetProcessor ($strName);
	}


	/**
	 * Returns content processor object
	 *
	 * @param string $strName Processor identifier
	 * @return \Cmp3\ContentProcessing\ProcessorInterface
	 */
	public function GetProcessor ($strName)
	{
		if (!array_key_exists($strName, $this->processors)) {
			throw new Exception ('Processor "'.$strName.'" is not available!');
		}

		return $this->processors[$strName];
	}


	/**
	 * Remove a content processor
	 *
	 * @param string $strName Processor identifier
	 * @return void
	 */
	public function RemoveProcessor ($strName)
	{
		unset($this->processors[$strName]);
	}


	/**
	 * Returns true if processors are registered
	 *
	 * @return boolean
	 */
	public function hasProcessors ()
	{
		return ($this->processors ? true : false);
	}


	/**
	 * Set the highest priority for a processor
	 *
	 * @param string $strName Processor identifier
	 * @return void
	 */
	public function SetProcessorPriorityTop ($strName)
	{
		array_reverse($this->processors);
		$processor = $this->processors[$strName];
		unset($this->processors[$strName]);
		$this->processors[$strName] = $processor;
		array_reverse($this->processors);
	}


	/**
	 * Set the lowest priority for a processor
	 *
	 * @param string $strName Processor identifier
	 * @return void
	 */
	public function SetProcessorPriorityBottom ($strName)
	{
		$processor = $this->processors[$strName];
		unset($this->processors[$strName]);
		$this->processors[$strName] = $processor;
	}


	/**
	 * Sets the priority order of the processors
	 *
	 * @param string|array $order
	 * @return void
	 */
	public function SetProcessorOrder($order)
	{
		$order = is_array($order) ? $order : explode(',', $order);
		if (count($order)) {
			$processors = $this->processors;
			$this->processors = array();
			foreach ($order as $id) {
				if (array_key_exists($id, $processors)) {
					$this->processors[$id] = $processors[$id];
					unset($processors[$id]);
				}
			}
			foreach ($processors as $id) {
				$this->processors[$id] = $processors[$id];
				unset($processors[$id]);
			}
		}
	}




	/***************************************
	 *
	 *   processing
	 *
	 ***************************************/


	/**
	 * Returns the processed content
	 *
	 * @param \Cmp3\Content\Content 	$objContent Content to be processed
	 */
	public function Process ($objContent)
	{
		foreach ($this->processors as $id => $objProcessor) {

			if ($objProcessor->Config->isEnabled('enabled', true)) {
				if ($this->objLogger) $this->objLogger->Info('Call content processor: ' . $id);
				$objProcessor->Process($objContent);
			} else {
				if ($this->objLogger) $this->objLogger->Info('Content processor ' . $id . ' is disabled');
			}
		}
	}




	/***************************************
	 *
	 *   Configuration
	 *
	 ***************************************/


	/**
	 * Returns hash which can be used as identifier for caching purposes
	 *
	 * @return string hash
	 */
	public function GetConfigHash ()
	{
		$hash = '';
		foreach ($this->processors as $id => $objProcessor) {
			$hash .= $objProcessor->GetConfigHash();
		}
		return md5($hash);
	}


}







