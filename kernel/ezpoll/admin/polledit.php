<?php
// 
// $Id: polledit.php 6304 2001-07-29 23:31:17Z kaid $
//
// Created on: <21-Sep-2000 10:39:19 ce>
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
$errorIni = new eZINI( "kernel/ezpoll/admin/intl/" . $Language . "/polledit.php.ini", false );

// include_once( "ezpoll/classes/ezpoll.php" );
// include_once( "ezpoll/classes/ezpollchoice.php" );
// include_once( "ezpoll/classes/ezvote.php" );
 
require( "kernel/ezuser/admin/admincheck.php" );

if ( isset( $back ) )
{
    eZHTTPTool::header( "Location: /poll/pollist/" );
    exit();
}

// Insert
if ( isset( $action ) && $action == "New" )
{
    $pollID = false;
    $showResult = false;
}
// Insert
if ( isset( $action ) && $action == "Insert" )
{
    if ( $name )
    {
        $poll = new eZPoll();
        if ( $isEnabled == "on" )
        {
            $poll->setIsEnabled ( true );
        }
        else
        {
            $poll->setIsEnabled ( false );
        }

        if ( isset( $isClosed ) && $isClosed == "on" )
        {
            $poll->setIsClosed ( true );
        }
        else
        {
            $poll->setIsClosed ( false );
        }
        
        if ( $showResult == "on" )
        {
            $poll->setShowResult ( true );
        }
        else
        {
            $poll->setShowResult ( false );
        }
        
        if ( isset( $anonymous ) && $anonymous == "on" )
        {
            $poll->setAnonymous ( true );
        }
        else
        {
        $poll->setAnonymous ( false );
        }
        
        if ( !$description )
        {
            $languageIni = new eZINI( "kernel/ezpoll/admin/" . "intl/" . $Language . "/polledit.php.ini", false );
            $description =  $languageIni->variable( "strings", "description_default" );
        }
        
        $poll->setName( $name );
        $poll->setDescription( $description );
        $poll->store();
        
        $pollID = $poll->id();
        
        // clear the menu cache
        if ( file_exists("ezpoll/cache/menubox.cache" )  )
            eZPBFile::unlink( "ezpoll/cache/menubox.cache" );
        
        if ( isset( $choice ) == true  )
        {
            $action = "Edit";
        }
        else
        {
            eZHTTPTool::header( "Location: /poll/pollist/" );
            exit();
        }
    }
    else
    {
        $errorMsg = $errorIni->variable( "strings", "noname" );
    }
}

if ( isset ( $choice ) )
{
    $item = new eZPollChoice();
    $item->setName( $errorIni->variable( "strings", "newitem") );
    $item->setPollID( $pollID );
    $item->store();
    
}

if ( isset( $deleteChoice ) )
{
    if( count( $pollArrayID ) > 0 )
    {
        foreach( $pollArrayID as $itemIndex )
        {
            $item = new eZPollChoice( $pollChoiceID[$itemIndex] );
            $item->delete();
        }
    }
}


// Update
if ( isset( $action ) && $action == "Update" )
{
    $poll = new eZPoll();
    $poll->get( $pollID );

    if ( $isEnabled== "on" )
    {
        $poll->setIsEnabled ( true );
    }
    else
    {
        $poll->setIsEnabled ( false );
    }

    if ( $isClosed == "on" )
    {
        $poll->setIsClosed ( true );
    }
    else
    {
        $poll->setIsClosed ( false );
    }

    if ( $showResult == "on" )
    {
        $poll->setShowResult ( true );
    }
    else
    {
        $poll->setShowResult ( false );
    }

    if ( $anonymous == "on" )
    {
        $poll->setAnonymous ( true );
    }
    else
    {
        $poll->setAnonymous ( false );
    }

    $poll->setName( $name );
    $poll->setDescription( $description );
    $poll->store();

    if( count( $pollChoiceID ) > 0 )
    {
        $i = 0;
        foreach( $pollChoiceID as $itemID )
        {
            $item = new eZPollChoice( $itemID );
            $item->setName( $pollChoiceName[$i] );
            $item->store();
            $i++;
        }
    }
    // clear the menu cache
    if ( file_exists("ezpoll/cache/menubox.cache" )  )
        eZPBFile::unlink( "ezpoll/cache/menubox.cache" );

    if( isset( $ok ) )
    {
        eZHTTPTool::header( "Location: /poll/pollist/" );
        exit();
    }

    eZHTTPTool::header( "Location: /poll/polledit/edit/$pollID" );
    exit();
}

// Delete
if ( isset( $action ) && $action == "Delete" )
{
    $poll = new eZPoll();
    $poll->get( $pollID );
    $poll->delete();

    // clear the menu cache
    if ( file_exists("ezpoll/cache/menubox.cache" )  )
        eZPBFile::unlink( "ezpoll/cache/menubox.cache" );
    
    eZHTTPTool::header( "Location: /poll/pollist/" );
    exit();
}

$t = new eZTemplate( "kernel/ezpoll/admin/" . $ini->variable( "eZPollMain", "AdminTemplateDir" ),
                     "kernel/ezpoll/admin/intl/", $Language, "polledit.php" );

$t->setAllStrings();

$t->set_file( array( "poll_edit_page" => "polledit.tpl"
                     ) );

$t->set_block( "poll_edit_page", "poll_choice_tpl", "poll_choice" );

$t->set_var( "site_style", $SiteDesign );

$Action_value = "insert";
$name = "";
$description = "";
$isEnabled = "";
$isClosed = "";
$anonymous = "";
$nopolls = "";
// Edit
if ( isset( $action ) && $action == "Edit" )
{
    $poll = new eZPoll();
    $poll->get( $pollID );

    $name = $poll->name();
    $description = $poll->description();

    if ( $poll->isEnabled() == true )
    {
        $isEnabled = "checked";
    }

    if ( $poll->isClosed() == true )
    {
        $isClosed = "checked";
    }

    if ( $poll->showResult() == true )
    {
        $showResult = "checked";
    }

    if ( $poll->anonymous() == true )
    {
        $anonymous = "checked";
    }

    $Action_value = "update";
    $languageIni = new eZINI( "kernel/ezpoll/admin/" . "intl/" . $Language . "/polledit.php.ini", false );
    $headline =  $languageIni->variable( "strings", "head_line_edit" );
}

// Poll choice list
$pollChoice = new eZPollChoice();

$pollChoiceList = $pollChoice->getAll( $pollID );

if ( !$pollChoiceList )
{
    $languageIni = new eZINI( "kernel/ezpoll/admin/" . "intl/" . $Language . "/polledit.php.ini", false );
    $nopolls =  $languageIni->variable( "strings", "nopolls" );
    $t->set_var( "poll_choice", "" );
    
}

$i=0;
foreach( $pollChoiceList as $pollChoiceItem )
{
    if ( ( $i %2 ) == 0 )
        $t->set_var( "td_class", "bglight" );
    else
        $t->set_var( "td_class", "bgdark" );

    $t->set_var( "choice_id", $pollChoiceItem->id() );
    $t->set_var( "poll_choice_name", $pollChoiceItem->name() );
    $vote = new eZVote();
    $t->set_var( "poll_number", $pollChoiceItem->voteCount() );
    $t->set_var( "index_nr", $i );
    
    $t->parse( "poll_choice", "poll_choice_tpl", true );
    $i++;
}

$t->set_var( "poll_id", $pollID );
$t->set_var( "name_value", $name );
$t->set_var( "description_value", $description );
$t->set_var( "is_enabled", $isEnabled );
$t->set_var( "is_closed", $isClosed );
$t->set_var( "show_result", $showResult );
$t->set_var( "anonymous", $anonymous );

$t->set_var( "action_value", $Action_value );
$t->set_var( "nopolls", $nopolls );


if ( !isset ( $headline ) )
{
    $languageIni = new eZINI( "kernel/ezpoll/admin/" . "intl/" . $Language . "/polledit.php.ini", false );
    $headline =  $languageIni->variable( "strings", "head_line_insert" );
}
$t->set_var( "head_line", $headline );
$t->set_var( "error_msg", isset( $errorMsg ) ? $errorMsg : false );

$t->pparse( "output", "poll_edit_page" );

?>