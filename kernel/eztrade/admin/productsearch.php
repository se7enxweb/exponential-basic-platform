<?php
//
// $id: productsearch.php 7709 2001-10-09 08:06:02Z ce $
//
// Created on: <13-Sep-2000 14:56:11 bf>
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
// include_once( "classes/ezcurrency.php" );
// include_once( "classes/ezlist.php" );

$ini = eZINI::instance( 'site.ini' );

$language = $ini->variable( "eZTradeMain", "Language" );
$limit = $ini->variable( "eZTradeMain", "ProductSearchLimit" );

// include_once( "eztrade/classes/ezproductcategory.php" );
// include_once( "eztrade/classes/ezproduct.php" );

$t = new eZTemplate( "kernel/eztrade/admin/" . $ini->variable( "eZTradeMain", "AdminTemplateDir" ),
                     "kernel/eztrade/admin/intl/", $language, "productsearch.php" );

$t->setAllStrings();

$t->set_file( "product_list_page_tpl", "productsearch.tpl" );

// path
$t->set_block( "product_list_page_tpl", "path_item_tpl", "path_item" );

// product
$t->set_block( "product_list_page_tpl", "product_list_tpl", "product_list" );
$t->set_block( "product_list_tpl", "product_item_tpl", "product_item" );
$t->set_block( "product_item_tpl", "product_active_item_tpl", "product_active_item" );
$t->set_block( "product_item_tpl", "product_inactive_item_tpl", "product_inactive_item" );

$t->set_var( "site_style", $siteDesign );

if ( !isset( $limit ) or !is_numeric( $limit ) )
    $limit = 10;
if ( !isset( $offset ) or !is_numeric( $offset ) )
    $offset = 0;

$t->set_var( "search_text", $search );

// products
$product = new eZProduct();
$totalTypes = $product->activeProductSearchCount( $search, FALSE );
$productList = $product->activeProductSearch( $search, $offset, $limit, FALSE );
// $sortMode="time";
// $productList = $product->activeProductSearch( $sortMode, $search, $offset, $limit );

$locale = new eZLocale( $language );
$i=0;
$t->set_var( "product_list", "" );

foreach ( $productList as $product )
{
    $t->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );

    $t->set_var( "product_name", $product->name() );
    $category = $product->categoryDefinition();
    $t->set_var( "product_category", is_a( $category, "eZProductCategory" ) ?
                 $category->name() : "", "&nbsp;" );
    $t->set_var( "product_category_id", is_a( $category, "eZProductCategory" ) ?
                 $category->id() : "", "&nbsp;" );

    $price = new eZCurrency( $product->price() );

    $t->set_var( "product_price", $locale->format( $price ) );
    $t->set_var( "product_active_item", "" );
    $t->set_var( "product_inactive_item", "" );
    if ( $product->showProduct() )
    {
        $t->parse( "product_active_item", "product_active_item_tpl" );
    }
    else
    {
        $t->parse( "product_inactive_item", "product_inactive_item_tpl" );
    }

    if ( $product->productType() == 2 )
        $t->set_var( "action_url", "voucher" );
    else
        $t->set_var( "action_url", "productedit" );

    $t->set_var( "product_id", $product->id() );

    $t->parse( "product_item", "product_item_tpl", true );
    $i++;
}

$t->set_var( "offset", $offset );

$t->set_var( "product_start", $offset + 1 );
$t->set_var( "product_end", min( $offset + $limit, $totalTypes ) );
$t->set_var( "product_total", $totalTypes );

eZList::drawNavigator( $t, $totalTypes, $limit, $offset, "product_list_tpl" );

if ( count( $productList ) > 0 )
    $t->parse( "product_list", "product_list_tpl" );
else
    $t->set_var( "product_list", "" );

$t->pparse( "output", "product_list_page_tpl" );

?>