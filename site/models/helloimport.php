<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Model
 *
 * Note:
 * -	as we only require limitied external function, we do not extend from JModelAdmin as this
 *	is an abstract class and requires getForm($data = array(), $loadData = true)
 *	to be defined! If you leave that out, the application just shows an empty frame!
 * -	you can use JModelLegacy without that problem as this is not an abstract class.
 *
 * @since  0.0.1
 */
class HelloWorldModelHelloImport extends JModelLegacy
{
	/**
	 * update current message
	 */
	public function importItems($rows)
	{
		// database columns to import
		$colnames	= array("id", "uid", "greeting");

		// get column header names from csv file
		$headers	= array_shift($rows);

		// quoted table and column names for sql commands
		$db	= JFactory::getDbo();
		$table	= $db->quoteName('#__helloworld');
		$keyqt	= $db->quoteName($colnames[0]);
		$colqt	= array();

		// find mapping of header column names to table column names
		$map	= array();
		$pk	= null;		// mapping of table primary key field
		foreach($headers as $i => $val) {
			foreach($colnames as $colnum => $colname) {
				if( $colname == strtolower(trim($val)) ) {
					if ($colnum == 0) {
						$pk	= $i;
					} else {
						$map[$colnum]	= $i;
						$colqt[$colnum]	= $db->quoteName($colname);
					}
					break;
				}
			}
		}

		// update the database.  Loop over all lines in csv file
		$ok	= true;		//assume success

		foreach ($rows as $row) {
			$key	= (int)$row[$pk];

			// first query if record with this id already exists
			// if so, $numRows will be > 0
			$query		= $db->getQuery(true);	//new query
			$query->select( $keyqt )->from( $table )->where( $keyqt.'='.$key );
			$db->setQuery($query)->execute();
			$numRows	= $db->getNumRows();

			// set new values for columns
			$query->clear();			//new query
			foreach($map as $colnum => $hdr) {
				$query->set( $colqt[$colnum]."=".$db->quote( $row[$hdr]) );
			}

			if( $numRows > 0 ) {			// pk already exists so update record
				$query->update( $table );
				$query->where(  $keyqt.'='.$key );
			} else {				// new pk so insert new record
				$query->insert($table);
			}

			$result	= $db->setQuery($query)->execute();
			$ok	= $ok && $result;
		}
		return $ok;
	}
}
