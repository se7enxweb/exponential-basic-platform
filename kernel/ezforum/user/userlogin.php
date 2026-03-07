<?php
// 
// $Id: userlogin.php 9518 2002-05-08 11:51:36Z vl $
//
// Created on: <14-Oct-2000 15:41:17 bf>
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
// include_once( "classes/eztexttool.php" );
// include_once( "ezuser/classes/ezuser.php" );

$ini = eZINI::instance( 'site.ini' );

$Language = $ini->variable( "eZForumMain", "Language" );



if ( eZUser::currentUser() )
{
    if ( isset( $redirectURL ) )
    {
        $additionalURLInfo="?RedirectURL=$redirectURL";
    } 
    else 
    {
    	$redirectURL='';
    }
    
    if ( $action == "newsimple" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/new/$forumID/$additionalURLInfo" );
    }

    if ( $action == "replysimple" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/reply/$replyToID/$forumID/$additionalURLInfo" );
    }
    
    if ( $action == "new" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/new/$forumID/$additionalURLInfo" );
    }

    if ( $action == "edit" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/edit/$messageID/$additionalURLInfo" );
    }

    if ( $action == "delete" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/delete/$messageID/$additionalURLInfo" );
    }

    if ( $action == "reply" )
    {
        eZHTTPTool::header( "Location: /forum/messageedit/reply/$replyToID/$additionalURLInfo" );
    }    
}
else
{
    $anonymous = false;
    
    if ( isset( $redirectURL ) )
    {
        $additionalURLInfo="?RedirectURL=$redirectURL";
    } 
    else 
    {
        $additionalURLInfo = "";
    	$redirectURL='';
    }
    
    switch ( $action )
    {
        case "new":
        {
            // include_once( "ezforum/classes/ezforum.php" );
            // include_once( "ezforum/classes/ezforummessage.php" );

            $checkForumID = $forumID;
            if( !isset( $additionalURLInfo ) )
            {
                $additionalURLInfo = "";
            }
           
            include( "kernel/ezforum/user/messagepermissions.php" );

            if ( $forumPost == true )
            {
                eZHTTPTool::header( "Location: /forum/messageedit/new/$forumID/$additionalURLInfo" );
            }
        }
        break;
        
        case "reply":
        {
            // include_once( "ezforum/classes/ezforum.php" );
            // include_once( "ezforum/classes/ezforummessage.php" );
            
            $msg = new eZForumMessage( $replyToID );
            
            $checkForumID = $msg->forumID();

            include( "kernel/ezforum/user/messagepermissions.php" );
            
            if ( $forumPost == true )
            {
                eZHTTPTool::header( "Location: /forum/messageedit/reply/$replyToID/$additionalURLInfo" );
            }
        }
        break;
    }
    
    if ( $anonymous == false )
    {
        $t = new eZTemplate( "kernel/ezforum/user/" . $ini->variable( "eZForumMain", "TemplateDir" ),
                             "kernel/ezforum/user/intl/", $Language, "userlogin.php" );

        $t->setAllStrings();

        $t->set_file( "user_login_tpl", "userlogin.tpl" );

        if ( $action == "newsimple" )
        {
            $t->set_var( "redirect_url", eZTextTool::htmlspecialchars( $redirectURL ) );
        }

        if ( $action == "replysimple" )
        {
            $t->set_var( "redirect_url", eZTextTool::htmlspecialchars( $redirectURL ) );
        }

        if ( $action == "new" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/new/$forumID/" );
        }

        if ( $action == "edit" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/edit/$messageID/" );
        }

        if ( $action == "delete" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/delete/$messageID/" );
        }

        if ( $action == "reply" )
        {
            $t->set_var( "redirect_url", "/forum/messageedit/reply/$replyToID/" );
        }

        $t->pparse( "output", "user_login_tpl" );
    }
}

?>