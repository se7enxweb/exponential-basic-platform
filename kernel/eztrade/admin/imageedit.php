<?php
// 
// $id: imageedit.php 6233 2001-07-20 11:42:02Z jakobn $
//
// Created on: <21-Sep-2000 10:32:36 bf>
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
// include_once( "classes/ezlog.php" );

// include_once( "classes/ezimagefile.php" );

// include_once( "ezimagecatalogue/classes/ezimage.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZTradeMain", "Language" );

// include_once( "eztrade/classes/ezproductcategory.php" );
// include_once( "eztrade/classes/ezproduct.php" );

if ( $action == "Insert" )
{
    $file = new eZPBImageFile();

    if ( $file->getUploadedFile( "userfile" ) )
    { 
        $product = new eZProduct( $productID );
        $image = new eZImage();
        $image->setName( $name );
        $image->setCaption( $caption );

        $image->setImage( $file );
        
        $image->store();
        
        $product->addImage( $image );

        if ( count( $product->images() ) == 1 )
        {
            $product->setThumbnailImage( $image );
            $product->setMainImage( $image );
        }
		
		$objectPermission = new eZObjectPermission();
		eZObjectPermission::setPermission( -1, $image->id(), "imagecatalogue_image", "r" );
		eZObjectPermission::setPermission( 1, $image->id(), "imagecatalogue_image", "w" );

        eZPBLog::writeNotice( "Picture added to product: $productID  from IP: $REMOTE_ADDR" );
    }
    else
    {
        print( $file->name() . " not uploaded successfully" );
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /trade/productedit/imagelist/" . $productID . "/" );
    exit();
}

if ( $action == "Update" )
{
    $file = new eZPBImageFile();
    
    if ( $file->getUploadedFile( "userfile" ) )
    {
        $product = new eZProduct( $productID );

        $oldImage = new eZImage( $imageID );
        $product->deleteImage( $oldImage );
        
        $image = new eZImage();
        $image->setName( $name );
        $image->setCaption( $caption );

        $image->setImage( $file );
        
        $image->store();
        
        $product->addImage( $image );
    }
    else
    {
        $image = new eZImage( $imageID );
        $image->setName( $name );
        $image->setCaption( $caption );
        $image->store();
    }
    
    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /trade/productedit/imagelist/" . $productID . "/" );
    exit();
}


if ( $action == "Delete" )
{
    $product = new eZProduct( $productID );

    if ( count ( $imageArrayID ) != 0 )
    {
        foreach( $imageArrayID as $imageID )
        {
            $image = new eZImage( $imageID );
            $product->deleteImage( $image );
        }
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /trade/productedit/imagelist/" . $productID . "/" );
    exit();    
}

// update captions

if ( $action == "UpdateImages" )

	{
         for ( $i = 0; $i < count( $imageUpdateArrayID ); $i++ )
        	{
            	if ( $newCaption[$i] != $oldCaption[$i] )
			{
			$image = new eZImage($imageUpdateArrayID[$i]);
			$image->setCaption( $newCaption[$i] );
			$image->store();
			}
	      	  }

	include_once( "classes/ezhttptool.php" );
	eZHTTPTool::header( "Location: /trade/productedit/imagelist/" . $productID . "/" );
	exit();    
		}



// store the image definition
if ( $action == "StoreDef" )
{
    $product = new eZProduct( $productID );

	for ( $i = 0; $i < count( $imageUpdateArrayID ); $i++ )
        	{
            	if ( $newCaption[$i] != $oldCaption[$i] )
			{
			$image = new eZImage($imageUpdateArrayID[$i]);
			$image->setCaption( $newCaption[$i] );
			$image->store();
			}
		}

    // Unset main page image radiobutton
    if ( isset( $noMainImage ) )
    {
        $product->setMainImage( false );
        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /trade/productedit/imagelist/" . $productID . "/" );
        exit();
    }

    // Unset mini page image radiobutton
    if ( isset( $noMiniImage ) )
    {
        $product->setThumbnailImage( false );
        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /trade/productedit/imagelist/" . $productID . "/" );
        exit();
    }

    if ( isset( $thumbnailImageID ) &&  ( $thumbnailImageID != 0 ) &&  ( $thumbnailImageID != "" ) )
    {
        $thumbnail = new eZImage( $thumbnailImageID );
        $product->setThumbnailImage( $thumbnail );
    }

    if ( isset( $mainImageID ) &&  ( $mainImageID != 0 ) &&  ( $mainImageID != "" ) )
    {
        $main = new eZImage( $mainImageID );
        $product->setMainImage( $main );
    }
    
    if ( isset( $newImage ) )
    {
        print( "new image" );
        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /trade/productedit/imageedit/new/$productID/" );
        exit();
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /trade/productedit/edit/" . $productID . "/" );
    exit();
}

$t = new eZTemplate( "kernel/eztrade/admin/" . $ini->variable( "eZTradeMain", "AdminTemplateDir" ),
                     "kernel/eztrade/admin/intl/", $language, "imageedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "image_edit_page" => "imageedit.tpl",
    ) );


$t->set_block( "image_edit_page", "image_tpl", "image" );

//default values
$t->set_var( "name_value", "" );
$t->set_var( "caption_value", "" );
$t->set_var( "action_value", "Insert" );
$t->set_var( "option_id", "" );
$t->set_var( "image", "" );

if ( $action == "Edit" )
{
    $product = new eZProduct( $productID );
    $image = new eZImage( $imageID );

    $t->set_var( "image_id", $image->id() );
    $t->set_var( "name_value", $image->name() );
    $t->set_var( "caption_value", $image->caption() );
    $t->set_var( "action_value", "Update" );


    $t->set_var( "image_alt", $image->caption() );

    $variation = $image->requestImageVariation( 150, 150 );
    
    $t->set_var( "image_src", "/" .$variation->imagePath() );
    $t->set_var( "image_width", $variation->width() );
    $t->set_var( "image_height", $variation->height() );
    $t->set_var( "image_file_name", $image->originalFileName() );
    $t->parse( "image", "image_tpl" );
}

$product = new eZProduct( $productID );
    
$t->set_var( "product_name", $product->name() );
$t->set_var( "product_id", $product->id() );

$t->pparse( "output", "image_edit_page" );

?>
