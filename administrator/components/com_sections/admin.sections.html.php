<?php
/**
* @package Mambo
* @subpackage Sections
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

class sections_html {
	/**
	* Writes a list of the categories for a section
	* @param array An array of category objects
	* @param string The name of the category section
	*/
	function show( &$rows, $scope, $myid, &$pageNav, $option ) {
		global $my;

		mosCommonHTML::loadOverlib();
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table class="adminheading">
		<tr>
			 <th class="sections">
			<?php echo T_('Section Manager'); ?>
			</th>
		</tr>
		</table>

		<table class="adminlist">
		<tr>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows );?>);" />
			</th>
			<th class="title">
			<?php echo T_('Section Name'); ?>
			</th>
			<th width="10%">
			<?php echo T_('Published'); ?>
			</th>
			<th colspan="2" width="5%">
			<?php echo T_('Reorder'); ?>
			</th>
			<th width="2%">
			<?php echo T_('Order'); ?>
			</th>
			<th width="1%">
			<a href="javascript: saveorder( <?php echo count( $rows )-1; ?> )"><img src="images/filesave.png" border="0" width="16" height="16" alt="<?php echo T_('Save Order'); ?>" /></a>
			</th>
			<th width="8%">
			<?php echo T_('Access'); ?>
			</th>
			<th width="12%" nowrap>
			<?php echo T_('Section ID'); ?>
			</th>
			<th width="12%" nowrap>
			<?php echo T_('# Categories'); ?>
			</th>
			<th width="12%" nowrap>
			<?php echo T_('# Active'); ?>
			</th>
			<th width="12%" nowrap>
			<?php echo T_('# Trash'); ?>
			</th>

		</tr>
		<?php
		$k = 0;
		for ( $i=0, $n=count( $rows ); $i < $n; $i++ ) {
			$row = &$rows[$i];

			$link = 'index2.php?option=com_sections&scope=content&task=editA&hidemainmenu=1&id='. $row->id;

			$access 	= mosCommonHTML::AccessProcessing( $row, $i );
			$checked 	= mosCommonHTML::CheckedOutProcessing( $row, $i );
			$published 	= mosCommonHTML::PublishedProcessing( $row, $i );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20" align="right">
				<?php echo $pageNav->rowNumber( $i ); ?>
				</td>
				<td width="20">
				<?php echo $checked; ?>
				</td>
				<td width="35%">
				<?php
				if ( $row->checked_out && ( $row->checked_out != $my->id ) ) {
					echo $row->name. " ( ". $row->title ." )";
				} else {
					?>
					<a href="<?php echo $link; ?>">
					<?php echo $row->name. " ( ". $row->title ." )"; ?>
					</a>
					<?php
				}
				?>
				</td>
				<td align="center">
				<?php echo $published;?>
				</td>
				<td>
				<?php echo $pageNav->orderUpIcon( $i ); ?>
				</td>
				<td>
				<?php echo $pageNav->orderDownIcon( $i, $n ); ?>
				</td>
				<td align="center" colspan="2">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td align="center">
				<?php echo $access;?>
				</td>
				<td align="center">
				<?php echo $row->id; ?>
				</td>
				<td align="center">
				<?php echo $row->categories; ?>
				</td>
				<td align="center">
				<?php echo $row->active; ?>
				</td>
				<td align="center">
				<?php echo $row->trash; ?>
				</td>
				<?php
				$k = 1 - $k;
				?>
			</tr>
			<?php
		}
		?>
		</table>

		<?php echo $pageNav->getListFooter(); ?>

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="scope" value="<?php echo $scope;?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="chosen" value="" />
		<input type="hidden" name="act" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		</form>
		<?php
	}

	/**
	* Writes the edit form for new and existing categories
	*
	* A new record is defined when <var>$row</var> is passed with the <var>id</var>
	* property set to 0.  Note that the <var>section</var> property <b>must</b> be defined
	* even for a new record.
	* @param mosCategory The category object
	* @param string The html for the image list select list
	* @param string The html for the image position select list
	* @param string The html for the ordering list
	* @param string The html for the groups select list
	*/
	function edit( &$row, $option, &$lists, &$menus ) {
		global $mosConfig_live_site;
		if ( $row->name != '' ) {
			$name = $row->name;
		} else {
			$name = T_("New Section");
		}
		if ($row->image == "") {
			$row->image = 'blank.png';
		}
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			if ( pressbutton == 'menulink' ) {
				if ( form.menuselect.value == "" ) {
					alert( "<?php echo T_('Please select a Menu'); ?>" );
					return;
				} else if ( form.link_type.value == "" ) {
					alert( "<?php echo T_('Please select a menu type'); ?>" );
					return;
				} else if ( form.link_name.value == "" ) {
					alert( "<?php echo T_('Please enter a Name for this menu item'); ?>" );
					return;
				}
			}

			if (form.name.value == ""){
				alert("<?php echo T_('Section must have a name'); ?>");
			} else if (form.title.value ==""){
				alert("<?php echo T_('Section must have a title'); ?>");
			} else {
				<?php getEditorContents( 'editor1', 'description' ) ; ?>
				submitform(pressbutton);
			}
		}
		// show / hide publishing information
			function displayParameterInfo()
			{

				if(document.getElementById('simpleediting').style.display == 'block')
				{
					document.getElementById('simpleediting').style.display = 'none';
					document.getElementById('show').style.display = 'block';
					document.getElementById('hide').style.display = 'none';
					document.adminForm.simple_editing.value ='on';
				}
				else
				{
					document.getElementById('simpleediting').style.display = 'block';
					document.getElementById('show').style.display = 'none';
					document.getElementById('hide').style.display = 'block';
					document.adminForm.simple_editing.value ='off';
				}

			}
		</script>
		<?php
		if($_SESSION['simple_editing'] == 'on')
		{
			$simpleediting ='none';
			$simple = 'block';
			$advanced = 'none';
		}
		else
		{

			$advanced = 'block';
			$simple = 'none';
			$simpleediting ='block';
		}

		?>
		<form action="index2.php" method="post" name="adminForm">
		<input type ="hidden" name="simple_editing" value='' />
		<table class="adminheading">
		<tr>
			<th class="sections">
			<?php echo T_('Section:'); ?>
			<small>
			<?php echo $row->id ? T_('Edit') : T_('New');?>
			</small>
			<small><small>
			[ <?php echo $name ; ?> ]
			</small></small>
			</th>
		</tr>
		</table>
		<table width="100%">
			<tr>
				<td valign="top" align="right">
				<div id = "show" style="display:<?php echo $simple;?>">
				<a href="javascript:displayParameterInfo();"><?php echo T_('Show Advanced Details'); ?></a>
				</div>
				<div id = "hide" style="display:<?php echo $advanced;?>">
				<a href="javascript:displayParameterInfo();"><?php echo T_('Hide Advanced Details'); ?></a>
				</div>
				</td>
			</tr>
		</table>
		<table width="100%">
		<tr>
			<td valign="top" >
				<table class="adminform">
				<tr>
					<th colspan="3">
					<?php echo T_('Section Details'); ?>
					</th>
				</tr>
				<tr>
					<td width="150">
					<?php echo T_('Scope:'); ?>
					</td>
					<td width="85%" colspan="2">
					<strong>
					<?php echo $row->scope; ?>
					</strong>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo T_('Title:'); ?>
					</td>
					<td colspan="2">
					<input class="text_area" type="text" name="title" value="<?php echo $row->title; ?>" size="50" maxlength="50" title="<?php echo T_('A short name to appear in menus'); ?>" />
					</td>
				</tr>
				<tr>
					<td>
					<?php echo (isset($row->section) ? T_("Category") : T_("Section"));?> <?php echo T_('Name:'); ?>
					</td>
					<td colspan="2">
					<input class="text_area" type="text" name="name" value="<?php echo $row->name; ?>" size="50" maxlength="255" title="<?php echo T_('A long name to be displayed in headings'); ?>" />
					</td>
				</tr>
				<tr>
					<td>
					<?php echo T_('Image:'); ?>
					</td>
					<td>
					<?php echo $lists['image']; ?>
					</td>
					<td rowspan="4" width="50%">
					<?php
						$path = $mosConfig_live_site . "/images/";
						if ($row->image != "blank.png") {
							$path.= "stories/";
						}
					?>
					<img src="<?php echo $path;?><?php echo $row->image;?>" name="imagelib" width="80" height="80" border="2" alt="<?php echo T_('Preview'); ?>" />
					</td>
				</tr>
				<tr>
					<td>
					<?php echo T_('Image Position:'); ?>
					</td>
					<td>
					<?php echo $lists['image_position']; ?>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo T_('Ordering:'); ?>
					</td>
					<td>
					<?php echo $lists['ordering']; ?>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo T_('Access Level:'); ?>
					</td>
					<td>
					<?php echo $lists['access']; ?>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo T_('Published:'); ?>
					</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top">
					<?php echo T_('Description:'); ?>
					</td>
					<td colspan="2">
					<?php
					// parameters : areaname, content, hidden field, width, height, rows, cols
					editorArea( 'editor1',  $row->description , 'description', '100%;', '300', '60', '20' ) ; ?>
					</td>
				</tr>
				</table>
			</td>
			<td valign="top" align="right">
			<div id="simpleediting" style="display:<?php echo $simpleediting;?>">
			<table cellspacing="0" cellpadding="0" border="0" width="100%" >
				<tr>
					<td width="40%">
			<?php
			if ( $row->id > 0 ) {
    				?>
				<table class="adminform">
				<tr>
					<th colspan="2">
					<?php echo T_('Link to Menu'); ?>
					</th>
				</tr>
				<tr>
					<td colspan="2">
					<?php echo T_('This will create a new menu item in the menu you select'); ?>
					<br /><br />
					</td>
				</tr>
				<tr>
					<td valign="top" width="100px">
					<?php echo T_('Select a Menu'); ?>
					</td>
					<td>
					<?php echo $lists['menuselect']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="100px">
					<?php echo T_('Select Menu Type'); ?>
					</td>
					<td>
					<?php echo $lists['link_type']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" width="100px">
					<?php echo T_('Menu Item Name'); ?>
					</td>
					<td>
					<input type="text" name="link_name" class="inputbox" value="" size="25" />
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
					<input name="menu_link" type="button" class="button" value="<?php echo T_('Link to Menu'); ?>" onClick="submitbutton('menulink');" />
					</td>
				</tr>
				<tr>
					<th colspan="2">
					<?php echo T_('Existing Menu Links'); ?>
					</th>
				</tr>
				<?php
				if ( $menus == NULL ) {
					?>
					<tr>
						<td colspan="2">
						<?php echo T_('None'); ?>
						</td>
					</tr>
					<?php
				} else {
					mosCommonHTML::menuLinksSecCat( $menus );
				}
				?>
				<tr>
					<td colspan="2">
					</td>
				</tr>
				</table>
			<?php
			} else {
			?>

			<table class="adminform" width="40%">
				<tr><th>&nbsp;</th></tr>
				<tr><td><?php echo T_('Menu links available when saved'); ?></td></tr>
			</table>
			<?php
			}
			?>
			</td>
		</tr>
		</table>
		</div>
		</td>
		</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="scope" value="<?php echo $row->scope; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="oldtitle" value="<?php echo $row->title ; ?>" />
		</form>
		</td>
		</tr>
		</table>
		<?php
	}


	/**
	* Form to select Section to copy Category to
	*/
	function copySectionSelect( $option, $cid, $categories, $contents, $section ) {
		?>
		<form action="index2.php" method="post" name="adminForm">
		<br />
		<table class="adminheading">
		<tr>
			<th class="sections">
			<?php echo T_('Copy Section'); ?>
			</th>
		</tr>
		</table>

		<br />
		<table class="adminform">
		<tr>
			<td width="3%"></td>
			<td align="left" valign="top" width="30%">
			<strong><?php echo T_('Copy to Section:'); ?></strong>
			<br />
			<input class="text_area" type="text" name="title" value="" size="35" maxlength="50" title="<?php echo T_('The new Section name'); ?>" />
			<br /><br />
			</td>
			<td align="left" valign="top" width="20%">
			<strong><?php echo T_('Categories being copied:'); ?></strong>
			<br />
			<?php
			echo "<ol>";
			if ($categories) {
				foreach ( $categories as $category ) {
					echo "<li>". $category->name ."</li>";
					echo "\n <input type=\"hidden\" name=\"category[]\" value=\"$category->id\" />";
				}
			} else {
				echo 'none';
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top" width="20%">
			<strong><?php echo T_('Content Items being copied:'); ?></strong>
			<br />
			<?php
			echo "<ol>";
			if ($contents) {
				foreach ( $contents as $content ) {
					echo "<li>". $content->title ."</li>";
					echo "\n <input type=\"hidden\" name=\"content[]\" value=\"$content->id\" />";
				}
			} else {
				echo 'none';
			}
			echo "</ol>";
			?>
			</td>
			<td valign="top">
			<?php echo T_('This will copy the Categories listed
			<br />
			and all the items within the category (also listed)
			<br />
			to the new Section created.'); ?>
			</td>.
		</tr>
		</table>
		<br /><br />

		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="section" value="<?php echo $section;?>" />
		<input type="hidden" name="boxchecked" value="1" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="scope" value="content" />
		<?php
		foreach ( $cid as $id ) {
			echo "\n <input type=\"hidden\" name=\"cid[]\" value=\"$id\" />";
		}
		?>
		</form>
		<?php
	}

}
?>