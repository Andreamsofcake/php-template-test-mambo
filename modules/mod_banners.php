<?php
/**
* @package Mambo
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

$clientids = $params->get( 'banner_cids', '' );
$where = '';
if( $clientids <> '' ) {
	$where = "\nAND cid in ($clientids)";
}
	
$moduleclass_sfx = $params->get( 'moduleclass_sfx' );

$sql = "SELECT count(*) AS numrows FROM #__banner WHERE showBanner=1"
	. (($where<>'') ? $where : "");
$database->setQuery( $sql );

$numrows = $database->loadResult();
if ($numrows === null) {
	echo $database->stderr( true );
	return;
}

if ($numrows > 1) {
	mt_srand( (double) microtime()*1000000 );
	$bannum = mt_rand( 0, --$numrows );
} else {
	$bannum = 0;
}

$sql = "SELECT * FROM #__banner WHERE showBanner=1 "
	. (($where<>'') ? $where : "")
	. "\nLIMIT $bannum,1";
$database->setQuery( $sql );

$banner = null;
if ($database->loadObject( $banner )) {
	$database->setQuery( "UPDATE #__banner SET impmade=impmade+1 WHERE bid='$banner->bid'" );
	if(!$database->query()) {
		echo $database->stderr( true );
		return;
	}
	$banner->impmade++;
	
	if ($numrows > 0) {
		// Check if this impression is the last one and print the banner
		if ($banner->imptotal == $banner->impmade) {
			$query = "INSERT INTO #__bannerfinish (cid, type, name, impressions, clicks, imageurl, datestart, dateend)
				VALUES ('$banner->cid', '$banner->type', '$banner->name', '$banner->impmade', '$banner->clicks', '$banner->imageurl', '$banner->date', now())";
			$database->setQuery($query);
			if(!$database->query()) {
				die($database->stderr(true));
			}
			
			$query="DELETE FROM #__banner WHERE bid=$banner->bid";
			$database->setQuery($query);
			if(!$database->query()) {
				die($database->stderr(true));
			}
		}

		if (trim( $banner->custombannercode )) {
			echo $banner->custombannercode;
		} else if (eregi( "(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$", $banner->imageurl )) {
			$imageurl = "$mosConfig_live_site/images/banners/$banner->imageurl";
			echo "<a href=\"".sefRelToAbs("index.php?option=com_banners&amp;task=click&amp;bid=$banner->bid")."\" target=\"_blank\"><img src=\"$imageurl\" border=\"0\" alt=\"".T_('Banner')."\" /></a>";
			
		} else if (eregi("\.swf$", $banner->imageurl)) {
			$imageurl = "$mosConfig_live_site/images/banners/".$banner->imageurl;
			echo "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" border=\"5\">
					<param name=\"movie\" value=\"$imageurl\"><embed src=\"$imageurl\" loop=\"false\" pluginspage=\"http://www.macromedia.com/go/get/flashplayer\" type=\"application/x-shockwave-flash\"></embed></object>";
		}
	}
} else {
	echo "&nbsp;";
}
?>
