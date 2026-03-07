<?php
//
// $Id: datasupplier.php 6203 2001-07-19 11:56:33Z jakobn $
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


// Explicit POST/GET extraction — replaces the kernel register_globals hack for this module.
$action           = eZHTTPTool::getVar( 'Action' );
$adArrayID        = eZHTTPTool::getVar( 'AdArrayID' ) ?? [];
$adDescription    = eZHTTPTool::getVar( 'AdDescription' );
$adID             = eZHTTPTool::getVar( 'AdID' );
$adTitle          = eZHTTPTool::getVar( 'AdTitle' );
$adURL            = eZHTTPTool::getVar( 'AdURL' );
$addImages        = eZHTTPTool::getVar( 'AddImages' );
$browse           = eZHTTPTool::getVar( 'Browse' );
$cancel           = eZHTTPTool::getVar( 'Cancel' );
$caption          = eZHTTPTool::getVar( 'Caption' );
$categoryArrayID  = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$categoryID       = eZHTTPTool::getVar( 'CategoryID' );
$clickPrice       = eZHTTPTool::getVar( 'ClickPrice' );
$deleteAds        = eZHTTPTool::getVar( 'DeleteAds' );
$deleteCategories = eZHTTPTool::getVar( 'DeleteCategories' );
$description      = eZHTTPTool::getVar( 'Description' );
$htmlBanner       = eZHTTPTool::getVar( 'HTMLBanner' );
$id               = eZHTTPTool::getVar( 'ID' );
$isActive         = eZHTTPTool::getVar( 'IsActive' );
$name             = eZHTTPTool::getVar( 'Name' );
$parentID         = eZHTTPTool::getVar( 'ParentID' );
$preview          = eZHTTPTool::getVar( 'Preview' );
$useHTML          = eZHTTPTool::getVar( 'UseHTML' );
$viewPrice        = eZHTTPTool::getVar( 'ViewPrice' );
// URL-routing variables are set below inside the switch and override the above POST defaults.

switch ( $url_array[2] )
{
    case "archive" :
    {
        $categoryID = $url_array[3];

        include( "kernel/ezad/admin/adlist.php" );
    }
    break;

    case "statistics" :
    {
        $adID = $url_array[3];
        
        include( "kernel/ezad/admin/adstatistics.php" );        
    }
    break;

    case "ad" :
    {
        if ( $url_array[3] == "new" )
        {
            $action = "New";
        }

        if ( $url_array[3] == "insert" )
        {
            $action = "Insert";
        }

        if ( $url_array[3] == "edit" )
        {
            $action = "Edit";
        }

        if ( $url_array[3] == "update" )
        {
            $action = "Update";
        }

        if ( $url_array[3] == "delete" )
        {
            $action = "Delete";
        }
        
        if( empty( $adID ) )
        {
            $adID = $url_array[4];
        }
        include( "kernel/ezad/admin/adedit.php" );
    }
    break;
    
    case "category" :
    {
        if ( $url_array[3] == "new" )
        {
            $action = "New";
        }

        if ( $url_array[3] == "insert" )
        {
            $action = "Insert";
        }

        if ( $url_array[3] == "edit" )
        {
            $action = "Edit";
        }

        if ( $url_array[3] == "update" )
        {
            $action = "Update";
            $categoryID = $url_array[4];
        }

        if ( $url_array[3] == "delete" )
        {
            $action = "Delete";
        }
        if( empty( $categoryID ) )
        {
            $categoryID = $url_array[4];
        }
        include( "kernel/ezad/admin/categoryedit.php" );
    }
    break;

}

?>