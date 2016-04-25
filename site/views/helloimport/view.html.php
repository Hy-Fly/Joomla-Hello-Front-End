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
 * HelloWorld View
 */
class HelloWorldViewHelloImport extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;

	/**
	 * Display the HelloWorld view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// check if form has sent a file
		$files	= JFactory::getApplication()->input->files;
		$file	= $files->get('importfile');			//Joomla way of $_FILES['importfile']

		if( !empty($file['name']) )
		{
			JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));	//check for form tampering
			
			switch (pathinfo($file['name'], PATHINFO_EXTENSION)) {
			case "csv":
				$ok	= $this->import_csv($file);
				break;
			case "xls":
			case "xlsx":
				$ok	= $this->import_xls($file);
				break;
			default:
				echo "something else";
			}
			echo	( $ok
					? JText::_('COM_HELLOWORLD_IMPORT_SUCCESS')
					: JText::_('COM_HELLOWORLD_IMPORT_ERRORS')
				) . "<br><br>";
		}

		parent::display($tpl);	// Display the template
	}

	/**
	 *	import csv
	 */
	public function import_csv($file)
        {
		// Reads the content which has been uploaded to the tmp file into a text var
		$content = file_get_contents($file['tmp_name']);
		unlink($file['tmp_name']);				//delete tmp file

		$rows	= $this->cvs2array($content, ';');
		$model	= $this->getModel('HelloImport');		//for db read/write
		return	$model->importItems($rows);
	}

	public function cvs2array($content, $delimiter)
	{
		$data		= array();
		$content	= str_replace("\r","\n", $content);
		$content	= str_replace("\n\n","\n", $content);
		$rows		= explode("\n",trim($content));

		echo '<table style="margin:10px;">' . "\n";
		foreach( $rows as $row )
		{
			echo '<tr>' . PHP_EOL;
			if (trim($row))
			{
				$fields	= explode($delimiter,$row);
				array_push($data, $fields);

				foreach( $fields as $f )
				{
					echo	'<td style="border: 1px solid black; padding: 5px;">'
						. $f .'</td>' . PHP_EOL;
				}
			}
			echo '</tr>' . PHP_EOL;
		}
		echo '</table>' . PHP_EOL;

		return $data;
	}

	/**
	 *	import xls
	 */
	public function import_xls($file)
        {
		// Reads the content which has been uploaded to the tmp file into a text var
		$xls		= $file['tmp_name'];
		$objPHPExcel	= PHPExcel_IOFactory::load($xls);
		$sht		= $objPHPExcel->getActiveSheet();

		$highestColumn	= $sht->getHighestDataColumn();
		$highestRow	= $sht->getHighestDataRow();
		$range		= "A1:".$highestColumn.$highestRow;

		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		echo '<table style="margin:10px;">' . "\n";
		for ($row = 1; $row <= $highestRow; ++$row) {
			echo '<tr>' . PHP_EOL;
			for ($col = 0; $col < $highestColumnIndex; ++$col) {
				echo	'<td style="border: 1px solid black; padding: 5px;">' .
					$sht->getCellByColumnAndRow($col, $row)->getValue() .
					'</td>' . PHP_EOL;
			}
			echo '</tr>' . PHP_EOL;
		}
		echo '</table>' . PHP_EOL;

		$rows	= $sht->rangeToArray($range);
		$model	= $this->getModel('HelloImport');		//for db read/write
		return	$model->importItems($rows);
		}
}
