<?php
//
// $Id: vote.php 7115 2001-09-09 11:49:45Z bf $
//
// Created on: <20-Sep-2000 13:32:11 ce>
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
// include_once( "classes/eztemplate.php" );
// include_once( "classes/ezhttptool.php" );

$ini = eZINI::instance( 'site.ini' );

$Language = $ini->variable( "eZPollMain", "Language" );
$DOC_ROOT = $ini->variable( "eZPollMain", "DocumentRoot" );
if ( $ini->variable( "eZPollMain", "AllowDoubleVotes" ) == "enabled" )
   $allowDoubleVotes = true;


// include_once( "ezpoll/classes/ezpoll.php" );
// include_once( "ezpoll/classes/ezvote.php" );
// include_once( "ezpoll/classes/ezpollchoice.php" );
// include_once( "ezsession/classes/ezsession.php" );

$session = eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

// Check if poll is closed.
$poll = new eZPoll( $pollID );
if ( $poll->isClosed() )
{
    eZHTTPTool::header( "Location: /poll/result/$pollID" );
    exit();
}

// Check if the poll is anonymous or not.
$poll = new eZPoll( $pollID );
if ( !$poll->anonymous() )
{
    $pollUser = eZUser::currentUser();
    if ( !$pollUser )
    {
        eZHTTPTool::header( "Location: /user/user/new/" );
        exit();
    }
}
else
{

    $vote = new eZVote();
    //check if user has or can vote twice

    if ( $allowDoubleVotes )
    {
        $voted = false;
    }
    else
    {
        if ( $ini->variable( "eZPollMain", "DoubleVoteCheck" ) == "ip" )
        {
            if ( $vote->ipHasVoted( $REMOTE_ADDR, $pollID ) == true )
            {
                $voted = true;
            }
            else
            {
                $voted = false;
            }
        }
        else
        {
            if ( $GLOBALS["eZPollVote$pollID"] == "voted" )
            {
                $voted = true;                
            }
            else
            {
                $voted = false;
            }

            setcookie ( "eZPollVote$pollID", "voted", time() + ( 3600 * 24 * 365 ), "/",  "", 0 )
                or print( "Error: could not set cookie." );
        }

    }

}

if ( $pollUser )
{
    $checkvote = new eZVote();
    if ( $checkvote->isVoted( $pollUser->id(), $pollID  ))
        $voted = true;
    else
        $voted = false;
}

if ( !$voted )
{
    $vote = new eZVote();
    $vote->setPollID( $pollID );
    $vote->setChoiceID( $choiceID );
    $vote->setVotingIP( $REMOTE_ADDR );
    if ( $pollUser )
        $vote->setUserID( $pollUser->id() );
    if ( !$choiceID == 0 )
    $vote->store();
}

eZHTTPTool::header( "Location: /poll/result/" . $pollID );
exit();

?>