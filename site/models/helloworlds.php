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
 * HelloWorldList Model
 *
 * @since  0.0.1
 */
class HelloWorldModelHelloWorlds extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'greeting',
				'uid',
				'published'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * populateState reinitializes the model from getUserStateFromRequest, which
	 * implies the 'get' data from the URL, the 'post data' from a submit,
	 * and from the application UserState if no get/post data exists.
	 *
	 * The model keeps track of the status in the model State, but not in more
	 * 'permanent' places. The model is stateless over page reload, so
	 * it reinitilizes after a post, get or user url change. If you want to
	 * keep track of settings for filters, pagination or ordering, you have to
	 * do it yourself, for instance in the view. Preferably not in the model
	 * to keep this stateless?
	 *
	 * By default limit and limitstart are reset after list[fullordering].
	 * This is adapted to user get/post and UserState.
	 *
	 * Note that pagination can be set in the url with e.g.
	 * index.php?...&limit=15&limitstart=30
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// use default settings to start with
		parent::populateState($ordering, $direction);

		$app		= JFactory::getApplication();

		$limit		= (int)$app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart	= (int)$app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0, 'int');
		$limitstart	= ($limit == 0) ? 0 : (floor($limitstart / $limit) * $limit);
		$this->setState('list.start', $limitstart);
	}

	/**
	 * saveListState: saves the present list parameters in the UserState
	 *
	 * in een $(document).ready wordt <input id="js-stools-field-order" name="list[fullordering]">
	 * on the fly aangemaakt die meekomt met de Joomla.tableOrdering('id','desc','') submit as
	 * ["list"]=>  array(1) { ["fullordering"]=>string(8) "null ASC" }
	 * See media/jui/js/jquery.searchtools.js
	 * This is for the ProtoStar template and may work differently for other templates.
	 *
	 * so:
	 * * for a 'submit', e.g. by clicking the sort column, the 'list' array does not help
	 * as it only contains "fullordering" and not the column id. However, populateState also
	 * checks the "old ordering fields" 'get' parameters and still sets 'filter_order' in State.
	 * So selecting the order column should be fine.
	 * * a page reload without a submit, for instance when returning after visiting the item
	 * edit page has no list[fullordering]. In this case the saved 'list' values are restored
	 * by the standard populateState, as intended.
	 * * Note: do not call getUserStateFromRequest(list...) in the mean time, as this function not only
	 * retrieves 'list' but also updates the UserState from the 'get' parameters as side effect!
	 */
	public function saveListState()
	{
		$limit			= $this->state->get('list.limit');
		$filter_order		= $this->state->get('list.ordering');
		$filter_order_Dir	= $this->state->get('list.direction');
		JFactory::getApplication()->setUserState($this->context.'.list',array(
			'limit' 	=> $limit,
			'ordering'	=> $filter_order,
			'direction'	=> $filter_order_Dir,
			) );
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query	->select('u.name as uname, h.*' )
			->from($db->quoteName('#__helloworld').' as h')
			->leftJoin($db->quoteName('#__users').'as u on h.uid=u.id');

		// Filter: like / search
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('greeting LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(published IN (0, 1))');
		}

		// Filter by user ID
		$uid	= $this->getState('filter.uid');

		if (is_numeric($uid))
		{
			$query->where('h.uid = ' . (int) $uid);
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering',  'greeting');
		$orderDirn 	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		return $query;
	}
}
