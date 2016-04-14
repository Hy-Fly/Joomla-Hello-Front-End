<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

/*
 *	JHtml and other functions are geared towards the backend admin environment, and expect for instance
 *	the form name to be adminForm. So that is the name used here, although it concerns the frontend
 */

JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->filter_order);
$listDirn	= $this->escape($this->filter_order_Dir);
$nrColumns	= 7;			//number of columns in the table of the list.
?>
<form action="index.php?option=com_helloworld&view=helloworlds" method="post" id="adminForm" name="adminForm">
	<?php echo JText::_('COM_HELLOWORLD_HELLOWORLDS_FILTER'); ?>
	<?php
		echo JLayoutHelper::render(
			'joomla.searchtools.default',
			array('view' => $this)
		);
	?>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
			<th width="1%"><?php  echo JText::_('COM_HELLOWORLD_NUM'); ?></th>
			<th width="2%"><?php  echo JHtml::_('grid.checkall'); ?></th>
			<th width="75%"><?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_GREET_NAME', 'greeting', $listDirn, $listOrder); ?></th>
			<th width="5%"><?php  echo JHtml::_('grid.sort', 'COM_HELLOWORLD_UID', 'uid', $listDirn, $listOrder); ?></th>
			<th width="10%"><?php echo JHtml::_('grid.sort', 'COM_HELLOWORLD_USER_NAME', 'uname', $listDirn, $listOrder); ?></th>
			<th width="5%"><?php  echo JHtml::_('grid.sort', 'COM_HELLOWORLD_PUBLISHED', 'published', $listDirn, $listOrder); ?></th>
			<th width="2%"><?php  echo JHtml::_('grid.sort', 'COM_HELLOWORLD_ID', 'id', $listDirn, $listOrder); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
			<td colspan="<?php echo $nrColumns; ?>"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) :
					$link = JRoute::_( 'index.php?option=com_helloworld&task=helloworld.edit&id=' . $row->id );
				?>
					<tr>
						<td><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
						<td>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_HELLOWORLD_EDIT_HELLOWORLD'); ?>">
								<?php echo $row->greeting; ?>
							</a>
						</td>
						<td><?php echo $row->uid; ?></td>
						<td><?php echo $row->uname; ?></td>
						<td align="center"><?php echo JHtml::_('jgrid.published', $row->published, $i, 'helloworlds.', true, 'cb'); ?></td>
						<td align="center"><?php echo $row->id; ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
