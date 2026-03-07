<?php
//
// $id: categorylist.php 8565 2001-11-21 17:34:16Z br $
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
    if ( is_a( $productID, "eZProduct" ) )
    {
        $categoryID = $productID->categoryDefinition( false );
        $categoryArray = $productID->categories( false );
        $productID = $productID->id();
    }

    $files = eZCacheFile::files( "kernel/eztrade/cache/", array( "productlist",
                                                          array_merge( $categoryID, $categoryArray ) ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }
    $files = eZCacheFile::files( "kernel/eztrade/cache/", array( "hotdealslist" ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }
}


$ini = eZINI::instance( 'site.ini' );

$language = $ini->variable( "eZTradeMain", "Language" );
$limit = $ini->variable( "eZTradeMain", "AdminProductLimit" );
$hotDealImageWidth  = $ini->variable( "eZTradeMain", "HotDealImageWidth" );
$hotDealImageHeight  = $ini->variable( "eZTradeMain", "HotDealImageHeight" );
$showQuantity = $ini->variable( "eZTradeMain", "ShowQuantity" ) == "true";

// include_once( "eztrade/classes/ezproductcategory.php" );
// include_once( "eztrade/classes/ezproduct.php" );

$t = new eZTemplate( "kernel/eztrade/admin/" . $ini->variable( "eZTradeMain", "AdminTemplateDir" ),
                     "kernel/eztrade/admin/intl/", $language, "categorylist.php" );

// Set detail or normal mode
if ( isSet ( $detailView ) )
{
    $session = eZSession::globalSession();
    $session->setVariable( "TradeViewMode", "Detail" );
}
if ( isSet ( $normalView ) )
{
    $session = eZSession::globalSession();
    $session->setVariable( "TradeViewMode", "Normal" );
}

$checkMode = eZSession::globalSession();

if ( $checkMode->variable( "TradeViewMode" ) == "Detail" )
{
    $detailView = true;
}
else if ( $checkMode->variable( "TradeViewMode" ) == "Normal" )
{
    $normalView = true;
}

$t->setAllStrings();

$t->set_file( "category_list_page_tpl", "categorylist.tpl" );

// path
$t->set_block( "category_list_page_tpl", "path_item_tpl", "path_item" );

// category
$t->set_block( "category_list_page_tpl", "category_list_tpl", "category_list" );
$t->set_block( "category_list_tpl", "category_item_tpl", "category_item" );

$t->set_block( "category_list_page_tpl", "normal_view_button", "normal_button" );
$t->set_block( "category_list_page_tpl", "detail_view_button", "detail_button" );

// product
$t->set_block( "category_list_page_tpl", "product_list_tpl", "product_list" );
$t->set_block( "product_list_tpl", "normal_list_tpl", "normal_list" );
$t->set_block( "product_list_tpl", "detail_view_tpl", "detail_view" );
$t->set_block( "product_list_tpl", "update_button_tpl", "update_button" );

$t->set_block( "normal_list_tpl", "product_item_tpl", "product_item" );

$t->set_block( "product_item_tpl", "product_active_item_tpl", "product_active_item" );
$t->set_block( "product_item_tpl", "product_inactive_item_tpl", "product_inactive_item" );

$t->set_block( "product_item_tpl", "voucher_icon_tpl", "voucher_icon" );
$t->set_block( "product_item_tpl", "product_icon_tpl", "product_icon" );

$t->set_block( "product_item_tpl", "inc_vat_item_tpl", "inc_vat_item" );
$t->set_block( "product_item_tpl", "ex_vat_item_tpl", "ex_vat_item" );

//detail view templates

$t->set_block( "detail_view_tpl", "detail_voucher_icon_tpl", "detail_voucher_icon" );
$t->set_block( "detail_view_tpl", "detail_product_icon_tpl", "detail_product_icon" );

$t->set_block( "detail_view_tpl", "vat_select_tpl", "vat_select" );
$t->set_block( "detail_view_tpl", "box_select_tpl", "box_select" );
$t->set_block( "detail_view_tpl", "module_linker_button_tpl", "module_linker" );
$t->set_block( "detail_view_tpl", "shipping_select_tpl", "shipping_select" );
$t->set_block( "detail_view_tpl", "quantity_item_tpl", "quantity_item" );
$t->set_block( "quantity_item_tpl", "day_item_tpl", "day_item" );
$t->set_block( "detail_view_tpl", "section_item_tpl", "section_item" );
$t->set_block( "section_item_tpl", "link_item_tpl", "link_item" );

//$t->set_block( "product_item_tpl", "inc_vat_item_tpl", "inc_vat_item" );
//$t->set_block( "product_item_tpl", "ex_vat_item_tpl", "ex_vat_item" );
$t->set_block( "detail_view_tpl", "product_image_tpl", "product_image" );
$t->set_block( "detail_view_tpl", "no_product_image_tpl", "no_product_image" );
$t->set_block( "detail_view_tpl", "main_image_tpl", "main_image" );
$t->set_block( "detail_view_tpl", "no_main_image_tpl", "no_main_image" );
$t->set_block( "detail_view_tpl", "value_tpl", "value" );
$t->set_block( "detail_view_tpl", "multiple_value_tpl", "multiple_value" );
$t->set_block( "detail_view_tpl", "image_tpl", "image" );
// move up / down
$t->set_block( "detail_view_tpl", "detail_absolute_placement_item_tpl", "detail_absolute_placement_item" );
$t->set_block( "detail_view_tpl", "detail_inc_vat_item_tpl", "detail_inc_vat_item" );
$t->set_block( "detail_view_tpl", "detail_ex_vat_item_tpl", "detail_ex_vat_item" );

// move up / down
//$t->set_block( "product_list_tpl", "absolute_placement_header_tpl", "absolute_placement_header" );
$t->set_block( "product_item_tpl", "absolute_placement_item_tpl", "absolute_placement_item" );

$t->set_var( "site_style", $siteDesign );

if( isset( $parentID ) )
{
    $category = new eZProductCategory( $parentID );
}
else
{
    $category = new eZProductCategory( 0 );
}
// $category->copy( true );

$parentID = 0;

// $category = new eZProductCategory();

//if( isset( $parentID ) )
//{
//    $category->get( $parentID );
//}
//else
//{
//    $category->get( 1 );
//}

$category->get();
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
$totalTypes = $category->productCount( $category->sortMode(), true, false, $category->id() );
$productList = $category->products( $category->sortMode(), true, $offset, $limit, true, $category->id() );

$locale = new eZLocale( $language );
$i = 0;
$t->set_var( "product_list", "" );

/*if ( $category->sortMode() == "absolute_placement" )
{
    $t->parse( "absolute_placement_header", "absolute_placement_header_tpl" );
}
else
{
    $t->set_var( "absolute_placement_header", "" );
}*/

$categoryList = $category->getTree( );

foreach ( $productList as $product )
{
    $t->set_var( "category_array_id", $i );
    $t->set_var( "td_class", ( $i % 2 ) == 0 ? "bglight" : "bgdark" );
    $t->set_var( "product_id", $product->id() );

        // $category = new eZProductCategory();

	$t->set_var( "value", "" );
	$t->set_var( "multiple_value", "" );
	foreach ( $categoryList as $catItem )
	{
        $defCat = $product->categoryDefinition();
	    if ( $product->existsInCategory( $catItem[0] ) 
		&& ( $defCat->id() != $catItem[0]->id() ) )
        {
            $t->set_var( "multiple_selected", "selected" );
        }
        else
        {
            $t->set_var( "multiple_selected", "" );
        }

        if ( $defCat->id() == $catItem[0]->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
		
	$t->set_var( "option_value", $catItem[0]->id() );
    $t->set_var( "option_name", $catItem[0]->name() );

    if ( $catItem[1] > 0 )
        $t->set_var( "option_level", str_repeat( "&nbsp;", $catItem[1] ) );
    else
        $t->set_var( "option_level", "" );

    $t->parse( "value", "value_tpl", true );
    $t->parse( "multiple_value", "multiple_value_tpl", true );
	}

    $t->set_var( "product_image", "" );
    $t->set_var( "no_product_image", "" );
    $image = $product->thumbnailImage();

    if  ( $image )
    {
        $thumbnail = $image->requestImageVariation( $hotDealImageWidth, $hotDealImageHeight );
        
        if ( $thumbnail )
        {
                $t->set_var( "thumbnail_image_uri", "/" . $thumbnail->imagePath() );
                $t->set_var( "thumbnail_image_width", $thumbnail->width() );
                $t->set_var( "thumbnail_image_height", $thumbnail->height() );
                $t->parse( "product_image", "product_image_tpl" );            
        }
	}
    else
    {
        $t->set_var( "product_image", "" );
		$t->parse( "no_product_image", "no_product_image_tpl" );
    }

    $t->set_var( "main_image", "" );
    $t->set_var( "no_main_image", "" );
	
    $image = $product->mainImage();

    if  ( $image )
    {
        $thumbnail = $image->requestImageVariation( $hotDealImageWidth, $hotDealImageHeight );
        
        if ( $thumbnail )
        {
                $t->set_var( "main_image_uri", "/" . $thumbnail->imagePath() );
                $t->set_var( "main_image_width", $thumbnail->width() );
                $t->set_var( "main_image_height", $thumbnail->height() );
                $t->parse( "main_image", "main_image_tpl" );            
        }
	}
    else
    {
        $t->set_var( "main_image", "" );
		$t->parse( "no_main_image", "no_main_image_tpl" );
    }

    //    die($product->brief());
    // rendering contains html
    
    $t->set_var( "product_name", htmlspecialchars($product->name()) );
    $t->set_var( "product_number", $product->productNumber() );

    if ( isSet ( $detailView ) )
    {
      $t->set_var( "brief_value", $product->briefPlain() );
      $t->set_var( "description_value", $product->descriptionPlain() );
     }else{
      $t->set_var( "brief_value", $product->brief() );
      $t->set_var( "description_value", $product->description() );
     }

    $t->set_var( "is_hot_deal_checked", "" );
    if ( $product->isHotDeal() == true )
    $t->set_var( "is_hot_deal_checked", "checked" );
	
	$t->set_var( "showproduct_checked", "" );
    if ( $product->showProduct() == true )
    $t->set_var( "showproduct_checked", "checked" );

	$t->set_var( "showprice_checked", "" );
    if ( $product->showPrice() == true )
    $t->set_var( "showprice_checked", "checked" );
	
	$t->set_var( "discontinued_checked", "" );
    if ( $product->discontinued() == true )
    $t->set_var( "discontinued_checked", "checked" );
	
    $t->set_var( "include_vat", "" );
    $t->set_var( "exclude_vat", "" );
    if ( $product->includesVAT() == true )
    $t->set_var( "include_vat", "selected" );
	else
    $t->set_var( "exclude_vat", "selected" );
		
	// show the VAT values
    $t->set_var( "vat_select", "" );
	$vat = new eZVATType();
	$vatTypes = $vat->getAll();
	$vatType = $product->vatType();

	foreach ( $vatTypes as $type )
	{
    	if ( $vatType  and  ( $vatType->id() == $type->id() ) )
	    {
        $t->set_var( "vat_selected", "selected" );
    	}
    else
    	{
        $t->set_var( "vat_selected", "" );
    	}
        
    $t->set_var( "vat_id", $type->id() );
    $t->set_var( "vat_name", $type->name() . " (" . $type->value() . ")%" );

    $t->parse( "vat_select", "vat_select_tpl", true );
	}

	// show the box types
        $t->set_var( "box_select", "" );
	$box = new eZBoxType();
	$boxTypes = $box->getAll();
	$boxType = $product->boxType();
	foreach ( $boxTypes as $type )
	{
    	if ( $boxType  and  ( $boxType->id() == $type->id() ) )
	    	$t->set_var( "box_selected", "selected" );
        else
            $t->set_var( "box_selected", "" );
      	    $t->set_var( "box_id", $type->id() );
//		$t->set_var( "box_name", $type->name()." (".$type->length()."x".$type->width()."x".$type->height()." in)" );
		$t->set_var( "box_name", $type->name() );
	    $t->parse( "box_select", "box_select_tpl", true );
	}
		
	// show shipping groups

    $t->set_var( "shipping_select", "" );
	$group = new eZShippingGroup();
	$groups = $group->getAll();
	$shippingGroup = $product->shippingGroup();  

	foreach ( $groups as $group )
	{
    	if ( $shippingGroup and $shippingGroup->id() == $group->id() )
    	{
        	$t->set_var( "selected", "selected" );
	    }
    	else
	    {
    	    $t->set_var( "selected", "" );
    	}

	    $t->set_var( "shipping_group_id", $group->id() );
    	
   		$t->set_var( "shipping_group_name", $group->name() );

	    $t->parse( "shipping_select", "shipping_select_tpl", true );
	}

	$weight = $product->weight();
	if ($weight)
		$t->set_var( "weight_value", $weight );
	else
		$t->set_var( "weight_value", "" );

	// Show quantity
	$quantity = $product->totalQuantity();
	$t->set_var( "quantity_item", "" );
	$t->set_var( "quantity_value", $quantity );

	// show expected stock date

    if ( $product->stockDate() )
    {
		$stock = new eZDate();
        $stock->setTimeStamp( $product->stockDate() );	
        $stockYear = $stock->year();
        $stockMonth = $stock->month();
        $stockDay = $stock->day();
    }
    else
    {
        $stockYear = "";
        $stockMonth = "";
        $stockDay = "";
    }
	
    for ( $j = 1; $j <= 31; $j++ )
    {
        $t->set_var( "day_id", $j );
        $t->set_var( "day_value", $j );
        $t->set_var( "selected", "" );
    	if ( $stockDay == $j )
        	$t->set_var( "selected", "selected" );
    	if ( $stockDay == "" and $j == date(j) )
        	$t->set_var( "selected", "selected" );
        $t->parse( "day_item", "day_item_tpl", true );
    }

    $month_array = array( 1 => "select_january",
                          2 => "select_february",
                          3 => "select_march",
                          4 => "select_april",
                          5 => "select_may",
                          6 => "select_june",
                          7 => "select_july",
                          8 => "select_august",
                          9 => "select_september",
                          10 => "select_october",
                          11 => "select_november",
                          12 => "select_december" );

	foreach ( $month_array as $month )
    	$t->set_var( $month, "" );
    $var_name =& $month_array[$stockMonth];
    if ( $var_name == "" )
	    $var_name =& $month_array[date(n)];
    $t->set_var( $var_name, "selected" );
	if ( $stockYear )
		$t->set_var( "stockyear", $stockYear );
	else
		$t->set_var( "stockyear", date(Y) );

if ( $showQuantity )
	$t->parse( "quantity_item", "quantity_item_tpl" );

    $t->set_var( "product_price", "" );
    $t->set_var( "product_price_inc_vat", "" );
    if ( $product->hasPrice() )
    {
//        $price = new eZCurrency( $product->price() );

//        $t->set_var( "product_price", $locale->format( $price ) );

		$t->set_var( "product_price", $product->localePrice( $pricesIncludeVAT ) );

    }


    $t->set_var( "external_link", $product->externalLink() );
    $t->set_var( "keywords_value", $product->keywords() );

    $priceArray = "";
    $options = $product->options();
    $high = 0;
    $low = 0;
    foreach ( $options as $option )
    {
        if ( is_a( $option, "eZOption" ) )
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
/*    if ( count( $options ) > 0 )
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
    // $t->set_var( "product_id", $product->id() );

    $t->set_var( "category_id", $category->id() );

if ( isSet ( $detailView ) )
   {
    if ( $category->sortMode() == "absolute_placement" )
    {
        $t->parse( "detail_absolute_placement_item", "detail_absolute_placement_item_tpl" );
    }
    else
    {
        $t->set_var( "detail_absolute_placement_item", "" );
    }
   }
   else
   {
    if ( $category->sortMode() == "absolute_placement" )
    {
        $t->parse( "absolute_placement_item", "absolute_placement_item_tpl" );
    }
    else
    {
        $t->set_var( "absolute_placement_item", "" );
    }
    }

    $t->set_var( "product_icon", "" );
    $t->set_var( "voucher_icon", "" );
    $t->set_var( "detail_product_icon", "" );
    $t->set_var( "detail_voucher_icon", "" );

    // If product type == 1, render the product object as a product
    // If product type == 1, render the product object as a voucher
	if ( isSet ( $detailView ) )
  	{
    	if ( $product->productType() == 1 )
    	{
        	$t->set_var( "url_action", "productedit" );
        	$t->parse( "detail_product_icon", "detail_product_icon_tpl" );
    	}
    	if ( $product->productType() == 2 )
    	{
        	$t->set_var( "url_action", "voucher" );
        	$t->parse( "detail_voucher_icon", "detail_voucher_icon_tpl" );
    	}
  	}
  else
  {
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
   }
// link list
$module_link = new eZModuleLink( "eZTrade", "Product", $product->id() );
$sections = $module_link->sections();
$t->set_var( "section_item", "" );
foreach ( $sections as $section )
{
    $t->set_var( "link_item", "" );
    $t->set_var( "section_name", $section->name() );
    $t->set_var( "section_id", $section->id() );
    $links = $section->links();
    foreach ( $links as $link )
    {
        $t->set_var( "link_name", $link->name() );
        $t->set_var( "link_url", $link->url() );
        $t->set_var( "link_id", $link->id() );
        $t->parse( "link_item", "link_item_tpl", true );
    }
    $t->parse( "section_item", "section_item_tpl", true );
}
	

// get additional images
$t->set_var( "image", "" );
$images = $product->images();

$t->set_var( "image", "" );
$t->set_var( "image_list", "" );
$image_count = 0;

foreach ( $images as $imageArray )
{
    $image = $imageArray["Image"];

      //  $t->set_var( "image_name", $image->name() );
        $t->set_var( "image_caption", eZTextTool::nl2br( $image->caption() ) );
      //  $t->set_var( "image_id", $image->id() );

        $variation = $image->requestImageVariation( $hotDealImageWidth, $hotDealImageHeight );

        $t->set_var( "image_url", "/" .$variation->imagePath() );
        $t->set_var( "image_width", $variation->width() );
        $t->set_var( "image_height", $variation->height() );
        $t->parse( "image", "image_tpl", true );
}

    // Set the detail or normail view
    if ( isSet ( $detailView ) )
    {
        $t->set_var( "product_item", "" );
        $t->parse( "detail_view", "detail_view_tpl", true );
    }
    else
    {
        $t->set_var( "detail_view", "" );
    $t->parse( "product_item", "product_item_tpl", true );
    }

    $i++;
}

if ( isSet ( $detailView ) )
{
    $t->set_var( "is_detail_view", "true" );
    $t->set_var( "detail_button", "" );
    $t->set_var( "normal_list", "" );
    $t->parse( "update_button", "update_button_tpl" );
    $t->parse( "normal_button", "normal_view_button" );
}
else
{
    $t->set_var( "is_detail_view", "" );
    $t->set_var( "normal_button", "" );
    $t->set_var( "update_button", "" );
    $t->parse( "detail_button", "detail_view_button" );
}

$t->set_var( "offset", $offset );

/*
echo "TotalTypes:".$totalTypes."<br>";
echo "Limit:".$limit."<br>";
echo "Offset:".$offset."<br>";
exit();
*/

eZList::drawNavigator( $t, $totalTypes, $limit, $offset, "product_list_tpl" );
/*
if ( $user  )
{
	$session->setVariable( "RedirectURL", "/trade/categorylist/parent/$parentID/$offset" );
}
*/

if (( count( $productList ) > 0 ) and ( !isSet ($detailView) ))
	{
	$t->parse( "product_list", "product_list_tpl" );
	$t->parse( "normal_list", "normal_list_tpl" );
	}

if ( count( $productList ) > 0 )
    $t->parse( "product_list", "product_list_tpl" );
else
	{
    $t->set_var( "product_list", "" );
    $t->set_var( "normal_list", "" );
	}

$t->pparse( "output", "category_list_page_tpl" );

?>