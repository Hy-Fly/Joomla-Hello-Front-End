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
 * HelloWorld Controller
 *
 * "JControllerForm is a controller tailored to suit most form-based admin operations"
 * with standard functions for edit, save etc. The functions apply, save2new and save2copy
 * are registered in the constructor to refer to the function 'save'
 * See libraries/legacy/controller/form.php
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 * @since       0.0.9
 */
class HelloWorldControllerHelloWorld extends JControllerForm
{
	/**
	* Implement to allow edit or not.
	* Treat item for which asset has not been defined yet as new item
	* otherwise no permission will be granted to user.
	*
	* Overwrites: JControllerForm::allowEdit
	*
	* @param array $data
	* @param string $key
	* @return bool
	*/
	protected function allowEdit($data = array(), $key = 'id')
	{
		$id 	= (int)$data['asset_id'];
		if( $id == 0 )
		{
			return $this->allowAdd($data);
		}

		$id 	= isset( $data[ $key ] ) ? $data[ $key ] : 0;
		if( !empty( $id ) )
		{
			return JFactory::getUser()->authorise( "core.edit", "com_helloworld.message." . $id );
		}
	}
}
