<?php
/**
 * @subpackage  com_helloworld
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');	// No direct access
JHtml::_('behavior.formvalidation');
?>
<form	action="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloimport'); ?>"
	method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<input type="file" name="importfile" id="importfile" />
	<input type="submit" value="Upload" name="submit" />
	<a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworlds'); ?>" class="btn btn-default" style="margin-left:100px;">Go Back</a>

	<?php echo JHtml::_('form.token'); ?>
</form>
