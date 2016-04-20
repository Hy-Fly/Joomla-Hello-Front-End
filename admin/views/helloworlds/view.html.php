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
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$app			= JFactory::getApplication();
		$inp			= $app->input;
		$model			= $this->getModel();
		$state			= $this->get('State');
		$this->state		= $state;
		$this->filter_order	= $state->get('list.ordering');
		$this->filter_order_Dir	= $state->get('list.direction');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		$this->pagination	= $this->get('Pagination');
		$this->script		= $this->get('Script');

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

		// Set the submenu
		HelloWorldHelper::addSubmenu('messages');	//start with messages menu
		$this->sidebar = JHtmlSidebar::render();	//show sidebar

		// Set the toolbar and number of found items
		$this->addToolBar();

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
		$title = JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLDS');

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
		if ($this->canDo->get('core.edit'))		// show export button if admin user has edit permissions
		{
			// dedicated buttons to start subcontroller with format='raw'
			$toolbar	= JToolbar::getInstance('toolbar');
			$toolbar->addButtonPath(JPATH_COMPONENT.'/button');
			$toolbar->appendButton('RawFormat',  'download', 'Export csv', 'helloexport.exportcsv');
			$toolbar->appendButton('RawFormat',  'download', 'Export xls', 'helloexport.exportxls');
			$toolbar->appendButton('ImportFile', 'upload',   'Import csv', 'helloimport.importcsv', 'importcsv');
			$toolbar->appendButton('ImportFile', 'upload',   'Import xls', 'helloimport.importxls', 'importxls');
		}
		if ($this->canDo->get('core.admin'))
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_helloworld');
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
		$site		= "/administrator/components/com_helloworld/";
		$document->setTitle(  JText::_('COM_HELLOWORLD_ADMINISTRATION') );
		$document->addScript( $site."views/helloworlds/submitbutton.js" );
	}
}
