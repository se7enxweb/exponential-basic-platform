<?php
//
// $Id: messageedit.php 9550 2002-05-21 09:19:02Z jhe $
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
// include_once( "classes/eztemplate.php" );
// include_once( "ezforum/classes/ezforum.php" );
// include_once( "ezforum/classes/ezforummessage.php" );
// include_once( "ezforum/classes/ezforumcategory.php" );

$ini = eZINI::instance( 'site.ini' );

$wwwDir = $ini->WWWDir;
$index = $ini->Index;

$Language = $ini->variable( "eZForumMain", "Language" );
$allowedTags = $ini->variable( "eZForumMain", "AllowedTags" );
$allowHTML = $ini->variable( "eZForumMain", "AllowHTML" );

// Migrate the debug variable into a Module Specific Process DebugOutput Variable
// Module View Data Context Debug Output
$debugMessageEdit = false;
$debugMessagePermissions = false; // Set to true to see the debug output

if ( isset( $editButton ) )
{
    $action = "edit";
}

if ( !empty( $cancelButton ) )
{
    $action = "cancel";
}

if ( !empty( $previewButton ) )
{
    $action = "preview";
}

if ( $action == "preview" )
{
    $newMessageTopic = trim( $newMessageTopic );
    $newMessageBody = $newMessageBody;

    if ( empty( $newMessageTopic ) || empty( $newMessageBody ) )
    {
        $error = true;
        $action = $startAction;
    }
}
// Select which main page we are going to view.

switch ( $action )
{
    case "reply":
    case "new":
    case "edit":
    {
        $t = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                             "kernel/ezforum/user/intl", $Language, "message.php" );

        $t->set_file( "page", "messageedit.tpl"  );
        $t->set_block( "page", "errors_tpl", "errors_item" );
        $t->set_var( "errors_item", "" );
    }
    break;

    case "delete":
    {
        $t = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                             "kernel/ezforum/user/intl", $Language, "message.php" );

        $t->set_file( "page", "messagedelete.tpl"  );
    }
    break;

    case "preview":
    {
        $t = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                             "kernel/ezforum/user/intl", $Language, "message.php" );

        $t->set_file( "page", "messagepreview.tpl"  );
	    $t->set_block( "page", "moderated_tpl", "moderated" );
        $t->set_var( "moderated", "" );
    }
    break;

    /*
    case "preview":
    {
        $t = new eZTemplate( "ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                             "ezforum/user/intl", $Language, "message.php" );

        $t->set_file( "page", "messagepreview.tpl"  );
    }
    break;
    */

    case "completed":
    {
        $t = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                             "kernel/ezforum/user/intl", $Language, "message.php" );

        $t->set_file( "page", "messageposted.tpl"  );
        $t->setAllStrings();
    }
    break;
}


// Any errors?
$error = false;
$errors = false;

$locale = new eZLocale( $Language );

// Do some action!
switch ( $action )
{
    case "dodelete":
    {
        $msg = new eZForumMessage( $messageID );

        $checkMessageID = $messageID;
        $checkForumID = $msg->forumID();

        include( "kernel/ezforum/user/messagepermissions.php" );
        // include_once( "classes/ezhttptool.php" );
        if ( $messageDelete == false )
        {
            eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
        }

        $msg->delete();
        eZHTTPTool::header( "Location: /forum/messagelist/$checkForumID" );

    }
    break;

    case "delete":
    {
        $actionValue = "dodelete";
        $startAction = "delete";
        $endAction = "dodelete";

        $msg = new eZForumMessage( $messageID );

        $checkMessageID = $messageID;
        $checkForumID = $msg->forumID();
        include( "kernel/ezforum/user/messagepermissions.php" );

        // include_once( "classes/ezhttptool.php" );
        if ( !$messageDelete )
        {
            eZHTTPTool::header( "Location: /forum/messageedit/forbidden/?Tried=$action&TriedMessage=$checkMessageID&TriedForum=$checkForumID" );
        }

        $doParse = true;
        $showPath = true;
        $isPreview = false;
        include_once( "kernel/ezforum/user/messagepath.php" );

        $showMessage = true;
        include_once( "kernel/ezforum/user/messagebody.php" );
    }
    break;

    case "completed":
    {
        $msg = new eZForumMessage( $messageID );

        $checkMessageID = $msg->id();
        $checkForumID = $msg->forumID();
        include( "kernel/ezforum/user/messagepermissions.php" );

        // include_once( "classes/ezhttptool.php" );
        if ( isset( $messageEdit ) && $messageEdit == false )
        {
            eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
        }

        // Just tell the geezers that their posting has been sent or queued for moderation.
        // Also inform of any e-mails sent.

        $doParse = true;
        $showPath = true;
        $isPreview = false;
        include_once( "kernel/ezforum/user/messagepath.php" );

        $showMessage = true;
        include_once( "kernel/ezforum/user/messagebody.php" );
    }
    break;

    case "cancel":
    {
        // If PreviewID is set then we need to delete the object.
        // Since all objects are smart enough to not generate any
        // error messages if we new an empty object and then delete it
        // no ifs are neccessary.
        $msg = new eZForumMessage( $previewID );
        $msg->delete();

        // include_once( "classes/ezhttptool.php" );
        if ( empty( $redirectURL ) )
        {
            if ( empty( $forumID ) )
            {
                eZHTTPTool::header( "Location: /forum/categorylist" );
            }
            else
            {
                eZHTTPTool::header( "Location: /forum/messagelist/$forumID" );
            }
        }
        else
        {
            eZHTTPTool::header( "Location: $redirectURL" );
        }
    }
    break;

    case "insert":
    {
        $actionValue = "completed";
        $msg = new eZForumMessage( $originalID );

        $checkMessageID = $originalID;
        $forumID = $msg->forumID();
        $checkForumID = $forumID;

        include( "kernel/ezforum/user/messagepermissions.php" );

        // include_once( "classes/ezhttptool.php" );

        if ( isset( $forumPost ) && !$forumPost )
        {
            eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
        }

		$linkModules = $ini->variable( "eZForumMain", "LinkModules" );
		$module_array = explode(',', $linkModules );
		unset ($linkModules);
		foreach ( $module_array as $module)
		{
			$moduleSubArray = explode( ':', $module );
			list($module_name, $forum_id) = $moduleSubArray;
			$linkModules[$module_name] = $forum_id;
		}
			
        // echo "<hr>";
		// var_dump($linkModules);
        // echo "<hr>";
        // var_dump($forumID);
        // echo "<hr>";

        $forum = new eZForum( $forumID );
		$messageCount = $forum->messageCount( false, true );
		$categories = $forum->categories( false );

		$count = count ( array_intersect( $linkModules, $categories ) );

        /*		echo "<pre>LinkModules:";
		print_r ( $linkModules );
		echo "Categories:";
		print_r ( $categories );
		echo "Count:";
		print_r ($count);
		echo "<br>Messagecount: ".$messageCount."<br>";
		echo "RedirectURL: ".$HTTP_HOST.$wwwDir.$index.$redirectURL."<br>";
		echo "</pre>"; 
		exit(); */

		if ( (  $count > 0 )
		  && 
			( $messageCount == 0 ) )

		{   
	        $mailTemplateIni = new eZINI( "kernel/ezforum/user/intl/" . $Language . "/message.php.ini", false );
//			$Topic = $mailTemplateIni->variable( "strings", "auto_topic" )
			$body_prefix = $mailTemplateIni->variable( "strings", "auto_body" );

			// graham : the old index.php way (re: update db content please)
			//			$new_body = "<p>".$body_prefix." "."<a href=\"http://".$HTTP_HOST.$wwwDir.$index.$redirectURL."\">".$forum->name()."</a>.<br/></br>".$msg->body();
			$new_body = "<p>".$body_prefix." "."<a href=\"http://".$HTTP_HOST.$redirectURL."\">".$forum->name()."</a>.<br/></br>".$msg->body();
			$msg->setBody( $new_body );
			//$UserName = $ini->variable( "eZForumMain", "AnonymousPoster" );
			//$auto_msg = new eZForumMessage();
	        //$auto_msg->setIsTemporary( false );
    	    //$auto_msg->setForumID( $forumID );
	        //$auto_msg->disableEmailNotice();
    	    //$auto_msg->setUserID( 0 );
        	//$auto_msg->setUserName( $UserName );
			//$auto_msg->setTopic( $forum->name() );
        	//$auto_msg->setBody( $Body );
			//if ( $forum->isModerated() )
            //{
            //    $auto_msg->setIsApproved ( false );
			//}
			//$auto_msg->store();
		}

        $msg->setIsTemporary( false );
        $msg->store();

        if ( $startAction == "reply" )
        {
            include_once( "kernel/ezforum/user/messagereply.php" );
        }
        else
        {
            if ( !is_object( $forum ) )
            {
                $forum = new eZForum( $forumID );
            }
			
            // send mail to admin

            // include_once( "ezmail/classes/ezmail.php" );
            $mail = new eZMail();
            $replyAddress = $ini->variable( "eZForumMain", "ReplyAddress" );

            $locale = new eZLocale( $Language );

            $mailTemplate = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                                            "kernel/ezforum/user/intl", $Language, "mailreply.php" );

            $mailTemplate->set_file( "mailreply", "mailreply.tpl" );
            $mailTemplate->setAllStrings();
            $mailTemplate->set_block( "mailreply", "link_tpl", "link" );

            $author = $msg->user();
            
            $headersInfo = ( getallheaders() );

            if ( $author->id() == 0 )
            {
                $mailTemplate->set_var( "author", $ini->variable( "eZForumMain", "AnonymousPoster" ) );
            }
            else
            {
                $mailTemplate->set_var( "author", $author->firstName() . " " . $author->lastName() );
            }
            $mailTemplate->set_var( "posted_at", $locale->format( $msg->postingTime() ) );

            $subject_line = $mailTemplate->Ini->variable( "strings", "admin_subject" );


            $mailTemplate->set_var( "link_1", "http://" . $headersInfo["Host"] . $wwwDir. $index. "/forum/message/" . $msg->id() );
            $mailTemplate->parse( "link", "link_tpl" );

                $mailTemplate->set_var( "topic", $msg->topic() );
                $mailTemplate->set_var( "body", $msg->body() );
                $forumID = $msg->forumID();
                $forum = new eZForum( $forumID );
                $mailTemplate->set_var( "forum_name", $forum->name() );
                $mailTemplate->set_var( "forum_link", "http://"  . $headersInfo["Host"] . $wwwDir . $index. "/forum/messagelist/" . $forum->id() );
                $mailTemplate->set_var( "link_2", "http://" . $ini->variable( "site", "AdminSiteURL" ) . "/forum/messageedit/edit/" . $msg->id() );
                $mailTemplate->set_var( "intl-info_message_1", $mailTemplate->Ini->variable( "strings", "admin_info_message_1" ) );
                $mailTemplate->set_var( "intl-info_message_2", $mailTemplate->Ini->variable( "strings", "admin_info_message_2" ) );
                $mailTemplate->set_var( "intl-info_message_3", $mailTemplate->Ini->variable( "strings", "admin_info_message_3" ) );
                $mailTemplate->set_var( "intl-info_message_4", $mailTemplate->Ini->variable( "strings", "admin_info_message_4" ) );

                $bodyText = ( $mailTemplate->parse( "dummy", "mailreply" ) );

                $mail->setSubject( $subject_line );
                $mail->setBody( $bodyText );

                $mail->setFrom( $author->email() );
                $mail->setTo( $replyAddress );

                $mail->send();

            // send mail to forum moderator
            $moderator = $forum->moderator();

            if ( is_object( $moderator ) )
            {
                $moderators = eZUserGroup::users( $moderator->id() );

                if ( count( $moderators ) > 0 )
                {
                    foreach ( $moderators as $moderatorItem )
                    {
                        // include_once( "ezmail/classes/ezmail.php" );
                        $mail = new eZMail();

                        $locale = new eZLocale( $Language );

                        $mailTemplate = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                                                       "kernel/ezforum/user/intl", $Language, "mailreply.php" );

                        $mailTemplate->set_file( "mailreply", "mailreply.tpl" );
                        $mailTemplate->setAllStrings();
                        $mailTemplate->set_block( "mailreply", "link_tpl", "link" );
                        $headersInfo = getallheaders();

                        $author = $msg->user();

                        if ( $author->id() == 0 )
                        {
                            if ( $msg->userName() )
                                $mailTemplate->set_var( "author", $msg->userName() );
                            else
                                $mailTemplate->set_var( "author", $ini->variable( "eZForumMain", "AnonymousPoster" ) );
                        }
                        else
                        {
                            $mailTemplate->set_var( "author", $author->firstName() . " " . $author->lastName() );
                        }
                        $mailTemplate->set_var( "posted_at", $locale->format( $msg->postingTime() ) );

                        $subject_line = $mailTemplate->Ini->variable( "strings", "moderator_subject" );

                        $mailTemplate->set_var( "topic", $msg->topic() );
                        $mailTemplate->set_var( "body", $msg->body() );

                        $mailTemplate->set_var( "forum_name", $forum->name() );
                        $mailTemplate->set_var( "forum_link", "http://"  . $headersInfo["Host"] . "/forum/messagelist/" . $forum->id() );

                        if ( $forum->isModerated() )
                        {
                            $mailTemplate->set_var( "link_1", "" );
                            $mailTemplate->set_var( "link", "" );
                        }
                        else
                        {
                            $mailTemplate->set_var( "link_1", "http://" . $headersInfo["Host"] . "/forum/message/" . $msg->id() );
                            $mailTemplate->parse( "link", "link_tpl" );
                        }
                        $mailTemplate->set_var( "link_2", "http://" . $ini->variable( "site", "AdminSiteURL" ) . "/forum/messageedit/edit/" . $msg->id() );
                        $mailTemplate->set_var( "intl-info_message_1", $mailTemplate->Ini->variable( "strings", "moderator_info_message_1" ) );
                        $mailTemplate->set_var( "intl-info_message_2", $mailTemplate->Ini->variable( "strings", "moderator_info_message_2" ) );
                        $mailTemplate->set_var( "intl-info_message_3", $mailTemplate->Ini->variable( "strings", "moderator_info_message_3" ) );
                        $mailTemplate->set_var( "intl-info_message_4", $mailTemplate->Ini->variable( "strings", "moderator_info_message_4" ) );

                        $bodyText = $mailTemplate->parse( "dummy", "mailreply" );

                        $mail->setSubject( $subject_line );
                        $mail->setBody( $bodyText );

                        $mail->setFrom( $moderatorItem->email() );
                        $mail->setTo( $moderatorItem->email() );

                        $mail->send();
                    }
                }
            }

            if ( $forum->isModerated() )
            {
                $msg->setIsApproved ( false );
                $msg->store();
            }
        }

        if ( isset( $redirectURL ) && $redirectURL != "" )
        {
            eZHTTPTool::header( "Location: $redirectURL" );
        }
        else
        {
            eZHTTPTool::header( "Location: /forum/messageedit/$actionValue/$originalID?ReplyToID=$replyToID&ActionStart=$ActionStart" );
        }
        exit();
    }
    break;

    case "update":
    {
        $actionValue = "completed";
        $msg = new eZForumMessage( $originalID );
        $tmpmsg = new eZForumMessage( $previewID );

        $checkMessageID = $originalID;
        $checkForumID = $msg->forumID();
        include( "kernel/ezforum/user/messagepermissions.php" );

        // include_once( "classes/ezhttptool.php" );
        if ( isset( $messageEdit ) && $messageEdit == false )
        {
            eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
        }

        $msg->setTopic( $tmpmsg->topic() );
        $msg->setBody( $tmpmsg->body() );
        $msg->setEmailNotice( $tmpmsg->emailNotice() );

        $msg->store();
        if ( $redirectURL != "" )
        {
            eZHTTPTool::header( "Location: $redirectURL" );
        }
        else
        {
            eZHTTPTool::header( "Location: /forum/messageedit/$actionValue/$originalID?ActionStart=$ActionStart" );
        }
        exit();
    }

    case "new":
    {
        $startAction = "new";
        $endAction = "insert";
        $actionValue = "preview";
        $newMessagePostedAt = htmlspecialchars( $ini->variable( "eZForumMain", "FutureDate" ) );

        $showMessage = false;
        include_once( "kernel/ezforum/user/messagebody.php" );

        $msg = new eZForumMessage();
        $msg->setIsTemporary( true );
        $msg->setForumID( $forumID );

        $checkMessageID = 0;
        $checkForumID = $msg->forumID();
        $messageOwner = true;
        include( "kernel/ezforum/user/messagepermissions.php" );

        if ( !$forumPost )
        {
            // include_once( "classes/ezhttptool.php" );
            eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
        }

        $doParse = true;
        $showPath = true;
        $isPreview = true;
        include_once( "kernel/ezforum/user/messagepath.php" );

        $showMessageForm = true;
        $showEmptyMessageForm = true;
        $showVisibleMessageForm = true;
        $showHiddenMessageForm = true;
        $showReplyInfo = true;
        $showBodyInfo = true;
        include_once( "kernel/ezforum/user/messageform.php" );
        
    }
    break;

    case "edit":
    {
        if ( !isset( $startAction ) )
        {
            $startAction = "edit";
            $endAction = "update";
        }

        unset( $newMessageAuthor );
        unset( $newMessagePostedAt );

        $newMessagePostedAt = htmlspecialchars( $ini->variable( "eZForumMain", "FutureDate" ) );

        $msg = new eZForumMessage( $messageID );

        if ( $msg->id() >= 1 )
        {
            $forumID = $msg->forumID();
        }

        $actionValue = "preview";

        $checkMessageID = $messageID;
        $checkForumID = $forumID;


        include( "kernel/ezforum/user/messagepermissions.php" );

        if ( isset( $messageEdit ) && !$messageEdit && !$error )
        {
            //include_once( "classes/ezhttptool.php" );
            eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
        }


        $doParse = true;
        $showPath = true;
        $isPreview = false;
        include_once( "kernel/ezforum/user/messagepath.php" );

        $showMessageForm = true;
        $showEmptyMessageForm = false;
        $showVisibleMessageForm = true;
        $showHiddenMessageForm = true;
        $showReplyInfo = true;
        $showBodyInfo = true;
        include_once( "kernel/ezforum/user/messageform.php" );

        $doPrint = true;
    }
    break;

    case "reply":
    {
        $startAction = $action;
        $actionValue = "preview";
        $endAction = "insert";

        $messageID = $replyToID;
        $newMessagePostedAt = htmlspecialchars( $ini->variable( "eZForumMain", "FutureDate" ) );
        $replyTags = $ini->variable( "eZForumMain", "ReplyTags" );
        $replyStartTag = $ini->variable( "eZForumMain", "ReplyStartTag" );
        $replyEndTag = $ini->variable( "eZForumMain", "ReplyEndTag" );

        $msg = new eZForumMessage( $messageID );
        $forum = new eZForum( $msg->forumID() );
        $forumID = $forum->id();
        $checkMessageID = $messageID;
        $checkForumID = $forumID;

        include( "kernel/ezforum/user/messagepermissions.php" );

        /* Please clarify this block of code. 
        if ( isset( $messageReply ) && !$messageReply )
        {
            //#// include_once( "classes/ezhttptool.php" );
            //#eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
        }
        */

        if ( $replyTags == "enabled" )
        {
            $newMessageBody = $replyStartTag . "\n" . $msg->body() . "\n" . $replyEndTag;
        }
        else
        {
            // include_once( "classes/eztexttool.php" );
            $newMessageBody = eZTextTool::addPre( $msg->body() );
        }

        $user = eZUser::currentUser();

        $newMessageTopic = $msg->topic();

        $replyPrefix = $ini->variable( "eZForumMain", "ReplyPrefix" );

        if ( !is_null( $newMessageTopic) && !preg_match( "/^$replyPrefix/", $newMessageTopic ) )
        {
            $newMessageTopic = $replyPrefix . $newMessageTopic;
            $messageTopic = $newMessageTopic;
        }

        $doParse = true;
        $showMessage = true;
        include_once( "kernel/ezforum/user/messagebody.php" );

        $showPath = true;
        $isPreview = false;
        include_once( "kernel/ezforum/user/messagepath.php" );

        $showMessageForm = true;
        $showEmptyMessageForm = false;
        $showVisibleMessageForm = true;
        $showHiddenMessageForm = true;
        $showReplyInfo = true;
        $showBodyInfo = true;
        $newMessageAuthor = true;
        include_once( "kernel/ezforum/user/messageform.php" );

        $doPrint = true;
    }
    break;

    case "preview":
    {
        $actionValue = $endAction;
        if ( isset( $error ) && $error == false )
        {
            if ( empty( $previewID ) )
            {
                switch ( $startAction )
                {
                    case "edit":
                    {
                        $msg = new eZForumMessage();
                        $tmpmsg = new eZForumMessage( $messageID );
                        $msg = $tmpmsg->cloneObject();
                    }
                    break;

                    case "reply":
                    {
                        $msg = new eZForumMessage();
                        $tmpmsg = new eZForumMessage( $replyToID );
                        $forumID = $tmpmsg->forumID();
                        $msg->setForumID( $forumID );
                        $msg->setParent( $replyToID );

                        $forum = new eZForum( $forumID );

                        if ( $forum->isModerated() )
                        {
                            $msg->setIsApproved( false );
                        }
                        else
                        {
                            $msg->setIsApproved( true );
                        }
                    }
                    break;

                    case "new":
                    {
                        $msg = new eZForumMessage();
                        $msg->setForumID( $forumID );
                    }
                    break;

                    default:
                    {
                        $msg = new eZForumMessage( $originalID );
                    }
                    break;

                }

                if ( is_object( $user ) )
                {
                    $msg->setUserID( $user->id() );
                }
                else
                {
                    $msg->setUserID( 0 );
                    $msg->setUserName( $authorName );
                }
            }
            else
            {
                $msg = new eZForumMessage( $previewID );
            }

            if ( isset( $newMessageNotice ) && (string)$newMessageNotice == "on" )
            {
                $msg->enableEmailNotice();
            }
            else
            {
                $msg->disableEmailNotice();
            }

            $allowedTags = $ini->variable( "eZForumMain", "AllowedTags" );
            $allowHTML = $ini->variable( "eZForumMain", "AllowHTML" );
            
            if ( isset( $allowHTML ) && (string)$allowHTML == "enabled" )
            {
                $msg->setTopic( $newMessageTopic );
                $msg->setBody( $newMessageBody );
            }
            else
            {
                $msg->setTopic( strip_tags( $newMessageTopic ) );
                $msg->setBody( strip_tags( $newMessageBody, $allowedTags ) );
            }

            $msg->setIsTemporary( true );

            $msg->store();
            $previewID = $msg->id();

            if ( isset( $endAction ) && $endAction == "insert" )
            {
                $originalID = $previewID;
            }
            else
            {
                $originalID = $messageID;
            }

            $messageID = $previewID;

            $checkMessageID = $msg->id();
            $checkForumID = $msg->forumID();
            include( "kernel/ezforum/user/messagepermissions.php" );

            if ( isset( $messageEdit ) && $messageEdit == false )
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /error/403?Info=" . errorPage( "forum_main", "/forum/categorylist/", 403 ) );
            }

            $showPath = true;
            $isPreview = false;
            include_once( "kernel/ezforum/user/messagepath.php" );
            $forum = new eZForum( $checkForumID );
            if ( $forum->isModerated() )
            {
                    $t->parse( "moderated", "moderated_tpl", true );
            }
            $showMessage = true;
            include_once( "kernel/ezforum/user/messagebody.php" );

            $showMessageForm = true;
            $showEmptyMessageForm = false;
            $showVisibleMessageForm = false;
            $showHiddenMessageForm = true;
            $showReplyInfo = true;
            $showBodyInfo = true;
            include_once( "kernel/ezforum/user/messageform.php" );
        }

        $doPrint = true;
    }
    break;

    default:
    {
        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /error/404?Info=" . errorPage( "forum_main", "/forum/categorylist/", 404 ) );
    }
    break;
}

if( $debugMessageEdit === true )
{
    print( "ActionValue = " . ( isset( $actionValue ) ? $actionValue : false ) . " <br>" );
    print( "NewMessageBody = " . ( isset( $newMessageBody ) ? $newMessageBody : false ) . " <br>" );
    print( "MessageBody = " . ( isset( $messageBody ) ? $messageBody : false ) . " <br>" );
    print( "PreviewID = " . ( isset( $previewID ) ? $previewID : false ) . " <br>" );
    print( "ReplyToID = " . ( isset( $replyToID ) ? $replyToID : false ) . " <br>" );
    print( "OriginalID = " . ( isset( $originalID ) ? $originalID : false ) . " <br>" );
    print( "MessageID = " . ( isset( $messageID ) ? $messageID : false ) . " <br>" );
    print( "RedirectURL = " . ( isset( $redirectURL ) ? $redirectURL : false ) . " <br>" );
    print( "ForumID = " . ( isset( $forumID ) ? $forumID : false ) . " <br>" );
}

$t->set_var( "start_action", $startAction );
$t->set_var( "end_action", $endAction );
$t->set_var( "action_value", $actionValue );
$t->set_var( "message_id", isset( $messageID ) ? $messageID : false );


if ( isset( $allowHTML ) && $allowHTML == "enabled" )
{
    $t->set_var( "html_tags", $allowedTags );
}
else
    $t->set_var( "html_tags", "" );

$t->setAllStrings();

if ( isset( $doPrint ) && $doPrint == true )
{
    $t->pparse( "output", "page" );
}
else
{
    $t->pparse( "output", "page" );
}

?>