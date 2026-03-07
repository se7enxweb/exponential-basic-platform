<?php
// 
// $id: extendedsearch.php 6206 2001-07-19 12:19:22Z jakobn $
//
// Created on: <29-Mar-2001 11:15:24 amos>
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
// include_once( "classes/ezlist.php" );

// include_once( "ezarticle/classes/ezarticlecategory.php" );
// include_once( "ezarticle/classes/ezarticle.php" );

$ini = eZINI::instance( 'site.ini' );

$language = $ini->variable( "eZArticleMain", "Language" );

$t = new eZTemplate( "kernel/ezarticle/user/" . $ini->variable( "eZArticleMain", "TemplateDir" ),
                     "kernel/ezarticle/user/intl/", $language, "extendedsearch.php" );

$t->setAllStrings();

$t->set_file( "extended_search_tpl", "extendedsearch.tpl" );

$t->set_block( "extended_search_tpl", "search_item_tpl", "search_item" );
$t->set_block( "search_item_tpl", "category_item_tpl", "category_item" );

$t->set_block( "extended_search_tpl", "article_list_tpl", "article_list" );
$t->set_block( "article_list_tpl", "article_item_tpl", "article_item" );

$contents = eZArticle::shortContents();
$t->set_var( "category_item", "" );
foreach( $contents as $content )
{
    $t->set_var( "category_id", $content );
    $t->set_var( "category_name", $content );
    $t->set_var( "category_level", "" );
    $t->set_var( "category_selected", $category == $content ? "selected" : "" );
    $t->parse( "category_item", "category_item_tpl", true );
}
$t->set_var( "search_text", $searchText );
$t->set_var( "search_url_text", $searchText == "" ? "+" : $searchText );

$t->parse( "search_item", "search_item_tpl" );

$t->set_var( "article_list", "" );

if ( !is_numeric( $offset ) )
    $offset = 0;
if ( !is_numeric( $max ) )
    $max = 4;

if ( isset( $search ) )
{
    $words = preg_split( "/[, ]+/", $searchText );
    $keywords = array();
    foreach( $words as $word )
    {
        $keyword = strtolower( trim( $word ) );
        if ( $keyword != "" )
            $keywords[] = $keyword;
    }
    $articles = eZArticle::searchByShortContent( $category, $keywords, $offset, $max );
    $t->set_var( "article_item", "" );
    $t->set_var( "category", $category == "" ? "+" : $category );
    foreach( $articles as $article )
    {
        $t->set_var( "article_id", $article->id() );
        $t->set_var( "article_name", $article->name() );
        $t->set_var( "article_page", 1 );
        $cats = $article->categories( false );
        $t->set_var( "article_category", $cats[0] );
        $t->parse( "article_item", "article_item_tpl", true );
    }
    $articleCount = eZArticle::searchByShortContent( $category, $keywords, true );

    eZList::drawNavigator( $t, $articleCount, $max, $offset, "article_list_tpl" );

    $t->parse( "article_list", "article_list_tpl" );

}

$t->pparse( "output", "extended_search_tpl" );

?>
