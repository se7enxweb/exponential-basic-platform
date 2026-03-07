<?php
//
// $Id: datasupplier.php 8099 2001-10-30 17:35:04Z fh $
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
if( eZPermission::checkPermission( $user, "eZBulkMail", "ModuleEdit" ) == false )
{
    eZHTTPTool::header( "Location: /error/403" );
    exit();
}

// Explicit POST/GET extraction — replaces the kernel register_globals hack for this module.
$addresses                 = eZHTTPTool::getVar( 'Addresses' );
$bulkMailArrayID           = eZHTTPTool::getVar( 'BulkMailArrayID' ) ?? [];
$cancel                    = eZHTTPTool::getVar( 'Cancel' );
$categoryArrayID           = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$categoryID                = eZHTTPTool::getVar( 'CategoryID' );
$delete                    = eZHTTPTool::getVar( 'Delete' );
$description               = eZHTTPTool::getVar( 'Description' );
$edit                      = eZHTTPTool::getVar( 'Edit' );
$editButton                = eZHTTPTool::getVar( 'EditButton' );
$footer                    = eZHTTPTool::getVar( 'Footer' );
$from                      = eZHTTPTool::getVar( 'From' );
$fromName                  = eZHTTPTool::getVar( 'FromName' );
$header                    = eZHTTPTool::getVar( 'Header' );
$listID                    = eZHTTPTool::getVar( 'ListID' );
$mailArrayID               = eZHTTPTool::getVar( 'MailArrayID' ) ?? [];
$mailBody                  = eZHTTPTool::getVar( 'MailBody' );
$mailID                    = eZHTTPTool::getVar( 'MailID' );
$name                      = eZHTTPTool::getVar( 'Name' );
$new                       = eZHTTPTool::getVar( 'New' );
$ok                        = eZHTTPTool::getVar( 'OK' ) ?? eZHTTPTool::getVar( 'Ok' );
$offset                    = eZHTTPTool::getVar( 'Offset' );
$preview                   = eZHTTPTool::getVar( 'Preview' );
$publicList                = eZHTTPTool::getVar( 'PublicList' );
$save                      = eZHTTPTool::getVar( 'Save' );
$send                      = eZHTTPTool::getVar( 'Send' );
$sendButton                = eZHTTPTool::getVar( 'SendButton' );
$sendMail                  = eZHTTPTool::getVar( 'SendMail' );
$singleListID              = eZHTTPTool::getVar( 'SingleListID' );
$subject                   = eZHTTPTool::getVar( 'Subject' );
$subscriptionGroupsArrayID = eZHTTPTool::getVar( 'SubscriptionGroupsArrayID' ) ?? [];
$templateArrayID           = eZHTTPTool::getVar( 'TemplateArrayID' ) ?? [];
$templateID                = eZHTTPTool::getVar( 'TemplateID' );
// URL-routing variables are set below inside the switch and override the above POST defaults.

switch ( $url_array[2] )
{
    case "categorylist":
    {
        $categoryID = $url_array[3];
        $offset = $url_array[4];
        if( $offset == "" )
            $offset = 0;
        include( "kernel/ezbulkmail/admin/categorylist.php" );
    }
    break;

    case "categoryedit" :
    {
        $categoryID = $url_array[3];
        if( !is_numeric( $categoryID ) )
            $categoryID = 0;
        include( "kernel/ezbulkmail/admin/categoryedit.php" );
    }
    break;

    case "templatelist" :
    {
        include( "kernel/ezbulkmail/admin/templatelist.php" );
    }
    break;

    case "templateedit" :
    {
        $templateID = $url_array[3];
        if( !is_numeric( $templateID ) )
            $templateID = 0;
        include( "kernel/ezbulkmail/admin/templateedit.php" );
    }
    break;

    case "mailedit" :
    {
        $mailID = $url_array[3];
        if( !is_numeric( $mailID ) )
            $mailID = 0;
        include( "kernel/ezbulkmail/admin/mailedit.php" );
    }
    break;

    case "drafts" :
    {
        include( "kernel/ezbulkmail/admin/maillist.php" );
    }
    break;

    case "send" :
        $sendButton = true;
    case "preview" :
        $editButton = true;
    case "view" :
    {
        $mailID = $url_array[3];
        if( !is_numeric( $mailID ) )
        {
            eZHTTPTool::header( "Location: /error/404" );
            exit();
        }
        include( "kernel/ezbulkmail/admin/mailview.php" );
    }
    break;


    case "masssubscribe":
    {
        if ( $ini->variable( "eZBulkMailMain", "UseEZUser" ) == "enabled" )
        {
            eZHTTPTool::header( "Location: /error/404" );
            exit();
        }
        include( "kernel/ezbulkmail/admin/masssubscribe.php" );
    }
    break;

    case "userlist":
    {
        $categoryID = $url_array[3];
        if( !is_numeric( $categoryID ) )
            $categoryID = 0;
        include( "kernel/ezbulkmail/admin/userlist.php" );
    }
    break;

    default:
    {
        eZHTTPTool::header( "Location: /error/404" );
        exit();
    }
    break;
}

?>