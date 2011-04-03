<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 David Bruehlmeier (typo3@bruehlmeier.com)
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
 * Abstract base class for the Party model. This class is
 * extended by the class tx_party_models_person and
 * tx_party_models_organisation.
 *
 * Depends on: div2007
 *
 * $Id$
 *
 * @author David Brühlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_party
 */

require_once(PATH_BE_div2007 . 'class.tx_div2007.php');

tx_div2007::load('tx_party_models_object');
tx_div2007::load('tx_party_models_names');
tx_div2007::load('tx_party_models_addresses');
tx_div2007::load('tx_party_models_electronicaddressidentifiers');


abstract class tx_party_models_party extends tx_party_models_object {

	protected $table = 'tx_party_parties';

	/**
	 * Returns the loaded instance of the party. Depending on the type of the party, this function
	 * returns an instance of tx_party_models_person or tx_party_models_organisation.
	 *
	 * @param	integer		$uid: UID of the party
	 * @return	object		Instance of tx_party_models_person or tx_party_models_organisation
	 */
	public static function getInstance ($uid) {
		$uid = intval($uid);

		// Get the type of the party
		$rec = t3lib_BEfunc::getRecord('tx_party_parties',$uid,'type');

		// Depending on the type, create the proper instance and load the data
		switch (intval($rec['type'])) {
			case 0:
				$party = t3lib_div::makeInstance('tx_party_models_person');
				$party->load($uid);
			break;
			case 1:
				$party = t3lib_div::makeInstance('tx_party_models_organisation');
				$party->load($uid);
			break;
			default:
				$party = null;
			break;
		}

		return $party;
	}


	/**
	 * Loads the party.
	 *
	 * @param	integer		$uid: UID of the party
	 * @param	string		$fields: Comma-separated list of field names to load (determined by the derived classes)
	 * @return	void		The data is loaded into the object
	 */
	public function load ($uid, $fields) {
		$uid = intval($uid);
		$groupBy = '';
		$orderBy = '';

		// Load the party from the database and build the object
		$query = $GLOBALS['TYPO3_DB']->SELECTquery($fields, $this->table, $this->table.'.uid='.$uid, $groupBy, $orderBy);
		$result = $GLOBALS['TYPO3_DB']->sql_query($query);
		if($result) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
			$this->overwriteArray($row);
		}

		// Names
		if ($this->get('names')) {

			// Load the names
			$names = t3lib_div::makeInstance('tx_party_models_names');
			$names->loadByParty($uid);
			$this->set('names',$names);

			// Include the values of the standard name as parameters of the party
			$standardName = $names->get('standard');
			if (is_object($standardName)) {
				for ($standardName->rewind(); $standardName->valid(); $standardName->next()) {
					if ($standardName->key() == 'remarks') {
						continue;	// Don't overwrite the remarks of the party
					}
					$this->set($standardName->key(), $standardName->current());
				}
			}
		}

		// Addresses
		if ($this->get('addresses')) {

			// Load the addresses
			$addresses = t3lib_div::makeInstance('tx_party_models_addresses');
			$addresses->loadByParty($uid);
			$this->set('addresses', $addresses);

			// Include the values of the standard address as parameters of the party
			$standardAddress = $addresses->get('standard');
			if (is_object($standardAddress)) {
				for ($standardAddress->rewind(); $standardAddress->valid(); $standardAddress->next()) {
					if ($standardAddress->key() == 'remarks') {
						continue;	// Don't overwrite the remarks of the party
					}
					$this->set($standardAddress->key(), $standardAddress->current());
				}
			}
		}

		// ElectronicAddressIdentifiers
		if ($this->get('electronic_address_identifiers')) {

			// Load the electronic address identifiers
			$electronicAddressIdentifiers = t3lib_div::makeInstance('tx_party_models_electronicaddressidentifiers');
			$electronicAddressIdentifiers->loadByParty($uid);
			$this->set('electronic_address_identifiers', $electronicAddressIdentifiers);

			// Include the values of the standard electronic address identifier as parameters of the party
			$standardElectronicAddressIdentifier = $electronicAddressIdentifiers->get('standard');
			if (is_object($standardElectronicAddressIdentifier)) {
				for ($standardElectronicAddressIdentifier->rewind(); $standardElectronicAddressIdentifier->valid(); $standardElectronicAddressIdentifier->next()) {
					if ($standardElectronicAddressIdentifier->key() == 'remarks') {
						continue;	// Don't overwrite the remarks of the party
					}
					$this->set($standardElectronicAddressIdentifier->key(), $standardElectronicAddressIdentifier->current());
				}
			}
		}
	}

	/**
	 * Returns the label of the party in the following format:
	 * "[label_name], [locality]"
	 *
	 * The data must be loaded before, by calling $this->load();
	 *
	 * @return	string		Label of the party
	 */
	public function getLabel () {
		if ($this->isEmpty()) {
			return FALSE;		// Data must be loaded
		}
		$label = array();
		$out = '';

		// Get all relevant parts
		$names = $this->get('names');
		$locality = $this->get('locality');

		// Assemble the label
		if (is_object($names) && is_object($names->get('standard'))) {
			$label[] = $names->get('standard')->getLabel();
		}
		if ($locality) {
			$label[] = $locality;
		}
		$out = implode(' - ',$label);
		return $out;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/party/models/class.tx_party_models_party.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/party/models/class.tx_party_models_party.php']);
}


?>