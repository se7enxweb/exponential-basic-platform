<?php
//
// $Id: datasupplier.php,v 1.6 2001/07/19 11:56:33 jakobn Exp $
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


$action         = eZHTTPTool::getVar( 'Action' );
$addImages      = eZHTTPTool::getVar( 'AddImages' );
$browse         = eZHTTPTool::getVar( 'Browse' );
$cancel         = eZHTTPTool::getVar( 'Cancel' );
$caption        = eZHTTPTool::getVar( 'Caption' );
$categoryArrayID = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$categoryID     = eZHTTPTool::getVar( 'CategoryID' );
$deleteCategories = eZHTTPTool::getVar( 'DeleteCategories' );
$deleteTips     = eZHTTPTool::getVar( 'DeleteTips' );
$description    = eZHTTPTool::getVar( 'Description' );
$htmlBanner     = eZHTTPTool::getVar( 'HTMLBanner' );
$id             = eZHTTPTool::getVar( 'ID' );
$isActive       = eZHTTPTool::getVar( 'IsActive' );
$isPublished    = eZHTTPTool::getVar( 'IsPublished' );
$locationID     = eZHTTPTool::getVar( 'LocationID' );
$locationItem   = eZHTTPTool::getVar( 'LocationItem' );
$locationList   = eZHTTPTool::getVar( 'LocationList' );
$name           = eZHTTPTool::getVar( 'Name' );
$parentID       = eZHTTPTool::getVar( 'ParentID' );
$preview        = eZHTTPTool::getVar( 'Preview' );
$sectionArray   = eZHTTPTool::getVar( 'SectionArray' ) ?? [];
$tipArrayID     = eZHTTPTool::getVar( 'TipArrayID' ) ?? [];
$tipDescription = eZHTTPTool::getVar( 'TipDescription' );
$tipID          = eZHTTPTool::getVar( 'TipID' );
$tipLocations   = eZHTTPTool::getVar( 'TipLocations' );
$tipTitle       = eZHTTPTool::getVar( 'TipTitle' );
$tipURL         = eZHTTPTool::getVar( 'TipURL' );
$useHTML        = eZHTTPTool::getVar( 'UseHTML' );

switch ( $url_array[2] )
{
    case "archive" :
    {
        $categoryID = $url_array[3];

        include( "kernel/eztip/admin/tiplist.php" );
    }
    break;

    case "statistics" :
    {
        $tipID = $url_array[3];
        
        include( "kernel/eztip/admin/tipstatistics.php" );        
    }
    break;

    case "tip" :
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
        
        if( empty( $tipID ) )
        {
            $tipID = $url_array[4];
        }
        include( "kernel/eztip/admin/tipedit.php" );
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
        if( !empty( $url_array[4] ) )
        {
            $categoryID = $url_array[4];
        }
        else
        {
            $categoryID = 0;
        }

        include( "kernel/eztip/admin/categoryedit.php" );
    }
    break;

}

?>