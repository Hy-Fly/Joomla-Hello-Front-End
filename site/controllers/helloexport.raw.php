<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

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
}
