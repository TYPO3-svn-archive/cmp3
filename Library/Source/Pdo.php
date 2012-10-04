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
 * @subpackage Content
 * @package    CMP3
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */



namespace Cmp3\Source;

#FIXME adapt or remove

/**
 * {@inheritdoc}
 *
 * This source handles content retrival from an external database using PDO
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage Source
 * @package    CMP3
 */
class Pdo extends SourceAbstract {




	function bla ()
	{
		if ($this->writeDevLog) \t3lib_div::devLog('getUser: dataSourceConfArr "'. \t3lib_div::arrayToLogString((array)$dataSourceConfArr), 'tx_nawauthpdo');

		$dbtype =   $this->replaceConstants($dataSourceConfArr['dbtype']);
		$hostname = $this->replaceConstants($dataSourceConfArr['host']);
		$username = $this->replaceConstants($dataSourceConfArr['username']);
		$password = $this->replaceConstants($dataSourceConfArr['password']);
		$dbname =   $this->replaceConstants($dataSourceConfArr['dbname']);
		$pdo_dsn =   $this->replaceConstants($dataSourceConfArr['pdo_dsn']);

		if ($this->writeDevLog) \t3lib_div::devLog("getUser: $dbtype:host=$hostname;dbname=$dbname", 'tx_nawauthpdo');


		try {

			if (!$pdo_dsn) {
				$pdo_dsn = "$dbtype:host=$hostname;dbname=$dbname";
			}


			$dbh = new PDO($pdo_dsn, $username, $password);

			if ($this->writeDevLog) \t3lib_div::devLog('getUser: Successfully connected to external database "'.$dataSourceConfArr['dbname'].'".', 'tx_nawauthpdo');

			/*** set the error reporting attribute ***/
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Prepare query for fetching username and password:
			$query = $this->replaceQueryMarkers ($dataSourceConfArr['query']);

			if ($this->writeDevLog) \t3lib_div::devLog('getUser: Query external database: '.$query.'', 'tx_nawauthpdo');


			/*** fetch into an PDOStatement object ***/
			$stmt = $dbh->query($query);


			if ($stmt->rowCount() == 0) {
				if ($this->writeDevLog) \t3lib_div::devLog('getUser: No user found. The query of the external database delivered an empty result, so the combination of username and password not was not found. QUERY: '.$query, 'tx_nawauthpdo', 2);

				// Check if a FEuser with this username already exists and if so - and we are told do so - delete it, because it no longer exists in the external db:
				if (intval($dataSourceConfArr['deleteLocalUsers'])) {
					$this->deleteLocalUser();
				}
				// No valid user? The ride ends here:
				return false;
			}


			/*** get row ***/
			$extDBRow = $stmt->fetch(PDO::FETCH_ASSOC);


			if ($extDBRow) {

			}


		} catch(PDOException $e) {

			if ($e->getCode()) {

				t3lib_div::sysLog('Error connecting external database! ' . $e->getCode() . ' - ' . $e->getMessage(), 'tx_nawauthpdo', 3);

			}

			if ($this->writeDevLog) \t3lib_div::devLog('getUser: Error connecting external database! Line: ' . $e->getLine() . ' - Code: ' . $e->getCode() . ' - ' . $e->getMessage(), 'tx_nawauthpdo', 3);
		}


		/*** close the database connection ***/
		$dbh = null;

	}

}


