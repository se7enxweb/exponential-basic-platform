<?php
//
// $Id: datasupplier.php 6204 2001-07-19 12:06:56Z jakobn $
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

//print $_SERVER["REQUEST_URI"];

// include_once( "classes/ezuritool.php" );

$listType = $url_array[2];


$url_array = eZURITool::split( $_SERVER['REQUEST_URI'] );
// $url_array = explode( "/", $_SERVER['REQUEST_URI'] );
$url_array_count = count( $url_array );

for( $i = $url_array_count; $i <= 25; $i++ )
{
    $url_array[$i] = false;
}

switch ( $listType )
{
    case "phonetype":
    {
        $phoneTypeID = $url_array[4];
        $action = $url_array[3];
        switch( $action )
        {
            // intentional fall through
            case "new":
            case "edit":
            case "update":
            case "insert":
            case "up":
            case "down":
            {
                include( "kernel/ezaddress/admin/phonetypeedit.php" );
                break;
            }
            case "list":
            {
                if ( is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                else
                    $index = false;
                include( "kernel/ezaddress/admin/phonetypelist.php" );
                break;
            }
            case "search":
            {
                if ( is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                if ( count( $url_array ) >= 5 && !isset( $searchText ) )
                {
                    $searchText = $url_array[5];
                    $searchText = eZURITool::decode( $searchText );
                }
                include( "kernel/ezaddress/admin/phonetypelist.php" );
                break;
            }
            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /address/error?Type=404&Uri=" . urlencode( $_SERVER['REQUEST_URI'] ) . "&Query=" . urlencode( $_SERVER['QUERY_STRING'] ?? '' ) . "&BackUrl=" . urlencode( $_SERVER['HTTP_REFERER'] ?? '' ) );
                break;
            }
        }
        break;
    }

    case "addresstype":
    {
        if( isset( $url_array[4] ) )
            $addressTypeID = $url_array[4];
        else
            $addressTypeID = false;
        $action = $url_array[3];
        switch( $url_array[3] )
        {
            // intentional fall through
            case "new":
            case "edit":
            case "update":
            case "insert":
            case "up":
            case "down":
            {
                include( "kernel/ezaddress/admin/addresstypeedit.php" );
                break;
            }
            case "list":
            {
                if ( isset( $url_array[4] ) && is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                else
                    $index = false;
                include( "kernel/ezaddress/admin/addresstypelist.php" );
                break;
            }
            case "search":
            {
                if ( isset( $url_array[4] ) && is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                else
                    $index = false;
                if ( count( $url_array ) >= 5 && !isset( $searchText ) )
                {
                    $searchText = $url_array[5];
                    $searchText = eZURITool::decode( $searchText );
                }
                include( "kernel/ezaddress/admin/addresstypelist.php" );
                break;
            }
            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /address/error?Type=404&Uri=" . urlencode( $_SERVER['REQUEST_URI'] ) . "&Query=" . urlencode( $_SERVER['QUERY_STRING'] ?? '' ) . "&BackUrl=" . urlencode( $_SERVER['HTTP_REFERER'] ?? '' ) );
                break;
            }
        }
        break;
    }
    
    case "onlinetype":
    {
        $action = $url_array[3];
        if( isset( $url_array[4] ) )
            $onlineTypeID = $url_array[4];
        else
            $onlineTypeID = false;
        
        switch( $action )
        {
            // intentional fall through
            case "new":
            case "edit":
            case "update":
            case "insert":
            case "up":
            case "down":
            {
                include( "kernel/ezaddress/admin/onlinetypeedit.php" );
                break;
            }
            case "list":
            {
                if ( isset( $url_array[4] ) && is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                else
                    $index = false;
                include( "kernel/ezaddress/admin/onlinetypelist.php" );
                break;
            }
            case "search":
            {
                if ( is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                if ( count( $url_array ) >= 5 && !isset( $searchText ) )
                {
                    $searchText = $url_array[5];
                    $searchText = eZURITool::decode( $searchText );
                }
                include( "kernel/ezaddress/admin/onlinetypelist.php" );
                break;
            }
            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /address/error?Type=404&Uri=" . urlencode( $_SERVER['REQUEST_URI'] ) . "&Query=" . urlencode( $_SERVER['QUERY_STRING'] ?? '' ) . "&BackUrl=" . urlencode( $_SERVER['HTTP_REFERER'] ?? '' ) );
                break;
            }
        }
        break;
    }

    case "country":
    {
        if( isset( $url_array[4] ) )
            $countryID = $url_array[4];
        else
            $countryID = false;

        $action = $url_array[3];
        switch ( $action )
        {
            // intentional fall through
            case "new":
            case "edit":
            case "update":
            case "insert":
            case "up":
            case "down":
            {
                include( "kernel/ezaddress/admin/countryedit.php" );
                break;
            }
            case "list":
            {
                if ( isset( $url_array[4] ) && is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                else
                    $index = false;
                include( "kernel/ezaddress/admin/countrylist.php" );
                break;
            }
            case "search":
            {
                if ( is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                if ( count( $url_array ) >= 5 && !isset( $searchText ) )
                {
                    $searchText = $url_array[5];
                    $searchText = eZURITool::decode( $searchText );
                }
                include( "kernel/ezaddress/admin/countrylist.php" );
                break;
            }

            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /address/error?Type=404&Uri=" . urlencode( $_SERVER['REQUEST_URI'] ) . "&Query=" . urlencode( $_SERVER['QUERY_STRING'] ?? '' ) . "&BackUrl=" . urlencode( $_SERVER['HTTP_REFERER'] ?? '' ) );
                break;
            }
        }
        break;
    }

    case "region":
    {
        $regionID = $url_array[4];
        $action = $url_array[3];
        switch ( $action )
        {
            // intentional fall through
            case "new":
            case "edit":
            case "update":
            case "insert":
            case "up":
            case "down":
            {
                include( "kernel/ezaddress/admin/regionedit.php" );
                break;
            }
            case "list":
            {
                if ( is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                include( "kernel/ezaddress/admin/regionlist.php" );
                break;
            }
            case "search":
            {
                if ( is_numeric( $url_array[4] ) )
                    $index = $url_array[4];
                if ( count( $url_array ) >= 5 && !isset( $searchText ) )
                {
                    $searchText = $url_array[5];
                    $searchText = eZURITool::decode( $searchText );
                }
                include( "kernel/ezaddress/admin/regionlist.php" );
                break;
            }

            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /address/error?Type=404&Uri=" . urlencode( $_SERVER['REQUEST_URI'] ) . "&Query=" . urlencode( $_SERVER['QUERY_STRING'] ?? '' ) . "&BackUrl=" . urlencode( $_SERVER['HTTP_REFERER'] ?? '' ) );
                break;
            }
        }
        break;
    }

    case "error":
    {
        include( "kernel/ezaddress/admin/error.php" );
        break;
    }

    default :
        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /address/error?Type=404&Uri=" . urlencode( $_SERVER['REQUEST_URI'] ) . "&Query=" . urlencode( $_SERVER['QUERY_STRING'] ?? '' ) . "&BackUrl=" . urlencode( $_SERVER['HTTP_REFERER'] ?? '' ) );
        break;
}

?>