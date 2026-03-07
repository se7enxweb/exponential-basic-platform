<?php
//
// $Id: datasupplier.php 6484 2001-08-17 13:36:01Z jhe $
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
// include_once( "classes/ezhttptool.php" );
$user = eZUser::currentUser();
if ( eZPermission::checkPermission( $user, "eZStats", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

$day            = eZHTTPTool::getVar( 'Day' );
$entryPageLimit = eZHTTPTool::getVar( 'EntryPageLimit' );
$excludeDomain  = eZHTTPTool::getVar( 'ExcludeDomain' );
$exitPageLimit  = eZHTTPTool::getVar( 'ExitPageLimit' );
$itemCount      = eZHTTPTool::getVar( 'ItemCount' );
$month          = eZHTTPTool::getVar( 'Month' );
$nextDay        = eZHTTPTool::getVar( 'NextDay' );
$nextMonth      = eZHTTPTool::getVar( 'NextMonth' );
$nextYear       = eZHTTPTool::getVar( 'NextYear' );
$offset         = eZHTTPTool::getVar( 'Offset' );
$prevDay        = eZHTTPTool::getVar( 'PrevDay' );
$prevMonth      = eZHTTPTool::getVar( 'PrevMonth' );
$prevYear       = eZHTTPTool::getVar( 'PrevYear' );
$viewLimit      = eZHTTPTool::getVar( 'ViewLimit' );
$viewMode       = eZHTTPTool::getVar( 'ViewMode' );
$year           = eZHTTPTool::getVar( 'Year' );

switch ( $url_array[2] )
{
    case "overview" :
    {
        include( "kernel/ezstats/admin/overview.php" );
    }
    break;

    case "pageviewlist" :
    {
        $viewMode = $url_array[3];
        $viewLimit = $url_array[4];
        $offset = $url_array[5];

        include( "kernel/ezstats/admin/pageviewlist.php" );
    }
    break;

    case "visitorlist" :
    {
        $viewMode = $url_array[3];
        $viewLimit = $url_array[4];
        $offset = $url_array[5];

        include( "kernel/ezstats/admin/visitorlist.php" );
    }
    break;

    case "refererlist" :
    {
        $viewMode = $url_array[3];
        $viewLimit = $url_array[4];
        $offset = $url_array[5];
        if ( !isset( $excludeDomain ) )
            $excludeDomain = $url_array[6];

        include( "kernel/ezstats/admin/refererlist.php" );
    }
    break;

    case "browserlist" :
    {
        $viewMode = $url_array[3];
        $viewLimit = $url_array[4];
        $offset = $url_array[5];

        include( "kernel/ezstats/admin/browserlist.php" );
    }
    break;

    case "requestpagelist" :
    {
        $viewMode = $url_array[3];
        $viewLimit = $url_array[4];
        $offset = $url_array[5];

        include( "kernel/ezstats/admin/requestpagelist.php" );
    }
    break;
    
    case "yearreport" :
    {
        $year = $url_array[3];

        include( "kernel/ezstats/admin/yearreport.php" );
    }
    break;

    case "monthreport" :
    {
        $year = $url_array[3];
        $month = $url_array[4];

        include( "kernel/ezstats/admin/monthreport.php" );
    }
    break;

    case "dayreport" :
    {
        $year = $url_array[3];
        $month = $url_array[4];
        $day = $url_array[5];

        include( "kernel/ezstats/admin/dayreport.php" );
    }
    break;

    case "productreport" :
    {
        $year = $url_array[3];
        $month = $url_array[4];
        
        include( "kernel/ezstats/admin/productreport.php" );
    }
    break;
    
    case "entryexitreport" :
    {
        $year = $url_array[3];
        $month = $url_array[4];
        
        include( "kernel/ezstats/admin/entryexitpages.php" );
    }
    break;
    
}

?>