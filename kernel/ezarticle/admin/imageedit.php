<?php
// 
// $id: imageedit.php 7089 2001-09-07 17:33:55Z fh $
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
$language = $ini->variable( "eZArticleMain", "Language" );

// include_once( "ezarticle/classes/ezarticlecategory.php" );
// include_once( "ezarticle/classes/ezarticle.php" );
// include_once( "ezuser/classes/ezauthor.php" );

if ( $action == "Insert" )
{
    $file = new eZPBImageFile();

    if ( $file->getUploadedFile( "userfile" ) )
    { 
        $article = new eZArticle( $articleID );
        $image = new eZImage();
        $image->setName( $name );
        $image->setCaption( $caption );

        if ( trim( $newPhotographerName ) != "" &&
             trim( $newPhotographerEmail ) != ""
             )
        {
            $author = new eZAuthor( );
            $author->setName( $newPhotographerName );
            $author->setEmail( $newPhotographerEmail );
            $author->store();
            $image->setPhotographer( $author );
        }
        else
        {
            $image->setPhotographer( $photoID );
        }
        
        
        if( $image->checkImage( $file ) && $image->setImage( $file ) )
        {
            $image->store();
            $article->addImage( $image );
            if ( count( $article->images() ) == 1 )
            {
                $article->setThumbnailImage( $image );
            }
				
		    $objectPermission = new eZObjectPermission();
						
			eZObjectPermission::setPermission( -1, $image->id(), "imagecatalogue_image", "r" );
			eZObjectPermission::setPermission( 1, $image->id(), "imagecatalogue_image", "w" );
		
            eZPBLog::writeNotice( "Picture added to article: $articleID  from IP: {$_SERVER['REMOTE_ADDR']}" );
        }
    }
    else
    {
        print( $file->name() . " not uploaded successfully" );
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /article/articleedit/imagelist/" . $articleID . "/" );
    exit();
}

if ( $action == "Update" )
{
    $file = new eZPBImageFile();
    $image = new eZImage( $imageID );
    
    if ( trim( $newPhotographerName ) != "" &&
         trim( $newPhotographerEmail ) != ""
         )
    {
        $author = new eZAuthor( );
        $author->setName( $newPhotographerName );
        $author->setEmail( $newPhotographerEmail );
        $author->store();
        $image->setPhotographer( $author );
    }
    else
    {
        $image->setPhotographer( $photoID );
    }
        
    if ( $file->getUploadedFile( "userfile" ) )
    {
        $article = new eZArticle( $articleID );


        $variations = $image->variations();

        if( $image->checkImage( $file ) && $image->setImage( $file ) )
        {
            if ( count ( $variations ) > 0 )
            {
                foreach( $variations as $variation )
                    $variation->delete();
            }
//            $oldImage = new eZImage( $imageID );
//            $article->deleteImage( $oldImage );

            $image->setName( $name );
            $image->setCaption( $caption );

            $image->store();
        
//            $article->addImage( $image );
        }
    }
    else
    {
//        $image = new eZImage( $imageID );
        $image->setName( $name );
        $image->setCaption( $caption );
        $image->store();
    }
    
    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /article/articleedit/imagelist/" . $articleID . "/" );
    exit();
}


if ( $action == "Delete" )
{
    $article = new eZArticle( $articleID );

    if ( count ( $imageArrayID ) != 0 )
    {
        foreach( $imageArrayID as $imageID )
        {
            $image = new eZImage( $imageID );
            $article->deleteImage( $image );
        }
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /article/articleedit/imagelist/" . $articleID . "/" );
    exit();
}

// store the image definition
if ( $action == "StoreDef" )
{
    $article = new eZArticle( $articleID );

    // Unset frontpage image radiobutton
    if ( isset( $noFrontImage ) )
    {
        $article->setThumbnailImage( false );
        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /article/articleedit/imagelist/" . $articleID . "/" );
        exit();
    }

    if ( isset( $thumbnailImageID ) && ( $thumbnailImageID != 0 ) && ( $thumbnailImageID != "" ) )
    {
        $thumbnail = new eZImage( $thumbnailImageID );
        $article->setThumbnailImage( $thumbnail );
    }

    if ( isset( $newImage ) )
    {
        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /article/articleedit/imageedit/new/$articleID/" );
        exit();
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /article/articleedit/edit/" . $articleID . "/" );
    exit();
}

$t = new eZTemplate( "kernel/ezarticle/admin/" . $ini->variable( "eZArticleMain", "AdminTemplateDir" ),
                     "kernel/ezarticle/admin/intl/", $language, "imageedit.php" );

$t->setAllStrings();

$t->set_file( array(
    "image_edit_page" => "imageedit.tpl",
    ) );


$t->set_block( "image_edit_page", "image_tpl", "image" );
$t->set_block( "image_edit_page", "photographer_item_tpl", "photographer_item" );

//default values
$t->set_var( "name_value", "" );
$t->set_var( "caption_value", "" );
$t->set_var( "action_value", "Insert" );
$t->set_var( "option_id", "" );
$t->set_var( "image", "" );

if ( $action == "Edit" )
{
    $article = new eZArticle( $articleID );
    $image = new eZImage( $imageID );
    $photographer = $image->photographer();
    $photographerID = $photographer->id();

    
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


$author = new eZAuthor();
$authorArray = $author->getAll();
foreach ( $authorArray as $author )
{
    if ( $photographerID == $author->id() )
    {
        $t->set_var( "selected", "selected" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }

    $t->set_var( "photo_id", $author->id() );
    $t->set_var( "photo_name", $author->name() );
    $t->parse( "photographer_item", "photographer_item_tpl", true );
}

$article = new eZArticle( $articleID );
    
$t->set_var( "article_name", $article->name() );
$t->set_var( "article_id", $article->id() );

$t->pparse( "output", "image_edit_page" );

?>