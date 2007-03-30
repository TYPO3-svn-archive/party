<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 David Bruehlmeier (typo3@bruehlmeier.com)
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
 * Model for a list of Addresses
 * 
 * Depends on: liv/div 
 *
 * @author David Brühlmeier <typo3@bruehlmeier.com>
 * @package TYPO3
 * @subpackage tx_party
 */

require_once (t3lib_extMgm::extPath('party').'models/class.tx_party_models_address.php');

class tx_party_models_addresses extends tx_lib_object {

	protected $table = 'tx_party_address_usages';
	
	/**
	 * Loads all addresses which are assigned to a specific party.
	 * 
	 * @param	integer		$uid: UID of the party
	 * @return	void		The data is loaded into the object
	 */
	public function loadByParty($partyUid) {
		$partyUid = intval($partyUid);
		$groupBy = '';
		$orderBy = '';
		
		// Load all addresses from the database and build the object
		$query = $GLOBALS['TYPO3_DB']->SELECTquery('address,standard', $this->table, $this->table.'.party='.$partyUid, $groupBy, $orderBy);
		$result = $GLOBALS['TYPO3_DB']->sql_query($query);
		$list = tx_div::makeInstance('tx_lib_object');
		if($result) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
				$item = t3lib_div::makeInstance('tx_party_models_address');
				$item->load($row['address']);
				$item->set('standard',$row['standard']);	// Include the value from the mm-table
				if ($item->get('standard') == 1) $this->set('standard',$item);
				$list->append($item);
			}
		}
		$this->set('list',$list);
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/party/models/class.tx_party_models_addresses.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/party/models/class.tx_party_models_addresses.php']);
}


?>