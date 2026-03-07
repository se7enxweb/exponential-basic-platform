<?php
//
// $Id: datasupplier.php 8182 2001-11-01 17:17:57Z ce $
//
// Created on: <24-Jul-2001 10:59:19 ce>
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
// include_once( "ezmediacatalogue/classes/ezmediacategory.php" );

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZMediaCatalogueMain", "DefaultSection" );

function writeAtAll()
{
    $user = eZUser::currentUser();
    if( eZObjectPermission::getObjects( "mediacatalogue_category", 'w', true ) < 1
        && !eZPermission::checkPermission( $user, "eZMediaCatalogue", "WriteToRoot" ) )
    {
        $text = "You do not have write permission to any categories";
        $info = urlencode( $text );
        eZHTTPTool::header( "Location: /error/403?Info=$info" );
        exit();
    }
    return true;
}

$user = eZUser::currentUser();

$action             = eZHTTPTool::getVar( 'Action' );
$attributeDefault   = eZHTTPTool::getVar( 'AttributeDefault' );
$attributeID        = eZHTTPTool::getVar( 'AttributeID' );
$attributeName      = eZHTTPTool::getVar( 'AttributeName' );
$attributeValue     = eZHTTPTool::getVar( 'AttributeValue' );
$cancel             = eZHTTPTool::getVar( 'Cancel' );
$caption            = eZHTTPTool::getVar( 'Caption' );
$categoryArray      = eZHTTPTool::getVar( 'CategoryArray' ) ?? [];
$categoryArrayID    = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$categoryID         = eZHTTPTool::getVar( 'CategoryID' );
$currentCategoryID  = eZHTTPTool::getVar( 'CurrentCategoryID' );
$delete             = eZHTTPTool::getVar( 'Delete' );
$deleteArrayID      = eZHTTPTool::getVar( 'DeleteArrayID' ) ?? [];
$deleteAttributes   = eZHTTPTool::getVar( 'DeleteAttributes' );
$deleteCategories   = eZHTTPTool::getVar( 'DeleteCategories' );
$deleteMedia        = eZHTTPTool::getVar( 'DeleteMedia' );
$deleteSelected     = eZHTTPTool::getVar( 'DeleteSelected' );
$description        = eZHTTPTool::getVar( 'Description' );
$imageDir           = eZHTTPTool::getVar( 'ImageDir' );
$mediaArrayID       = eZHTTPTool::getVar( 'MediaArrayID' ) ?? [];
$mediaDir           = eZHTTPTool::getVar( 'MediaDir' );
$mediaID            = eZHTTPTool::getVar( 'MediaID' );
$mediaUpload        = eZHTTPTool::getVar( 'MediaUpload' );
$name               = eZHTTPTool::getVar( 'Name' );
$newAttribute       = eZHTTPTool::getVar( 'NewAttribute' );
$newCategory        = eZHTTPTool::getVar( 'NewCategory' );
$newCreatorEmail    = eZHTTPTool::getVar( 'NewCreatorEmail' );
$newCreatorName     = eZHTTPTool::getVar( 'NewCreatorName' );
$ok                 = eZHTTPTool::getVar( 'OK' ) ?? eZHTTPTool::getVar( 'Ok' );
$offset             = eZHTTPTool::getVar( 'Offset' );
$parentID           = eZHTTPTool::getVar( 'ParentID' );
$photoID            = eZHTTPTool::getVar( 'PhotoID' );
$photographerID     = eZHTTPTool::getVar( 'PhotographerID' );
$position           = eZHTTPTool::getVar( 'Position' );
$read               = eZHTTPTool::getVar( 'Read' );
$readGroupArrayID   = eZHTTPTool::getVar( 'ReadGroupArrayID' ) ?? [];
$refererURL         = eZHTTPTool::getVar( 'RefererURL' );
$refreshTimer       = eZHTTPTool::getVar( 'RefreshTimer' );
$sectionID          = eZHTTPTool::getVar( 'SectionID' );
$syncDir            = eZHTTPTool::getVar( 'SyncDir' );
$syncMediaDir       = eZHTTPTool::getVar( 'SyncMediaDir' );
$typeID             = eZHTTPTool::getVar( 'TypeID' );
$update             = eZHTTPTool::getVar( 'Update' );
$variationID        = eZHTTPTool::getVar( 'VariationID' );
$write              = eZHTTPTool::getVar( 'Write' );
$writeGroupArrayID  = eZHTTPTool::getVar( 'WriteGroupArrayID' ) ?? [];

switch ( $url_array[2] )
{
    case "browse":
    {
        $categoryID = $url_array[3];
        include( "kernel/ezmediacatalogue/admin/browse.php" );
    }
    break;

    case "mediaview" :
    {
        $mediaID = $url_array[3];
        $variationID = $url_array[4];
        include( "kernel/ezmediacatalogue/admin/mediaview.php" );
    }
    break;

    case "media" :
    {
        switch ( $url_array[3] )
        {
            case "list" :
            {
                $categoryID = $url_array[4];
                if ( !is_numeric($categoryID ) )
                    $categoryID = 0;

                if ( $url_array[5] == "parent" )
                    $offset = $url_array[6];

                include( "kernel/ezmediacatalogue/admin/medialist.php" );
            }
            break;

            case "new" :
            {
                writeAtAll();
                $action = "New";
                include( "kernel/ezmediacatalogue/admin/mediaedit.php" );
            }
            break;
            
            case "Insert" :
            {
                writeAtAll();
                $action = "Insert";
                include( "kernel/ezmediacatalogue/admin/mediaedit.php" );
            }
            break;

            case "edit" :
            {
                $mediaID = $url_array[4];
                $action = "Edit";
                if( ( eZMedia::isOwner( $user, $mediaID ) ||
                     eZObjectPermission::hasPermission( $mediaID, "mediacatalogue_media", 'w' ) )
                    && writeAtAll() )
                {
                    include( "kernel/ezmediacatalogue/admin/mediaedit.php" );
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
                $mediaID = $url_array[4];
                $action = "Update";
                if( ( eZMedia::isOwner( $user, $mediaID ) ||
                     eZObjectPermission::hasPermission( $mediaID, "mediacatalogue_media", 'w' ) )
                    && writeAtAll() )
                    include( "kernel/ezmediacatalogue/admin/mediaedit.php" );
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
        $mediaID = $url_array[3];
        if ( !is_numeric( $mediaID ) )
            $mediaID = 0;
        if ( ( eZMedia::isOwner( $user, $mediaID ) ||
              eZObjectPermission::hasPermission( $mediaID, "mediacatalogue_media", 'r' ) ) )
            include( "kernel/ezmediacatalogue/admin/filedownload.php" );
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
        include( "kernel/ezmediacatalogue/admin/slideshow.php" );
    }
    break;

        case "typelist" :
    {
        include( "kernel/ezmediacatalogue/admin/typelist.php" );
    }
    break;
 
    case "typeedit" :
    {
        if ( $url_array[3] == "edit" )
        {
            $typeID = $url_array[4];
            $action = "Edit";
        }
        if ( $url_array[3] == "delete" )
        {
            $typeID = $url_array[4];
            $action = "Delete";
        }
        if ( $url_array[3] == "up" )
        {
            $typeID = $url_array[4];
            $attributeID = $url_array[5];
            $action = "up";
        }
        if ( $url_array[3] == "down" )
        {
            $typeID = $url_array[4];
            $attributeID = $url_array[5];
            $action = "down";
        }
 
        include( "kernel/ezmediacatalogue/admin/typeedit.php" );
    }
    break;

    
    case "category" :
    {
        switch( $url_array[3] )
        {
            case "list" :
            {
                $categoryID = $url_array[4];
                if ( !is_numeric($categoryID ) )
                    $categoryID = 0;
                $offset = $url_array[5];
                if ( $offset == "" )
                    $offset = 0;
                include( "kernel/ezmediacatalogue/admin/medialist.php" );
            }
            break;

            case "new" :
            {
                writeAtAll();
                $currentCategoryID = $url_array[4];
                $action = "New";
                include( "kernel/ezmediacatalogue/admin/categoryedit.php" );
            }
            break;

            case "insert" :
            {
                writeAtAll();
                $action = "Insert";
                $categoryID = $url_array[4];
                include( "kernel/ezmediacatalogue/admin/categoryedit.php" );
            }
            break;

            case "edit" :
            {
                $action = "Edit";
                $categoryID = $url_array[4];
                if( ( eZObjectPermission::hasPermission( $categoryID, "mediacatalogue_category", 'w' ) ||
                      eZMediaCategory::isOwner( $user, $categoryID ) )
                    && writeAtAll() )
                {
                    include( "kernel/ezmediacatalogue/admin/categoryedit.php" );
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
                if( ( eZObjectPermission::hasPermission( $categoryID, "mediacatalogue_category", 'w' ) ||
                     eZMediaCategory::isOwner( $user, $categoryID ) )
                    && writeAtAll() )
                {
                    include( "kernel/ezmediacatalogue/admin/categoryedit.php" );
                }
                else
                {
                    eZHTTPTool::header( "Location: /error/403?Info=FUCK" );
                    exit();
                }

            }
            break;


        }
    }
    break;
}

?>