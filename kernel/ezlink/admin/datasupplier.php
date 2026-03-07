<?php
// 
// $Id: datasupplier.php 6220 2001-07-20 11:15:21Z jakobn $
//
// Created on: <29-Jun-2001 13:57:58 bf>
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

// $url_array = explode( "/", $_SERVER['REQUEST_URI'] );

$user = eZUser::currentUser();
// include_once( "ezuser/classes/ezpermission.php" );
// include_once( "classes/ezhttptool.php" );

if( eZPermission::checkPermission( $user, "eZLink", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

// Explicit POST/GET extraction — replaces the kernel register_globals hack for this module.
$ok               = eZHTTPTool::getVar( 'OK' );
$browse           = eZHTTPTool::getVar( 'Browse' );
$back             = eZHTTPTool::getVar( 'Back' );
$delete           = eZHTTPTool::getVar( 'Delete' );
$deleteLinks      = eZHTTPTool::getVar( 'DeleteLinks' );
$update           = eZHTTPTool::getVar( 'Update' );
$attributes       = eZHTTPTool::getVar( 'Attributes' );
$name             = eZHTTPTool::getVar( 'Name' );
$url              = eZHTTPTool::getVar( 'Url' );
$keywords         = eZHTTPTool::getVar( 'Keywords' );
$description      = eZHTTPTool::getVar( 'Description' );
$accepted         = eZHTTPTool::getVar( 'Accepted' );
$linkCategoryID   = eZHTTPTool::getVar( 'LinkCategoryID' );
$categoryArray    = eZHTTPTool::getVar( 'CategoryArray' ) ?? [];
$typeID           = eZHTTPTool::getVar( 'TypeID' );
$attributeValue   = eZHTTPTool::getVar( 'AttributeValue' ) ?? [];
$attributeID      = eZHTTPTool::getVar( 'AttributeID' ) ?? [];
$imageID          = eZHTTPTool::getVar( 'ImageID' );
$linkArrayID      = eZHTTPTool::getVar( 'LinkArrayID' ) ?? [];
$getSite          = eZHTTPTool::getVar( 'GetSite' );
$addImages        = eZHTTPTool::getVar( 'AddImages' );
$deleteImage      = eZHTTPTool::getVar( 'DeleteImage' );
$queryString      = eZHTTPTool::getVar( 'QueryString' );
$deleteCategories = eZHTTPTool::getVar( 'DeleteCategories' );
$categoryArrayID  = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$sectionID        = eZHTTPTool::getVar( 'SectionID' );
$parentCategory   = eZHTTPTool::getVar( 'ParentCategory' );
$deleteSelected   = eZHTTPTool::getVar( 'DeleteSelected' );
$deleteAttributes = eZHTTPTool::getVar( 'DeleteAttributes' ) ?? [];
$newAttribute     = eZHTTPTool::getVar( 'NewAttribute' );
$attributeName    = eZHTTPTool::getVar( 'AttributeName' ) ?? [];
$actionValueArray = eZHTTPTool::getVar( 'ActionValueArray' ) ?? [];
$cancel           = eZHTTPTool::getVar( 'Cancel' );
$deleteArrayID    = eZHTTPTool::getVar( 'DeleteArrayID' ) ?? [];
$okType           = eZHTTPTool::getVar( 'Ok' );   // typeedit.php uses 'Ok' not 'OK'
$action           = eZHTTPTool::getVar( 'Action' ); // typeedit.php form hidden field
$offset           = eZHTTPTool::getVar( 'Offset' );
$lgid             = eZHTTPTool::getVar( 'LGID' );
// URL-routing variables are set below inside the switch and override the above POST defaults.

switch ( $url_array[2] )
{
    case "" :
    {
        include( "kernel/ezlink/admin/linkcategorylist.php" );
    }
    break;
    case "link" :
    {
        $LID = $url_array[3];
        include( "kernel/ezlink/admin/linkcategorylist.php" );
    }
    break;

    case "typelist" :
    {
        include( "kernel/ezlink/admin/typelist.php" );
    }
    break;
 
    case "typeedit" :
    {
        if ( $url_array[3] == "edit" )
        {
            $typeID = $url_array[4];
            $action = "Edit";
        }
        if ( $url_array[3] == "Insert" || $url_array[3] == "insert" )
        {
            $typeID = $url_array[4];
            $action = "Insert";
        }
        if ( $url_array[3] == "Update" || $url_array[3] == "update" )
        {
            $typeID = $url_array[4];
            $action = "Update";
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
 
        include( "kernel/ezlink/admin/typeedit.php" );
    }
    break;
    
    case "category" :
    {
        if ( $url_array[4] == "parent" )
        {
            $offset = $url_array[5];
            if ( !is_numeric( $offset ) )
                $offset = 0;
        }
        $linkCategoryID = $url_array[3];
        include( "kernel/ezlink/admin/linkcategorylist.php" );
    }
    break;

    case "unacceptedlist":
    {
        if ( $url_array[3] )
            $offset = $url_array[3];
        include( "kernel/ezlink/admin/unacceptedlist.php" );
    }
    break;
    case "unacceptededit":
    {
        include( "kernel/ezlink/admin/unacceptededit.php" );
    }
    break;
    
    case "linkedit" :
    {
        switch ( $url_array[3] )
        {
            case "new" :
            {
                $action = "new";
                include( "kernel/ezlink/admin/linkedit.php" );
            }
            break;
            
            case "insert" :
            {
                $linkID = $url_array[4];
                $action = isset( $update ) ? "AttributeList" : "insert";
                include( "kernel/ezlink/admin/linkedit.php" );
            }
            break;
            
            case "edit" :
            {
                $linkID = $url_array[4];
                $action = "edit";
                include( "kernel/ezlink/admin/linkedit.php" );
            }
            break;
            
            case "update" :
            {
                $linkID = $url_array[4];
                $action = isset( $update ) ? "AttributeList" : "update";
                include( "kernel/ezlink/admin/linkedit.php" );
            }
            break;
            
            case "delete" :
            {
                $linkID = $url_array[4];
                $action = "delete";
                include( "kernel/ezlink/admin/linkedit.php" );
            }
            break;
            
            case "attributeedit" :
            {
                $linkID = $url_array[4];
                include( "kernel/ezlink/admin/attributeedit.php" );
            }
            break;
        }
    }
    break;

    case "categoryedit" :
    {
        if ( $url_array[3] == "new" )
        {
            $action = "new";
            include( "kernel/ezlink/admin/categoryedit.php" );
        }
        else if ( $url_array[3] == "insert" )
        {
            $linkCategoryID = $url_array[4];
            $action = "insert";
            include( "kernel/ezlink/admin/categoryedit.php" );
        }

        else if ( $url_array[3] == "edit" )
        {
            $linkCategoryID = $url_array[4];
            $action = "edit";
            include( "kernel/ezlink/admin/categoryedit.php" );
        }
        else if ( $url_array[3] == "update" )
        {
            $linkCategoryID = $url_array[4];
            $action = "update";
            include( "kernel/ezlink/admin/categoryedit.php" );
        }
        else if ( $url_array[3] == "delete" )
        {
            $linkCategoryID = $url_array[4];
            $action = "delete";
            include( "kernel/ezlink/admin/categoryedit.php" );
        }
    }
    break;
    case "testbench" :
        include( "kernel/eztrade/admin/testbench.php" );
        break;
    case "search" :
    {
        if ( $url_array[3] == "parent" )
        {
            $queryString = urldecode( $url_array[4] );
            $offset = $url_array[5];
        }
        include( "kernel/ezlink/admin/search.php" );
    }
        break;
    case "norights" :
        include( "kernel/ezlink/admin/norights.php" );        
        break;
    case "gotolink" :
    {
        $action = $url_array[3];
        $linkID = $url_array[4];
        include( "kernel/ezlink/admin/gotolink.php" );
    }
    break;


    default :
        print( "<h1>Sorry, Your link page could not be found. </h1>" );
        break;
}

?>