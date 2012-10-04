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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\System;


/**
 * Exception which might be thrown by \Cmp3\System\Exec
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Misc
 * @package    CMP3
 */
class ExecException extends \Cmp3\Exception {}



/**
 * Executes a command
 *
 * Similar to exec() a command will be executed.
 * The difference is that error codes and error output can be accessed additionally to the standard output.
 *
 * Keep in mind that some programs always output something on the error channel or return exit codes that aren't any error.
 * 1. use SetOnErrorOutputException() or SetOnExitCodeException() to disabled exception
 * 2 extend class and implement own ErrorOccured() method
 *
 * Example:
 *
 * try {
 * 	$scriptCall = 	$this->scriptPath. 'wkhtmltopdf-i386 ' . escapeshellarg($url) . ' ' . escapeshellarg($this->filename);
 *
 * 	$objExec = new ExecProcess;
 * 	$objExec->SetOnErrorOutputException(false);
 * 	$objExec->Run($scriptCall);
 *
 * } catch (Exception $e) {
 * 	$content .= '<p>No PDF generated</p>';
 * 	$content .= '<p>script output: ' . str_replace("\n", '<br />', htmlspecialchars($objExec->GetOutput())) . '</p>';
 * 	$content .= '<p>script error: ' . str_replace("\n", '<br />', htmlspecialchars($objExec->GetErrorOutput())) . '</p>';
 * 	$content .= '<p>script return value: ' . htmlspecialchars($objExec->GetExitCode()) . '</p>';
 * }
 *
 *
 * 
 * REVIEWED 15.7.2010 RF
 *
 * @todo what about encoding? Reading ENV and do conversion?
 *
 * @todo maybe exit code calculation needed from http://www.php.net/manual/de/function.proc-open.php cbn at grenet dot org 18-Dec-2009 03:30 ??
 *
 * @see http://www.php.net/manual/de/function.proc-open.php
 * @see http://php.net/manual/de/function.exec.php
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Misc
 * @package    CMP3
 */
class Exec {

	/**
	 * the exit code returned by the called program
	 * @var integer
	 */
	protected $ExitCode = null;

	/**
	 * input data which will be passed as stdin to the program
	 * @var string
	 */
	protected $Input = null;

	/**
	 * output from the program
	 * @var string
	 */
	protected $Output = null;

	/**
	 * error output from the program
	 * @var string
	 */
	protected $ErrorOutput = null;

	/**
	 * flag indicates if an exception should be thrown on error output
	 * @var boolean
	 */
	protected $blnOnErrorOutputException = true;

	/**
	 * flag indicates if an exception should be thrown on exit code > 0
	 * @var boolean
	 */
	protected $blnOnExitCodeException = true;



	/**
	 * Set flag that indicates if an exception should be thrown on error output
	 *
	 * @param boolean $blnFlag
	 * @return void
	 */
	public function SetOnErrorOutputException($blnFlag = true)
	{
		$this->blnOnErrorOutputException = $blnFlag;
	}


	/**
	 * Set flag that indicates if an exception should be thrown on exit code > 0
	 *
	 * @param boolean $blnFlag
	 * @return void
	 */
	public function SetOnExitCodeException($blnFlag = true)
	{
		$this->blnOnExitCodeException = $blnFlag;

	}


	/**
	 * Set input data which will be passed as stdin to the program
	 *
	 * @param string $strInput
	 * @return void
	 */
	public function SetInput($strInput = null)
	{
		$this->Input = $strInput;

	}


	/**
	 * Runs a command
	 *
	 * @param string $command Command with parameter. Don't forget to escape parameter.
	 * @param string $cwd The initial working dir for the command. This must be an absolute directory path, or NULL  if you want to use the default value (the working dir of the current PHP process)
	 * @param array $env An array with the environment variables for the command that will be run, or NULL to use the same environment as the current PHP process
	 * @return string the output of the called command
	 * @throws \Cmp3\System\ExecException
	 */
	public function Run($command, $cwd=NULL, $env=NULL)
	{
		$this->Output = null;
		$this->ErrorOutput = null;
		$this->ExitCode = null;

		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
			1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
			2 => array("pipe", "w")   // stderr is a file to write to
		);

		$resource = proc_open($command, $descriptorspec, $pipes, $cwd, $env);
		if (is_resource($resource))
		{
			// $pipes now looks like this:
			// 0 => writeable handle connected to child stdin
			// 1 => readable handle connected to child stdout

			$stdin  = $pipes[0];
			$stdout = $pipes[1];
			$stderr = $pipes[2];

			$read_output = true;
			$read_error  = true;

			// write stdin data
			if ($this->Input !== NULL) {
				fwrite($stdin, $this->Input);
				fclose($stdin);
			}

			while ($read_error != false or $read_output != false) {
				if (feof($stdout)) {
					$read_output = false;
				} else {
					$this->Output .= $this->fgetsPending($stdout);
				}

				if (feof($stderr)) {
					$read_error = false;
				} else {
					$this->ErrorOutput .= $this->fgetsPending($stderr);
				}
			}

			foreach ($pipes as $pipe) {
				// stdin might be closed above
				if (is_resource($pipe)) {
					fclose($pipe);
				}
			}

			$this->ExitCode = proc_close($resource);

		} else {
			throw new ExecException('Create process failed! ' . substr ($command, 0, 15) . '...');
		}

		if ($this->ErrorOccured()) {
			throw new ExecException(($this->ErrorOutput ? $this->ErrorOutput : 'Exit code: ' . $this->ExitCode));
		}

		return $this->Output;
	}


	/**
	 * Check if the command returned an error
	 *
	 * This method should be changed in an extended class when the program returns very specific return codes for example.
	 *
	 * @return boolean
	 */
	protected function ErrorOccured()
	{
		if ($this->blnOnErrorOutputException AND !empty($this->ErrorOutput)) {
			return true;
		}

		if ($this->blnOnErrorOutputException AND ($this->ExitCode>0)) {
			return true;
		}

		return false;
	}


	/**
	 * Returns the output of the called command
	 *
	 * @return string|NULL
	 */
	public function GetOutput()
	{
		return $this->Output;
	}


	/**
	 * Returns the error messages of the called command
	 *
	 * @return string|NULL
	 */
	public function GetErrorOutput()
	{
		return $this->ErrorOutput;
	}


	/**
	 * Returns the exit code of the called command
	 *
	 * @return integer|NULL
	 */
	public function GetExitCode()
	{
		return $this->ExitCode;
	}


	/**
	 * Get a pending line of data from stream $in, waiting a maximum of $tv_sec seconds
	 *
	 * @param resource $in
	 * @param integer $tv_sec
	 * @return string
	 */
	protected function fgetsPending(&$in, $tv_sec=10) {

		/*

		http://de3.php.net/manual/de/function.proc-open.php

		I found that with disabling stream blocking I was sometimes attempting to read a return line before the external application had responded.
		So, instead, I left blocking alone and used this simple function to add a timeout to the fgets function

		 */

		if ( stream_select($read = array($in), $write=NULL, $except=NULL, $tv_sec) ) return fgets($in);
		else return FALSE;
	}
}



