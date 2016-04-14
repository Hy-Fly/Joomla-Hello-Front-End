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
 * HTML View class for the HelloWorld Component
 *
 * @since  0.0.1
 */
class HelloWorldViewHelloWorld extends JViewLegacy
{
	/**
	 * Display the HelloWorld view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// get some environment parameters
		$context    	= "message";    	    	//unique id for this view
		$this->HelloURI	= JFactory::getURI();		//fully qualified path of current page
		$this->usr  	= JFactory::getUser()->id;	//logged-on user
		$model	    	= $this->getModel();    	//model for this view
		$state	    	= $this->get('State');		//model state parameter store

		// check post from form if data needs to be updated
		$app		= JFactory::getApplication();
		$inp		= $app->input;
		$postmsg	= $inp->post->getString('usergreet');	//Joomla way of $_POST['usergreet']
		$getid  	= (int)$inp->get->getString('id');  	//is id specified as index.php?id=...
		$state->set($context.'.id', $getid);    	    	//save on behalve of 'model' functions

		if( !empty($postmsg) ) {
			//this view page is reloaded after a submit
			//first check for form tampering, and then update the data
			JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

			// Load data of the present record
			$this->item	= $this->get('Item');

			if( $this->item )
			{
				$uid	= $this->item->uid;	    	    	//owner of record
				$ok 	= ($this->usr == $uid); 	    	//record should be of logged-in user

				if( $ok ) {
					$ok	= $model->updateThisItem($postmsg);	//update the current record in the db
				}
				if( $ok ) {
					JFactory::getApplication()->enqueueMessage(JText::_('COM_HELLOWORLD_SAVE_SUCCESS'), 'success');
				} else {
					JFactory::getApplication()->enqueueMessage(JText::_('COM_HELLOWORLD_SAVE_ERRORS'), 'error');
				}
			}
		}

		$this->item	= $this->get('Item');	    	    	//load the data of the (new) record

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
		}

		// Display the view
		parent::display($tpl);
	}
}