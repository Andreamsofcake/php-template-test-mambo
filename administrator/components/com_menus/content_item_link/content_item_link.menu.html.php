<?php
/**
* @package Mambo
* @subpackage Menus
* @author Mambo Foundation Inc see README.php
* @copyright Mambo Foundation Inc.
* See COPYRIGHT.php for copyright notices and details.
* @license GNU/GPL Version 2, see LICENSE.php
* Mambo is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; version 2 of the License.
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

/**
* Disaply content item link
*/
class content_item_link_menu_html {

	function edit( &$menu, &$lists, &$params, $option, $content ) {
		global $mosConfig_live_site;
		?>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			//form.name.value = form.componentid.options[form.componentid.selectedIndex].text;
			//submitform( pressbutton );
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			if (pressbutton == 'redirect') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (trim(form.name.value) == ""){
				alert( "<?php echo T_('Link must have a name'); ?>" );
			} else if (trim(form.content_item_link.value) == ""){
				alert( "<?php echo T_('You must select a Content to link to'); ?>" );
			} else {
				form.link.value = "index.php?option=com_content&task=view&id=" + form.content_item_link.value;
				form.componentid.value = form.content_item_link.value;
				submitform( pressbutton );
			}
		}
		</script>

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th>
			<?php echo $menu->id ? T_('Edit') : T_('Add');?> <?php echo T_('Menu Item :: Link - Content Item'); ?>
			</th>
		</tr>
		</table>

		<table width="100%">
		<tr valign="top">
			<td width="60%">
				<table class="adminform">
				<tr>
					<th colspan="2">
					<?php echo T_('Details'); ?>
					</th>
				</tr>
				<tr>
					<td width="10%" align="right">
					<?php echo T_('Name:'); ?>
					</td>
					<td width="80%">
					<input class="inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $menu->name; ?>" />
					</td>
				</tr>
				<tr>
					<td width="10%" align="right" valign="top">
					<?php echo T_('Content to Link:'); ?>
					</td>
					<td width="80%">
					<?php echo $lists['content']; ?>
					</td>
				</tr>
				<tr>
					<td width="10%" align="right"><?php echo T_('Url:'); ?></td>
					<td width="80%">
					<?php echo $lists['link']; ?>
					</td>
				</tr>
				<tr>
					<td width="10%" align="right" valign="top">
					<?php echo T_('On Click, Open in:'); ?>
					</td>
					<td width="80%">
					<?php echo $lists['target']; ?>
					</td>
				</tr>
				<tr>
					<td align="right">
					<?php echo T_('Parent Item:'); ?>
					</td>
					<td>
					<?php echo $lists['parent']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo T_('Ordering:'); ?>
					</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right">
					<?php echo T_('Access Level:'); ?>
					</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right"><?php echo T_('Published:'); ?></td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				</table>
			</td>
			<td width="40%">
				<table class="adminform">
				<tr>
					<th>
					<?php echo T_('Parameters'); ?>
					</th>
				</tr>
				<tr>
					<td>
					<?php echo $params->render();?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<input type="hidden" name="scid" value="<?php echo $menu->componentid; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="id" value="<?php echo $menu->id; ?>" />
		<input type="hidden" name="componentid" value="" />
		<input type="hidden" name="link" value="" />
		<input type="hidden" name="menutype" value="<?php echo $menu->menutype; ?>" />
		<input type="hidden" name="type" value="<?php echo $menu->type; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>
		<?php
	}
}
?>