<?php
/**
 * display the form from the computed data
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$grt	= $this->item->greeting;
$id 	= $this->item->id;
$uid	= $this->item->uid;
$tCat	= $this->item->category;
$tCat	= ( $tCat and $this->item->params->get('show_category') )
			? (' ('.$tCat.')') : '';
?>
<h1><?php echo $this->item->greeting.(($this->item->category and $this->item->params->get('show_category'))
                                      ? (' ('.$this->item->category.')') : ''); ?></h1>

<p>Item is id=<?php echo $id; ?> of user=<?php echo $uid; ?>.
You are user: <?php echo $this->usr; ?></p>

<form action="<?php echo $this->HelloURI; ?>" method="post" id="HelloForm" name="HelloForm">
Greeting:	<input type="text" name="usergreet" value="<?php echo $grt; ?>">
<input type="submit" value="Update Record">
<?php echo JHtml::_('form.token'); ?>
</form>
