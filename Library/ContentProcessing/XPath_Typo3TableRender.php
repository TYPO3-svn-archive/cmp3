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
 * @copyright  Copyright (c) 2010 Bitmotion
 * @license    http://www.gnu.org/licenses/gpl-2.0.html     GNU General Public License, version 2
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU General Public License, version 3
 */


namespace Cmp3\ContentProcessing;


/**
 * Content processors to convert a dom node with typo3 table format to xhtml
 *
 *
 * @author Rene Fritz (r.fritz@bitmotion.de)
 * @subpackage ContentProcessing
 * @package    CMP3
 */
class XPath_Typo3TableRender extends XPathAbstract {


	/**
	 * Processes a DOM node
	 *
	 * @param DOMNode $objNode
	 */
	protected function ProcessNode($objNode)
	{
		$strFieldValue = $objNode->nodeValue;

		if (!$strFieldValue) {
			if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' No content in this node');
			return;
		}

		if ($this->objLogger) $this->objLogger->Info(__CLASS__ . ' Processing rte to html');

		// BE or FE that's the question here
		// is FE using ProcessTableRendering too?
		$strFieldValue = $this->ProcessTableRendering($strFieldValue);

		// use tidy to be safe
		$strFieldValue = \Cmp3\Xml\Tools::CleanHtml($strFieldValue);

		// for some reasons entities are created
		$strFieldValue = \Cmp3\Xml\Tools::DecodeHtmlEntities($strFieldValue);

		\Cmp3\Xml\Tools::ReplaceTextFieldWithRichText($objNode, $strFieldValue);

		$this->blnHasModified = true;
	}


	/**
	 * Rendering the "Table" type content element, normally called from TypoScript (tt_content.table.20)
	 *
	 * @param	string		Content input
	 * @return	string		HTML output.
	 * @access private
	 */
	function ProcessTableRendering($content)
	{
#TODO use configuration from flexform - see original function in css_styled_content
			if (!strcmp($content,''))	return '';

			$delimiter = '|';
			$quotedInput = '';

			$headerPos = $this->objConfig->GetValue('headerPos');
			$headerScope = ($headerPos=='top'?'col':'row');
			$headerScope = ' scope="'.$headerScope.'"';

			$useTfoot = false;

			// Split into single lines (will become table-rows):
			$rows = trim_explode(LF,$content);
			reset($rows);

			// Find number of columns to render:
			$cols = count(explode($delimiter,current($rows)));

			// Traverse rows (rendering the table here)
			$rCount = count($rows);
			foreach($rows as $k => $v)	{
				$cells = explode($delimiter,$v);

				// if the last line is empty skip it
				if ($rCount == ($k+1) AND !count(trim_explode($delimiter,$v))) {
					error_log('skip');
					break;
				}

				$newCells=array();
				for($a=0;$a<$cols;$a++)	{
					// remove quotes if needed
					if ($quotedInput && substr($cells[$a],0,1) == $quotedInput && substr($cells[$a],-1,1) == $quotedInput)	{
						$cells[$a] = substr($cells[$a],1,-1);
					}

					if (($headerPos == 'top' && !$k) || ($headerPos == 'left' && !$a))	{
						$newCells[$a] = '
						<th'.$headerScope.'>'.htmlspecialchars($cells[$a]).'</th>';
					} else {
						$newCells[$a] = '
						<td>'.htmlspecialchars($cells[$a]).'</td>';
					}
				}
				$rows[$k]='
				<tr>'.implode('',$newCells).'
				</tr>';
			}

			$addTbody = 0;
			$tableContents = '';
			if ($caption)	{
				$tableContents .= '
				<caption>'.$caption.'</caption>';
			}
			if ($headerPos == 'top' && $rows[0])	{
				$tableContents .= '<thead>'. $rows[0] .'
				</thead>';
				unset($rows[0]);
				$addTbody = 1;
			}
			if ($useTfoot)	{
				$tableContents .= '
				<tfoot>'.$rows[$rCount-1].'</tfoot>';
				unset($rows[$rCount-1]);
				$addTbody = 1;
			}
			$tmpTable = implode('',$rows);
			if ($addTbody)	{
				$tmpTable = '<tbody>'.$tmpTable.'</tbody>';
			}
			$tableContents .= $tmpTable;



			// Compile table output:
			$out = '
			<table>'.
			$tableContents.'
			</table>';

			// Return value
			return $out;
	}


}
