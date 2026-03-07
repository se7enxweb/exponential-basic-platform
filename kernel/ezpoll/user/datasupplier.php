<?php
//
// $Id: datasupplier.php 8732 2001-12-11 16:29:42Z br $
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

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZPollMain", "DefaultSection" );

$allowDoubleVotes = eZHTTPTool::getVar( 'AllowDoubleVotes' );
$choiceID         = eZHTTPTool::getVar( 'ChoiceID' );
$forumID          = eZHTTPTool::getVar( 'ForumID' );
$pollID           = eZHTTPTool::getVar( 'PollID' );
$redirectURL      = eZHTTPTool::getVar( 'RedirectURL' );
$show             = eZHTTPTool::getVar( 'Show' );
$voteID           = eZHTTPTool::getVar( 'VoteID' );
$voted            = eZHTTPTool::getVar( 'Voted' );

switch ( $url_array[2] )
{
    case "polls" :
    {
        include( "kernel/ezpoll/user/pollist.php" );
    }
    break;

    case "vote" :
    {
        $pollID = $url_array[3];
        if ( isset( $url_array[4] ) )
             $choiceID = $url_array[4];
        include( "kernel/ezpoll/user/vote.php" );
    }
    break;

    case "result" :
    {
        $pollID = $url_array[3];
        if ( isset( $url_array[4] ) )
        {
             $show = $url_array[4];
        }
        include( "kernel/ezpoll/user/result.php" );

        $poll = new eZPoll( $pollID );
        if ( $poll->get( $pollID ) )
        {
            $forum = $poll->forum();
            $forumID = $forum->id();
            $redirectURL = $_SERVER['REQUEST_URI'];
            include( "kernel/ezforum/user/messagesimplelist.php" );
        }
    }
    break;

    case "votebox" :
    {
        $pollID = $url_array[3];
        include( "kernel/ezpoll/user/votebox.php" );
    }
    break;
    case "votepage" :
    {
        $pollID = $url_array[3];
        include( "kernel/ezpoll/user/votepage.php" );
    }
    break;

    case "userlogin" :
    {
        $voteID = $url_array[4];
        include( "kernel/ezpoll/user/userlogin.php" );
    }    
    break;

    case "test":
    {
        include( "kernel/ezcontact/test.php" );
    }

}

?>