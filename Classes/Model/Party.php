<?php
namespace JambageCom\Party\Model;

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
 * Model for the class Party
 *
 * $Id: class.tx_party_models_account.php 81705 2013-11-23 10:37:30Z franzholz $
 *
 * @author Franz Holzinger <franz@ttproducts.de>
 * @package TYPO3
 * @subpackage party
 */

class Party extends AbstractObject {

	/**
	 * Table name for save or delete operations.
	 *
	 * @var	string
	 */
	static protected $tableName = 'tx_party_parties';

	/**
	 * Current class name.
	 *
	 *	static protected $className = __CLASS__;
	 *
	 * @var	string
	 */
	static protected $className = __CLASS__;

	protected $fieldToObjectMap = array(
		'account' => 'JambageCom\\Party\\Model\\Account',
		'address' => 'JambageCom\\Party\\Model\\Address',
		'allergy' => 'JambageCom\\Party\\Model\\Allergy',
		'birth_sign' => 'JambageCom\\Party\\Model\\BirthSign',
		'contact' => 'JambageCom\\Party\\Model\\Contact',
		'contact_number' => 'JambageCom\\Party\\Model\\ContactNumberUsage',
		'country_of_residence' => 'JambageCom\\Party\\Model\\CountryOfResidence',
		'disability' => 'JambageCom\\Party\\Model\\Disability',
		'document' => 'JambageCom\\Party\\Model\\Document',
		'electronic_address_identifier' => 'JambageCom\\Party\\Model\\ElectronicAddressIdentifierUsage',
		'ethnicity' => 'JambageCom\\Party\\Model\\Ethnicity',
		'event' => 'JambageCom\\Party\\Model\\Event',
		'favourite' => 'JambageCom\\Party\\Model\\Favourite',
		'habit' => 'JambageCom\\Party\\Model\\Habit',
		'hobby' => 'JambageCom\\Party\\Model\\Hobby',
		'identifier' => 'JambageCom\\Party\\Model\\Identifier',
		'image' => 'JambageCom\\Party\\Model\\Image',
		'language' => 'JambageCom\\Party\\Model\\Language',
		'mark' => 'JambageCom\\Party\\Model\\Mark',
		'membership' => 'JambageCom\\Party\\Model\\Membership',
		'name' => 'JambageCom\\Party\\Model\\Name',
		'nationality' => 'JambageCom\\Party\\Model\\Nationality',
		'occupation' => 'JambageCom\\Party\\Model\\Occupation',
		'organisation_type' => 'JambageCom\\Party\\Model\\Type',
		'organisation_nature' => 'JambageCom\\Party\\Model\\OrganisationNature',
		'physical_status' => 'JambageCom\\Party\\Model\\PhysicalStatus',
		'preference' => 'JambageCom\\Party\\Model\\Preference',
		'qualification' => 'JambageCom\\Party\\Model\\Qualification',
		'relationship' => 'JambageCom\\Party\\Model\\Relationship',
		'religion' => 'JambageCom\\Party\\Model\\Religion',
		'revenue' => 'JambageCom\\Party\\Model\\Revenue',
		'stock_market' => 'JambageCom\\Party\\Model\\StockMarket',
		'vehicle' => 'JambageCom\\Party\\Model\\Vehicle',
		'visa' => 'JambageCom\\Party\\Model\\Visa',
	);

	/**
	 * Returns the label of the party in the following format:
	 * "[name], [locality]"
	 *
	 * @return	string		Label of the party
	 */
	public function getLabel () {

		if (!$this->hasRecord()) {
			return FALSE;	// Data must be loaded
		}
		$label = array();
		$result = '';

		// Get all relevant parts
		$name = $this->getName();
		$address = $this->getAddress();
		$locality = $address->getLocality();

		// Assemble the label
		if (
			is_object($name) &&
			$name->getStandard()
		) {
			$label[] = $name->getLabel();
		}

		if ($locality) {
			$label[] = $locality;
		}

		if (
			!count($label) &&
			is_object($name)
		) {
			$firstName = $name->getFirstName();
			if ($firstName != '') {
				$label[] = $firstName;
			}
		}

		$result = implode(' - ', $label);
		return $result;
	}


	/**
	 * Loads all parties which belong to a certain PID.
	 *
	 * @param	integer		$pid: PID (Page ID) to select parties from
	 * @return	void		The data is loaded into the object
	 */
	public function loadByPid ($pid) {
		$pid = intval($pid);

		$select = 'uid,type';
		$from = 'tx_party_parties';
		$where = 'tx_party_parties.pid=' . $pid;

		$list = $this->selectFromDatabase($select, $from, $where);
		$this->set('list', $list);
	}

	/**
	 * Loads all parties with an address from a certain country.
	 *
	 * @param	integer		$countryUid: UID of the country
	 * @param	boolean		$onlyStandard: If set to TRUE, only the standard address of the party is relevant, else all addresses. Optional, default = TRUE
	 * @return	void		The data is loaded into the object
	 */
	public function loadByCountry (
		$countryUid,
		$onlyStandard = TRUE
	) {
		$countryUid = intval($countryUid);

		$select = 'a.party, c.type';
		$from = 'tx_party_address_usages a, tx_party_addresses b, tx_party_parties c';
		$where = 'a.party=b.parties AND a.party=c.uid AND b.country=' . $countryUid;
		$where.= $onlyStandard ? ' AND a.standard' : '';

		$list = $this->selectFromDatabase($select, $from, $where);
		$this->set('list', $list);
	}

	/**
	 * Executes a database query and returns a list of person/organisation objects.
	 *
	 * @param	string		$select: The fields to select
	 * @param	string		$from: The FROM clause
	 * @param	string		$where: The WHERE clause. The deleteClause is automatically added
	 * @param	string		$groupBy: The GROUP BY clause (optional)
	 * @param	string		$orderBy: The ORDER BY clause (optional)
	 * @param	string		$limit: The LIMIT clause (optional)
	 * @return	object		A tx_div2007_object instance with the selected persons/organisations
	 */
	private function selectFromDatabase (
		$select,
		$from,
		$where,
		$groupBy = '',
		$orderBy = '',
		$limit = ''
	) {
		$where = $where . tx_div2007_core::deleteClause($from);
		$query =
			$GLOBALS['TYPO3_DB']->SELECTquery(
				$select,
				$from,
				$where,
				$groupBy,
				$orderBy,
				$limit
			);
		$result = $GLOBALS['TYPO3_DB']->sql_query($query);
		$list = GeneralUtility::makeInstance('tx_div2007_object');
		if($result) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
				if ($row['type'] == 0) {
					$item = GeneralUtility::makeInstance('tx_party_models_person');
				}
				if ($row['type'] == 1) {
					$item = GeneralUtility::makeInstance('tx_party_models_organisation');
				}
				$item->load($row['uid']);
				$list->append($item);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($result);
		}
		return $list;
	}
}

?>