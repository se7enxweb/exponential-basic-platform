<?php
//
// $Id: datasupplier.php,v 1.2 2001/07/20 11:19:36 jakobn Exp $
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

$delMessage  = eZHTTPTool::getVar( 'DelMessage' );
$delete      = eZHTTPTool::getVar( 'Delete' );
$description = eZHTTPTool::getVar( 'Description' );
$edit        = eZHTTPTool::getVar( 'Edit' );
$message     = eZHTTPTool::getVar( 'Message' );
$messageID   = eZHTTPTool::getVar( 'MessageID' );
$messageSent = eZHTTPTool::getVar( 'MessageSent' );
$preview     = eZHTTPTool::getVar( 'Preview' );
$receiver    = eZHTTPTool::getVar( 'Receiver' );
$reply       = eZHTTPTool::getVar( 'Reply' );
$sendMessage = eZHTTPTool::getVar( 'SendMessage' );
$subject     = eZHTTPTool::getVar( 'Subject' );
$userName    = eZHTTPTool::getVar( 'UserName' );

switch( $url_array[2] )
{
    case "view" :
    {
        $messageID = $url_array[3];
        include( "kernel/ezmessage/user/messageview.php" );
    }
    break;    

    case "list" :
    {
        include( "kernel/ezmessage/user/messagelist.php" );
    }
    break;    

    case "edit" :
    {
       	include( "kernel/ezmessage/user/messageedit.php" );
    }
    break;
    
    case "popup" :
    {
       	include( "kernel/ezmessage/user/reciverpopup.php" );
    }
    break; 
    
    case "send" :
    {
       	include( "kernel/ezmessage/user/messagesend.php" );
    }
    break;
    
    default :
    {
        eZHTTPTool::header( "Location: /error/404" );
        exit();
    }
    break;
}

?>
