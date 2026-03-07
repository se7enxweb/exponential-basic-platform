<?php
//
// $Id: messageform.php 9553 2002-05-22 11:24:54Z jhe $
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

// include_once( "classes/ezlocale.php" );
// include_once( "classes/eztexttool.php" );

$ini = eZINI::instance( 'site.ini' );
$allowHTML = $ini->variable( "eZForumMain", "AllowHTML" );
$language = $ini->variable( "eZForumMain", "Language" );
$allowedTags = $ini->variable( "eZForumMain", "AllowedTags" );

$author = eZUser::currentUser();

$locale = new eZLocale( $language );

if ( isset( $showMessageForm ) && $showMessageForm )
{
    if ( isset( $showVisibleMessageForm ) && $showVisibleMessageForm )
    {
        $t->set_file( "form", "messageform.tpl"  );
        $t->set_block( "form", "author_field_tpl", "author_field" );
        $t->set_block( "author_field_tpl", "author_logged_in_tpl", "author_logged_in" );
        $t->set_block( "author_field_tpl", "author_not_logged_in_tpl", "author_not_logged_in" );

        $t->set_block( "form", "message_body_info_tpl", "message_body_info_item" );
        $t->set_block( "form", "message_reply_info_tpl", "message_reply_info_item" );
        $t->set_block( "form", "message_notice_checkbox_tpl", "message_notice_checkbox" );
        $t->set_var( "message_body_info_item", "" );
        $t->set_var( "message_reply_info_item", "" );
        $t->set_var( "message_notice_checkbox", "" );
	    $t->set_var( "allowed_tags", htmlspecialchars( $allowedTags ) );  
        $t->set_var( "headline", $t->Ini->variable( "strings", $action . "_headline" ) );
    }

    if ( isset( $showHiddenMessageForm ) && $showHiddenMessageForm )
    {
        $t->set_file( "hidden_form", "messagehiddenform.tpl" );
    }

    if (( $showBodyInfo ) && ( $allowHTML ))
        $t->parse( "message_body_info", "message_body_info_tpl" );

    if (  isset( $bodyInfo ) && $bodyInfo )
    {
        $t->parse( "message_body_info_item", "message_body_info_tpl" );
    }

    if (  isset( $showVisibleMessageForm ) && $showVisibleMessageForm && is_a( eZUser::currentUser(), "eZUser" ) )
    {
        $t->parse( "message_notice_checkbox", "message_notice_checkbox_tpl" );
    }

    if ( isset( $replyInfo ) && $replyInfo )
    {
        $t->parse( "message_reply_info_item", "message_reply_info_tpl" );
    }

    if ( isset( $error ) && $error )
    {
        $messageTopic = $newMessageTopic;
        $messageBody = $newMessageBody;

        $t->set_block( "errors_tpl", "error_missing_body_item_tpl", "error_missing_body_item" );
        $t->set_block( "errors_tpl", "error_missing_topic_item_tpl", "error_missing_topic_item" );

        if ( empty( $newMessageTopic ) )
        {
            $t->parse( "error_missing_topic_item", "error_missing_topic_item_tpl" );
        }
        else
        {
            $t->set_var( "error_missing_topic_item", "" );
        }

        if ( empty( $newMessageBody ) )
        {
            $t->parse( "error_missing_body_item", "error_missing_body_item_tpl" );
        }
        else
        {
            $t->set_var( "error_missing_body_item", "" );
        }

        $t->parse( "errors_item", "errors_tpl" );
    }

    if ( isset( $showEmptyMessageForm ) && $showEmptyMessageForm === true )
    {
        if ( !is_object( $msg ) )
        {
            $msg = new eZForumMessage( $messageID );
            $msg->setIsTemporary( true );
        }

        if ( isset( $newMessageTopic ) )
        {
            $messageTopic = $newMessageTopic;
        }
        else
        {
            $messageTopic = $msg->topic();
        }

        if ( isset( $newMessageBody ) )
        {
            $messageBody = $newMessageBody;
        }
        else
        {
            $messageBody = $msg->body();
        }

        $messageNotice = $msg->emailNotice();
        $forumID = $msg->forumId();

        if ( !$msg->isTemporary() && $action != "reply" )
        {
            $messagePostedAt = $locale->format( $msg->postingTime() );
        }
        else
        {
            $messagePostedAt = $newMessagePostedAt;
        }

        if ( isset( $newMessageNotice ) )
        {
            $messageNotice = $newMessageNotice;
        }
    }
    else
    {
        if ( isset( $newMessageAuthor ) )
        {
            $messageAuthor = $newMessageAuthor;
        }
        else
        {
            if ( $msg->userName() != "" && $action != "reply" )
            {
                $messageAuthor = $msg->userName();
            }
            else if ( !is_object( $author ) )
            {
                $author = eZUser::currentUser();
            }
        }
        if ( $msg->isTemporary() )
        {
            $messagePostedAt = $newMessagePostedAt;
        }
        else
        {
            $messagePostedAt = $locale->format( $msg->postingTime() );
        }
    }

    if ( is_object( $author ) && $author->id() > 0 )
    {
        $messageAuthor = $author->firstName() . " " . $author->lastName();
    }
    else if ( $msg->userName() != "" && $action != "reply" )
    {
        $messageAuthor = $msg->userName();
    }
    else
    {
        $messageAuthor = $ini->variable( "eZForumMain", "AnonymousPoster" );
    }

    switch ( $messageNotice )
    {
        case "on":
        case "y":
        case "checked":
        case 1:
        case true:
        {
            $messageNoticeText = $t->Ini->variable( "strings", "notice_yes" );
            $messageNotice = "checked";
            $newMessageNotice = "checked";
        }
        break;

        case "off":
        case "n":
        case "unchecked":
        case 0:
        case false:
        {
            $messageNoticeText = $t->Ini->variable( "strings", "notice_no" );
            $messageNotice = "";
            $newMessageNotice = "";
        }
        break;
    }

    $quote = "/". chr( 34 ) . "/";

    if( !is_null( $messageTopic ) )
    $messageTopic = preg_replace( $quote, "&#034;", $messageTopic );

    if( !is_null( $messageBody ) )
    $messageBody = preg_replace( $quote, "&#034;", $messageBody );

    // include_once( "classes/eztexttool.php" );

    $t->set_var( "message_topic", isset( $messageTopic ) ? $messageTopic : false );
    $t->set_var( "new_message_topic", isset( $newMessageTopic ) ? $newMessageTopic : false );
    $t->set_var( "message_body", isset( $messageBody ) ? $messageBody : false );
    $t->set_var( "new_message_body", isset( $newMessageBody ) ? $newMessageBody : false );
    $t->set_var( "message_posted_at", isset( $messagePostedAt ) ? $messagePostedAt : false );
    $t->set_var( "message_author", isset( $messageAuthor ) ? $messageAuthor : false );
    $t->set_var( "message_id", isset( $messageID ) ? $messageID : false );
    $t->set_var( "message_notice_text", isset( $messageNoticeText ) ? $messageNoticeText : false );
    $t->set_var( "message_notice", isset( $messageNotice ) ? $messageNotice : false );
    $t->set_var( "new_message_notice", isset( $newMessageNotice ) ? $newMessageNotice : false );

    $t->set_var( "reply_to_id", isset( $replyToID ) ? $replyToID : false );
    $t->set_var( "preview_id", isset( $previewID ) ? $previewID : false );
    $t->set_var( "original_id", isset( $originalID ) ? $originalID : false );

    $t->set_var( "forum_id", isset( $forumID ) ? $forumID : false );

    $t->set_var( "redirect_url", eZTextTool::htmlspecialchars( isset( $redirectURL ) ? $redirectURL : false ) );
    $t->set_var( "end_action", isset( $endAction ) ? $endAction : false );
    $t->set_var( "start_action", isset( $startAction ) ? $startAction : false );
    $t->set_var( "action_value", isset( $actionValue ) ? $actionValue : false );


    $allowedTags = $ini->variable( "eZForumMain", "AllowedTags" );
    $t->set_var( "allowed_tags", htmlspecialchars( $allowedTags ) );

    if ( $showVisibleMessageForm )
    {
        if ( is_object( $author ) && $author->id() > 0 )
        {
            $t->parse( "author_field", "author_logged_in_tpl" );
        }
        else
        {
            $t->parse( "author_field", "author_not_logged_in_tpl" );
        }
    }

    if ( $showHiddenMessageForm )
    {
        if ( isset( $doPrint ) )
        {
            $t->pparse( "message_hidden_form_file", "hidden_form" );
        }
        else
        {
            $t->parse( "message_hidden_form_file", "hidden_form" );
        }
    }

    if ( $showVisibleMessageForm )
    {
        if ( isset( $doPrint ) )
        {
            $t->parse( "message_form_file", "form" );
        }
        else
        {
            $t->parse( "message_form_file", "form" );
        }
    }
}
else
{
    $t->parse( "message_form_file", "" );
    $t->parse( "message_hidden_form_file", "" );
}

?>