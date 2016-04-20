<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

/** Include PHPExcel */
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/Classes/PHPExcel.php';

/**
 * HelloExport Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 * @since       0.0.9
 */
class HelloWorldControllerHelloExport extends JControllerForm
{
	/**
	 *	export as csv
	 */
	public function exportcsv()
	{
		// Get the input from the url / post
		$app		= JFactory::getApplication();
		$input		= $app->input;
		$filename	= "greetings";

		// the list with pk's for the selected items to export
		$pks		= $input->get('cid', array(), 'array');
		$model		= $this->getModel('HelloWorld');
		$content	= $model->getExportData($pks);

		foreach ($content as $row)
			{ print implode(';', $row)."\n";  }

		// write the header for an object in stead of html file.
		$app
		-> setHeader('Content-Type', 'application/cvs; charset=utf-8', true)
		//-> setHeader('Content-Length', strlen($content), true)
		-> setHeader('Content-Disposition', 'attachment; filename="'.$filename.'.csv"', true)
		-> setHeader('Content-Transfer-Encoding', 'binary', true)
		-> setHeader('Expires', '0', true)
		-> setHeader('Pragma','no-cache',true);
	}

	/**
	 *	perform the export as xls
	 */
	public function exportxls()
	{
		// Get the input from the url / post
		$input		= JFactory::getApplication()->input;

		// the list with pk's for the selected items to export
		$pks		= $input->get('cid', array(), 'array');

		$model		= $this->getModel('HelloWorld');

		// get the data from the db
		$content	= $model->getExportData($pks);

		// Create PHPExcel object
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("John Doe")
			->setLastModifiedBy("Jane Deer")
			->setTitle("My WorkSheet")
			->setSubject("Test Document")
			->setDescription("Test document for XLSX, generated using PHP classes.");

		$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);

		// enter cells in worksheet
		$sht	= $objPHPExcel->setActiveSheetIndex(0);
		$sht->fromArray( $content, NULL, 'A1' );

		// resize column width
		$sht->getColumnDimension('A')->setWidth(5);
		$sht->getColumnDimension('B')->setWidth(5);
		$sht->getColumnDimension('C')->setWidth(35);
		$sht->getColumnDimension('D')->setWidth(20);

		$sht->setSelectedCell('A1');

		// ********************************************
		// Export PHPExcel object

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		// Redirect output to a client's web browser (Excel2007)
		$filename	= "greetings";
		$app		= JFactory::getApplication();
		$app
		-> setHeader('Content-Type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',true)
		-> setHeader('Content-disposition','attachment;filename="'.$filename.'.xlsx";creation-date="'
				.JFactory::getDate()->toRFC822().'"',true)
		-> setHeader('Cache-Control','max-age=1',true)
		-> setHeader('Expires','Mon, 26 Jul 1997 05:00:00 GMT',true)		// Date in the past
		-> setHeader('Last-Modified',gmdate('D, d M Y H:i:s').' GMT',true)	// always modified
		-> setHeader('Cache-Control','cache, must-revalidate',true)
		-> setHeader('Pragma','public',true);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}
}
