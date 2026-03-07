<?php
// 
// $id: login.php 6389 2001-08-07 13:26:40Z jhe $
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
// include_once( "classes/ezlog.php" );
// include_once( "classes/ezhttptool.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZUserMain", "Language" );

// include_once( "ezuser/classes/ezuser.php" );
// include_once( "ezuser/classes/ezusergroup.php" );
// include_once( "ezuser/classes/ezmodule.php" );
// include_once( "ezuser/classes/ezpermission.php" );
// include_once( "ezsession/classes/ezsession.php" );


// Template
$t = new eZTemplate( "kernel/ezuser/admin/" .  $ini->variable( "eZUserMain", "AdminTemplateDir" ),
                     "kernel/ezuser/admin/" . "/intl", $language, "login.php" );
$t->setAllStrings();

$t->set_file( array(
    "login_tpl" => "login.tpl"
    ) );

$t->set_block( "login_tpl", "error_message_tpl", "error_message" );
$t->set_block( "login_tpl", "max_message_tpl", "max_message" );

if ( isset( $action ) && $action == "login" )
{
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    $remoteAddress = $_SERVER['REMOTE_ADDR'];

    $user = new eZUser();
    $user = $user->validateUser( $username, $password );

    if ( ( $user )  && eZPermission::checkPermission( $user, "eZUser", "AdminLogin" ) )
    {
        if ( $user->get( $user->ID ) )
        {
            $logins = $user->getLogins( $user->ID );
            $allowSimultaneousLogins =  $ini->variable( "eZUserMain", "SimultaneousLogins" );

            if ( $allowSimultaneousLogins == "disabled" )
            {
                $maxLogins = "1";
            }
            else
            {
                $maxLogins = $user->simultaneousLogins();
            }

            if ( ( $logins < $maxLogins ) || ( $maxLogins == 0 ) )
            {
                eZDebug::writeNotice( "Admin login: $username from IP: $remoteAddress" );

                eZUser::loginUser( $user );
                if ( !isset( $refererURL ) )
                    $refererURL = "/";
                
                // Show password change dialog, if admin is using default login
                if ( $username == "admin" && $password == "publish" )
                {
                    $refererURL = "/user/passwordchange/";
                }

                eZHTTPTool::header( "Location: $refererURL" );
                exit();
            }
            else
            {
                eZPBLog::writeWarning( "Max limit reached: $username from IP: $remoteAddress" );
        
                $maxerror = true;    
            }
        }
        else
        {
            eZPBLog::writeError( "Couldn't receive admin information on : $username from IP: $remoteAddress" );

            $error = true;
        }
    }
    else
    {
        eZPBLog::writeWarning( "Bad admin login: $username from IP: $remoteAddress" );
        
        $error = true;
    }
}

if ( !isset( $refererURL ) )
    $refererURL = $_SERVER['REQUEST_URI'];
    if ( preg_match( "#^/user/login.*#", $refererURL  ) )
    {
        $refererURL = "/";
        
    }

$t->set_var( "referer_url", $refererURL );

if ( isset( $action ) && $action == "logout" )
{
    eZUser::logout();
    eZHTTPTool::header( "Location: /" );
    exit();
}

if ( isset( $error ) && $error )
{
    $t->parse( "error_message", "error_message_tpl" );
}
else
{
    $t->set_var( "error_message", "" );
}

if ( isset( $maxerror ) && $maxerror )
{
    $t->parse( "max_message", "max_message_tpl" );
}
else
{
    $t->set_var( "max_message", "" );
}

$t->set_var( "action_value", "login" );
$t->pparse( "output", "login_tpl" );

?>