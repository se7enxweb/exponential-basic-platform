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
if( eZPermission::checkPermission( $user, "eZBug", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

// Explicit POST/GET extraction — replaces the kernel register_globals hack for this module.
$action           = eZHTTPTool::getVar( 'Action' );
$addCategory      = eZHTTPTool::getVar( 'AddCategory' );
$addPriority      = eZHTTPTool::getVar( 'AddPriority' );
$addStatus        = eZHTTPTool::getVar( 'AddStatus' );
$bugArrayID       = eZHTTPTool::getVar( 'BugArrayID' ) ?? [];
$bugID            = eZHTTPTool::getVar( 'BugID' );
$cancel           = eZHTTPTool::getVar( 'Cancel' );
$categoryArrayID  = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$categoryID       = eZHTTPTool::getVar( 'CategoryID' );
$categoryName     = eZHTTPTool::getVar( 'CategoryName' );
$delete           = eZHTTPTool::getVar( 'Delete' );
$deleteCategories = eZHTTPTool::getVar( 'DeleteCategories' );
$deletePriorities = eZHTTPTool::getVar( 'DeletePriorities' );
$deleteSelected   = eZHTTPTool::getVar( 'DeleteSelected' );
$deleteStatus     = eZHTTPTool::getVar( 'DeleteStatus' );
$description      = eZHTTPTool::getVar( 'Description' );
$fileArrayID      = eZHTTPTool::getVar( 'FileArrayID' ) ?? [];
$fileID           = eZHTTPTool::getVar( 'FileID' );
$globalSiteIni    = eZHTTPTool::getVar( 'GlobalSiteIni' );
$imageArrayID     = eZHTTPTool::getVar( 'ImageArrayID' ) ?? [];
$imageID          = eZHTTPTool::getVar( 'ImageID' );
$insertFile       = eZHTTPTool::getVar( 'InsertFile' );
$insertImage      = eZHTTPTool::getVar( 'InsertImage' );
$isClosed         = eZHTTPTool::getVar( 'IsClosed' );
$isPrivate        = eZHTTPTool::getVar( 'IsPrivate' );
$logMessage       = eZHTTPTool::getVar( 'LogMessage' );
$mailReplyTo      = eZHTTPTool::getVar( 'MailReplyTo' );
$mailReporter     = eZHTTPTool::getVar( 'MailReporter' );
$moduleArrayID    = eZHTTPTool::getVar( 'ModuleArrayID' ) ?? [];
$moduleID         = eZHTTPTool::getVar( 'ModuleID' );
$name             = eZHTTPTool::getVar( 'Name' );
$ok               = eZHTTPTool::getVar( 'Ok' );
$ownerID          = eZHTTPTool::getVar( 'OwnerID' );
$parentID         = eZHTTPTool::getVar( 'ParentID' );
$priorityArrayID  = eZHTTPTool::getVar( 'PriorityArrayID' ) ?? [];
$priorityID       = eZHTTPTool::getVar( 'PriorityID' );
$priorityName     = eZHTTPTool::getVar( 'PriorityName' );
$recursive        = eZHTTPTool::getVar( 'Recursive' );
$searchText       = eZHTTPTool::getVar( 'SearchText' );
$statusArrayID    = eZHTTPTool::getVar( 'StatusArrayID' ) ?? [];
$statusID         = eZHTTPTool::getVar( 'StatusID' );
$statusName       = eZHTTPTool::getVar( 'StatusName' );
$update           = eZHTTPTool::getVar( 'Update' );
$write            = eZHTTPTool::getVar( 'Write' );
$writeGroupArrayID = eZHTTPTool::getVar( 'WriteGroupArrayID' ) ?? [];
// URL-routing variables are set below inside the switch and override the above POST defaults.

switch ( $url_array[2] )
{
    case "archive" :        
    {
        $moduleID = $url_array[3];
        $action = "";
        include( "kernel/ezbug/admin/buglist.php" );
    }
    break;

    case "search" :        
    {
        include( "kernel/ezbug/admin/search.php" );
    }
    break;
    
    case "bugpreview" :
    case "view" :        
    {
        $bugID = $url_array[3];
        
        include( "kernel/ezbug/user/bugview.php" );
    }
    break;
    
    case "unhandled" :
    {
        include( "kernel/ezbug/admin/unhandledbugs.php" );
    }
    break;

    case "priority" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                include( "kernel/ezbug/admin/prioritylist.php" );
            }
            break;
        }
    }
    break;

    case "category" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                include( "kernel/ezbug/admin/categorylist.php" );
            }
            break;
        }
    }
    break;

    case "module" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                if( isset( $addModule ) )  // new
                {
                    $action = "new";
                    $parentID = $url_array[4];
                    include( "kernel/ezbug/admin/moduleedit.php" );
                }
                else 
                {
                    $parentID = $url_array[4];
                    include( "kernel/ezbug/admin/modulelist.php" );
                }
            }
            break;

            case "insert":
            {
                $action = "insert";
                include( "kernel/ezbug/admin/moduleedit.php" );
            }
            break;

            case "edit":
            {
                $action = "edit";
                $moduleID = $url_array[4];
                include( "kernel/ezbug/admin/moduleedit.php" );
            }
            break;

            case "update":
            {
                $action = "update";
                $moduleID = $url_array[4];
                include( "kernel/ezbug/admin/moduleedit.php" );
            }
            break;

            case "delete":
            {
                $action = "delete";
                $moduleID = $url_array[4];
                include( "kernel/ezbug/admin/moduleedit.php" );
            }
            break;

        }
    }
    break;

    case "status" :
    {
        switch( $url_array[3] )
        {
            case "list":
            {
                $parentID = $url_array[4];
                include( "kernel/ezbug/admin/statuslist.php" );
            }
            break;
        }
    }
    break;

    
    case "edit" :
    {
        if ( $url_array[3] == "new" )
        {
            $action = "New";
        }
        else if ( $url_array[3] == "edit" )
        {
            $action = "Edit";
            $bugID = $url_array[4];
        }
        else if( $url_array[3] == "fileedit" )
        {
            switch( $url_array[4] )
            {
                case  "new" :
                {
                    $action = "New";
                    $bugID = $url_array[5];
                    include( "kernel/ezbug/admin/fileedit.php" );
                }
                break;
                case  "edit" :
                {
                    $action = "Edit";
                    $bugID = $url_array[6];
                    $fileID = $url_array[5];
                    include( "kernel/ezbug/admin/fileedit.php" );
                }
                break;
                case "delete" :
                {
                    $action = "Delete";
                    $bugID = $url_array[6];
                    $fileID = $url_array[5];
                    include( "kernel/ezbug/admin/fileedit.php" );
                }
                break;
                default :
                {
                    include( "kernel/ezbug/admin/fileedit.php" );
                }
                break;
            }
        }
        else if( $url_array[3] == "imageedit" )
        {
            switch( $url_array[4] )
            {
                case "new":
                {
                    $action = "New";
                    $bugID = $url_array[5];
                    include( "kernel/ezbug/admin/imageedit.php" );
                }
                break;
                case "edit" :
                {
                    $action = "Edit";
                    $bugID = $url_array[6];
                    $imageID = $url_array[5];
                    include( "kernel/ezbug/admin/imageedit.php" );
                }
                break;
                case "delete" :
                {
                    $action = "Delete";
                    $bugID = $url_array[6];
                    $imageID = $url_array[5];
                    include( "kernel/ezbug/admin/imageedit.php" );
                }
                break;
                default :
                {
                    include( "kernel/ezbug/admin/imageedit.php" );
                }
                break;
            }
        }
        include( "kernel/ezbug/admin/bugedit.php" );
    }
    break;


    case "report" :
    {
        switch( $url_array[3] )
        {
            case "fileedit" :
            {
                if( $url_array[4] == "new")
                {
                    $action = "New";
                    $bugID = $url_array[5];
                    include( "kernel/ezbug/user/fileedit.php" );
                }
                else if( $url_array[4] == "edit" )
                {
                    $action = "Edit";
                    $bugID = $url_array[6];
                    $fileID = $url_array[5];
                    include( "kernel/ezbug/user/fileedit.php" );
                }
                else if( $url_array[4] == "delete" )
                {
                    $action = "Delete";
                    $bugID = $url_array[6];
                    $fileID = $url_array[5];
                    include( "kernel/ezbug/user/fileedit.php" );
                }
                else
                {
                    include( "kernel/ezbug/user/fileedit.php" );
                }
            }
            break;
            case "imageedit" :
            {
                if( $url_array[4] == "new")
                {
                    $action = "New";
                    $bugID = $url_array[5];
                    include( "kernel/ezbug/user/imageedit.php" );
                }
                else if( $url_array[4] == "edit" )
                {
                    $action = "Edit";
                    $bugID = $url_array[6];
                    $imageID = $url_array[5];
                    include( "kernel/ezbug/user/imageedit.php" );
                }
                else if( $url_array[4] == "delete" )
                {
                    $action = "Delete";
                    $bugID = $url_array[6];
                    $imageID = $url_array[5];
                    include( "kernel/ezbug/user/imageedit.php" );
                }
                else
                {
                    include( "kernel/ezbug/user/imageedit.php" );
                }
            }
            break;

            case "edit" :
            {
                $bugID = $url_array[4];
                $action = "Edit";
                include( "kernel/ezbug/admin/bugedit.php" );
            }
            break;

            
            default :
            {
                print( "Error: Bug file not found" );
            }
            break;
        }
    }
    break;
}

?>