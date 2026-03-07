<?php
//
// $Id: messagepermissions.php 7782 2001-10-11 11:06:20Z jhe $
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

// "Return" values set to most secure experience, our job
// is then to apply these to the job at hand.

// Is it possible to read this message;
$messageRead = false;

// If a message has replies it cannot be edited.
$messageEdit = false;

// Only owners can delete a message; it also has to have no replies.
$messageDelete = false;

// If a message is temporary, then no replies can be added.
$messageReply = false;

// Is it possible to add a message to this forum?
$forumPost = false;

// Is it possible to read this  this forum?
$forumRead = false;


// Which checks should be performed? All by default, this is just here for easy
// partitioning with if''s and for debugging purposes. Will also check if the
// pre-requisites are there for the test!

$checkMessageRead = true;
$checkMessageDelete = true;
$checkMessageEdit = true;
$checkMessageReply = true;
$checkForumPost = true;
$checkForumRead = true;

// Anonymous users "have" user id 0.
if ( isset( $user ) && is_object( $user ) )
{
    $userID = $user->id();
}
else
{
    $userID = 0;
}

// If a forum id isn''t provided for checking, we can''t check
// the forum permissions.
if ( isset( $checkForumID ) && $checkForumID > 0 )
{
    $checkForum = new eZForum( $checkForumID );
}
else
{
    $checkForumPost = false;
    $checkForumRead = false;
    // No point in checking for reading of message if you can''t access the forum
    $checkMessageID = false;
}

// If a message id isn''t provided for checking, we can''t check
// the message permissions.
if ( isset( $checkMessageID ) && $checkMessageID > 0 )
{
    $checkMessage = new eZForumMessage( $checkMessageID );
    // Check if the current user is the message owner.

    if ( $checkMessage->userID() == $userID )
    {
        $messageRead = true;
        $messageOwner = true;
    }
    else
    {
        $messageOwner = false;
    }
}
else
{
    $checkMessageReply = false;
    $checkMessageRead = false;
    $checkMessageEdit = false;
    $checkMessageDelete = false;
}


// You can read all forums unless they''re set to only allow
// a certain group of people.
if ( $checkForumRead )
{
    $group = $checkForum->group();

    if ( ( is_a( $group, "eZUserGroup" ) ) && ( $group->id() != 0 ) )
    {
        if ( is_a( $user, "eZUser" ) )
        {
            $groupList = $user->groups();

            foreach ( $groupList as $userGroup )
            {
                if ( $userGroup->id() == $group->id() )
                {
                    $forumRead = true;
                    break;
                }
            }
        }
    }
    else
    {
        $forumRead = true;
    }
}
else
{
    $forumRead = true;
}

// You can post to a forum you can read if you''re a logged in user.
// If the forum is set to anonymous anyone can post.
if ( $checkForumPost && $forumRead )
{
    if ( isset( $checkForum ) && $checkForum->isAnonymous() == true )
    {
        $forumPost = true;
    }
    else
    {
        if ( $forumRead == true && $userID != 0 )
        {
            $forumPost = true;
        }
    }
}
else
{
    $forumPost = true;
}

// If you can read the forum, you can read the message if:
//    * it is approved when in a moderated forum
//    * it is your own when it is a temporary message
//    * none of the above conditions are met
if ( $checkMessageRead && $forumRead )
{
    if ( isset( $checkMessage ) && $checkMessage->isTemporary() == true )
    {
        if ( $messageOwner == true )
        {
            $messageRead = true;
        }
    }
    else
    {
        if ( $checkForum->isModerated() == true )
        {
            if ( $checkMessage->isApproved() == true )
            {
                $messageRead = true;
            }
        }
        else
        {
            $messageRead = true;
        }
    }
}

// If you can read a message, you own it, and it hasn''t any replies
// you can edit it.
if ( $checkMessageEdit && $messageRead )
{
    if ( $messageOwner == true )
    {
        if ( eZForumMessage::countReplies( $checkMessage->id() ) == 0 )
        {
            $messageEdit = true;
        }
    }
}

// If you can read a message and post to the forum, you can reply to it,
// except temporary messages.
if ( $checkMessageReply  && $messageRead && $forumPost )
{
    if ( $checkMessage->isTemporary() == false )
    {
        $messageReply = true;
    }
}

// If you own a message and can edit it, you can delete it.
if ( $checkMessageDelete && $messageEdit )
{
    if ( $messageOwner == true )
    {
        $messageDelete = true;
    }
}

if ( $debugMessagePermissions === true )
{
    echo "<hr>\n";
    // include_once( "classes/eztexttool.php" );
    echo "UserID = " . $userID . "<br />\n";
    echo "MessageOwner = " . eZTextTool::boolText( $messageOwner ) . "<br />\n";
    echo "ForumRead = " . eZTextTool::boolText( $forumRead ) . "<br />\n";
    echo "ForumPost = " . eZTextTool::boolText( $forumPost ) . "<br />\n";
    echo "MessageRead = " . eZTextTool::boolText( $messageRead ) . "<br />\n";
    echo "MessageEdit = " . eZTextTool::boolText( $messageEdit ) . "<br />\n";
    echo "MessageReply = " . eZTextTool::boolText( $messageReply ) . "<br />\n";
    echo "MessageDelete = " . eZTextTool::boolText( $messageDelete ) . "<br />\n";
}

?>