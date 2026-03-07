<?php
// 
// $id: categorylist.php,v 1.32.2.2 2001/11/21 17:34:16 br Exp $
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
// include_once( "classes/ezcachefile.php" );
// include_once( "classes/ezlist.php" );

function deleteCache( $productID, $categoryID, $categoryArray )
{
    if ( get_class( $productID ) == "ezproduct" )
    {
        $categoryID = $productID->categoryDefinition( false );
        $categoryArray = $productID->categories( false );
        $productID = $productID->id();
    }

    $files = eZCacheFile::files( "eztrade/cache/", array( "productlist",
                                                          array_merge( $categoryID, $categoryArray ) ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }
    $files = eZCacheFile::files( "eztrade/cache/", array( "hotdealslist" ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }
}


$ini = eZINI::instance( 'site.ini' );

$language = $ini->variable( "eZTradeMain", "Language" );
$limit = $ini->variable( "eZTradeMain", "ProductLimit" );

// include_once( "eztrade/classes/ezproductcategory.php" );
// include_once( "eztrade/classes/ezproduct.php" );

$t = new eZTemplate( "kernel/eztrade/admin/" . $ini->variable( "eZTradeMain", "AdminTemplateDir" ),
                     "kernel/eztrade/admin/intl/", $language, "simplecategorylist.php" );

$t->setAllStrings();

$t->set_file( "category_list_page_tpl", "simplecategorylist.tpl" );

// path
$t->set_block( "category_list_page_tpl", "path_item_tpl", "path_item" );

// category
$t->set_block( "category_list_page_tpl", "category_list_tpl", "category_list" );
$t->set_block( "category_list_tpl", "category_item_tpl", "category_item" );

// product
$t->set_block( "category_list_page_tpl", "product_list_tpl", "product_list" );
$t->set_block( "product_list_tpl", "product_item_tpl", "product_item" );
$t->set_block( "product_item_tpl", "product_active_item_tpl", "product_active_item" );
$t->set_block( "product_item_tpl", "product_inactive_item_tpl", "product_inactive_item" );

$t->set_block( "product_item_tpl", "voucher_icon_tpl", "voucher_icon" );
$t->set_block( "product_item_tpl", "product_icon_tpl", "product_icon" );

$t->set_block( "product_item_tpl", "inc_vat_item_tpl", "inc_vat_item" );
$t->set_block( "product_item_tpl", "ex_vat_item_tpl", "ex_vat_item" );

// move up / down
$t->set_block( "product_list_tpl", "absolute_placement_header_tpl", "absolute_placement_header" );
$t->set_block( "product_item_tpl", "absolute_placement_item_tpl", "absolute_placement_item" );

$t->set_var( "site_style", $siteDesign );

$category = new eZProductCategory( 1 );
// $category->copy( true );


$category = new eZProductCategory();

if( isset( $parentID ) )
{
    $category->get( $parentID );
}
else
{
    $category->get();
}

// move products  up / down

if ( $category->sortMode() == "absolute_placement" )
{
    if ( is_numeric( $moveUp ) )
    {
        $category->moveUp( $moveUp );
        deleteCache( $moveUp, false, false );
    }
    if ( is_numeric( $moveDown ) )
    {
        $category->moveDown( $moveDown );
        deleteCache( $moveDown, false, false );
    }
}

// path
$pathArray = $category->path();

$t->set_var( "path_item", "" );
foreach ( $pathArray as $path )
{
    $t->set_var( "category_id", $path[0] );

    $t->set_var( "category_name", $path[1] );
    
    $t->parse( "path_item", "path_item_tpl", true );
}

$categoryList = $category->getByParent( $category );

// categories
$i = 0;
$t->set_var( "category_list", "" );
foreach ( $categoryList as $categoryItem )
{
    $t->set_var( "category_id", $categoryItem->id() );

    $t->set_var( "category_name", $categoryItem->name() );

    $parent = $categoryItem->parent();
    

    if ( ( $i % 2 ) == 0 )
    {
        $t->set_var( "td_class", "bglight" );
    }
    else
    {
        $t->set_var( "td_class", "bgdark" );
    }
    $t->set_var( "category_description", $categoryItem->description() );

    $t->parse( "category_item", "category_item_tpl", true );
    $i++;
}

if ( count( $categoryList ) > 0 )
     $t->set_var( "csv_dir", $ini->variable( "eZTradeMain", "CSVImportPath" ) );

if ( count( $categoryList ) > 0 )
    $t->parse( "category_list", "category_list_tpl" );
else
    $t->set_var( "category_list", "" );

if ( !isset( $limit ) or !is_numeric( $limit ) )
    $limit = 10;
if ( !isset( $offset ) or !is_numeric( $offset ) )
    $offset = 0;

// products
$totalTypes = $category->productCount( $category->sortMode(), true );
$productList = $category->products( $category->sortMode(), true, $offset, $limit, true, $category->id() );

$locale = new eZLocale( $language );
$i = 0;

$t->set_var( "product_list", "" );

if ( $category->sortMode() == "absolute_placement" )
{
    $t->parse( "absolute_placement_header", "absolute_placement_header_tpl" );
}
else
{
    $t->set_var( "absolute_placement_header", "" );
}

foreach ( $productList as $product )
{
    $t->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );

    $t->set_var( "product_name", $product->name() );

    $t->set_var( "product_price", "" );
    $t->set_var( "product_price_inc_vat", "" );
    if ( $product->hasPrice() )
    {
        $price = new eZCurrency( $product->price() );

        $t->set_var( "product_price", $locale->format( $price ) );
    }
    
    
    $priceArray = "";
    $options = $product->options();
    $high = 0;
    $low = 0;
    foreach ( $options as $option )
    {
        if ( get_class( $option ) == "ezoption" )
        {
            $optionValues = $option->values();
            if ( count( $optionValues ) > 1 )
            {
                $i=0;
                $priceArray = array();
                foreach ( $optionValues as $optionValue )
                {
                    $priceArray[$i] = $optionValue->price();
                    $i++;
                }
                $high += max( $priceArray );
                $low += min( $priceArray );
            }
        }
    }

    /*
    if ( count( $options ) > 0 )
    {
        $low = new eZCurrency( $low + $product->price() );
        $high = new eZCurrency( $high + $product->price() );
        if ( $low != $high )
            $t->set_var( "product_price", $locale->format( $low ) . " - " . $locale->format( $high ) );
        else
            $t->set_var( "product_price", $locale->format( $low ) );
    }
    
    $range = $product->priceRange();
    if ( $range )
    {
        $min = new eZCurrency( $range->min() );
        $max = new eZCurrency( $range->max() );
        
        $t->set_var( "product_price", $locale->format( $min ) . " - " . $locale->format( $max ) );
    }
    */
    
    if( $product->includesVAT() == true )
    {
        $t->set_var( "ex_vat_item", "" );
        $t->parse( "inc_vat_item", "inc_vat_item_tpl" );
    }
    else
    {
        $t->set_var( "inc_vat_item", "" );
        $t->parse( "ex_vat_item", "ex_vat_item_tpl" );
    }


    
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
    $t->set_var( "product_id", $product->id() );

    $t->set_var( "category_id", $category->id() );

    if ( $category->sortMode() == "absolute_placement" )
    {
        $t->parse( "absolute_placement_item", "absolute_placement_item_tpl" );
    }
    else
    {
        $t->set_var( "absolute_placement_item", "" );
    }

    $t->set_var( "product_icon", "" );
    $t->set_var( "voucher_icon", "" );

    // If product type == 1, render the product object as a product
    // If product type == 1, render the product object as a voucher
    if ( $product->productType() == 1 )
    {
        $t->set_var( "url_action", "productedit" );
        $t->parse( "product_icon", "product_icon_tpl" );
    }
    if ( $product->productType() == 2 )
    {
        $t->set_var( "url_action", "voucher" );
        $t->parse( "voucher_icon", "voucher_icon_tpl" );
    }

    $t->parse( "product_item", "product_item_tpl", true );
    $i++;
}

$t->set_var( "offset", $offset );

eZList::drawNavigator( $t, $totalTypes, $limit, $offset, "product_list_tpl" );

if ( count( $productList ) > 0 )    
    $t->parse( "product_list", "product_list_tpl" );
else
    $t->set_var( "product_list", "" );

$t->pparse( "output", "category_list_page_tpl" );

?>
