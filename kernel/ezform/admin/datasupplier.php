<?php
//
// $Id: datasupplier.php 7918 2001-10-17 07:22:28Z jhe $
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

// include_once( "classes/ezhttptool.php" );

// Explicit POST/GET extraction — replaces the kernel register_globals hack for this module.
$action        = eZHTTPTool::getVar( 'Action' );
$actionValue   = eZHTTPTool::getVar( 'ActionValue' );
$addValue      = eZHTTPTool::getVar( 'AddValue' );
$cancel        = eZHTTPTool::getVar( 'Cancel' );
$deleteSelected = eZHTTPTool::getVar( 'DeleteSelected' );
$elementBreak  = eZHTTPTool::getVar( 'ElementBreak' );
$elementID     = eZHTTPTool::getVar( 'ElementID' );
$formID        = eZHTTPTool::getVar( 'FormID' );
$fromID        = eZHTTPTool::getVar( 'FromID' );
$id            = eZHTTPTool::getVar( 'ID' );
$newElement    = eZHTTPTool::getVar( 'NewElement' );
$ok            = eZHTTPTool::getVar( 'OK' );
$offset        = eZHTTPTool::getVar( 'Offset' );
$operation     = eZHTTPTool::getVar( 'Operation' );
$preview       = eZHTTPTool::getVar( 'Preview' );
$size          = eZHTTPTool::getVar( 'Size' );
$store         = eZHTTPTool::getVar( 'Store' );
$test          = eZHTTPTool::getVar( 'Test' );
$update        = eZHTTPTool::getVar( 'Update' );
$value         = eZHTTPTool::getVar( 'Value' );
$valueDeleteID = eZHTTPTool::getVar( 'ValueDeleteID' );
$valueID       = eZHTTPTool::getVar( 'ValueID' );
// URL-routing variables are set below inside the switch and override the above POST defaults.

$operation = $url_array[2];
$action = $url_array[3];

switch ( $operation )
{
    case "form":
    {
        $formID = $url_array[4];
        
        switch ( $action )
        {
            case "edit":
            case "insert":
            case "update":
            case "delete":
            case "up":
            case "down":
            case "new":
            {
                include( "kernel/ezform/admin/formedit.php" );
            }
            break;
            
            case "list":
            {
                $offset = $url_array[5];
                include( "kernel/ezform/admin/formlist.php" );
            }
            break;
            
            case "view":
            case "process":
            {
                include( "kernel/ezform/admin/formview.php" );
            }
            break;
            
            case "preview":
            {
                include( "kernel/ezform/admin/formpreview.php" );
            }
            break;

            case "fixedvalues":
            {
                $fromID = $url_array[4];
                $elementID = $url_array[5];
                include( "kernel/ezform/admin/fixedvalues.php" );
            }
            break;
            
            default:
            {
                eZHTTPTool::header( "Location: /error/404" );
            }
            break;
        }
    }
    break;
    
    default:
    {
        eZHTTPTool::header( "Location: /error/404" );
    }
    break;
}

?>
