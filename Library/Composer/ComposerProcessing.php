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
 * @subpackage Composer
 * @package    CMP3
 * @copyright  Copyright (c) 2012 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Composer;


/**
 * Performs content processing using one or more Composers
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Composer
 * @package    CMP3
 */
class ComposerProcessing  extends \Cmp3\BaseLogger {

	/**
	 * Logger object
	 *
	 * @var \Cmp3\Log\Logger
	 */
	protected $objLogger;


	/**
	 *
	 * @var ComposerInterface[]
	 */
	protected $Composers = array();




	/**
	 * Creates an content Composer using the TypoScript style setup as parameter
	 *
	 * Example:
	 *
	 * 10 = \Cmp3\Composer\PdfShrink
	 * 10.quality = ebook

	 * 20 = \Cmp3\Composer\PdfOverlay
	 * 20.background = EXT:mypproject/stylesheets/nice-bg.pdf
	 * 20.multi = 1

	 * 30 = \Cmp3\Composer\PdfImpose
	 * 30.plan = EXT:mypproject/stylesheets/make_double_page_lua.plan
	 * 30.lua = 1
	 *
	 * @param \Cmp3\Config\ConfigInterface $objConfig processing configuration
	 * @param null|\Cmp3\Job\Job $objJob
	 * @param null|\Cmp3\Log\Logger $objLog Logger object
	 * @throws Exception
	 * @return ComposerProcessing
	 */
	public static function Factory ($objConfig, $objJob = null, $objLog = null)
	{
		$objComposerProcessing = false;

		if ($arrProperties = $objConfig->GetProperties('')) {

			$objProperties = array();
			$objProperties['Logger'] = $objLog;
			$objComposerProcessing = new ComposerProcessing($objProperties);

			foreach ($arrProperties as $key => $property) {

				if (is_array($property)) continue;

				if (class_exists($property)) {
					$strClass = $property;

					$reflection = new \ReflectionClass($strClass);

					// full namespace needed because of autoloader
					if ($reflection->implementsInterface('\Cmp3\Composer\ComposerInterface')) {

						$objConfigComposer = null;
						if ($objConfig->hasProperty($key.'.')) {
							$objConfigComposer = $objConfig->GetProxy($key.'.');
						} else {
							// dummy
							$objConfigComposer = new \Cmp3\Config\ArrayData();
						}
						$objConfigComposer->SetValue('debug', $objConfig->isEnabled('debug'));

						$objProperties = array();
						$objProperties['Logger'] = $objLog;
						$objProperties['Config'] = $objConfigComposer;
						if ($strDataKey = $objConfigComposer->getValue('useData')) {
							if (!$objJob) {
								throw new Exception('useData is used on configuration but the needed job to get the data from is not available');
							}
							$objProperties['Data'] = $objJob->GetData($strDataKey);
						}

						$objComposer = new $strClass($objProperties);

					} else {
						throw new Exception($strClass . ' is not of type ComposerInterface!');
					}

					if ($objLog) $objLog->Info("Add composer: $key = $property");
					$objComposerProcessing->AddComposer($objComposer);

				} else {
					throw new Exception("Can't find Composer class: " . $property);
				}
			}
		} else {
			if ($objLog) $objLog->Info("No composer configuration found");
		}

		return $objComposerProcessing;
	}

	/***************************************
	 *
	 *   Composer setup
	 *
	 ***************************************/


	/**
	 * Adds a content Composer
	 *
	 * @param \Cmp3\Composer\ComposerAbstract|\Cmp3\Composer\ComposerInterface $ComposerObject
	 * @param string|null $strName Composer identifier, default is class name of $ComposerObject
	 * @throws Exception
	 * @return void
	 */
	public function AddComposer ($ComposerObject, $strName=null)
	{
		if (!($ComposerObject instanceof ComposerInterface)) {
			throw new Exception ('Argument 1 passed to ' . __METHOD__ . ' must be an instance of ComposerInterface');
		}

		$strName = $strName ? $strName : get_class($ComposerObject);
		$this->Composers[$strName] = $ComposerObject;
	}


	/**
	 * Returns content Composer object
	 * alias for GetComposer()
	 *
	 * @param string $strName Composer identifier
	 * @return \Cmp3\Composer\ComposerInterface
	 */
	public function Composer ($strName)
	{
		return $this->GetComposer ($strName);
	}


	/**
	 * Returns content Composer object
	 *
	 * @param string $strName Composer identifier
	 * @return \Cmp3\Composer\ComposerInterface
	 */
	public function GetComposer ($strName)
	{
		if (!array_key_exists($strName, $this->Composers)) {
			throw new Exception ('Composer "'.$strName.'" is not available!');
		}

		return $this->Composers[$strName];
	}


	/**
	 * Remove a content Composer
	 *
	 * @param string $strName Composer identifier
	 * @return void
	 */
	public function RemoveComposer ($strName)
	{
		unset($this->Composers[$strName]);
	}


	/**
	 * Returns true if Composers are registered
	 *
	 * @return boolean
	 */
	public function hasComposers ()
	{
		return ($this->Composers ? true : false);
	}


	/**
	 * Set the highest priority for a Composer
	 *
	 * @param string $strName Composer identifier
	 * @return void
	 */
	public function SetComposerPriorityTop ($strName)
	{
		array_reverse($this->Composers);
		$Composer = $this->Composers[$strName];
		unset($this->Composers[$strName]);
		$this->Composers[$strName] = $Composer;
		array_reverse($this->Composers);
	}


	/**
	 * Set the lowest priority for a Composer
	 *
	 * @param string $strName Composer identifier
	 * @return void
	 */
	public function SetComposerPriorityBottom ($strName)
	{
		$Composer = $this->Composers[$strName];
		unset($this->Composers[$strName]);
		$this->Composers[$strName] = $Composer;
	}


	/**
	 * Sets the priority order of the Composers
	 *
	 * @param string|array $order
	 * @return void
	 */
	public function SetComposerOrder($order)
	{
		$order = is_array($order) ? $order : explode(',', $order);
		if (count($order)) {
			$Composers = $this->Composers;
			$this->Composers = array();
			foreach ($order as $id) {
				if (array_key_exists($id, $Composers)) {
					$this->Composers[$id] = $Composers[$id];
					unset($Composers[$id]);
				}
			}
			foreach ($Composers as $id) {
				$this->Composers[$id] = $Composers[$id];
				unset($Composers[$id]);
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
	public function Process ($objResultArray)
	{
		foreach ($this->Composers as $id => $objComposer) {

			if ($objComposer->Config->isEnabled('enabled', true)) {
				if ($this->objLogger) $this->objLogger->Info('Call result composer:' . $id);
				$objResultArray = $objComposer->Process($objResultArray);
			} else {
				if ($this->objLogger) $this->objLogger->Info('Composer ' . $id . ' is disabled');
			}
		}

		return $objResultArray;
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
		foreach ($this->Composers as $id => $objComposer) {
			$hash .= $objComposer->GetConfigHash();
		}
		return md5($hash);
	}


}







