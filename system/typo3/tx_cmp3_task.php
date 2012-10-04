<?php
/***************************************************************
*  Copyright notice
*
* (c) 2012 Rene Fritz <r.fritz@bitmotion.de>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class tx_cmp3_task extends tx_scheduler_Task {


	protected $intTimestamp;


	/**
	 * Public method, usually called by scheduler.
	 *
	 * @return boolean True on success
	 */
	public function execute()
	{
		// yesterday
		$this->intTimestamp = strtotime('now -1 day');

		syslog(LOG_INFO, __CLASS__.': Starting CMP3 garbage collection with expire date ' . date('c', $this->intTimestamp));

		$r1 = $this->ClearTemp();
		$r2 = $this->ClearLog();

		return ($r1 AND $r2);
	}


	/**
	 * Removes old files in typo3temp/tx_cmp3/
	 *
	 * @return boolean True on success
	 */
	public function ClearTemp()
	{
		return $this->RemovedOldFiles(
				PATH_site . "typo3temp/tx_cmp3/",
				false,
				'temp');
	}


	/**
	 * Removes old log files in typo3temp/tx_cmp3/log/
	 *
	 * @return boolean True on success
	 */
	public function ClearLog()
	{
		return $this->RemovedOldFiles(
				PATH_site . "typo3temp/tx_cmp3/log/",
				'log',
				'log');
	}


	/**
	 * Removes old files in given directory
	 *
	 * @return boolean True on success
	 */
	public function RemovedOldFiles($strPath, $FILE_EXT, $info)
	{
		$ext_count = array();

		if (is_dir($strPath)) {
		    if ($dh = opendir($strPath)) {
		        while (($filename = readdir($dh)) !== false) {
					if (is_file($strPath . $filename)) {

						if ($filename == 'index.html' OR $filename[0] == '.') {
							continue;
						}

			        	$strExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

			        	if ((($FILE_EXT === false) OR ($strExt === $FILE_EXT)) AND (filemtime($strPath . $filename) < $this->intTimestamp)) {
			        		unlink($strPath . $filename);
			        		$ext_count[$strExt] ++;
			        	}
					}
		        }
		        closedir($dh);
		    }
		} else {
			syslog(LOG_ERR, __CLASS__.": Removing old $info files failed! Directory not fould: $strPath" );
			return false;
		}


		if ($ext_count) {
			$strInfo = array();
			foreach ($ext_count as $key => $value) {
				$strInfo[] = "$value $key";
			}
			syslog(LOG_INFO, __CLASS__.": Removed old $info files (" .  implode(', ', $strInfo) . ") in $strPath");
		} else {
			syslog(LOG_INFO, __CLASS__.": No $info files removed in $strPath");

		}
		return true;
	}
}



