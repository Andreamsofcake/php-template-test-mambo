<?php
/**
* @package Mambo
* @subpackage Newsfeeds
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

// load the html drawing class
require_once( $mainframe->getPath( 'front_html' ) );

$feedid = intval( mosGetParam( $_REQUEST ,'feedid', 0 ) );
$catid = intval( mosGetParam( $_REQUEST ,'catid', 0 ) );

switch( $task ) {
	case 'view':
		showFeed( $option, $feedid );
		break;
	default:
		listFeeds( $option, $catid );
		break;
}


function listFeeds( $option, $catid ) {
	global $mainframe, $database, $my;
	global $mosConfig_shownoauth, $mosConfig_live_site, $mosConfig_absolute_path;
	global $cur_template, $Itemid;

	/* Query to retrieve all categories that belong under the contacts section and that are published. */
	$query = "SELECT cc.*, a.catid, COUNT(a.id) AS numlinks"
	. "\n FROM #__categories AS cc"
	. "\n LEFT JOIN #__newsfeeds AS a ON a.catid = cc.id"
	. "\n WHERE a.published='1'"
	. "\n AND cc.section='com_newsfeeds'"
	. "\n AND cc.published='1'"
	. "\n AND cc.access <= '". $my->gid ."'"
	. "\n GROUP BY cc.id"
	. "\n ORDER BY cc.ordering"
	;
	$database->setQuery( $query );
	$categories = $database->loadObjectList();

	$rows = array();
	$currentcat = NULL;
	if ( $catid ) {
		// url links info for category
		$query = "SELECT *"
		. "\n FROM #__newsfeeds"
		. "\n WHERE catid = '". $catid."'"
		 . "\n AND published='1'"
		. "\n ORDER BY ordering"
		;
		$database->setQuery( $query );
		$rows = $database->loadObjectList();

		// current category info
		$query = "SELECT name, description, image, image_position"
		. "\n FROM #__categories"
		. "\n WHERE id = '". $catid ."'"
		. "\n AND published = '1'"
		. "\n AND access <= '". $my->gid ."'"
		;
		$database->setQuery( $query );
		$database->loadObject( $currentcat );
	}

	// Parameters
	$menu =& new mosMenu( $database );
	$menu->load( $Itemid );
	$params =& new mosParameters( $menu->params );

	$params->def( 'page_title', 1 );
	$params->def( 'header', $menu->name );
	$params->def( 'pageclass_sfx', '' );
	$params->def( 'headings', 1 );
	$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
	$params->def( 'description_text', '' );
	$params->def( 'image', -1 );
	$params->def( 'image_align', 'right' );
	$params->def( 'other_cat_section', 1 );
	// Category List Display control
	$params->def( 'other_cat', 1 );
	$params->def( 'cat_description', 1 );
	$params->def( 'cat_items', 1 );
	// Table Display control
	$params->def( 'headings', 1 );
	$params->def( 'name', 1 );
	$params->def( 'articles', '1' );
	$params->def( 'link', '1' );

	if ( $catid ) {
		$params->set( 'type', 'category' );
	} else {
		$params->set( 'type', 'section' );
	}
	
	// page description
	$currentcat->descrip = '';
	if( ( @$currentcat->description ) <> '' ) {
		$currentcat->descrip = $currentcat->description;
	} else if ( !$catid ) {
		// show description
		if ( $params->get( 'description' ) ) {
			$currentcat->descrip = $params->get( 'description_text' );
		}
	}

	// page image
	$currentcat->img = '';
	$path = $mosConfig_live_site .'/images/stories/';
	if ( ( @$currentcat->image ) <> '' ) {
		$currentcat->img = $path . $currentcat->image;
		$currentcat->align = $currentcat->image_position;
	} else if ( !$catid ) {
		if ( $params->get( 'image' ) <> -1 ) {
			$currentcat->img = $path . $params->get( 'image' );
			$currentcat->align = $params->get( 'image_align' );
		}
	}

	// page header
	$currentcat->header = '';
	if ( @$currentcat->name <> '' ) {
		$currentcat->header = $currentcat->name;
		$pathway =& mosPathway::getInstance();
		$pathway->addItem($currentcat->header, '');
	} else {
		$currentcat->header = $params->get( 'header' );
	}
	// used to show table rows in alternating colours
	$tabclass = array( 'sectiontableentry1', 'sectiontableentry2' );

	$mainframe->SetPageTitle($menu->name);

	HTML_newsfeed::displaylist( $categories, $rows, $catid, $currentcat, $params, $tabclass );
}


function showFeed( $option, $feedid ) {
	global $database, $mainframe, $mosConfig_absolute_path, $Itemid;

	// Adds parameter handling
	$menu =& new mosMenu( $database );
	$menu->load( $Itemid );
	$params =& new mosParameters( $menu->params );
	$params->def( 'page_title', 1 );
	$params->def( 'header', $menu->name );
	$params->def( 'pageclass_sfx', '' );
	$params->def( 'back_button', $mainframe->getCfg( 'back_button' ) );
	// Feed Display control
	$params->def( 'feed_image', 1 );
	$params->def( 'feed_descr', 1 );
	$params->def( 'item_descr', 1 );
	$params->def( 'word_count', 0 );	
	
	if ( !$params->get( 'page_title' ) ) {
		$params->set( 'header', '' );
	}

	$_and = '';
	if ( $feedid ) {
		$_and = "\n AND id='". $feedid ."'";
	}

	$query = "SELECT name, link, numarticles, cache_time"
	. "\n FROM #__newsfeeds"
	. "\n WHERE published='1'"
	. "\n AND checked_out='0'"
	. $_and
	. "\n ORDER BY ordering"
	;
	$database->setQuery( $query );
	$newsfeeds = $database->loadObjectList();

	$mainframe->SetPageTitle($menu->name);

	HTML_newsfeed::showNewsfeeds( $newsfeeds, $params );
}

?>
