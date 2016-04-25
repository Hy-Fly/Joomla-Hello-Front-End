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

/** Include PHPExcel */
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/Classes/PHPExcel.php';

/**
 * HelloImport Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 */
class HelloWorldControllerHelloImport extends JControllerAdmin
{
	/**
	 *	import
	 *	Defer to the viewer where all the work is done.
	 */
	public function import_check()
	{
		$view		= $this->getView( 'HelloImport', 'html');	//display user form
		$model		= $this->getModel('HelloImport');		//for db read/write
		$view->setModel($model, true);					//define as default model for view
		$view->display();						//call default template
	}

	/**
	 *	Helper functions
	 *	Begin and end are the same, so are combined in separate helpers.
	 */
	private function import_start()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));	//check for form tampering
		$this->inp	= JFactory::getApplication()->input;
		$files		= $this->inp->files;
		$this->file	= reset($files->getArray());			//first (and only) file
		$this->ok	= !empty($this->file);				//file ok?
		return		$this->ok;
	}

	/**
	 *	End
	 */
	private function import_finish()
	{
		// return to present component and view
		$url		= $this->inp->getString('view');
		if( !empty($url) )
		{
			$url	= '&view='.$url;
		}
		$url		= 'index.php?option='.$this->inp->getString('option').$url;
  		$url		= JRoute::_($url);

		if( $this->ok )
		{
			$this->setRedirect($url, JText::_('COM_HELLOWORLD_IMPORT_SUCCESS'), 'message');	// return to submitting view
		} else {
			$this->setRedirect($url, JText::_('COM_HELLOWORLD_IMPORT_ERRORS'), 'error');
		}
	}

	/**
	 *	import csv
	 *	Executes without interaction, so stays in controller and has no view
	 */
	public function importcsv()
	{
		if( $this->import_start() )
		{
			// Reads the content which has been uploaded to the tmp file into a text var
			$content = file_get_contents($this->file['tmp_name']);
			unlink($this->file['tmp_name']);				//delete tmp file

			$rows		= $this->cvs2array($content, ';');
			$model		= $this->getModel('HelloImport');		//for db read/write
			$this->ok	= $model->importItems($rows);
		}
		$this->import_finish();
	}

	private function cvs2array($content, $delimiter)
	{
		$data		= array();
		$content	= str_replace("\r","\n", $content);
		$content	= str_replace("\n\n","\n", $content);
		$rows		= explode("\n",trim($content));

		foreach( $rows as $row )
		{
			if (trim($row))
			{
				$fields	= explode($delimiter,$row);
				array_push($data, $fields);
			}
		}
		return $data;
	}

	/**
	 *	Import an Excel xls file
	 */
	public function importxls()
	{
		if( $this->import_start() )
		{
			$xls		= $this->file['tmp_name'];
			$objPHPExcel	= PHPExcel_IOFactory::load($xls);
			$sht		= $objPHPExcel->getActiveSheet();

			$highestColumm	= $sht->getHighestDataColumn();
			$highestRow	= $sht->getHighestDataRow();
			$range		= "A1:".$highestColumm.$highestRow;

			$rows		= $sht->rangeToArray($range);
			$model		= $this->getModel('HelloImport');		//for db read/write
			$this->ok	= $model->importItems($rows);
		}
		$this->import_finish();
		return;
	}
}
