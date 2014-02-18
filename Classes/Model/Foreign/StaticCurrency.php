<?php
namespace JambageCom\Party\Model\Foreign;

/***************************************************************
*  Copyright notice
*
*  (c) 2014 Franz Holzinger (franz@ttproducts.de)
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
 * Model for the class StaticCurrency
 *
 * $Id: class.tx_party_models_account.php 81705 2013-11-23 10:37:30Z franzholz $
 *
 * @author Franz Holzinger <franz@ttproducts.de>
 * @package TYPO3
 * @subpackage party
 */

class StaticCurrency extends \JambageCom\Party\Model\AbstractObject {

	/**
	 * Table name for save or delete operations.
	 *
	 * @var	string
	 */
	static protected $tableName = 'static_currencies';

	/**
	 * Current class name.
	 *
	 *	static protected $className = __CLASS__;
	 *
	 * @var	string
	 */
	static protected $className = __CLASS__;

}

?>