<?php
// 
// $id: search.php 7268 2001-09-16 18:37:39Z bf $
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
// include_once( "ezuser/classes/ezobjectpermission.php" );
// include_once( "classes/ezlist.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZArticleMain", "Language" );
$limit = $ini->variable( "eZArticleMain", "AdminListLimit" );


if( isset( $delete ) && count( $articleArrayID ) > 0 )
{
    foreach( $articleArrayID as $articleID )
    {
        if( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' ) )
        {
            $article = new eZArticle( $articleID );
            $article->delete();
        }
    }
}

$t = new eZTemplate( "kernel/ezarticle/admin/" . $ini->variable( "eZArticleMain", "AdminTemplateDir" ),
                     "kernel/ezarticle/admin/intl/", $language, "search.php" );

$t->setAllStrings();

$t->set_file( array(
    "article_list_page_tpl" => "search.tpl"
    ) );

if ( !isset ( $offset ) )
    $offset = 0;
if ( !isset ( $startMonth ) or $startMonth == '' )
    $startMonth = 0;
if ( !isset ( $startDay ) or $startDay == ''  )
    $startDay = 0;
if ( !isset ( $startYear ) or $startYear == ''  )
    $startYear = 0;

if ( !isset ( $stopMonth ) or $stopMonth == '' )
    $stopMonth = 0;
if ( !isset ( $stopDay ) or $stopDay == ''  )
    $stopDay = 0;
if ( !isset ( $stopYear ) or $stopYear == ''  )
    $stopYear = 0;

// article
$t->set_block( "article_list_page_tpl", "article_list_tpl", "article_list" );
$t->set_block( "article_list_tpl", "article_item_tpl", "article_item" );
$t->set_block( "article_item_tpl", "article_is_published_tpl", "article_is_published" );
$t->set_block( "article_item_tpl", "article_not_published_tpl", "article_not_published" );
$t->set_block( "article_list_page_tpl", "article_delete_tpl", "article_delete" );

// Init url variables - for eZList...
$t->set_var( "url_start_stamp", urlencode( "+" ) );
$t->set_var( "url_stop_stamp", urlencode( "+" ) );
$t->set_var( "url_category_array", urlencode( "+" ) );
$t->set_var( "url_contentswriter_id", urlencode( "+" ) );
$t->set_var( "url_photographer_id", urlencode( "+" ) );

if ( checkdate ( $startMonth, $startDay, $startYear ) )
{
    $startDate = new eZDateTime( $startYear,  $startMonth, $startDay, $startHour, $startMinute, 0 );
    $startStamp = $startDate->timeStamp();
}
if ( checkdate ( $stopMonth, $stopDay, $stopYear ) )
{
    $stopDate = new eZDateTime( $stopYear, $stopMonth, $stopDay, $stopHour, $stopMinute, 0 );
    $stopStamp = $stopDate->timeStamp();
}


// BUILDING THE SEARCH
// If url parameters are present when loading page, they are decoded in the datasupplier
$paramsArray = array();
if ( $searchText )
{
    if ( isset( $startStamp ) )
    {
        $paramsArray["FromDate"] = $startStamp;
        $t->set_var( "url_start_stamp", urlencode( $startStamp ) );
    }
        
    if ( isset( $stopStamp ) )
    {
        $paramsArray["ToDate"] = $stopStamp;
        $t->set_var( "url_stop_stamp", urlencode( $stopStamp ) );
    }

    if( isset( $contentsWriterID ) && $contentsWriterID != 0 )
    {
        $paramsArray["AuthorID"] = $contentsWriterID;
        $t->set_var( "url_contentswriter_id", urlencode( $contentsWriterID ) );
    }

    if( isset( $photographerID ) && $photographerID != 0 )
    {
        $paramsArray["PhotographerID"] = $photographerID;
        $t->set_var( "url_photographer_id", urlencode( $photographerID ) );
    }

    if( isset( $categoryArray ) && is_array( $categoryArray ) && count( $categoryArray ) > 0 && !in_array( 0, $categoryArray ) )
    {
        $paramsArray["Categories"] = $categoryArray;

        // fix output string for URL
        $t->set_var( "url_category_array", urlencode( implode( "-", $categoryArray ) ) );
    }

 //   $paramsArray["SearchExcludedArticles"] = "true";
    
    
    $article = new eZArticle();
    $totalCount = false;
    $articleList = $article->search( $searchText, "time", true, $offset, $limit, $paramsArray, $totalCount );

    $t->set_var( "search_text", $searchText );
    $t->set_var( "url_text", urlencode ( $searchText ) );
}

if ( isset( $articleList ) && count ( $articleList ) > 0 )
{
    $locale = new eZLocale( $language );
    $i=0;
    $t->set_var( "article_list", "" );
    foreach ( $articleList as $article )
    {
        $t->set_var( "article_name", $article->name() );

        $t->set_var( "article_id", $article->id() );

        if ( $article->isPublished() == true )
        {
            $t->parse( "article_is_published", "article_is_published_tpl" );
            $t->set_var( "article_not_published", "" );        
        }
        else
        {
            $t->set_var( "article_is_published", "" );
            $t->parse( "article_not_published", "article_not_published_tpl" );
        }

        if ( ( $i % 2 ) == 0 )
        {
            $t->set_var( "td_class", "bglight" );
        }
        else
        {
            $t->set_var( "td_class", "bgdark" );
        }

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
{
    $t->parse( "article_list", "article_list_tpl" );
    $t->parse( "article_delete", "article_delete_tpl" );
}
else
{
    $t->set_var( "article_list", "" );
    $t->set_var( "article_delete", "" );
}

$t->set_var( "article_start", $offset + 1 );
$t->set_var( "article_end", min( $offset + $limit, $totalCount ) );
$t->set_var( "article_total", $totalCount );

$t->pparse( "output", "article_list_page_tpl" );

?>