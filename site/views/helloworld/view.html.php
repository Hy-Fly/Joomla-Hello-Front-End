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
 * HelloWorld View
 *
 * @since  0.0.1
 */
class HelloWorldViewHelloWorld extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// some preparations for testing this step in the tutorial
		$state	    	= $this->get('State');
		$id 	    	= (int)$state->get('helloworld.id');
		if($id <= 0)
		{
			$state->set('helloworld.id', (int)1 );
		}
		$this->setLayout('edit');


		// include settings from the admin backend
		$this->includeAdminEnv();

		// Get the Data
		$this->form 	= $this->get('Form');
		$this->item 	= $this->get('Item');
		$this->script	= $this->get('Script');

		// What Access Permissions does this user have? What can (s)he do?
		// Treat item for which asset has not been defined yet as new item
		// otherwise no permission will be granted to user
		$asid	    	= ($this->item->asset_id == 0) ? 0 : $this->item->id;
		$this->canDo	= HelloWorldHelper::getActions($asid);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Set the toolbar
		$this->addToolBar();
		echo JToolbar::getInstance('toolbar')->render('toolbar');

		// display caller
		$usr  	= JFactory::getUser()->id;	//logged-on user
		$id 	= $this->item->id;
		echo "\n<p>Hello user $usr. This is item # $id";

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
		$input	= JFactory::getApplication()->input;
		$isNew 	= ( $this->item->id == 0 );

		JToolBarHelper::title($isNew	? JText::_('COM_HELLOWORLD_HELLOWORLD_NEW')
						: JText::_('COM_HELLOWORLD_HELLOWORLD_EDIT'), 'helloworld');

		// Build the actions for new and existing records.
		if ($isNew)
		{
			// For new records, check the create permission.
			if ($this->canDo->get('core.create'))
			{
				JToolBarHelper::apply ('helloworld.apply',    'JTOOLBAR_APPLY');
				JToolBarHelper::save  ('helloworld.save',     'JTOOLBAR_SAVE' );
				JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png',
				                       'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('helloworld.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save ('helloworld.save',  'JTOOLBAR_SAVE' );

				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get('core.create'))
				{
					JToolBarHelper::custom('helloworld.save2new', 'save-new.png', 'save-new_f2.png',
					                       'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($this->canDo->get('core.create'))
			{
				JToolBarHelper::custom('helloworld.save2copy', 'save-copy.png', 'save-copy_f2.png',
				                       'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew		= ($this->item->id == 0);
		$document	= JFactory::getDocument();
		$site		= "/components/com_helloworld/";
		$document->setTitle( $isNew ? JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING')
		                            : JText::_('COM_HELLOWORLD_HELLOWORLD_EDITING') );
		$document->addScript( '/'. $this->script );
		$document->addScript( $site."views/helloworld/submitbutton.js" );
		$document->addStyleSheet( $site."media/css/helloworld.css",'text/css' );
		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}

	/**
	 *	include the functionality from the administrative backend
	 *	Joomla relies heavily on the administration environment for the
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
