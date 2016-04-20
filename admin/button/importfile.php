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
 * Renders a custom button
 */
class JToolbarButtonImportFile extends JToolbarButton
{
	protected $_name = 'ImportFile';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string  $type  Button type, unused string.
	 * @param   string  $name  icon name for the button
	 * @param   string  $text  text for the button. Also used for suffix for name.
	 * @param   string  $task  controller task to execute after upload of file
	 * @param   string  $file  parameter name for import file
	 *
	 * @return  string   HTML string for the button
	 *
	 * @since   3.0
	 */
	public function fetchButton($type = 'ImportFile', $name = '', $text = 'ImportFile', $task = '', $file = 'importfile')
	{
		// return to present component and view
		$inp		= JFactory::getApplication()->input;
		$url		= $inp->getString('view');
		if( !empty($url) )
		{
			$url	= '&view='.$url;
		}
		$url		= 'index.php?option='.$inp->getString('option').$url;
  		$url		= JRoute::_($url);

		$iconclass	= $this->fetchIconClass($name);
		$suffix		= strtolower(str_replace(" ","-", trim($text)));
		$file		= strtolower(str_replace(" ","-", trim($file)));
		$btntext	= JText::_($text);

		return	'<form action="'.$url.'" '."\n\t".
			'method="post" name="form-imp-'.$suffix.'" id="form-imp-'.$suffix.'" '.
			'enctype="multipart/form-data" style="margin:0;padding:0;">'."\n\t".

			'<label class="btn btn-small">'.

			'<input type="file" name="'.$file.'" style="display:none" '.
			'onchange="document.getElementById(\'form-imp-'.$suffix.'\').submit(); "/>'."\n\t".

			'<span class="'.$iconclass.'"></span>'."\n\t".
			$btntext.'</label>'."\n\t".

			'<input type="hidden" value="'.$task.'" name="task"/>'.
			JHtml::_('form.token')."</form>\n";
	}

	/**
	 * Get the button CSS Id
	 *
	 * @param   string  $type  Not used.
	 * @param   string  $html  Not used.
	 * @param   string  $id    The id prefix for the button.
	 *
	 * @return  string  Button CSS Id
	 *
	 * @since   3.0
	 */
	public function fetchId($type = 'ImportFile', $html = '', $id = 'custom')
	{
		return strtolower( $this->_parent->getName() . '-' . $type );
	}
}
