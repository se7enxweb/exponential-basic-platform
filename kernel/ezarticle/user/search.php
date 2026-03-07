<?php
// 
// $id: search.php 9891 2003-09-04 16:13:04Z br $
//
// Created on: <28-Oct-2000 15:56:58 bf>
//
// This source file is part of Exponential Basic, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//


// include_once( "classes/INIFile.php" );
// include_once( "classes/eztemplate.php" );
// include_once( "classes/ezlocale.php" );

// include_once( "ezarticle/classes/ezarticlecategory.php" );
// include_once( "ezarticle/classes/ezarticle.php" );
// include_once( "classes/ezlist.php" );

$language = $ini->variable( "eZArticleMain", "Language" );
$limit = $ini->variable( "eZArticleMain", "SearchListLimit" );
$searchWithinSections = $ini->variable( "eZArticleMain", "SearchWithinSections" );

// init the section
if ( isset ($sectionIDOverride) )
{
    $globalSectionID = $sectionIDOverride;
    // include_once( "ezsitemanager/classes/ezsection.php" );

    $sectionObject = eZSection::globalSectionObject( $sectionIDOverride );
    $sectionObject->setOverrideVariables();
}
else
{
    $sectionIDOverride = false;
}

$t = new eZTemplate( "kernel/ezarticle/user/" . $ini->variable( "eZArticleMain", "TemplateDir" ),
                     "kernel/ezarticle/user/intl/", $language, "search.php" );

$t->setAllStrings();

// $t->set_file( "article_list_page_tpl", "search.tpl" );

$t->set_file( array(
    "article_list_page_tpl" => "search.tpl"
    ) );

// article
$t->set_block( "article_list_page_tpl", "article_list_tpl", "article_list" );
$t->set_block( "article_list_tpl", "article_item_tpl", "article_item" );

// Init url variables - for eZList...
$t->set_var( "url_start_stamp", urlencode( "+" ) );
$t->set_var( "url_stop_stamp", urlencode( "+" ) );
$t->set_var( "url_category_array", urlencode( "+" ) );
$t->set_var( "url_contentswriter_id", urlencode( "+" ) );
$t->set_var( "url_photographer_id", urlencode( "+" ) );

if ( isset($_REQUEST['StartMonth']) && isset($_REQUEST['StartDay']) && isset($_REQUEST['StartYear']) && 
	checkdate ( $_REQUEST['StartMonth'], $_REQUEST['StartDay'], $_REQUEST['StartYear'] ) )
{
    $startDate = new eZDateTime( $_REQUEST['StartYear'], $_REQUEST['StartMonth'], $_REQUEST['StartDay'], 
    	$_REQUEST['StartHour'], $_REQUEST['StartMinute'], 0 );
    $startStamp = $startDate->timeStamp();
}
if ( isset($_REQUEST['StopMonth']) && isset($_REQUEST['StopDay']) && isset($_REQUEST['StopYear']) &&
	 checkdate ( $stopMonth, $stopDay, $stopYear ) )
{
    $stopDate = new eZDateTime( $_REQUEST['StopYear'], $_REQUEST['StopMonth'], $_REQUEST['StopDay'], 
    			$_REQUEST['StopHour'], $_REQUEST['StopMinute'], 0 );
    $stopStamp = $stopDate->timeStamp();
}

$t->set_var( "search_text", "" );

if ( isset( $categoryID ) )
{
	$category = new eZArticleCategory( $categoryID );
} 
else 
{
	$category = new eZArticleCategory();
}

$t->set_var( "current_category_id", $category->id() );
$t->set_var( "current_category_name", $category->name() );
$t->set_var( "current_category_description", $category->description() );

$tmpSearchText = str_replace( "<", "&lt;", $_REQUEST['SearchText'] );
$tmpSearchText = str_replace( ">", "&gt;", $tmpSearchText );
$t->set_var( "search_text", $tmpSearchText );

$offset = isset ( $_REQUEST['Offset'] )?isset ( $_REQUEST['Offset'] ):0;

// articles
$paramsArray = array();
if ( isset($_REQUEST['SearchText']) )
{
/*    if ( isset( $startStamp ) )
    {
        $paramsArray["FromDate"] = $startStamp;
        $t->set_var( "url_start_stamp", urlencode( $startStamp ) );
    }

    if ( isset( $stopStamp ) )
    {
        $paramsArray["ToDate"] = $stopStamp;
        $t->set_var( "url_stop_stamp", urlencode( $stopStamp ) );
    }   */
    
    if ( $searchWithinSections == "enabled" )
    {
	if ( isset( $sectionsList ) )
	{
	    $paramsArray["SectionsList"] = $sectionsList;
	}
	else
	{
	    $paramsArray["SectionsList"] = "$sectionIDOverride";
	}
    }										       

    if( isset($_REQUEST['ContentsWriterID']) )
    {
        $paramsArray["AuthorID"] = $_REQUEST['ContentsWriterID'];
        $t->set_var( "url_contentswriter_id", htmlspecialchars( $_REQUEST['ContentsWriterID'] ) );
    }

    if( isset($_REQUEST['PhotographerID'] ))
    {
        $paramsArray["PhotographerID"] = $_REQUEST['PhotographerID'];
        $t->set_var( "url_photographer_id", htmlspecialchars( $_REQUEST['PhotographerID'] ) );
    }

    if( isset($_REQUEST['CategoryArray']) && is_array( $_REQUEST['CategoryArray'] ) && 
    	count( $_REQUEST['CategoryArray'] ) > 0 && !in_array( 0, $_REQUEST['CategoryArray'] ) )
    {
        $paramsArray["Categories"] = $_REQUEST['CategoryArray'];

        // fix output string for URL
        $t->set_var( "url_category_array", htmlspecialchars( implode( "-", $_REQUEST['CategoryArray'] ) ) );
    }
/*
	echo "StartStamp: ".$startStamp."<br>";
	echo "StopStamp: ".$stopStamp."<br>";
	echo "SectionID: ".$sectionIDOverride."<br>";
	echo "ContentWriterID: ".$contentsWriterID."<br>";
	echo "PhotographerID: ".$photographerID."<br>";  
	echo "<pre>";
	print_r ( $paramsArray );
	echo "</pre>";
	exit();  */

    $t->set_var( "search_text", $tmpSearchText );

    $article = new eZArticle();
    $totalCount = 0;
    $articleList = $article->search( $_REQUEST['SearchText'], "time", false, $offset, $limit, $paramsArray, $totalCount );

    $t->set_var( "url_text", htmlspecialchars( $_REQUEST['SearchText'] ) );
}

// if ( ( $maxSearchForArticles != 0 ) && ( $maxSearchForArticles < $totalCount ) )

if ( isset( $articleList ) && count ( $articleList ) > 0 )
{
    $locale = new eZLocale( $language );
    $i=0;
    $t->set_var( "article_list", "" );
    foreach ( $articleList as $article )
    {
        $t->set_var( "article_name", $article->name() );

        $t->set_var( "article_id", $article->id() );
	
        $t->set_var( "category_id", $article->GetCategory( $sectionIDOverride ) );

        if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "td_class", "bglight" );
        }
        else
        {
            $t->set_var( "td_class", "bgdark" );
        }

        $published = $article->published();

        $t->set_var( "article_published", $locale->format( $published ) );    
    
        $t->parse( "article_item", "article_item_tpl", true );
        $i++;
    }
}

//echo "<pre>";
//print_r ( $t );

//echo "totalCount:".$totalCount."<br>";
//echo "Limit:".$limit."<br>";
//echo "Offset:".$offset."<br>";
//echo "</pre>";
//exit();

eZList::drawNavigator( $t, $totalCount, $limit, $offset, "article_list_page_tpl" );


if ( isset( $articleList ) && count( $articleList ) > 0 )    
    $t->parse( "article_list", "article_list_tpl" );
else
    $t->set_var( "article_list", "" );

if ( $totalCount == 0 )
    $t->set_var( "article_start", 0 );
else
    $t->set_var( "article_start", $offset + 1 );

$t->set_var( "article_end", min( $offset + $limit, $totalCount ) );
$t->set_var( "article_total", $totalCount );

if ( isset ($sectionIDOverride) ) $t->set_var( "section_id", $sectionIDOverride );

$t->pparse( "output", "article_list_page_tpl" );

?>