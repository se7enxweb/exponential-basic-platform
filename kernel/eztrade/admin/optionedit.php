<?php
// 
// $id: optionedit.php 8119 2001-10-31 11:19:09Z ce $
//
// Created on: <20-Sep-2000 10:18:33 bf>
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
// include_once( "classes/ezhttptool.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZTradeMain", "Language" );
$stdHeaders = $ini->variable( "eZTradeMain", "StandardOptionHeaders" );
$minHeaders = $ini->variable( "eZTradeMain", "MinimumOptionHeaders" );
$minValues = $ini->variable( "eZTradeMain", "MinimumOptionValues" );
$simpleOptionHeaders = $ini->variable( "eZTradeMain", "SimpleOptionHeaders" ) == "true" ? true : false;
$showQuantity = $ini->variable( "eZTradeMain", "ShowQuantity" ) == "true";

// include_once( "eztrade/classes/ezproductcategory.php" );
// include_once( "eztrade/classes/ezproduct.php" );
// include_once( "eztrade/classes/ezoption.php" );
// include_once( "eztrade/classes/ezoptionvalue.php" );
// include_once( "eztrade/classes/ezpricegroup.php" );

if ( !isset( $optionPrice ) or !is_array( $optionPrice ) )
    $optionPrice = array();
if ( !isset( $optionValueDescription ) or !is_array( $optionValueDescription ) )
    $optionValueDescription = array();
if ( isset( $deleteOption ) )
{
    foreach( $deleteOptionID as $option_id )
    {
        $option = new eZOption( $option_id );
        $option->delete();
    }

    $files = eZCacheFile::files( "kernel/eztrade/cache/", array( array( "productview", "productprint" ),
                                                          $productID, NULL ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

    eZHTTPTool::header( "Location: /trade/productedit/optionlist/$productID/" );
    exit();
}

$product = new eZProduct( $productID );

if ( isset( $delete ) )
{
    if ( isset( $optionDelete ) )
    {
        foreach( $optionDelete as $del )
        {
            unset( $optionValue[$del] );
            unset( $optionValueID[$del] );
            $optionValueID = array_values( $optionValueID );
            $valueID = $optionValueID;
            unset( $optionPrice[$del] );
            unset( $optionMainPrice[$del] );
            unset( $optionQuantity[$del] );
        }
    }
    if ( isset( $optionDescriptionDelete ) )
    {
        foreach( $optionDescriptionDelete as $del )
        {
            unset( $optionValueDescription[$del] );
            $count = count( $optionValue );
            for ( $i = 0; $i < $count; $i++ )
            {
                unset( $optionValue[$i][$del] );
            }
            $valueCount = max( $minHeaders, $valueCount - 1 );
        }
    }
}


if ( isset( $abort ) )
{
    eZHTTPTool::header( "Location: /trade/productedit/optionlist/$productID/" );
    exit();
}

if ( isset( $ok ) )
{
    $option = new eZOption( $optionID );
    $option->setName( $optionName );
    $option->setDescription( $description );

    $option->store();

    if ( !is_numeric( $optionID ) )
        $product->addOption( $option );

    $option->removeHeaders();
    $option->addHeader( $optionValueDescription );

    //$option->removeValues();
    $i = 0;
    $option_ids = array();

    foreach ( $optionValue as $name )
    {
        if ( $name != "" )
        {
            if ( is_numeric( $optionValueID[$i] ) and $optionValueID[$i] > 0 )
                $option_ids[] = $optionValueID[$i];
        }
        $i++;
    }

    $optionObject = new eZOptionValue();
    $orig_option_ids = $optionObject->getByOption( $option, false );
    $old_option_ids = array_diff( $orig_option_ids, $option_ids );
    foreach( $old_option_ids as $id )
    {
        $optionValueObject = new eZOptionValue();
        $optionValueObject->delete( $id );
    }

    $i = 0;
    foreach ( $optionValue as $name )
    {
        if ( $name != "" )
        {
            if ( !is_null( $optionValueID[$i] ) and $optionValueID[$i] > 0 )
                $value = new eZOptionValue( $optionValueID[$i] );
            else
                $value = new eZOptionValue();
            $value->setPrice( $optionMainPrice[$i] );
            $value->setOptionID( $option->id() );
            $value->store();

            if ( $showQuantity )
            {
                $value->setTotalQuantity( !is_null( $optionQuantity[$i] ) ? $optionQuantity[$i] : false );
            }

            $value->removeDescriptions();
            $value->addDescription( $name );
            //var_dump( $optionPrice ); die();
            $option_price = $optionPrice[$i];

            eZPriceGroup::removePrices( $productID, $option->id(), $value->id() );

            if ( count( $option_price ) > 0 )
            {
                reset( $option_price );
                foreach( $option_price as $group => $price )
                {
                    if ( is_numeric( $price ) )
                    {
                        $priceGroupObject = new eZPriceGroup();
                        $priceGroupObject->addPrice( $productID, $group, $price, $option->id(), $value->id() );
                    }
                }
            }
        }
        $i++;
    }

    $files = eZCacheFile::files( "kernel/eztrade/cache/", array( array( "productview", "productprint" ),
                                                          $productID, NULL ),
                                 "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

    eZHTTPTool::header( "Location: /trade/productedit/optionlist/$productID/" );
    exit();
}

$t = new eZTemplate( "kernel/eztrade/admin/" . $ini->variable( "eZTradeMain", "AdminTemplateDir" ),
                     "kernel/eztrade/admin/intl/", $language, "optionedit.php" );

$t->setAllStrings();

$t->set_file( "option_edit_page", "optionedit.tpl" );

$t->set_block( "option_edit_page", "value_header_item_tpl", "value_header_item" );
$t->set_block( "option_edit_page", "group_item_tpl", "group_item" );
$t->set_block( "option_edit_page", "option_quantity_header_tpl", "option_quantity_header" );

$t->set_block( "option_edit_page", "value_headers_tpl", "value_headers" );

$t->set_block( "value_headers_tpl", "value_description_item_tpl", "value_description_item" );
$t->set_block( "value_description_item_tpl", "value_description_item_checkbox_tpl", "value_description_item_checkbox" );

$t->set_block( "option_edit_page", "option_item_tpl", "option_item" );
$t->set_block( "option_item_tpl", "value_item_tpl", "value_item" );
$t->set_block( "option_item_tpl", "option_price_item_tpl", "option_price_item" );
$t->set_block( "option_item_tpl", "option_quantity_item_tpl", "option_quantity_item" );

$t->set_block( "option_edit_page", "new_description_tpl", "new_description" );

//default values
$t->set_var( "name_value", "" );
$t->set_var( "description_value", "" );
$t->set_var( "option_values", "" );
$t->set_var( "hidden_fields", "" );
$t->set_var( "action_value", "Insert" );
$t->set_var( "option_id", "" );

$t->set_var( "product_name", $product->name() );

$groups = eZPriceGroup::getAll( false );
$t->set_var( "group_item", "" );
foreach( $groups as $group )
{
    $price_group = new eZPriceGroup( $group );
    $t->set_var( "price_group_name", $price_group->name() );
    $t->parse( "group_item", "group_item_tpl", true );
}
$count = count ( $groups );

$main_price = array();

if ( isset( $action ) && $action == "New" )
{
    $optionValueDescription = $stdHeaders;
    $optionValue = array();
    $optionMainPrice = array();
    $optionPrice = array();
    $newValue = true;
}

if ( isset( $action ) && $action == "Edit" )
{
    $option = new eZOption( $optionID );
    $values = $option->values();
    $valueID = array();
    $optionValueDescription = $option->descriptionHeaders();
    $i = 0;
    foreach( $stdHeaders as $header )
    {
        if ( !isset( $optionValueDescription[$i] ) )
            $optionValueDescription[$i] = $header;
        $i++;
    }
    $hiddenArray = "";
    $valueText = "";
    $optionValue = array();
    $optionMainPrice = array();
    $optionPrice = array();
    $optionQuantity = array();
    $i = 0;
    foreach ( $values as $value )
    {
        $optionValue[$i] = $value->descriptions();
        $optionValue[$i][] = "";
        $optionMainPrice[] = $value->price();
        $optionValueID[$i] = $value->id();
        $optionQuantity[$i] = $value->totalQuantity();
        $valueid = $value->id();
        $valueID[] = $valueid;
        $prices = eZPriceGroup::prices( $productID, $optionID, $value->id() );
        foreach( $groups as $group )
        {
            foreach( $prices as $price )
            {
                if ( $price["PriceID"] == $group )
                    $optionPrice[$valueid][$group] = $price["Price"];
            }
        }
        $i++;
    }
    $valueCount = max( $minHeaders, count( $optionValueDescription ) );
    if ( $simpleOptionHeaders )
        $valueCount = $minHeaders;

    $optionName = $option->name();
    $description = $option->description();
}

if ( isset( $newValue ) )
{
    $optionValue[] = array();
    $optionValueID[] = "";
    $valueID[] = "";
    $option_price = array();
    for( $i = 0; $i < $count; ++$i )
    {
        $option_price[$groups[$i]] = "";
    }
    $optionPrice[] = $option_price;
}

while( max( count( $optionValue ), count( $valueID ), count( $optionPrice ) ) < $minValues )
{
    $optionValue[] = array();
    $optionValueID[] = "";
    $valueID[] = "";
    $option_price = array();
    for( $i = 0; $i < $count; ++$i )
    {
        $option_price[$groups[$i]] = "";
    }
    $optionPrice[] = $option_price;
}

if ( isset( $newDescription ) )
{
    for( $i = 0; $i < count( $optionValue ); $i++ )
    {
        $optionValue[$i][] = "";
    }
    $optionValueDescription[] = "";
    $valueCount = max( $minHeaders, $valueCount + 1 );
}
else
{
    $valueCount = 0;
}

$value_count = max( $minHeaders, $valueCount );

$t->set_var( "option_quantity_header", "" );
if ( $showQuantity )
    $t->parse( "option_quantity_header", "option_quantity_header_tpl" );

$t->set_var( "value_count", $value_count );

$t->set_var( "value_headers", "" );
if ( !$simpleOptionHeaders )
{
    reset( $optionValueDescription );
    foreach( $optionValueDescription as $header )
    {
        $value_header_item = $header;
    }
    // $value_header_item = each( $optionValueDescription );
    $t->parse( "value_description_item_checkbox", "value_description_item_checkbox_tpl" );
    for ( $i = 0; $i < max( $minHeaders, $value_count ); $i++ )
    {
        if ( isset( $value_header_item[1] ) )
        {
            $t->set_var( "option_description_value", $value_header_item[1] );
        }
        else
        {
            $t->set_var( "option_description_value", "" );
        }
        //$t->set_var( "option_description_value", $value_header_item[1] );
        $t->set_var( "value_description_index", $i );
        $t->parse( "value_description_item", "value_description_item_tpl", true );
        foreach( $optionValueDescription as $header )
        {
            $value_header_item = $header;
        }
    }

    $t->parse( "value_headers", "value_headers_tpl" );
}

reset( $optionPrice );
$index = 0;
$j = 0;
$t->set_var( "option_item", "" );
$t->set_var( "group_count", $count );
foreach( $optionMainPrice as $header )
{
    if( $header != "" )
        $main_price[] = $header;
}
//$main_price = each( $optionMainPrice );

foreach ( $optionValue as $value )
{
    $t->set_var( "value_pos", $index + 1 );
    $t->set_var( "value_index", $index );
    $t->set_var( "number_run", $j );
    $t->set_var( "option_value_id", $optionValueID[$index] );
    $t->set_var( "value_item", "" );
    reset( $value );
    $value_item = array();
    // $optionQuantity = array();

    foreach( $value as $header )
    {
        if( $header != "" )
            $value_item = $header;
    }

    // $value_item = each( $value );
    for( $i = 0; $i < max( $minHeaders, $value_count ); $i++ )
    {
        // $value_item = each( $value );
        if ( isset( $value_item ) && $value_item != array()  )
        {
            $t->set_var( "option_value", $value_item );
        }
        else
        {
            $t->set_var( "option_value", "" );
        }

        $t->parse( "value_item", "value_item_tpl", true );
        foreach( $value as $header )
        {
            $value_item = $header;
        }
        //$value_item = each( $value );
    }

    $t->set_var( "main_price_value", isset( $main_price[$index] ) ? $main_price[$index] : false );

    $t->set_var( "option_price_item", "" );
    foreach( $optionPrice as $header )
    {
        if( $header != "" )
        $option_price[] = $header;
    }

    // $option_price = each( $optionPrice );
    $i = 0;
    foreach( $groups as $group )
    {
        $t->set_var( "price_value", isset( $option_price[$index][$group] ) ? $option_price[$index][$group] : false ); //$option_price[1][$group] );
        $t->set_var( "price_group", $group );
        $t->set_var( "value_index", $i );
        $t->parse( "option_price_item", "option_price_item_tpl", true );
        $i++;
    }

    $t->set_var( "option_quantity_item", "" );
    if ( $showQuantity )
    {
        $t->set_var( "quantity_value", isset( $optionQuantity[$index] ) ? $optionQuantity[$index] : false ); // $optionQuantity[$index] );
        $t->parse( "option_quantity_item", "option_quantity_item_tpl" );
    }

    $t->parse( "option_item", "option_item_tpl", true );
    foreach( $optionMainPrice as $header )
    {
        $main_price[] = $header;
    }
    // $main_price = each( $optionMainPrice );
    $index++;
    $j++;
}

$t->set_var( "option_id", isset( $optionID ) ? $optionID : false );
$t->set_var( "name_value", isset( $optionName ) ? $optionName : false );
$t->set_var( "description_value", isset( $description ) ? $description : false);

$t->set_var( "new_description", "" );
if ( !$simpleOptionHeaders )
    $t->parse( "new_description", "new_description_tpl" );

$t->set_var( "product_id", $productID );

$t->pparse( "output", "option_edit_page" );

?>