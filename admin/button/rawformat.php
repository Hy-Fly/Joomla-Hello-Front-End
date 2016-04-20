<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * Renders an adapted standard button
 */
class JToolbarButtonRawFormat extends JToolbarButtonStandard
{
	protected $_name = 'RawFormat';

	/**
	 * Get the JavaScript command for the button
	 * Refer to the script function RawFormatSubmitbutton in stead of the
	 * standard Joomla.submitbutton
	 */
	protected function _getCommand($name, $task, $list)
	{
		return	str_replace("Joomla.submitbutton", "RawFormatSubmitbutton",
			parent::_getCommand($name, $task, $list) );
	}
}
