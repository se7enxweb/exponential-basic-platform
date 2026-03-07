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


// include_once( "classes/ezhttptool.php" );
// include_once( "ezuser/classes/ezpermission.php" );

$user = eZUser::currentUser();
if( eZPermission::checkPermission( $user, "eZSiteManager", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

$url_array = explode( "/", $_SERVER['REQUEST_URI'] );

$action                 = eZHTTPTool::getVar( 'Action' );
$altID                  = eZHTTPTool::getVar( 'AltID' );
$alternativeArrayID     = eZHTTPTool::getVar( 'AlternativeArrayID' ) ?? [];
$alternativeArrayName   = eZHTTPTool::getVar( 'AlternativeArrayName' ) ?? [];
$alternativeDeleteArray = eZHTTPTool::getVar( 'AlternativeDeleteArray' ) ?? [];
$alternativeID          = eZHTTPTool::getVar( 'AlternativeID' );
$cancel                 = eZHTTPTool::getVar( 'Cancel' );
$delete                 = eZHTTPTool::getVar( 'Delete' );
$deleteQuestionArray    = eZHTTPTool::getVar( 'DeleteQuestionArray' ) ?? [];
$deleteQuestions        = eZHTTPTool::getVar( 'DeleteQuestions' );
$description            = eZHTTPTool::getVar( 'Description' );
$gameArrayID            = eZHTTPTool::getVar( 'GameArrayID' ) ?? [];
$gameID                 = eZHTTPTool::getVar( 'GameID' );
$isCorrect              = eZHTTPTool::getVar( 'IsCorrect' );
$limit                  = eZHTTPTool::getVar( 'Limit' );
$name                   = eZHTTPTool::getVar( 'Name' );
$newAlternative         = eZHTTPTool::getVar( 'NewAlternative' );
$newQuestion            = eZHTTPTool::getVar( 'NewQuestion' );
$ok                     = eZHTTPTool::getVar( 'OK' );
$offset                 = eZHTTPTool::getVar( 'Offset' );
$quest                  = eZHTTPTool::getVar( 'Quest' );
$questionArrayID        = eZHTTPTool::getVar( 'QuestionArrayID' ) ?? [];
$questionArrayName      = eZHTTPTool::getVar( 'QuestionArrayName' ) ?? [];
$questionID             = eZHTTPTool::getVar( 'QuestionID' );
$startDay               = eZHTTPTool::getVar( 'StartDay' );
$startMonth             = eZHTTPTool::getVar( 'StartMonth' );
$startYear              = eZHTTPTool::getVar( 'StartYear' );
$stopDay                = eZHTTPTool::getVar( 'StopDay' );
$stopMonth              = eZHTTPTool::getVar( 'StopMonth' );
$stopYear               = eZHTTPTool::getVar( 'StopYear' );

switch ( $url_array[2] )
{
    case "game":
    {
        switch ( $url_array[3] )
        {
            case "list":
            {
                if ( $url_array[4] == "parent" )
                    $offset = $url_array[5];
                include( "kernel/ezquiz/admin/gamelist.php" );
            }
            break;
            
            case "edit":
            {
                if ( isset( $url_array[4] ) && is_numeric( $url_array[4] ) )
                {
                    $gameID = $url_array[4];
                    $action = "edit";
                }
                else
                {
                    $gameID = false;
                }

                include ( "kernel/ezquiz/admin/gameedit.php" );
            }
            break;
            case "new":
            case "delete":
            case "update":
            case "insert":
            {
                if ( isset( $url_array[4] ) && is_numeric( $url_array[4] ) )
                {
                    $gameID = $url_array[4];
                }
                else
                {
                    $gameID = false;
                    $action = "New";
                }

                include ( "kernel/ezquiz/admin/gameedit.php" );
            }
            break;

            case "questionedit":
            {
                if ( is_numeric( $url_array[4] ) )
                    $questionID = $url_array[4];
                include ( "kernel/ezquiz/admin/questionedit.php" );
            }
            break;
        }
        break;

    }
    break;

    default :
    {
        eZHTTPTool::header( "Location: /error/404" );
        exit();
    }
    break;
}

?>