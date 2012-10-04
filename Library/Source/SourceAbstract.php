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
 * @subpackage Source
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\Source;



/**
 * {@inheritdoc}
 *
 *
 * Container for a content source which includes:
 * - configuration
 * - content processor
 *
 * After all it provides a content object ready for conversion eg. to PDF
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Source
 * @package    CMP3
 *
 * @property-read \Cmp3\Content\Type $Type
 * @property-read boolean $isProcessed
 * @property \Cmp3\ContentProcessing\ProcessorInterface $Processor
 */
abstract class SourceAbstract extends \Cmp3\BaseConfig implements SourceInterface {


	/**
	 * @var \Cmp3\Content\Content
	 */
	protected $objContent;

	/**
	 * @var \Cmp3\Job\Job
	 */
	protected $objJob;

	/**
	 * @var \Cmp3\ContentProcessing\ProcessorInterface
	 */
	protected $objProcessor;

	/**
	 *
	 * @var boolean
	 */
	protected $blnIsProcessed = false;



	/**
	 * Override method to perform a property "Get"
	 * This will get the property $strName
	 *
	 * @param string $strName Name of the property to get
	 * @return mixed
	 */
	public function __get($strName)
	{
		switch ($strName) {

			case 'isProcessed':
				$this->blnIsProcessed;

			case 'Type':
				// possibly the content isn't fetched already
				if ($this->objContent) {
					return $this->objContent->Type;
				}
				return strtolower($this->objConfig->GetValue('type'));

			case 'Job':
				return $this->objJob;

			case 'Processor':
				if ($this->objProcessor) {
					return $this->objProcessor;
				}
				if (is_null($this->objProcessor)) {
					if ($this->objConfig->hasProperty('processing')) {
						$objConfig = $this->objConfig->GetProxy('processing');
						$this->objProcessor = \Cmp3\ContentProcessing\ContentProcessing::Factory($objConfig, $this->objJob, $this->objLogger);
					}
				} else {
					$this->objProcessor = false;
				}
				return $this->objProcessor;

			default:
				return parent::__get($strName);
		}
	}

	/**
	 * Override method to perform a property "Set"
	 * This will set the property $strName with $mixValue
	 *
	 * @param string $strName Name of the property to get
	 * @param string $mixValue Value of the property to set
	 * @throws \Cmp3\WrongTypeException
	 * @return mixed
	 */
	public function __set($strName, $mixValue)
	{
		switch ($strName) {

			case 'Job':
				return $this->objJob = $mixValue;

			case 'Processor':
				if (!($mixValue instanceof \Cmp3\ContentProcessing\ProcessorInterface)) {
					throw new WrongTypeException($strName, $mixValue, '\Cmp3\ContentProcessing\ProcessorInterface');
				}
				return $this->objProcessor = $mixValue;

			default:
				return parent::__set($strName, $mixValue);
		}
	}



	/**
	 * Return an content object
	 *
	 * @return \Cmp3\Content\ContentInterface
	 */
	public function GetContent()
	{
		if (!$this->blnIsProcessed) {
			$this->Process();
		}

		return $this->objContent;
	}


	/**
	 * Fetch and preprocess source content for PDF conversion
	 *
	 * @return \Cmp3\Content\ContentInterface
	 */
	public function Process()
	{
		if ($this->blnIsProcessed) {
			return $this->objContent;
		}


		if ($this->objLogger)  $this->objLogger->Info(__CLASS__ . ' Fetching content from source');
		$this->FetchContent();

		if (!$this->Processor) {
			if ($this->objLogger)  $this->objLogger->Info(__CLASS__ . ' No processing defined for source');
			return $this->objContent;
		}

		$this->ProcessContent($this->objContent);

		return $this->objContent;
	}


	/**
	 * This actually retrieves the content and sets objContent
	 *
	 * @throws Exception
	 * @return void
	 */
	protected function FetchContent()
	{
		throw new Exception  (__FUNCTION__.' not implemented');
	}


	/**
	 * Processes content using objProcessor
	 *
	 * @param \Cmp3\Content\ContentInterface $objContent
	 * @return	\Cmp3\Content\ContentInterface Returns the processed content
	 */
	protected function ProcessContent ($objContent)
	{
		if ($this->Processor) {
			if ($this->objLogger)  $this->objLogger->Info(__CLASS__ . ' Start content processing for source');

			$this->Processor->Process($objContent);
			$this->blnIsProcessed = true;
		}

		return $objContent;
	}
}


