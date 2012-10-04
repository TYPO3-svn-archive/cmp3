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
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Job
 * @package    CMP3
 */
class Task {
#TODO constructor

	/**
	 * The job this task belongs to
	 *
	 * @var Job
	 */
	public $Job;

	/**
	 *
	 * @var \Cmp3\Source\SourceInterface
	 */
	public $Source;

	/**
	 * Result object
	 * @var Result
	 */
	public $Result;

	/**
	 *
	 * @var \Cmp3\Config\ConfigResource
	 */
	public $Config;

	/**
	 *
	 * @var \Cmp3\Transformation\TransformerInterface
	 */
	public $Transformation;



# TODO unused
	/**
	 * Sets the parent job object
	 *
	 * @param Job $objJob
	 */
	public function SetJob($objJob)
	{
		$this->Job = $objJob;
	}


	/**
	 * Runs this task
	 *
	 */
	public function Run()
	{
		if ($this->Job->Logger) $this->Job->Logger->Info("Task run");

		/* @var $objContent \Cmp3\Content\Content */
		if ($this->Job->Logger) $this->Job->Logger->Info("Get content from source");
		$objContent = $this->Source->GetContent();

		// pre processing
		if ($this->Config->hasProperty('preProcessing.')) {
			$objConfig = $this->Config->GetProxy('preProcessing.');
			if ($this->Job->Logger) $this->Job->Logger->Info("Start pre processing");
			$this->ProcessContent($objContent, $objConfig);
		}

		// transformation
		if ($this->Transformation) {
			if ($this->Job->Logger) $this->Job->Logger->Info("Start transformation with " . get_class($this->Transformation));
			$this->Transformation->Process($objContent);
		}

		// post processing
		if ($this->Config->hasProperty('postProcessing.')) {
			$objConfig = $this->Config->GetProxy('postProcessing.');
			if ($this->Job->Logger) $this->Job->Logger->Info("Start post processing");
			$this->ProcessContent($objContent, $objConfig);
		}

		if ($this->Job->Logger) $this->Job->Logger->Info("Task finished");
		$this->Result = new Result($objContent);
	}



	/**
	 * Processes content
	 *
	 * @param mixed $objContent
	 * @param \Cmp3\Config\ConfigInterface $objConfig
	 */
	protected function ProcessContent($objContent, $objConfig)
	{
		$objContentProcessing = null;

		if ($objConfig) {
			$objContentProcessing = \Cmp3\ContentProcessing\ContentProcessing::Factory($objConfig, $this->Job, $this->Job->Logger);
		}

		if ($objContentProcessing) {
			$objContent = $objContentProcessing->Process($objContent);
		}
	}

}


