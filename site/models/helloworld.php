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
 * @since  0.0.1
 */
class HelloWorldModelHelloWorld extends JModelAdmin
{
	/**
	 * Method to get a table object, load it if necessary.
	 * Note that the directories
	 *	administrator/components/com_helloworld/table[s]
	 * are in the path so the frontend will find the table
	 * definitions in the backend.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'HelloWorld', $prefix = 'HelloWorldTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm( 'com_helloworld.helloworld', 'helloworld',
				array( 'control'=>'jform', 'load_data'=>$loadData ) );
		return ( empty($form) ) ? false : $form;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript()
	{
		return 'components/com_helloworld/models/forms/helloworld.js';
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the UserState session cache for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			'com_helloworld.edit.helloworld.data',
			array()
		);

		if (empty($data))
		{	// if no previous data is present, get the item record from the db
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to check if it's OK to delete a message. Overwrites JModelAdmin::canDelete
	 */
	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return JFactory::getUser()->authorise( "core.delete", "com_helloworld.message." . $record->id );
		}
	}

	/**
	 * Method to get the data that should be exported.
	 * @return  mixed  The data.
	 */
	public function getExportData($pks)
	{
		$pklist = implode(',', $pks);

		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query	-> select('h.id, h.uid, h.greeting, c.title as category')
			-> from($db->quoteName('#__helloworld').' as h')
			-> leftJoin($db->quoteName('#__categories').' as c ON h.catid=c.id')
			-> where('h.id IN ('.$pklist.')');

		$db	-> setQuery((string)$query);
		$rows	= $db->loadRowList();

		// return the results as an array of items, each consisting of an array of fields
		$content	= array( array('id', 'uid', 'greeting', 'category') );	//header with column names
		$content	= array_merge( $content,  $rows);
		return $content;
	}
}
