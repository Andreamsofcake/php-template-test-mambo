<?php
/**
* @package Mambo
* @subpackage Content
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

class HTML_content {
	/**
	* Writes a list of the content items
	* @param array An array of content objects
	*/
	function showList( &$rows, $search, $pageNav, $option, $lists ) {
		global $my, $acl;

		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			<th class="frontpage" rowspan="2">
			<?php echo T_('Frontpage Manager'); ?>
			</th>
			<td width="right">
			<?php echo $lists['sectionid'];?>
			</td>
			<td width="right">
			<?php echo $lists['catid'];?>
			</td>
			<td width="right">
			<?php echo $lists['authorid'];?>
			</td>
		</tr>
		<tr>
			<td align="right" colspan="2">
			<?php echo T_('Filter:'); ?>
			</td>
			<td>
			<input type="text" name="search" value="<?php echo $search;?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="5">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
			</th>
			<th class="title">
			<?php echo T_('Title'); ?>
			</th>
			<th width="10%" nowrap="nowrap">
			<?php echo T_('Published'); ?>
			</th>
			<th colspan="2" nowrap="nowrap" width="5%">
			<?php echo T_('Reorder'); ?>
			</th>
			<th width="2%">
			<?php echo T_('Order'); ?>
			</th>
			<th width="1%">
			<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )"><img src="images/filesave.png" border="0" width="16" height="16" alt="<?php echo T_('Save Order'); ?>" /></a>
			</th>
			<th width="8%" nowrap="nowrap">
			<?php echo T_('Access'); ?>
			</th>
			<th width="10%" align="left">
			<?php echo T_('Section'); ?>
			</th>
			<th width="10%" align="left">
			<?php echo T_('Category'); ?>
			</th>
			<th width="10%" align="left">
			<?php echo T_('Author'); ?>
			</th>
		</tr>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) {
			$row = &$rows[$i];

			if (!$row->sectionid == 0 && !$row->catid == 0){
				$link = 'index2.php?option=com_content&sectionid=0&task=edit&hidemainmenu=1&id='. $row->id;
				$row->sect_link = 'index2.php?option=com_sections&task=editA&hidemainmenu=1&id='. $row->sectionid;
				$row->cat_link 	= 'index2.php?option=com_categories&task=editA&hidemainmenu=1&id='. $row->catid;
			} else {
				$link = 'index2.php?option=com_typedcontent&task=edit&hidemainmenu=1&id='. $row->id;
				$row->sect_name	= 'Static Content';
				$row->name 		= 'Static Content';
				$row->sect_link = 'index2.php?option=com_typedcontent';
				$row->cat_link 	= 'index2.php?option=com_typedcontent';
			}




			$now = date( "Y-m-d H:i:s" );
			if ( $now <= $row->publish_up && $row->state == "1" ) {
				$img = 'publish_y.png';
				$alt = T_('Published');
			} else if ( ( $now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00" ) && $row->state == "1" ) {
				$img = 'publish_g.png';
				$alt = T_('Published');
			} else if ( $now > $row->publish_down && $row->state == "1" ) {
				$img = 'publish_r.png';
				$alt = T_('Expired');
			} elseif ( $row->state == "0" ) {
				$img = "publish_x.png";
				$alt = T_('Unpublished');
			}

			$times = '';
		    if ( isset( $row->publish_up ) ) {
				  if ( $row->publish_up == '0000-00-00 00:00:00' ) {
						$times .= '<tr><td>'.T_('Start:').' '.T_('Always').'</td></tr>';
				  } else {
						$times .= '<tr><td>'.T_('Start:').' '. $row->publish_up .'</td></tr>';
				  }
		    }
		    if ( isset( $row->publish_down ) ) {
				  if ( $row->publish_down == '0000-00-00 00:00:00' ) {
						$times .= '<tr><td>'.T_('Finish:').' '.T_('No Expiry').'</td></tr>';
				  } else {
				  $times .= '<tr><td>'.T_('Finish:').' '. $row->publish_down .'</td></tr>';
				  }
		    }

			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );

			if ( $acl->acl_check( 'administration', 'manage', 'users', $my->usertype, 'components', 'com_users' ) ) {
				if ( $row->created_by_alias ) {
					$author = $row->created_by_alias;
				} else {
					$linkA 	= 'index2.php?option=com_users&task=editA&hidemainmenu=1&id='. $row->created_by;
					$author = '<a href="'. $linkA .'" title="'.T_('Edit User').'">'. $row->author .'</a>';
				}
			} else {
				if ( $row->created_by_alias ) {
					$author = $row->created_by_alias;
				} else {
					$author = $row->author;
				}
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td>
				<?php echo $checked; ?>
				</td>
				<td>
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->title;
				} else {
					?>
					<a href="<?php echo $link; ?>" title="<?php echo T_('Edit Content'); ?>">
					<?php echo $row->title; ?>
					</a>
					<?php
				}
				?>
				</td>
				<?php
				if ( $times ) {
					?>
					<td align="center">
					<a href="javascript: void(0);" onmouseover="return overlib('<table><?php echo $times; ?></table>', CAPTION, '<?php echo T_('Publish Information'); ?>', BELOW, RIGHT);" onMouseOut="return nd();" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $row->state ? "unpublish" : "publish";?>')">
					<img src="images/<?php echo $img;?>" width="12" height="12" border="0" alt="<?php echo $alt;?>" />
					</a>
					</td>
					<?php
				}
				?>
				<td>
				<?php echo $pageNav->orderUpIcon( $i ); ?>
				</td>
				<td>
				<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
				</td>
				<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->fpordering;?>" class="text_area" style="text-align: center" />
				</td>
				<td align="center">
				<?php echo $access;?>
				</td>
				<td>
				<a href="<?php echo $row->sect_link; ?>" title="<?php echo T_('Edit Section'); ?>">
				<?php echo $row->sect_name; ?>
				</a>
				</td>
				<td>
				<a href="<?php echo $row->cat_link; ?>" title="<?php echo T_('Edit Category'); ?>">
				<?php echo $row->name; ?>
				</a>
				</td>
				<td>
				<?php echo $author; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</table>

		<?php
		echo $pageNav->getListFooter();
		mosCommonHTML::ContentLegend();
		?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
}
?>