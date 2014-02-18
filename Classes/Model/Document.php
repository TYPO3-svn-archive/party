<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
*  Copyright notice
*
*  (c) 2014 David Bruehlmeier (typo3@bruehlmeier.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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


/**
 * Model for the class Document
 *
 * $Id: class.tx_party_models_document.php 81705 2013-11-23 10:37:30Z franzholz $
 *
 * @author David Brühlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage party
 */



class Document extends AbstractObject {

	/**
	 * Table name for save or delete operations.
	 *
	 * @var	string
	 */
	static protected $tableName = 'tx_party_documents';

	/**
	 * Current class name.
	 *
	 *	static protected $className = __CLASS__;
	 *
	 * @var	string
	 */
	static protected $className = __CLASS__;

	protected $fieldToObjectMap = array(
		'party' => 'JambageCom\\Party\\Model\\Party',
		'document' => 'JambageCom\\Party\\Model\\Type',
		'issued_by' => 'JambageCom\\Party\\Model\\Party',
	);

	/**
	 * Returns the label of the Document in the following format:
	 * "[type]: [document_id] ([party])"
	 *
	 * The data must be loaded before, by calling $this->load();
	 *
	 * @return	string		Label of the Document
	 */
	public function getLabel () {
		if (!$this->hasRecord()) {
			return FALSE;		// Data must be loaded
		}
		$label = array();
		$result = '';

		// Get all relevant parts
		$documentType = $this->getDocumentType();
		$party = $this->getParty();

		// Assemble the label
		if ($documentType->hasRecord()) {
			$label[0] = $documentType->getLabel() . ':';
		}
		if ($documentId) {
			$label[1] = $documentId;
		}
		if ($party->hasRecord()) {
			$label[2] = '(' . $party->getLabel() . ')';
		}

		$result = implode(' ', $label);
		return $result;
	}
}

?>