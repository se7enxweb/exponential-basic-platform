<?php
//
// $Id: datasupplier.php 8879 2002-01-04 14:29:07Z kaid $
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
// include_once( "classes/eztemplate.php" );

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZForumMain", "DefaultSection" );

$action                = eZHTTPTool::getVar( 'Action' );
$actionValue           = eZHTTPTool::getVar( 'ActionValue' );
$additionalURLInfo     = eZHTTPTool::getVar( 'AdditionalURLInfo' );
$allowHTML             = eZHTTPTool::getVar( 'AllowHTML' );
$allowedTags           = eZHTTPTool::getVar( 'AllowedTags' );
$anonymous             = eZHTTPTool::getVar( 'Anonymous' );
$articleID             = eZHTTPTool::getVar( 'ArticleID' );
$articleName           = eZHTTPTool::getVar( 'ArticleName' );
$authorName            = eZHTTPTool::getVar( 'AuthorName' );
$bodyInfo              = eZHTTPTool::getVar( 'BodyInfo' );
$cancelButton          = eZHTTPTool::getVar( 'CancelButton' );
$categoryID            = eZHTTPTool::getVar( 'CategoryID' );
$checkForumID          = eZHTTPTool::getVar( 'CheckForumID' );
$checkForumPost        = eZHTTPTool::getVar( 'CheckForumPost' );
$checkForumRead        = eZHTTPTool::getVar( 'CheckForumRead' );
$checkMessageDelete    = eZHTTPTool::getVar( 'CheckMessageDelete' );
$checkMessageEdit      = eZHTTPTool::getVar( 'CheckMessageEdit' );
$checkMessageID        = eZHTTPTool::getVar( 'CheckMessageID' );
$checkMessageRead      = eZHTTPTool::getVar( 'CheckMessageRead' );
$checkMessageReply     = eZHTTPTool::getVar( 'CheckMessageReply' );
$editButton            = eZHTTPTool::getVar( 'EditButton' );
$endAction             = eZHTTPTool::getVar( 'EndAction' );
$error                 = eZHTTPTool::getVar( 'Error' );
$errors                = eZHTTPTool::getVar( 'Errors' );
$forumCategory         = eZHTTPTool::getVar( 'ForumCategory' );
$forumCategoryID       = eZHTTPTool::getVar( 'ForumCategoryID' );
$forumCategoryName     = eZHTTPTool::getVar( 'ForumCategoryName' );
$forumID               = eZHTTPTool::getVar( 'ForumID' );
$forumMessages         = eZHTTPTool::getVar( 'ForumMessages' );
$forumName             = eZHTTPTool::getVar( 'ForumName' );
$forumPost             = eZHTTPTool::getVar( 'ForumPost' );
$forumRead             = eZHTTPTool::getVar( 'ForumRead' );
$hideThreads           = eZHTTPTool::getVar( 'HideThreads' );
$info                  = eZHTTPTool::getVar( 'Info' );
$limit                 = eZHTTPTool::getVar( 'Limit' );
$listTemplate          = eZHTTPTool::getVar( 'ListTemplate' );
$locale                = eZHTTPTool::getVar( 'Locale' );
$messageAuthor         = eZHTTPTool::getVar( 'MessageAuthor' );
$messageBody           = eZHTTPTool::getVar( 'MessageBody' );
$messageDelete         = eZHTTPTool::getVar( 'MessageDelete' );
$messageEdit           = eZHTTPTool::getVar( 'MessageEdit' );
$messageID             = eZHTTPTool::getVar( 'MessageID' );
$messageNotice         = eZHTTPTool::getVar( 'MessageNotice' );
$messageNoticeText     = eZHTTPTool::getVar( 'MessageNoticeText' );
$messageOwner          = eZHTTPTool::getVar( 'MessageOwner' );
$messagePathOverride   = eZHTTPTool::getVar( 'MessagePathOverride' );
$messagePostedAt       = eZHTTPTool::getVar( 'MessagePostedAt' );
$messageRead           = eZHTTPTool::getVar( 'MessageRead' );
$messageReply          = eZHTTPTool::getVar( 'MessageReply' );
$messageTopic          = eZHTTPTool::getVar( 'MessageTopic' );
$moduleName            = eZHTTPTool::getVar( 'ModuleName' );
$newMessageAuthor      = eZHTTPTool::getVar( 'NewMessageAuthor' );
$newMessageBody        = eZHTTPTool::getVar( 'NewMessageBody' );
$newMessageLimit       = eZHTTPTool::getVar( 'NewMessageLimit' );
$newMessageNotice      = eZHTTPTool::getVar( 'NewMessageNotice' );
$newMessagePostedAt    = eZHTTPTool::getVar( 'NewMessagePostedAt' );
$newMessageTopic       = eZHTTPTool::getVar( 'NewMessageTopic' );
$offset                = eZHTTPTool::getVar( 'Offset' );
$originalID            = eZHTTPTool::getVar( 'OriginalID' );
$pageCaching           = eZHTTPTool::getVar( 'PageCaching' );
$previewButton         = eZHTTPTool::getVar( 'PreviewButton' );
$previewID             = eZHTTPTool::getVar( 'PreviewID' );
$primaryName           = eZHTTPTool::getVar( 'PrimaryName' );
$primaryURL            = eZHTTPTool::getVar( 'PrimaryURL' );
$productID             = eZHTTPTool::getVar( 'ProductID' );
$queryString           = eZHTTPTool::getVar( 'QueryString' );
$redirectURL           = eZHTTPTool::getVar( 'RedirectURL' );
$replyEndTag           = eZHTTPTool::getVar( 'ReplyEndTag' );
$replyInfo             = eZHTTPTool::getVar( 'ReplyInfo' );
$replyPrefix           = eZHTTPTool::getVar( 'ReplyPrefix' );
$replyStartTag         = eZHTTPTool::getVar( 'ReplyStartTag' );
$replyTags             = eZHTTPTool::getVar( 'ReplyTags' );
$replyToID             = eZHTTPTool::getVar( 'ReplyToID' );
$reviewLimit           = eZHTTPTool::getVar( 'ReviewLimit' );
$searchResult          = eZHTTPTool::getVar( 'SearchResult' );
$searchText            = eZHTTPTool::getVar( 'SearchText' );
$showBodyInfo          = eZHTTPTool::getVar( 'ShowBodyInfo' );
$showEmptyMessageForm  = eZHTTPTool::getVar( 'ShowEmptyMessageForm' );
$showHiddenMessageForm = eZHTTPTool::getVar( 'ShowHiddenMessageForm' );
$showMessage           = eZHTTPTool::getVar( 'ShowMessage' );
$showMessageForm       = eZHTTPTool::getVar( 'ShowMessageForm' );
$showPath              = eZHTTPTool::getVar( 'ShowPath' );
$showReplyInfo         = eZHTTPTool::getVar( 'ShowReplyInfo' );
$showSearch            = eZHTTPTool::getVar( 'ShowSearch' );
$showThreads           = eZHTTPTool::getVar( 'ShowThreads' );
$showVisibleMessageForm = eZHTTPTool::getVar( 'ShowVisibleMessageForm' );
$simpleUserList        = eZHTTPTool::getVar( 'SimpleUserList' );
$startAction           = eZHTTPTool::getVar( 'StartAction' );
$userID                = eZHTTPTool::getVar( 'UserID' );
$userLimit             = eZHTTPTool::getVar( 'UserLimit' );
$userListLimit         = eZHTTPTool::getVar( 'UserListLimit' );

function errorPage( $primaryName, $primaryURL, $type )
{
    $ini = eZINI::instance( 'site.ini' );

    $t = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                         "kernel/ezforum/user/intl", $ini->variable( "eZForumMain", "Language" ), "message.php" );

    $t->set_file( "page", "messageerror.tpl"  );
    $t->set_var( "primary_url", $primaryURL  );
    $t->set_var( "primary_url_name", $t->Ini->variable( "strings", $primaryName  ) );
    if ( $type == 404 )
    {
        $t->set_var( "error_1", $t->Ini->variable( "strings", 'error_missing_page_1'  ) );
        $t->set_var( "error_2", $t->Ini->variable( "strings", 'error_missing_page_2'  ) );
        $t->set_var( "error_3", $t->Ini->variable( "strings", 'error_missing_page_3'  ) );
    }
    else
    {
        $t->set_var( "error_1", $t->Ini->variable( "strings", 'error_forbidden_page_1'  ) );
        $t->set_var( "error_2", $t->Ini->variable( "strings", 'error_forbidden_page_2'  ) );
        $t->set_var( "error_3", $t->Ini->variable( "strings", 'error_forbidden_page_3'  ) );
    }
    $t->setAllStrings();

    $error = $t->parse( "error", "page" );
    $info = stripslashes( $error );
    $error = urlencode( $info );
    return $error;
}

switch ( $url_array[2] )
{
    case "userlogin":
    {
        $action = $url_array[3];
        
        switch ( $action )
        {
            case "edit":
            case "delete":
            {
                $messageID = $url_array[4];
                include( "kernel/ezforum/user/userlogin.php" );
            }
            break;
        }
        if ( $url_array[3] == "new" )
        {         
            $action = $url_array[3];
            $forumID = $url_array[4];
            $messageID = $url_array[4];
            include( "kernel/ezforum/user/userlogin.php" );
        }

        if ( $url_array[3] == "reply" )
        {         
            $action = $url_array[3];
            $replyToID = $url_array[4];
            include( "kernel/ezforum/user/userlogin.php" );
        }
        
        if ( $url_array[3] == "newsimple" )
        {
            $forumID = $url_array[4];
            include( "kernel/ezforum/user/userlogin.php" );
        }

        if ( $url_array[3] == "replysimple" )
        {
            $forumID = $url_array[4];
            $replyToID = $url_array[5];
            include( "kernel/ezforum/user/userlogin.php" );
        }
    }
    break;

    case "categorylist":
    {
        include( "kernel/ezforum/user/categorylist.php" );
    }
    break;
        
    case "forumlist":
    {
        $categoryID = $url_array[3];
        include( "kernel/ezforum/user/forumlist.php" );
    }
    break;
    
    case "messagelist":
    {
        $forumID = $url_array[3];

        if ( $url_array[4] == "parent" )
            $offset = $url_array[5];

        include( "kernel/ezforum/user/messagelist.php" );
    }
    break;

    case "messagelistflat":
    {
        $forumID = $url_array[3];

        if ( $url_array[4] == "parent" )
            $offset = $url_array[5];

        include( "kernel/ezforum/user/messagelistflat.php" );
    }
    break;
    
    case "messagesimpleedit":
    case "messagesimplereply":
    case "reply":
    case "messageedit":
    case "newpost":
    case "newsimple":
    {
        $action = $url_array[3];
        $id = $url_array[4];

        switch ( $action )
        {
            case "reply":
            {
                $replyToID = $id;
                $forumID = $url_array[5];
            }
            break;

            case "preview":
            {
                $replyToID = $id;
            }
            break;

            case "new":
            {
                $forumID = $id;
            }
            break;

            case "edit":
            case "completed":
            case "insert":
            case "update":
            case "delete":
            case "dodelete":
            {
                $messageID = $id;
            }
            break;          
        }
        include( "kernel/ezforum/user/messageedit.php" );

    }
    break;

    case "message":
    {
        $messageID = $url_array[3];
        include( "kernel/ezforum/user/message.php" );
    }
    break;
        
    case "search" :
    {
        if ( isset( $url_array[3] ) and $url_array[3] == "parent" )
        {
            $queryString = urldecode( $url_array[4] );
            $offset = $url_array[5];
            if  ( !is_numeric( $offset ) )
                $offset = 0;
        }

        include( "kernel/ezforum/user/search.php" );
    }
    break;

    default :
    {
        eZHTTPTool::header( "Location: /error/404?Info=" . errorPage( "forum_main", "/forum/categorylist/", 404 ) );
    }
    break;        
}

?>