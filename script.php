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
 * Script file of HelloWorld component
 */
class com_helloWorldInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		// $parent is the class calling this method
		$parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		// $parent is the class calling this method
		echo '<p>' . JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';

		// by default allow Public UserGroup to access the component
		$comp	= "com_helloworld";
		$db	= JFactory::getDbo();

		// Find Public usergroup.
		// The Public group is identified by parent_id=0
		$query	= $db->getQuery(true)
			-> select ($db->quoteName(array('id', 'title') ))
			-> from   ($db->quoteName('#__usergroups'))
			-> where  ($db->quoteName('parent_id') .'=0' );
		$db->setQuery($query);
		$result 	= $db->loadAssoc();

		if( empty($result) )
		{
			// just return if no usergroup record found
			return false;
		}

		// the id of the Public UserGroup
		$publicID	= (int)$result['id'];
		if( $publicID <= 0 )
		{
			// just return if no valid usergroup id found
			return false;
		}

		// UserGroup permissions for component
		$assets	= $db->quoteName('#__assets');
		$where	= $db->quoteName('name') .'='. $db->quote($comp);
		$query	= $db->getQuery(true)
			->select ($db->quoteName(array('id', 'name', 'rules')))
			->from   ($assets)
			->where  ($where);
		$db->setQuery($query);

		// Load the results as an array of fields
		$result	= $db->loadAssoc();

		if( empty($result) )
		{
			// just return if no asset record found
			return false;
		}

		// Decode the rule settings
		$result 	= json_decode($result['rules'], true);

		// allow access to Public UserGroup
		$result['helloworld.access'][$publicID]	= (int)1;

		// Encode and store the new permissions
		$result 	= json_encode($result);
		$query	= $db->getQuery(true)
			-> update ($assets)
			-> set    ('rules=' . $db->quote($result) )
			-> where  ($where);
		$db->setQuery($query);
		$result	= $db->execute();
	}
}
