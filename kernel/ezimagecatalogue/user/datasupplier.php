<?php
//
// $id: datasupplier.php 9623 2002-06-11 08:20:30Z jhe $
//
// Created on: <23-Oct-2000 17:53:46 bf>
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

// include_once( "ezuser/classes/ezpermission.php" );
// include_once( "ezuser/classes/ezobjectpermission.php" );
// include_once( "classes/ezhttptool.php" );
// include_once( "ezuser/classes/ezuser.php" );
// include_once( "ezimagecatalogue/classes/ezimagecategory.php" );
// include_once( "ezimagecatalogue/classes/ezimage.php" );

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZImageCatalogueMain", "DefaultSection" );
$userComments = $ini->variable( "eZImageCatalogueMain", "UserComments" );

// HTTP input variables (replaces register_globals extraction)
$action             = eZHTTPTool::getVar( 'Action' );
$cancel             = eZHTTPTool::getVar( 'Cancel' );
$caption            = eZHTTPTool::getVar( 'Caption' );
$description        = eZHTTPTool::getVar( 'Description' );
$name               = eZHTTPTool::getVar( 'Name' );
$parentID           = eZHTTPTool::getVar( 'ParentID' );
$categoryID         = eZHTTPTool::getVar( 'CategoryID' );
$currentCategoryID  = eZHTTPTool::getVar( 'CurrentCategoryID' );
$sectionID          = eZHTTPTool::getVar( 'SectionID' );
$searchText         = eZHTTPTool::getVar( 'SearchText' );
$newCategory        = eZHTTPTool::getVar( 'NewCategory' );
$newPhotographerName  = eZHTTPTool::getVar( 'NewPhotographerName' );
$newPhotographerEmail = eZHTTPTool::getVar( 'NewPhotographerEmail' );
$photographerID     = eZHTTPTool::getVar( 'PhotographerID' );
$photoID            = eZHTTPTool::getVar( 'PhotoID' );
$folderID           = eZHTTPTool::getVar( 'FolderID' );
$refererURL         = eZHTTPTool::getVar( 'RefererURL' );
$detailView         = eZHTTPTool::getVar( 'DetailView' );
$normalView         = eZHTTPTool::getVar( 'NormalView' );
$showOriginal       = eZHTTPTool::getVar( 'ShowOriginal' );
$deleteCategories   = eZHTTPTool::getVar( 'DeleteCategories' );
$deleteImages       = eZHTTPTool::getVar( 'DeleteImages' );
// Array POST vars
$categoryArrayID    = $_POST['CategoryArrayID']    ?? [];
$categoryArray      = $_POST['CategoryArray']      ?? [];
$imageArrayID       = $_POST['ImageArrayID']       ?? [];
$readGroupArrayID   = $_POST['ReadGroupArrayID']   ?? [];
$writeGroupArrayID  = $_POST['WriteGroupArrayID']  ?? [];
$uploadGroupArrayID = $_POST['UploadGroupArrayID'] ?? [];

function writeAtAll()
{
    $user = eZUser::currentUser();
    if( eZObjectPermission::getObjects( "imagecatalogue_category", 'w', true ) < 1
        && eZObjectPermission::getObjects( "imagecatalogue_category", 'u', true ) < 1
        && !eZPermission::checkPermission( $user, "eZImageCatalogue", "WriteToRoot" ) )
    {
        $text = "You do not have write permission to any categories";
        $info = urlencode( $text );
        eZHTTPTool::header( "Location: /error/403?Info=$info" );
        exit();
    }
    return true;
}

$user = eZUser::currentUser();
switch ( $url_array[2] )
{
    case "customimage" :
    {
        $imageID = $url_array[3];
        $imageWidth = $url_array[4];
        $imageHeight = $url_array[5];
        include( "kernel/ezimagecatalogue/user/customimage.php" );
    }
    break;

    case "imageview" :
    {
        $imageID = $url_array[3];
        $variationID = $url_array[4];
        include( "kernel/ezimagecatalogue/user/imageview.php" );

        if  ( isset( $PrintableVersion ) && ( $PrintableVersion != "enabled" ) &&  ( $userComments == "enabled" ) )
        {
            $redirectURL = "/imagecatalogue/imageview/$imageID/";
            $image = new eZImage ( $imageID );
            if ( ( $image->id() >= 1 ) )    //  && $product->discuss() )
            {
                for ( $i = 0; $i < count( $url_array ); $i++ )
                {
                    if ( ( $url_array[$i] ) == "parent" )
                    {
                        $next = $i + 1;
                        $offset = $url_array[$next];
                    }
                }
                $forum = $image->forum();
                $forumID = $forum->id();
                // Compat alias for un-refactored ezforum module
                $ForumID = $forumID; $Offset = $offset;
                include( "kernel/ezforum/user/messagesimplelist.php" );
            }
        }
    }
    break;

    case "search" :
    {
        $categoryID = $url_array[3];

        if ( !is_numeric( $categoryID ) )
            $categoryID = 0;

        if( isset( $url_array[4] ) )    
        {
            $offset = $url_array[4];
        }
        else
        {
            $offset = 0;
        }   

        include( "kernel/ezimagecatalogue/user/imagelist.php" );
        
    }
    break;


    case "image" :
    {
        switch ( $url_array[3] )
        {
            case "list" :
            {
                if ( isset( $url_array[4] ) )
                {
                    $categoryID = $url_array[4];
                }
                else
                {
                    $categoryID = 0;
                }
                
                if ( !is_numeric($categoryID ) )
                {
                    $categoryID = 0;
                }
                
                if( isset( $url_array[6] ) )    
                {
                    $offset = $url_array[6];
                }
                else
                {
                    $offset = 0;
                }   

                if ( $offset == "" && is_Numeric( $url_array[4] ) && is_Numeric( $url_array[5] ) )
                {
                    $offset = $url_array[5];
                }
                else if ( $offset == "" )
                {
                    $offset = 0;
                }
                include( "kernel/ezimagecatalogue/user/imagelist.php" );
            }
            break;

            case "new" :
            {
                writeAtAll();
                $action = "New";
                include( "kernel/ezimagecatalogue/user/imageedit.php" );
            }
            break;
            
            case "Insert" :
            {
                writeAtAll();
                $action = "Insert";
                include( "kernel/ezimagecatalogue/user/imageedit.php" );
            }
            break;

            case "edit" :
            {
                $imageID = $url_array[4];
                $action = "Edit";
                if( ( eZImage::isOwner( $user, $imageID ) ||
                     eZObjectPermission::hasPermission( $imageID, "imagecatalogue_image", 'w' ) )
                    && writeAtAll() )
                {
                    include( "kernel/ezimagecatalogue/user/imageedit.php" );
                }
                else
                {
                    eZHTTPTool::header( "Location: /error/403" );
                    exit();
                }
            }
            break;

            case "update" :
            {
                $imageID = $url_array[4];
                $action = "Update";
                if( ( eZImage::isOwner( $user, $imageID ) ||
                     eZObjectPermission::hasPermission( $imageID, "imagecatalogue_image", 'w' ) )
                    && writeAtAll() )
                    include( "kernel/ezimagecatalogue/user/imageedit.php" );
                else
                {
                    eZHTTPTool::header( "Location: /error/403" );
                    exit();
                }
            }
            break;
            default :
            {
                eZHTTPTool::header( "Location: /error/404" );
                exit();
            }
        }
    }
    break;

    case "download" :
    {
        $imageID = $url_array[3];
        if ( !is_numeric( $imageID ) )
            $imageID = 0;
        if ( ( eZImage::isOwner( $user, $imageID ) ||
              eZObjectPermission::hasPermission( $imageID, "imagecatalogue_image", 'r' ) ) )
        {
            while ( ob_get_level() > 0 )
                ob_end_clean();
            include( "kernel/ezimagecatalogue/user/filedownload.php" );
        }
        else
        {
            eZHTTPTool::header( "Location: /error/404" );
            exit();
        }
    }
    break;

    case "slideshow" :
    {
        $categoryID = $url_array[3];
        if ( !is_numeric( $categoryID ) )
            $categoryID = 0;
        $position = $url_array[4];
        if ( !is_numeric( $position ) )
            $position = 0;
        $refreshTimer = $url_array[5];
        include( "kernel/ezimagecatalogue/user/slideshow.php" );
    }
    break;
    
    case "category" :
    {
        switch( $url_array[3] )
        {
           
            case "new" :
            {
                writeAtAll();
                $currentCategoryID = $url_array[4];
                $action = "New";
                include( "kernel/ezimagecatalogue/user/categoryedit.php" );
            }
            break;

            case "insert" :
            {
                writeAtAll();
                $action = "Insert";
                include( "kernel/ezimagecatalogue/user/categoryedit.php" );
            }
            break;

            case "edit" :
            {
                $action = "Edit";
                $categoryID = $url_array[4];
                if( ( eZObjectPermission::hasPermission( $categoryID, "imagecatalogue_category", 'w' ) ||
                      eZImageCategory::isOwner( $user, $categoryID ) )
                    && writeAtAll() )
                {
                    include( "kernel/ezimagecatalogue/user/categoryedit.php" );
                }
                else
                {
                    eZHTTPTool::header( "Location: /error/403" );
                    exit();
                }
            }
            break;

            case "update" :
            {
                $action = "Update";
                $categoryID = $url_array[4];
                if( ( eZObjectPermission::hasPermission( $categoryID, "imagecatalogue_category", 'w' ) ||
                     eZImageCategory::isOwner( $user, $categoryID ) )
                    && writeAtAll() )
                {
                    include( "kernel/ezimagecatalogue/user/categoryedit.php" );
                }
                else
                {
                    $info= urlencode( "You have no permission to update categories" );
                    eZHTTPTool::header( "Location: /error/403?Info=$info" );
                    exit();
                }

            }
            break;
        }
    }
    break;

    default:
        $info = urlencode( "This page does not exist!" );
        eZHTTPTool::header( "Location: /error/403?Info=$info" );

}

?>