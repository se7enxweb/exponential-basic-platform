<?php
//
// $id: datasupplier.php 9501 2002-05-02 17:09:58Z br $
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
$update             = eZHTTPTool::getVar( 'Update' );
$updateImages       = eZHTTPTool::getVar( 'UpdateImages' );
$refererURL         = eZHTTPTool::getVar( 'RefererURL' );
$detailView         = eZHTTPTool::getVar( 'DetailView' );
$normalView         = eZHTTPTool::getVar( 'NormalView' );
$showOriginal       = eZHTTPTool::getVar( 'ShowOriginal' );
$deleteCategories   = eZHTTPTool::getVar( 'DeleteCategories' );
$deleteImages       = eZHTTPTool::getVar( 'DeleteImages' );
$folderID           = eZHTTPTool::getVar( 'FolderID' );
// Array POST vars
$categoryArrayID    = $_POST['CategoryArrayID']    ?? [];
$categoryArray      = $_POST['CategoryArray']      ?? [];
$imageArrayID       = $_POST['ImageArrayID']       ?? [];
$imageUpdateArrayID = $_POST['ImageUpdateArrayID'] ?? [];
$newCaption         = $_POST['NewCaption']         ?? [];
$oldCaption         = $_POST['OldCaption']         ?? [];
$readGroupArrayID   = $_POST['ReadGroupArrayID']   ?? [];
$writeGroupArrayID  = $_POST['WriteGroupArrayID']  ?? [];
$uploadGroupArrayID = $_POST['UploadGroupArrayID'] ?? [];

function writeAtAll()
{
    $user = eZUser::currentUser();
    if( eZObjectPermission::getObjects( "imagecatalogue_category", 'w', true ) < 1
        && !eZPermission::checkPermission( $user, "eZImageCatalogue", "WriteToRoot" ) )
    {
        $text = "You do not have write permission to any categories";
        $info = urlencode( $text );
        eZHTTPTool::header( "Location: /error/403?Info=$info" );
        exit();
    }
    return true;
}


switch ( $url_array[2] )
{
        case "customimage" :
    {
        $imageID = $url_array[3];
        $imageWidth = $url_array[4];
        $imageHeight = $url_array[5];
        include( "kernel/ezimagecatalogue/admin/customimage.php" );
    }
    break;

    case "search" :
    {
        include( "kernel/ezimagecatalogue/admin/imagelist.php" );
    }
    break;

    case "browse":
    {
        $categoryID = $url_array[3];
        include( "kernel/ezimagecatalogue/admin/browse.php" );
    }
    break;

    case "browsesearch":
    {
        include( "kernel/ezimagecatalogue/admin/browse.php" );
    }
    break;
    
    case "unassigned":
    {
        $offset = $url_array[3];
        $limit = $url_array[4];
        include( "kernel/ezimagecatalogue/admin/unassigned.php" );
    }
    break;

    case "imageview" :
    {
        $imageID = $url_array[3];
        $variationID = $url_array[4];
        include( "kernel/ezimagecatalogue/admin/imageview.php" );
    }
    break;

	case "import" :
    {
        include( "kernel/ezimagecatalogue/admin/imagelist.php" );
    }
    break;

    case "image" :
    {
        switch ( $url_array[3] )
        {
            case "list" :
            {
                $categoryID = $url_array[4];
                if ( !is_numeric($categoryID ) )
                    $categoryID = 0;
                $offset = $url_array[6];

                if ( $offset == "" && is_Numeric( $url_array[4] ) && is_Numeric( $url_array[5] ) )
                {
                    $offset = $url_array[5];
                }
                else if ( $offset == "" )
                {
                    $offset = 0;
                }
                include( "kernel/ezimagecatalogue/admin/imagelist.php" );
            }
            break;

            case "new" :
            {
                writeAtAll();
                $action = "New";
                include( "kernel/ezimagecatalogue/admin/imageedit.php" );
            }
            break;
            
            case "Insert" :
            {
                writeAtAll();
                $action = "Insert";
                include( "kernel/ezimagecatalogue/admin/imageedit.php" );
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
                    include( "kernel/ezimagecatalogue/admin/imageedit.php" );
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
                    include( "kernel/ezimagecatalogue/admin/imageedit.php" );
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
            include( "kernel/ezimagecatalogue/admin/filedownload.php" );
        else
        {
            eZHTTPTool::header( "Location: /error/404" );
            exit();
        }
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
                include( "kernel/ezimagecatalogue/admin/categoryedit.php" );
            }
            break;

            case "insert" :
            {
                writeAtAll();
                $action = "Insert";
                include( "kernel/ezimagecatalogue/admin/categoryedit.php" );
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
                    include( "kernel/ezimagecatalogue/admin/categoryedit.php" );
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
                    include( "kernel/ezimagecatalogue/admin/categoryedit.php" );
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