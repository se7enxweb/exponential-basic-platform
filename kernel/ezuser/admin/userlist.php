<?php
//
// $id: userlist.php 9671 2002-07-14 14:11:58Z kaid $
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
// include_once( "classes/ezlist.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZUserMain", "Language" );
$errorIni = new eZINI( "kernel/ezuser/admin/intl/" . $language . "/userlist.php.ini", false );

$max = $ini->variable( "eZUserMain", "MaxUserList" );

// include_once( "ezuser/classes/ezuser.php" );
// include_once( "ezuser/classes/ezusergroup.php" );

require( "kernel/ezuser/admin/admincheck.php" );

$t = new eZTemplate( "kernel/ezuser/admin/" . $ini->variable( "eZUserMain", "AdminTemplateDir" ),
                     "kernel/ezuser/admin/" . "/intl", $language, "userlist.php" );
$t->setAllStrings();

$t->set_file( "user_list_page", "userlist.tpl" );

$t->set_block( "user_list_page", "user_item_tpl", "user_item" );
$t->set_block( "user_item_tpl", "user_email_item_tpl", "user_email_item" );
$t->set_block( "user_item_tpl", "user_empty_email_item_tpl", "user_empty_email_item" );

$t->set_block( "user_list_page", "group_item_tpl", "group_item" );

$t->set_var( "site_style", $siteDesign );
$t->set_var( "OldSearchText", "" );

$user = new eZUser();

$orderBy = "name";

if( !isset( $lastName ) )
    $lastName = false;
if( !isset( $firstName ) )
    $firstName = false;
if( !isset( $login ) )
    $login = false;
if( !isset( $eMail ) )
    $eMail = false;
if( !isset( $login ) )
    $login = false;
if( !isset( $searchText ) )
    $searchText = false;

$lastName = addslashes(trim($lastName));
$firstName = addslashes(trim($firstName));
$login = addslashes(trim($login));
$eMail = addslashes(trim($eMail));
$searchText = addslashes (trim ($searchText) );

if ( !is_numeric( $max ) )
    $max = 10;
if ( !is_numeric( $index ) )
    $index = 0;

if ( isset( $search ) && $searchText != "" )
{
    $userList = $user->search( $searchText, $orderBy );
    $totalTypes =  count( $userList );
}
else if (   $firstName != "" 
		  or $lastName != ""
		  or $login != ""
		  or $eMail != ""
		 )
{
	$userList = $user->search( $searchText, $orderBy, $lastName, $firstName, $eMail, $login, $match);
	$totalTypes =  count( $userList );
}
else if ( $groupID == 0 )
{
    $userList = $user->getAll( $orderBy, true, false, $max, $index );
    $totalTypes = $user->getAllCount();
}
else
{
    $usergroup = new eZUserGroup();
    $userList = $usergroup->users( $groupID, $orderBy );
    $totalTypes =  count( $userList );
}

$t->set_var( "user_count", count( $userList ) );
$t->set_var( "total_user_count", $totalTypes );

if ( count( $userList ) == 0 )
{
    $error = $errorIni->variable( "strings", "no_users" );
    $t->set_var( "user_item", $error );
}
else
{
    $i = 0;
    foreach ( $userList as $userItem )
    {
        $t->set_var( "user_email_item", "" );
        $t->set_var( "user_empty_email_item", "" );

        if ( ( $i % 2 ) == 0 )
            $t->set_var( "td_class", "bglight" );
        else
            $t->set_var( "td_class", "bgdark" );
        
        $t->set_var( "first_name", $userItem->firstName() );
        $t->set_var( "last_name", $userItem->lastName() );
        $t->set_var( "login_name", $userItem->login() );
        $email = $userItem->email();
        if ( empty( $email ) )
        {
            $t->parse( "user_empty_email_item", "user_empty_email_item_tpl" );
        }
        else
        {
            $t->set_var( "email", $email );
            $t->parse( "user_email_item", "user_email_item_tpl" );
        }
        $t->set_var( "email", $userItem->email() );
        $t->set_var( "user_id", $userItem->id() );
        
//      if ( $userItem->infoSubscription( ) == true )
//      {
//          print( $userItem->email() . "<br>" );
//      }
        
        $t->parse( "user_item", "user_item_tpl", true );
        $i++;
    }
}

eZList::drawNavigator( $t, $totalTypes, $max, $index, "user_list_page" );

$group = new eZUserGroup();
$groupList = $group->getAll();
    

foreach ( $groupList as $groupItem )
{

//  print( $groupID . " " . $groupItem->id() . "<br>" );
   
    if ( $groupItem->id() == $groupID )
    {
        $t->set_var( "is_selected", "selected" );
    }
    else
    {
        $t->set_var( "is_selected", "" );
    }
    $t->set_var( "group_name", $groupItem->name() );
    $t->set_var( "group_id", $groupItem->id() );

    $t->parse( "group_item", "group_item_tpl", true );
}

$t->set_var( "current_group_id", $groupID );
$t->set_var( "sort_order", $orderBy );

$t->pparse( "output", "user_list_page" );

?>