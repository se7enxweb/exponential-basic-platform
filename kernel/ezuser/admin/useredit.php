<?php
// 
// $id: useredit.php 9390 2002-04-04 16:14:47Z br $
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
$language = $ini->variable( "eZUserMain", "Language" );
$error_msg = false;
$error = new eZINI( "kernel/ezuser/admin/intl/" . $language . "/useredit.php.ini", false );

// include_once( "ezmail/classes/ezmail.php" );
// include_once( "classes/ezlog.php" );
// include_once( "ezuser/classes/ezuser.php" );
// include_once( "ezuser/classes/ezusergroup.php" );

require( "kernel/ezuser/admin/admincheck.php" );

if ( isset( $_POST['DeleteUsers'] ) )
{
    $action = "DeleteUsers";
}

if ( isset( $_POST['Back'] ) )
{
    eZHTTPTool::header( "Location: /user/userlist/" );
    exit();
}


// do not allow editing users with root access while you do not.
$currentUser = eZUser::currentUser();

if( isset( $_POST['UserID'] ) && $_POST['UserID'] != '' )
{
    $editUser = new eZUser( $_POST['UserID'] );
    if( !$currentUser->hasRootAccess() && $editUser->hasRootAccess() )
    {
        $info = urlencode( "Can't edit a user with root priveliges." );
        eZHTTPTool::header( "Location: /error/403?Info=$info" );
        exit();
    }
}

if ( $action == "insert" )
{
    if ( eZPermission::checkPermission( $user, "eZUser", "UserAdd" ) )
    {
        if ( $_POST['Login'] != "" &&
             $_POST['Email'] != "" &&
             $_POST['FirstName'] != "" &&
             $_POST['LastName'] != "" &&
             $_POST['SimultaneousLogins'] != "")
        {
            if ( ( $_POST['Password'] == $_POST['VerifyPassword'] ) && ( strlen( $_POST['VerifyPassword'] ) > 2 ) )
            {
                $user = new eZUser();
                $user->setLogin( $_POST['Login'] );
                if ( !$user->exists( $user->login() ) )
                {
                    $tmp[0] = $_POST['Email'];
                    if ( eZMail::validate( $tmp[0] ) )
                    {
                        $user->setPassword( $_POST['Password'] );
                        $user->setEmail( $_POST['Email'] );
                        $user->setAccountNumber( $_POST['AccountNumber'] );
                        $user->setFirstName( $_POST['FirstName'] );
                        $user->setLastName( $_POST['LastName'] );
                        $user->setSignature( $_POST['Signature'] );
                        $user->setSimultaneousLogins( $_POST['SimultaneousLogins'] );

                        if ( $_POST['InfoSubscription'] == "on" )
                            $user->setInfoSubscription( true );
                        else
                            $user->setInfoSubscription( false );
                        
                        $user->store();
                        eZPBLog::writeNotice( "User created: " . $_POST['FirstName'] . " " . $_POST['LastName'] ." (" . $_POST['Login'] .") ". $_POST['Email'] . " " . $_POST['SimultaneousLogins'] . " from IP: " . $_SERVER['REMOTE_ADDR'] );
                        
                        // Add user to groups
                        $groupArray = array_unique( array_merge( $_POST['GroupArray'], $_POST['MainGroup'] ) );
                        $group = new eZUserGroup();
                        $user->get( $user->id() );
                        $user->removeGroups();
                        foreach ( $groupArray as $groupID )
                        {
                            $group = new eZUserGroup();
//                            $user->get( $user->id() );
//                            $user->removeGroups();
                            $group->get( $groupID );
                            if ( ( $group->isRoot() && $currentUser->hasRootAccess() ) || !$group->isRoot() )
                            {
                                $group->adduser( $user );
                                $groupname = $group->name();
                                eZPBLog::writeNotice( "User added to group: $groupname from IP: " . $_SERVER['REMOTE_ADDR'] );
                            }
                        }
                        
                        $user->setGroupDefinition( $mainGroup );
			            $action = false;
                        eZHTTPTool::header( "Location: /user/userlist/" );
                        exit();
                    }
                    else
                    {
                        $error_msg = $error->variable( "strings", "error_email" );
                    }
                }
                else
                {
                    $error_msg = $error->variable( "strings", "error_user_exists" );
                }
                
            }
            else
            {
                $error_msg = $error->variable( "strings", "error_password" );
            }
        }
        else
        {
            $error_msg = $error->variable( "strings", "error_missingdata" );
        }
    }
    else
    {
        $error_msg = $error->variable( "strings", "error_norights" );
    }
}

if ( $action == "update" )
{
    if ( eZPermission::checkPermission( $user, "eZUser", "UserModify" ) )
    {
        if( isset( $_POST['Login'] ) )
    	    $login = $_POST['Login'];
    	if( isset( $_POST['Email'] ) )
            $email = $_POST['Email'];
        if( isset( $_POST['FirstName'] ) )
            $firstName = $_POST['FirstName'];
        if( isset( $_POST['LastName'] ) )
       	    $lastName = $_POST['LastName'];

        if( isset( $_POST['AccountNumber'] ) )
       	    $accountNumber = $_POST['AccountNumber'];
        if( isset( $_POST['Signature'] ) )
       	    $signature = $_POST['Signature'];
        if( isset( $_POST['SimultaneousLogins'] ) )
       	    $simultaneousLogins = $_POST['SimultaneousLogins'];
        if( isset( $_POST['InfoSubscription'] ) )
            $infoSubscription = $_POST['InfoSubscription'];
        if( isset( $_POST['Password'] ) )
            $password = $_POST['Password'];
        if( isset( $_POST['VerifyPassword'] ) )
            $verifyPassword = $_POST['VerifyPassword'];
        if( isset( $_POST['UserID'] ) )
            $userID = $_POST['UserID'];

        if ( $email != "" &&
        $firstName != "" &&
        $lastName != "" &&
        $simultaneousLogins != "")
        {
            if (  ( ( $password == $verifyPassword ) && ( strlen( $verifyPassword ) > 2 ) ) ||
                  ( ( $password == $verifyPassword ) && ( strlen( $verifyPassword ) == 0 ) ) )
            {
                $user->setLogin( $login );
                {
                    if ( eZMail::validate( $email ) )
                    {
                        $user = new eZUser();
                        $user->get( $userID );
                        
                        $user->setEmail( $email );
                        $user->setSignature( $signature );

                        if ( $infoSubscription == "on" )
                            $user->setInfoSubscription( true );
                        else
                            $user->setInfoSubscription( false );

                        $user->setFirstName( $firstName );
                        $user->setLastName( $lastName );
                        $user->setAccountNumber( $accountNumber );
                        $user->setSimultaneousLogins( $simultaneousLogins );
                        
                        if ( strlen( $password ) > 0 )
                        {
                            $user->setPassword( $password );
                        }
                            
                        $user->store();
                        eZPBLog::writeNotice( "User updated: $firstName $lastName ($login) $email from IP: " . $_SERVER['REMOTE_ADDR'] );

                        // Remove user from groups
                        $user->removeGroups();
                        
                        // Add user to groups
			            if( isset( $_POST['GroupArray'] ) && isset( $_POST['MainGroup'] ) )
                        {
		 	                $groupArray = array_unique( array_merge( $_POST['GroupArray'], array( $_POST['MainGroup'] ) ) );
			}
			else {
		 	    $groupArray[] = $_POST['MainGroup'];
			}
                        $group = new eZUserGroup();
                        $user->get( $user->id() );
                        $user->removeGroups();
                        foreach ( $groupArray as $groupID )
                        {
                            $group = new eZUserGroup();
//                            $user->get( $user->id() );
//                            $user->removeGroups();
                            $group->get( $groupID );
//                            if ( ( $group->isRoot() && $currentUser->hasRootAccess() ) || !$group->isRoot() )
                            {
                                $group->adduser( $user );
                                $groupname = $group->name();
                                eZPBLog::writeNotice( "User added to group: $groupname from IP: " . $_SERVER['REMOTE_ADDR'] );
                            }
                        }

                        $user->setGroupDefinition( $_POST['MainGroup'] );
                        eZHTTPTool::header( "Location: /user/userlist/" );
                        exit();
                    }
                    else
                    {
                        $error_msg = $error->variable( "strings", "error_email" );
                    }
                }
                
            }
            else
            {
                $error_msg = $error->variable( "strings", "error_password" );
            }
        }
        else
        {
            $error_msg = $error->variable( "strings", "error_missingdata" );
        }
    }
    else
    {
        $error_msg = $error->variable( "strings", "error_norights" );
    }
    $actionValue = "update";
}

if ( $action == "delete" )
{
    if ( eZPermission::checkPermission( $user, "eZUser", "UserDelete" ) )
    {
        $user = new eZUser();
        $user->get( $userID );
        $firstName = $user->firstName();
        $lastName = $user->lastName();
        $email = $user->email();
        $login = $user->login();
        $accountNumber = $user->accountNumber();
        $simultaneousLogins = $user->simultaneousLogins();
        
        $user->delete();
        
        eZPBLog::writeNotice( "User deleted: $firstname $lastname ($login) $email $simultaneousLogins from IP: $REMOTE_ADDR" );
        eZHTTPTool::header( "Location: /user/userlist/" );
        exit();
    }
    else
    {
        $error_msg = $error->variable( "strings", "error_norights" );
    }
}
$currentUser = eZUser::currentUser();
if ( $action == "DeleteUsers" )
{
    if( eZPermission::checkPermission( $user, "eZUser", "UserDelete" ) )
    {
        if ( count ( $userArrayID ) != 0 )
        {
            foreach( $userArrayID as $userID )
            {
                $user = new eZUser( $userID );
                $login = $user->login();
                if( $user->hasRootAccess() && !$currentUser->hasRootAccess() )
                {
                    $currentLogin = $currentUser->login();
                    eZPBLog::writeNotice( "$currentLogin failed to delete user $login since he can't delete users with root privelidges." );
                }
                else
                {
                    $firstName = $user->firstName();
                    $lastName = $user->lastName();
                    $email = $user->email();
                    $login = $user->login();
                    $simultaneousLogins = $user->simultaneousLogins();
                
                    $user->delete();
            
                    eZPBLog::writeNotice( "User deleted: $firstname $lastname ($login) $email $simultaneousLogins from IP: $REMOTE_ADDR" );
                }
            }
        }
    }
    eZHTTPTool::header( "Location: /user/userlist/" );
    exit();
}

$t = new eZTemplate( "kernel/ezuser/admin/" . $ini->variable( "eZUserMain", "AdminTemplateDir" ),
 "kernel/ezuser/admin/" . "/intl", $language, "useredit.php" );
$t->setAllStrings();

$t->set_file( array(
    "user_edit" => "useredit.tpl"
     ) );

$t->set_block( "user_edit", "main_group_item_tpl", "main_group_item" );
$t->set_block( "user_edit", "group_item_tpl", "group_item" );

if ( $action == "new" )
{
    $firstName = "";
    $lastName = "";
    $email = "";
    $login = "";
    $phone = "";
    $signature = "";
    $userID = eZUser::currentUser()->ID;
    $accountNumber = "";
    $simultaneousLogins = $ini->variable( "eZUserMain", "DefaultSimultaneousLogins" );
}

$actionValue = "insert";

if ( $action == "update" )
{
    $actionValue = "update";
}

$headline = new eZINI( "kernel/ezuser/admin/intl/" . $language . "/useredit.php.ini", false );
$t->set_var( "head_line", $headline->variable( "strings", "head_line_insert" ) );

$group = new eZUserGroup();

$groupList = $group->getAll();


$user = 0;
$t->set_var( "read_only", "" );
$user = new eZUser();

if( isset( $_POST['UserID'] ) )
    $userID = $_POST['UserID'];

$user->get( $userID );

if ( $action == "edit" )
{
/*
$messages = eZForumMessage::lastMessages( 10 , $user, $userID );
echo "<pre>";
print_r($messages);
echo "</pre>";
exit;
*/
    if( $user->infoSubscription() == true )
        $infoSubscription = "checked";
    else
        $infoSubscription = "";
    
    $firstName = $user->firstName();
    $lastName = $user->lastName();
    $email = $user->email();
    $login = $user->login();
    $accountNumber = $user->accountNumber();
    $signature = $user->signature();
    $simultaneousLogins = $user->simultaneousLogins();
    $phone = "";
    
    $headline = new eZINI( "kernel/ezuser/admin/intl/" . $language . "/useredit.php.ini", false );
    $t->set_var( "head_line", $headline->variable( "strings", "head_line_edit" ) );

    $t->set_var( "read_only", "readonly=readonly" );

    $actionValue = "update";
}
else // either new or failed edit... must put htmlspecialchars on stuff we got from form.
{
    $login = false;
    $email = false;
    $firstName = false;
    $lastName = false;
    $phone = "";
    $accountNumber = "";
    $signature = false;
    $simultaneousLogins = false;
    $infoSubscription = false;


    if( isset( $_POST['Login'] ) )
        $login = $_POST['Login'];
    if( isset( $_POST['Email'] ) )
        $email = $_POST['Email'];
    if( isset( $_POST['FirstName'] ) )
        $firstName = $_POST['FirstName'];
    if( isset( $_POST['LastName'] ) )
        $lastName = $_POST['LastName'];
    if( isset( $_POST['Phone'] ) )
        $phone = $_POST['Phone'];
    if( isset( $_POST['AccountNumber'] ) )
        $accountNumber = $_POST['AccountNumber'];
    if( isset( $_POST['Signature'] ) )
        $signature = $_POST['Signature'];
    if( isset( $_POST['SimultaneousLogins'] ) )
        $simultaneousLogins = $_POST['SimultaneousLogins'];
    if( isset( $_POST['InfoSubscription'] ) )
        $infoSubscription = $_POST['InfoSubscription'];
    if( isset( $_POST['UserID'] ) )
        $userID = $_POST['UserID'];

    $firstName = htmlspecialchars( $firstName );
    $lastName = htmlspecialchars( $lastName );
    $phone = htmlspecialchars( $phone );
    $lastName = htmlspecialchars( $lastName );
    $login = htmlspecialchars( $login );
    $accountNumber = htmlspecialchars( $accountNumber );
    $signature = htmlspecialchars( $signature );
    $email = htmlspecialchars( $email );
}

$mainGroup = $user->groupDefinition();
$groupArray = $user->groups();
foreach ( $groupList as $groupItem )
{
    $t->set_var( "group_name", $groupItem->name() );
    $t->set_var( "group_id", $groupItem->id() );
    
    if ( $mainGroup == $groupItem->id() )
        $t->set_var( "main_selected", "selected" );
    else
        $t->set_var( "main_selected", "" );
    
    // add validation code here. $user->isValid();
    if ( $user )
    {
        $found = false;
        foreach ( $groupArray as $group )
        {
            if ( $group->id() == $groupItem->id() && $group->id() != $mainGroup )
            {
                $found = true;
            }
        }
        if ( $found == true )
            $t->set_var( "selected", "selected" );
        else
            $t->set_var( "selected", "" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }

    $t->parse( "main_group_item", "main_group_item_tpl", true );
    $t->parse( "group_item", "group_item_tpl", true );
}

$t->set_var( "info_subscription", $infoSubscription );
$t->set_var( "error", $error_msg );
$t->set_var( "first_name_value", $firstName );
$t->set_var( "last_name_value", $lastName );
$t->set_var( "email_value", $email );
$t->set_var( "phone_value", $phone );
$t->set_var( "login_value", $login );
$t->set_var( "account_number_value", $accountNumber );
$t->set_var( "signature", $signature );
$t->set_var( "password_value", "" );
$t->set_var( "verify_password_value", "" );
$t->set_var( "action_value", $actionValue );
$t->set_var( "user_id", $userID );
$t->set_var( "simultaneouslogins_value", $simultaneousLogins );
$t->pparse( "output", "user_edit" );

?>