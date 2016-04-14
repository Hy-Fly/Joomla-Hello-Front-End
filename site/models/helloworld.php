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
class HelloWorldModelHelloWorld extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	2.5
	 */
	protected function populateState()
	{
		// Get the message id
		$this->context	= "message";		//unique id for this view
		$jinput = JFactory::getApplication()->input;
		$id     = $jinput->get('id', 0, 'INT');
		$this->setState($this->context.'.id', $id);

		// Load the parameters.
		$this->setState('params', JFactory::getApplication()->getParams());
		parent::populateState();
	}

	/**
	 * Method to get a table object, load it if necessary.
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
	 * Get the message
	 * @return object The message to be displayed to the user
	 */
	public function getItem()
	{
		if (!isset($this->item))
		{
			$id	    	= (int)$this->getState($this->context.'.id');
			$usr    	= (int)JFactory::getUser()->id;

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query	->select( 'h.id, h.greeting, h.uid, h.params, c.title as category' )
			    	->from( $db->quoteName('#__helloworld').' as h' )
			    	->leftJoin( $db->quoteName('#__categories').' as c ON h.catid=c.id' );

			if( $id>0 )
			{	// user indicated a specific message item
				$query->where('h.id=' . (int)$id);
			} elseif( $usr == 0 ) {
				// user is not logged in so select first item
				$query->where('h.id=1');				
			} else {
				// user is logged in so get his own (first) item
				$query->where('h.uid=' . $usr);
			}

			$db->setQuery((string)$query);
			if ($this->item = $db->loadObject())
			{
				// Load the JSON string
				$params = new JRegistry;
				$params->loadString($this->item->params, 'JSON');
				$this->item->params = $params;

				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($this->item->params);
				$this->item->params = $params;

				$this->setState($this->context.'.id', $this->item->id);
			}
		}
		return $this->item;
	}

	/**
	 *	Update current message
 	 *	@return true on success
	 */
	public function updateThisItem($msg)
	{
		$this->item 	= null;	    	    	    	    		//invalidate cached item

		// Create an object for the record we are going to update.

		$obj	    	= new stdClass();	    	    	    	//create empty object
		$obj->id    	= $this->getState($this->context.'.id');	//key for present item
		$obj->greeting	= $msg; 	    	    	    	    	//new value
		$obj->uid   	= (int)JFactory::getUser()->id; 	    	//owner of this greeting message

		// Update their details in the users table using id as the primary key.
		$db		= JFactory::getDbo();
		return $db->updateObject('#__helloworld', $obj, 'id');  	//true on success
	}
}
