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
 * HelloWorlds View
 *
 * @since  0.0.1
 */
class HelloWorldViewHelloWorlds extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically
	 *                        searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// include settings from the admin backend
		$this->includeAdminEnv();

		// Get data from the model
		$app	    	    	= JFactory::getApplication();
		$inp	    	    	= $app->input;
		$model	    	    	= $this->getModel();
		$state	    	    	= $this->get('State');
		$this->state	    	= $state;
		$this->filter_order 	= $state->get('list.ordering');
		$this->filter_order_Dir	= $state->get('list.direction');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		$this->pagination   	= $this->get('Pagination');
		$this->script	    	= $this->get('Script');

		$model->saveListState();

		//	read array of all records from the database
		$this->items		= $this->get('Items');

		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = HelloWorldHelper::getActions();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the toolbar and number of found items
		$this->addToolBar();
		echo JToolbar::getInstance('toolbar')->render('toolbar');

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$title = JText::_('COM_HELLOWORLD_HELLOWORLDS');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}

		JToolBarHelper::title($title, 'helloworld');

		if ($this->canDo->get('core.create'))
		{
			JToolBarHelper::addNew('helloworld.add', 'JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit'))
		{
			JToolBarHelper::editList('helloworld.edit', 'JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'helloworlds.delete', 'JTOOLBAR_DELETE');
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document	= JFactory::getDocument();
		$site		= "/components/com_helloworld/";
		$document->setTitle( JText::_('COM_HELLOWORLD_SITE') );
		$document->addScript(     $site."views/helloworlds/submitbutton.js");
		$document->addStyleSheet( $site."media/css/helloworld.css",'text/css');
	}

	/**
	 *	include the functionality from the administrative backend
	 *	Joomla elies heavily on the administration environment for the
	 *	WORLD functions at hand, so add these settings.
	 */
	private function includeAdminEnv()
	{
		// load the language files for the admin messages as well
		$language	= JFactory::getLanguage();
		$language->load('joomla', JPATH_ADMINISTRATOR, null, true);
		$language->load('com_helloworld', JPATH_ADMINISTRATOR, null, true);

		JLoader::register('JToolBarHelper',   JPATH_ADMINISTRATOR . '/includes/toolbar.php');
		JLoader::register('JSubMenuHelper',   JPATH_ADMINISTRATOR . '/includes/subtoolbar.php');
		JLoader::register('HelloWorldHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helloworld.php');
	}
}
