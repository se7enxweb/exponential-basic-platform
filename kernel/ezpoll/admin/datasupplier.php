<?php
//
// $Id: datasupplier.php 6225 2001-07-20 11:22:30Z jakobn $
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
if( eZPermission::checkPermission( $user, "eZPoll", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}
//print $_SERVER['REQUEST_URI'];

$url_array = explode( "/", $_SERVER['REQUEST_URI'] );

$action         = eZHTTPTool::getVar( 'Action' );
$addPoll        = eZHTTPTool::getVar( 'AddPoll' );
$anonymous      = eZHTTPTool::getVar( 'Anonymous' );
$back           = eZHTTPTool::getVar( 'Back' );
$choice         = eZHTTPTool::getVar( 'Choice' );
$deleteChoice   = eZHTTPTool::getVar( 'DeleteChoice' );
$deletePolls    = eZHTTPTool::getVar( 'DeletePolls' );
$description    = eZHTTPTool::getVar( 'Description' );
$isClosed       = eZHTTPTool::getVar( 'IsClosed' );
$isEnabled      = eZHTTPTool::getVar( 'IsEnabled' );
$langaugeIni    = eZHTTPTool::getVar( 'LangaugeIni' );
$name           = eZHTTPTool::getVar( 'Name' );
$ok             = eZHTTPTool::getVar( 'Ok' );
$pollArrayID    = eZHTTPTool::getVar( 'PollArrayID' ) ?? [];
$pollChoiceID   = eZHTTPTool::getVar( 'PollChoiceID' );
$pollChoiceName = eZHTTPTool::getVar( 'PollChoiceName' );
$pollID         = eZHTTPTool::getVar( 'PollID' );
$showResult     = eZHTTPTool::getVar( 'ShowResult' );

switch ( $url_array[2] )
{
    case "pollist" :
    {
        if( isset( $deletePolls ) )
            $action = "Delete";

        if ( isset( $addPoll ) )
        {
            include( "kernel/ezpoll/admin/polledit.php" );
        }
        else
        {        
            include( "kernel/ezpoll/admin/pollist.php" );
        }
    }
    break;

    case "polledit" :
        if ( ( $url_array[3] == "new" ) )
        {
            $action = "New";
            include( "kernel/ezpoll/admin/polledit.php" );
        }
        else if ( ( $url_array[3] == "insert" ) )
        {
            $action = "Insert";
            include( "kernel/ezpoll/admin/polledit.php" );
        }
        else if( ( $url_array[3] == "edit" ) )
        {
            $action = "Edit";
            $pollID = $url_array[4];
            include( "kernel/ezpoll/admin/polledit.php" );
        }
        else if( ( $url_array[3] == "update" ) )
        {
            $action = "Update";
            $pollID = $url_array[4];
            include( "kernel/ezpoll/admin/polledit.php" );
        }
        break;
}

?>