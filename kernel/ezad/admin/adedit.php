<?php
// 
// $Id: adedit.php 9307 2002-02-27 16:53:26Z master $
//
// Created on: <16-Nov-2000 13:02:32 bf>
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
// include_once( "classes/ezimagefile.php" );
// include_once( "classes/ezlog.php" );
// include_once( "classes/ezhttptool.php" );

// include_once( "classes/ezdatetime.php" );

// include_once( "ezad/classes/ezad.php" );
// include_once( "ezad/classes/ezadcategory.php" );

if ( isset ( $deleteAds ) )
{
    $action = "DeleteAds";
}

if ( isset ( $preview ) )
{
    if ( is_numeric ( $adID ) && ( $adID != 0 ) )
    {
        $action = "Update";
    }
    else
    {
        $action = "Insert";
    }
}

if( isset( $adURL ) )
    $adUrl = trim( $adURL );

// Get images from the image browse function.
if ( ( isset ( $addImages ) ) and ( is_numeric( $adID ) ) and ( is_numeric ( $adID ) ) )
{
    $image = new eZImage( $ImageID );
    $ad = new eZAd( $adID );
    $ad->setImage( $image );
    $ad->store();
    $action = "Edit";
}

if ( $action == "Insert" )
{
    $category = new eZAdCategory( $categoryID );
    
    $ad = new eZAd( );

    $ad->setName( $adTitle );
    $ad->setDescription( $adDescription );
    
    if ( $isActive == "on" )
    {
        $ad->setIsActive( true );
    }
    else
    {
        $ad->setIsActive( false );
    }
    
    if ( $useHTML == "on" )
    {
        $ad->setUseHTML( true );
    }
    else
    {
        $ad->setUseHTML( false );
    }

    $ad->setHTMLBanner( $htmlBanner );    
    
// Why we cant make internal banners without http:// ? -- EP ---
//    if ( !preg_match( "/^([a-z]+:\/\/)/", $adUrl ) )
//    {
//        if( !preg_match( "/^(ftp\.)/", $adUrl ) )
//            $real_url = "http://" . $adUrl;
//        else
//            $real_url = "ftp://" . $adUrl;
//    }
//    else
//    {
//        $real_url = $adUrl;
//    }
    $real_url = $adUrl;
    
    $ad->setURL( $real_url );
    
    $ad->setClickPrice( $clickPrice );
    $ad->setViewPrice( $viewPrice );

    $file = new eZPBImageFile();

    if ( $file->getUploadedFile( "AdImage" ) )
    { 
        $image = new eZImage();
        $image->setName( $name );
        $image->setCaption( $caption );

        $image->setImage( $file );
        
        $image->store();
        
        $ad->setImage( $image );

        eZPBLog::writeNotice( "Picture added to ad: $adID  from IP: $REMOTE_ADDR" );
    }

//      $dateTime = new eZDateTime( 2000, 11, 13, 14, 0, 15 );
//      $ad->setOriginalPublishingDate( $dateTime );

    $ad->store();

    $category->addAd( $ad );

    if ( isset ( $browse ) )
    {
        $adID = $ad->id();
        
        $session = eZSession::globalSession();
        $session->setVariable( "SelectImages", "single" );
        $session->setVariable( "ImageListReturnTo", "/ad/ad/edit/$adID/" );
        $session->setVariable( "NameInBrowse", $ad->name() );
        eZHTTPTool::header( "Location: /imagecatalogue/browse/" );
        exit();
    }
    if ( isset( $preview ) )
    {
        $action = "Edit";
        $adID = $ad->id();
    }
    else
    {        
        eZHTTPTool::header( "Location: /ad/archive/$categoryID/" );
        exit();
    }
    
}

if ( $action == "Update" )
{
    $category = new eZAdCategory( $categoryID );
    
    $ad = new eZAd( $adID );

    $ad->setName( $adTitle );
    $ad->setDescription( $adDescription );


  
    if ( $isActive == "on" )
    {
        $ad->setIsActive( true );
    }
    else
    {
        $ad->setIsActive( false );
    }

    if ( $useHTML == "on" )
    {
        $ad->setUseHTML( true );
    }
    else
    {
        $ad->setUseHTML( false );
    }

    $ad->setHTMLBanner( $htmlBanner );    
    
//    if ( !preg_match( "/^([a-z]+:\/\/)/", $adUrl ) )
//    {
//        if( !preg_match( "/^(ftp\.)/", $adUrl ) )
//            $real_url = "http://" . $adUrl;
//        else
//            $real_url = "ftp://" . $adUrl;
//    }
//    else
//    {
//        $real_url = $adUrl;
//    }

    $real_url = $adUrl;

    $ad->setURL( $real_url );

    $ad->setClickPrice( $clickPrice );
    $ad->setViewPrice( $viewPrice );
    
//      $dateTime = new eZDateTime( 2000, 11, 13, 14, 0, 15 );
//      $ad->setOriginalPublishingDate( $dateTime );

    $file = new eZPBImageFile();

    if ( $file->getUploadedFile( "AdImage" ) )
    { 
        $image = new eZImage();
        $image->setName( $name );
        $image->setCaption( $caption );

        $image->setImage( $file );

        $image->store();

        $ad->setImage( $image );

        eZPBLog::writeNotice( "Picture added to ad: $adID  from IP: $REMOTE_ADDR" );
    }

    $ad->store();

    $ad->removeFromCategories();
    $category->addAd( $ad );

    if ( isset ( $browse ) )
    {
        $adID = $ad->id();
        
        $session = eZSession::globalSession();
        $session->setVariable( "SelectImages", "single" );
        $session->setVariable( "ImageListReturnTo", "/ad/ad/edit/$adID/" );
        $session->setVariable( "NameInBrowse", $ad->name() );
        eZHTTPTool::header( "Location: /imagecatalogue/browse/" );
        exit();
    }
    if ( isset( $preview ) )
    {
        $action = "Edit";        
    }
    else
    {        
        eZHTTPTool::header( "Location: /ad/archive/$categoryID/" );
        exit();
    }
}

if ( $action == "Delete" )
{
    $ad = new eZAd( $adID );
    $ad->delete();

    eZHTTPTool::header( "Location: /ad/archive/$categoryID/" );
    exit();    
}

if ( $action == "DeleteAds" )
{
    if ( count ( $adArrayID ) != 0 )
    {
        foreach( $adArrayID as $adID )
        {
            $ad = new eZAd( $adID );
            $cat = $ad->categories();
            $categoryID = $cat[0]->id();
            $ad->delete();
        }
    }

    eZHTTPTool::header( "Location: /ad/archive/$categoryID/" );
    exit();
}


$ini = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZAdMain", "Language" );
$ImageDir = $ini->variable( "eZAdMain", "ImageDir" );

$t = new eZTemplate( "kernel/ezad/admin/" . $ini->variable( "eZAdMain", "AdminTemplateDir" ),
                     "kernel/ezad/admin/intl/", $Language, "adedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "ad_edit_page_tpl" => "adedit.tpl"
    ) );

$t->set_block( "ad_edit_page_tpl", "value_tpl", "value" );
$t->set_block( "ad_edit_page_tpl", "image_tpl", "image" );


$t->set_var( "action_value", "insert" );

$t->set_var( "ad_title_value", "" );
$t->set_var( "ad_date_value", "" );
$t->set_var( "ad_description_value", "" );
$t->set_var( "ad_url_value", "" );
$t->set_var( "ad_click_price_value", "" );
$t->set_var( "ad_view_price_value", "" );
$t->set_var( "ad_id", "" );
$t->set_var( "image", "" );
$t->set_var( "html_banner", "" );
$t->set_var( "use_html", "" );

if ( $action == "Edit" )
{
    $ad = new eZAd( $adID );

    $t->set_var( "ad_title_value", $ad->name() );
    $t->set_var( "ad_description_value", $ad->description() );
    $t->set_var( "ad_url_value", $ad->url() );
    $t->set_var( "ad_id", $ad->id() );
    $t->set_var( "action_value", "update" );

    $t->set_var( "ad_click_price_value", $ad->clickPrice() );
    $t->set_var( "ad_view_price_value", $ad->viewPrice() );

    $t->set_var( "html_banner", $ad->htmlBanner() );

    
    if ( $ad->isActive() == true )
    {
        $t->set_var( "ad_is_active", "checked" );
    }
    else
    {
        $t->set_var( "ad_is_active", "" );
    }

    if ( $ad->useHTML() == true )
    {
        $t->set_var( "use_html", "checked" );
    }
    else
    {
        $t->set_var( "use_html", "" );
    }
    
    $image = $ad->image();

    
    if ( $image )
    {
        $t->set_var( "image_src",  $image->filePath() );
        $t->set_var( "image_width", $image->width() );
        $t->set_var( "image_height", $image->height() );
        $t->set_var( "image_file_name", $image->originalFileName() );
        $t->parse( "image", "image_tpl" );
    }
    else
    {
        $t->set_var( "image", "" );
    }
        
    
    
    $cats = $ad->categories();

    $defCat = $cats[0];
}


// category select
$category = new eZAdCategory();
$categoryArray = $category->getTree();

foreach ( $categoryArray as $catItem )
{
    if ( $action == "Edit" )
    {
        if ( $defCat->id() == $catItem[0]->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }
    else
    {
        $t->set_var( "selected", "" );
    }    

    if ( $catItem[1] > 0 )
        $t->set_var( "option_level", str_repeat( "&nbsp;", $catItem[1] ) );
    else
        $t->set_var( "option_level", "" );
    
    $t->set_var( "option_value", $catItem[0]->id() );
    $t->set_var( "option_name", $catItem[0]->name() );

    $t->parse( "value", "value_tpl", true );    
}


$t->pparse( "output", "ad_edit_page_tpl" );

?>