<?php
// 
// $Id: messageedit.php,v 1.5 2001/08/17 13:36:00 jhe Exp $
//
// Created on: <05-Jun-2001 17:19:01 bf>
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
// include_once( "classes/ezhttptool.php" );
// include_once( "classes/eztemplate.php" );
// include_once( "classes/INIFile.php" );
// include_once( "classes/eztexttool.php" );

// include_once( "ezuser/classes/ezuser.php" );

// include_once( "ezmessage/classes/ezmessage.php" );
// include_once( "ezmessage/classes/ezmessagemessagedefinition.php" );

$messageSent = false;
if ( isset( $sendMessage ) )
{
if ( substr( trim( $receiver ) ,strlen( trim( $receiver ) ) -1 ) == "," )
$receiver = substr( trim( $receiver ) , 0, strlen( trim( $receiver ) ) -1 );

    $users = explode( ",", $receiver );

    // check for valid users:
    $usersValid = true;
    foreach ( $users as $user )
    {
        $user = trim( $user );

        if ( !eZUser::exists( $user ) )            
            $usersValid = false;
    }
    
    if ( $usersValid == true )
    {
        foreach ( $users as $user )
        {
            $user = trim( $user );
            
            $message = new eZMessage( );
            if ( trim ( $subject ) == "" )
            	$subject = "None Subject";
            $message->setSubject( $subject );
            if ( trim ( $description ) == "" )
            	$description = "None Description";
            $message->setDescription( $description );
            $toUser = eZUser::getUser( $user );
            $message->setToUser( $toUser );

            $fromUser = eZUser::currentUser();
            
            $message->setFromUser( $fromUser );

            $message->store();
$messageID = $message->id();

$messageDefinition = new eZMessageDefinition();
$messageDefinition->setMessageID( $messageID );
$messageDefinition->setToUserID( $toUser );
$messageDefinition->setFromUserID( $fromUser );
$messageDefinition->store();

$messageDefinition = new eZMessageDefinition();
$messageDefinition->setMessageID( $messageID );
$messageDefinition->setToUserID( $fromUser );
$messageDefinition->setFromUserID( $toUser );
$messageDefinition->store();
            $messageSent = true;
            
        }
    }    
}

$ini = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZMessageMain", "Language" );
$t = new eZTemplate( "kernel/ezmessage/user/" . $ini->variable( "eZMessageMain", "TemplateDir" ),
                     "kernel/ezmessage/user/intl", $Language, "messageedit.php" );

$locale = new eZLocale( $Language );



$t->set_file( "message_page_tpl", "messageedit.tpl" );

$t->set_block( "message_page_tpl", "error_tpl", "error" );
$t->set_block( "message_page_tpl", "message_sent_tpl", "message_sent" );
$t->set_block( "message_page_tpl", "message_verify_tpl", "message_verify" );
$t->set_block( "message_verify_tpl", "message_receiver_tpl", "message_receiver" );
$t->set_block( "message_page_tpl", "message_edit_tpl", "message_edit" );


$t->setAllStrings();

$receiver = eZHTTPTool::getVar( "Receiver");
$subject = eZHTTPTool::getVar( "Subject" );
$description = eZHTTPTool::getVar( "Description" );
$reply = eZHTTPTool::getVar( "Reply" );
$edit = eZHTTPTool::getVar( "Edit" );
$preview = eZHTTPTool::getVar( "Preview" );

$t->set_var( "receiver", $receiver );
$t->set_var( "subject", $subject );
$t->set_var( "description", $description );

$mes = new eZMessage( );
$t->set_var( "show_description", $mes->render( $description ) );
$t->set_var( "error", "" );

if ( isSet ( $reply ) )
{
    $fromUser = new eZUser ( $FromUserID );
    $t->set_var( "full_name", $fromUser->firstName() );
    if ( $message != "" )
{
    $t->set_var( "description", eZTextTool::addPre( $message ), ">" );
}
else
{
    $t->set_var( "description", "" );
}
    $t->set_var( "receiver", $fromUser->login()  );
}

if ( $messageSent == true )
{
    $t->parse( "message_sent", "message_sent_tpl" );
    $t->set_var( "message_verify", "" );
    $t->set_var( "message_edit", "" );
}
else if ( !isset( $preview ) || isset( $edit ) )
{
    $t->parse( "message_edit", "message_edit_tpl" );

if ($receiver != "")
{
         $userName = eZUser::getUser( $receiver );
         $t->set_var( "full_name", $userName->firstName()." ".$userName->lastName() );
}
    $t->set_var( "message_verify", "" );
    $t->set_var( "message_sent", "" );
    
}
else
{

if ( substr( trim( $receiver ) ,strlen( trim( $receiver ) ) -1 ) == "," )
$receiver = substr( trim( $receiver ) , 0, strlen( trim( $receiver ) ) -1 );
    $users = explode( ",", $receiver );
    
    // check for valid users:
    $usersValid = true;
    foreach ( $users as $user )
    {
        $user = trim( $user );
        
        if ( !eZUser::exists( $user )  )            
            $usersValid = false;
    }
    
    if ( $usersValid == true )
    {
    	//$fromUser = eZUser::currentUser();
    	//print_r($fromUser);
        foreach ( $users as $user )
        {
            $user = trim( $user );
            
            $toUser = eZUser::getUser( $user );

            $t->set_var( "login", $user );
            $t->set_var( "first_name", $toUser->firstName() );
            $t->set_var( "last_name", $toUser->lastName() );
            $t->parse( "message_receiver", "message_receiver_tpl", true );            
        }
    
        $t->parse( "message_verify", "message_verify_tpl" );
        $t->set_var( "message_edit", "" );
        $t->set_var( "message_sent", "" );        
    }
    else
    {
        // show error
        $t->parse( "error", "error_tpl" );
        $t->parse( "message_edit", "message_edit_tpl" );
        $t->set_var( "message_verify", "" );
        $t->set_var( "message_sent", "" );

    }
}

$t->pparse( "output", "message_page_tpl" );

if ( $messageSent == true )
{
    include( "kernel/ezmessage/user/messagelist.php" );
}

?>

