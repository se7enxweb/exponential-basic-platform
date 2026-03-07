<?php
//
// $Id: datasupplier.php 7420 2001-09-24 11:53:43Z jhe $
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
?>


<?php
$url_array = explode( "/", $_SERVER['REQUEST_URI'] );

// include_once( "ezuser/classes/ezpermission.php" );
// include_once( "classes/ezhttptool.php" );
$user = eZUser::currentUser();
if ( eZPermission::checkPermission( $user, "eZForum", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

// Explicit POST/GET extraction — replaces the kernel register_globals hack for this module.
$action           = eZHTTPTool::getVar( 'Action' );
$actionValueArray = eZHTTPTool::getVar( 'ActionValueArray' ) ?? [];
$allowHTML        = eZHTTPTool::getVar( 'AllowHTML' );
$anonymousPoster  = eZHTTPTool::getVar( 'AnonymousPoster' );
$body             = eZHTTPTool::getVar( 'Body' );
$categoryArrayID  = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$categoryID       = eZHTTPTool::getVar( 'CategoryID' );
$categorySelectID = eZHTTPTool::getVar( 'CategorySelectID' );
$deleteCategories = eZHTTPTool::getVar( 'DeleteCategories' );
$deleteForums     = eZHTTPTool::getVar( 'DeleteForums' );
$deleteMessages   = eZHTTPTool::getVar( 'DeleteMessages' );
$description      = eZHTTPTool::getVar( 'Description' );
$forumArrayID     = eZHTTPTool::getVar( 'ForumArrayID' ) ?? [];
$forumID          = eZHTTPTool::getVar( 'ForumID' );
$generateStaticPage = eZHTTPTool::getVar( 'GenerateStaticPage' );
$isAnonymous      = eZHTTPTool::getVar( 'IsAnonymous' );
$isModerated      = eZHTTPTool::getVar( 'IsModerated' );
$limit            = eZHTTPTool::getVar( 'Limit' );
$messageArrayID   = eZHTTPTool::getVar( 'MessageArrayID' ) ?? [];
$messageAuthor    = eZHTTPTool::getVar( 'MessageAuthor' );
$messageID        = eZHTTPTool::getVar( 'MessageID' );
$name             = eZHTTPTool::getVar( 'Name' );
$offset           = eZHTTPTool::getVar( 'Offset' );
$queryString      = eZHTTPTool::getVar( 'QueryString' );
$refererURL       = eZHTTPTool::getVar( 'RefererURL' );
$rejectReason     = eZHTTPTool::getVar( 'RejectReason' );
$sectionID        = eZHTTPTool::getVar( 'SectionID' );
$startAction      = eZHTTPTool::getVar( 'StartAction' );
$topic            = eZHTTPTool::getVar( 'Topic' );
$unapprovdLimit   = eZHTTPTool::getVar( 'UnapprovdLimit' );
// URL-routing variables are set below inside the switch and override the above POST defaults.

switch ( $url_array[2] )
{
    case "forumlist":
    {
        $categoryID = $url_array[3];
        include( "kernel/ezforum/admin/forumlist.php" );
    }
    break;

    case "unapprovedlist":
    {
        if ( $url_array[3] == "parent" )
            $offset = $url_array[4];
        else
            $offset = 0;
        include( "kernel/ezforum/admin/unapprovedlist.php" );
    }
    break;
    case "unapprovededit":
    {
        include( "kernel/ezforum/admin/unapprovededit.php" );
    }
    break;

    
    case "messagelist":
    {
        $forumID = $url_array[3];

        if ( isset( $url_array[5] ) && isset( $url_array[4] ) && $url_array[4] == "parent" )
            $offset = $url_array[5];
        else
            $offset = 0;

        include( "kernel/ezforum/admin/messagelist.php" );
    }
    break;

    case "search" :
    {
        if ( $url_array[3] == "parent" )
        {
            $queryString = urldecode( $url_array[4] );
            $offset = $url_array[5];
            if  ( !is_numeric( $offset ) )
                $offset = 0;
        }
        include( "kernel/ezforum/admin/search.php" );
    }
    break;


    case "message":
    {
        $messageID = $url_array[3];
        include( "kernel/ezforum/admin/message.php" );
    }
    break;

    case "messageedit":
    {
        if ( $url_array[3] == "edit" )
        {
            $action = "edit";
            $messageID = $url_array[4];
            include( "kernel/ezforum/admin/messageedit.php" );
        }
        if ( $url_array[3] == "update" )
        {
            $action = "update";
            $messageID = $url_array[4];
            include( "kernel/ezforum/admin/messageedit.php" );
        }
        if ( $url_array[3] == "delete" )
        {
            $action = "delete";
            $messageID = $url_array[4];
            include( "kernel/ezforum/admin/messageedit.php" );
        }
    }
    break;
    case "forumedit":
    {
        if ( $url_array[3] == "new" )
        {
            $action = "new";
            include( "kernel/ezforum/admin/forumedit.php" );
        }

        if ( $url_array[3] == "insert" )
        {
            $action = "insert";
            include( "kernel/ezforum/admin/forumedit.php" );
        }

        if ( $url_array[3] == "edit" )
        {
            $action = "edit";
            $forumID = $url_array[4];
            include( "kernel/ezforum/admin/forumedit.php" );
        }
        if ( $url_array[3] == "update" )
        {
            
            $action = "update";
            $forumID = $url_array[4];
            include( "kernel/ezforum/admin/forumedit.php" );
        }
        if ( $url_array[3] == "delete" )
        {
            $action = "delete";
            $forumID = $url_array[4];
            include( "kernel/ezforum/admin/forumedit.php" );
        }
    }
    break;

    case "categoryedit":
    {
        if ( $url_array[3] == "new" )
        {
            $action = "new";
            $categoryID = false;
            $sectionID = false;
            include( "kernel/ezforum/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "insert" )
        {
            $action = "insert";
            include( "kernel/ezforum/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "edit" )
        {
            $action = "edit";
            $categoryID = $url_array[4];
            include( "kernel/ezforum/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "update" )
        {
            $action = "update";
            $categoryID = $url_array[4];
            include( "kernel/ezforum/admin/categoryedit.php" );
        }
        if ( $url_array[3] == "delete" )
        {
            $action = "delete";
            $categoryID = $url_array[4];
            include( "kernel/ezforum/admin/categoryedit.php" );
        }
    }
    break;

    case "categorylist" :
    {
        include( "kernel/ezforum/admin/categorylist.php" );
    }
    break;
    case "norights":
    {
        include( "kernel/ezforum/admin/norights.php" );
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