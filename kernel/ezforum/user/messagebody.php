<?php
// 
// $Id: messagebody.php 9898 2004-07-08 12:57:16Z br $
//
// Created on: <21-Feb-2001 18:00:00 pkej>
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

// include_once( "classes/INIFile.php" );
// include_once( "classes/ezlocale.php" );

$ini = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZCalendarMain", "Language" );
$locale = new eZLocale( $Language );

if ( isset( $showMessage ) && $showMessage )
{
    // include_once( "classes/eztexttool.php" );
    $allowedTags = $ini->variable( "eZForumMain", "AllowedTags" );
    $allowHTML = $ini->variable( "eZForumMain", "AllowHTML" );
    
    $t->set_file( "body", "messagebody.tpl" );
    
    $msg = new eZForumMessage( $messageID );

    if( !isset( $messageTopic ) )
    $messageTopic = $msg->topic();


    $messageBody = eZTextTool::nl2br( $msg->body() );
    $messageBody = stripslashes($messageBody);
    
    $author = new eZUser( $msg->userID() );
    $messageNotice = $msg->emailNotice();

    if ( isset( $newMessageAuthor ) )
    {
        if ( $msg->userName() && $action != "reply" )
            $messageAuthor = $msg->userName();
        else
            $messageAuthor = $newMessageAuthor;
    }
    else
    {
        if ( !is_object( $author ) )
        {
            $author = new eZUser( $msg->userId() );
        }

        if ( $author->id() == 0 )
        {
            if ( $msg->userName() && $action != "reply" )
                $messageAuthor = $msg->userName();
            else
                $messageAuthor = $ini->variable( "eZForumMain", "AnonymousPoster" );
        }
        else
        {
            $messageAuthor = $author->firstName() . " " . $author->lastName();
        }
    }

    if ( isset( $newMessagePostedAt ) )
    {
        $messagePostedAt = $newMessagePostedAt;
    }
    else
    {
        $messagePostedAt = $locale->format( $msg->postingTime() );
    }

    if ( isset( $newMessageNotice ) )
    {
        $messageNotice = $newMessageNotice;
    }

    switch ( $messageNotice )
    {
        case "on":
        case "y":
        case "checked":
        case 1:
        case true:
        {
            $t->Ini->variable( "strings", "notice_yes" );
        }
        break;

        case "off":
        case "n":
        case "unchecked":
        case 0:
        case false:
        {
            $t->Ini->variable( "strings", "notice_no" );
        }
        break;
    }
    $t->set_var( "message_topic", htmlspecialchars( $messageTopic ) );
    $t->set_var( "message_body", eZTextTool::nl2br( htmlspecialchars( $messageBody ) ) );
    $t->set_var( "message_posted_at", $messagePostedAt );
    $t->set_var( "message_author", htmlspecialchars( $messageAuthor ) );
    $t->set_var( "message_id", $messageID );
    $t->set_var( "message_notice", $messageNotice );

    if ( isset( $doPrint ) && $doPrint == true )
    {
        $t->pparse( "message_body_file", "body" );
    }
    else
    {
        $t->parse( "message_body_file", "body" );
    }
}
else
{
    $t->set_var( "message_body_file", "" );
}

?>