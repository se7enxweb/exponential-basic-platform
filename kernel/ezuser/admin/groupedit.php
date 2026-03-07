<?php
//
// $id: groupedit.php 9537 2002-05-15 16:02:04Z bf $
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

// include_once( "ezuser/classes/ezuser.php" );
// include_once( "ezuser/classes/ezusergroup.php" );
// include_once( "ezuser/classes/ezmodule.php" );
// include_once( "ezuser/classes/ezpermission.php" );

require( "kernel/ezuser/admin/admincheck.php" );

$user = eZUser::currentUser();

if ( isset( $deleteGroups ) and isset( $groupArrayID ) )
{
    $hasRoot = $user->hasRootAccess();
    foreach ( $groupArrayID as $groupid )
    {
	if( $hasRoot )
	{
        (new eZUserGroup())->delete( $groupid );
	}
	else
	{
	    $group = new eZUserGroup( $groupid );
	    if( !$group->isRoot() )
            (new eZUserGroup())->delete( $groupid );
	}
    }
    eZHTTPTool::header( "Location: /user/grouplist" );
    exit();
}

if ( isset( $back ) )
{
    eZHTTPTool::header( "Location: /user/grouplist/" );
    exit();
}



// do not allow editing users with root access while you do not.
if( isset( $groupID ) )
{
    $editGroup = new eZUserGroup( $groupID );
    if( !$user->hasRootAccess() && $editGroup->isRoot() )
    {
	$info = urlencode( "Can't edit a group with root priveliges." );
	eZHTTPTool::header( "Location: /error/403?Info=$info" );
	exit();
    }
}

if ( isset( $action ) && $action == "insert" )
{
    if ( eZPermission::checkPermission( $user, "eZUser", "GroupAdd" ) )
    {
		if ( $name == "" || $description == "" )
		{
			$error = new eZINI( "kernel/ezuser/admin/intl/" . $language . "/groupedit.php.ini", false );
			$error_msg =  $error->variable( "strings", "error_msg" );
		}
		else
		{
			$group = new eZUserGroup();
			$group->setName( $name );
			$group->setDescription( $description );
			$group->setSessionTimeout( $sessionTimeout );
			$group->setGroupURL( $groupURL );

			if ( isset( $isRoot ) && $user->hasRootAccess() )
			$group->setIsRoot( 1 );
			else
			$group->setIsRoot( 0 );
			$permission = new eZPermission();

			$group->store();

			$group->get( $group->id() );

			$permissionList = $permission->getAll();

			foreach ( $permissionList as $permissionItem )
			{
			$permissionItem->setEnabled( $group, false );
			}

			if ( isset( $permissionArray ) && count ( $permissionArray ) > 0 )
			{
				foreach ( $permissionArray as $permissionID )
				{
					$permission->get( $permissionID );
					$permission->setEnabled( $group, true );
				}
			}

			eZHTTPTool::header( "Location: /user/grouplist/" );
			exit();
		}
    }
    else
    {
		eZHTTPTool::header( "Location: /error/403/" );
	exit();
    }
}

if ( isset( $action ) && $action == "delete" )
{
    if ( eZPermission::checkPermission( $user, "eZUser", "GroupDelete" ) )
    {

	$group = new eZUserGroup();
	$group->get( $groupID );

	$group->delete();

	eZHTTPTool::header( "Location: /user/grouplist/" );
	exit();
    }
    else
    {
	print( "No rights.");
    }
}

if ( isset( $action ) && $action == "update" )
{
    if ( eZPermission::checkPermission( $user, "eZUser", "GroupModify" ) )
    {
	$permission = new eZPermission();
	$group = new eZUserGroup();
	$group->get( $groupID );
	$group->setName( $name );
	$group->setGroupURL( $groupURL );
	$group->setDescription( $description );
	$group->setSessionTimeout( $sessionTimeout );

	if ( isset( $isRoot ) && $user->hasRootAccess() )
	    $group->setIsRoot( true );
	else
	    $group->setIsRoot( false );

	$permissionList = $permission->getAll();

	foreach ( $permissionList as $permissionItem )
	{
	    $permissionItem->setEnabled( $group, false );
	}

	foreach ( $permissionArray as $permissionID )
	{
	    $permission->get( $permissionID );
	    $permission->setEnabled( $group, true );
	}

	$group->store();

	eZHTTPTool::header( "Location: /user/grouplist/" );
	exit();
    }
    else
    {
	eZHTTPTool::header( "Location: /error/403/" );
	exit();
    }
}

// Template
$t = new eZTemplate( "kernel/ezuser/admin/" . $ini->variable( "eZUserMain", "AdminTemplateDir" ),
"kernel/ezuser/admin/" . "/intl", $language, "groupedit.php" );
$t->setAllStrings();

$t->set_file( "group_edit", "groupedit.tpl" );

$t->set_block( "group_edit", "module_list_header_tpl", "module_header" );
$t->set_block( "module_list_header_tpl", "permission_list_tpl", "permission_item" );
$t->set_block( "permission_list_tpl", "permission_enabled_tpl", "is_enabled_item" );

$headline = new eZINI( "kernel/ezuser/admin/intl/" . $language . "/groupedit.php.ini", false );
$t->set_var( "head_line", $headline->variable( "strings", "head_line_insert" ) );

if ( isset( $action ) && $action == "new" )
{
    $name = "";
    $description = "";
    $groupURL = "";
}
$actionValue = "insert";

// Edit
if ( isset( $action ) && $action == "edit" )
{
    $group = new eZUserGroup();
    $group->get( $groupID );

    $name = $group->name();
    $description = $group->description();
    $groupURL = $group->groupURL();
    $sessionTimeout = $group->sessionTimeout();
    $isRoot = $group->isRoot();
    $actionValue = "update";

    $headline = new eZINI( "kernel/ezuser/admin/intl/" . $language . "/groupedit.php.ini", false );
    $t->set_var( "head_line", $headline->variable( "strings", "head_line_edit" ) );
}

// List over all modules.
$module = new eZModule();
$moduleList = $module->getAll( true );

foreach ( $moduleList as $moduleItem )
{
    $t->set_var( "module_name", $moduleItem->name() );
    $t->set_var( "module_id", $moduleItem->id() );

	$permission = new eZPermission();
    $permissionList = $permission->getAllByModule( $moduleItem );

    $t->set_var( "permission_item", "" );

    foreach ( $permissionList as $permissionItem )
    {
	$t->set_var( "permission_name", $permissionItem->name() );
	$t->set_var( "permission_id", $permissionItem->id() );

	if ( isset( $group ) && get_class ( $group ) == "eZUserGroup" && $permissionItem->isEnabled( $group ) )
	{
	    $t->set_var( "is_enabled", "checked" );
	}
	else
	{
	    $t->set_var( "is_enabled", "" );
	}

	$t->parse( "permission_item", "permission_list_tpl", true );
    }

    if ( count( $permissionList ) > 0 )
        $t->parse( "module_header", "module_list_header_tpl", true );
	else
	    $t->set_var( "module_header", "" );
}

$t->set_var( "error_msg", isset( $error_msg ) ? $error_msg : false );
$t->set_var( "name_value", isset( $name ) ? $name : false );
$t->set_var( "description_value", isset( $description ) ? $description : false );
$t->set_var( "group_url_value", isset( $groupURL ) ? $groupURL : false );
$t->set_var( "session_timeout_value", isset( $sessionTimeout ) ? $sessionTimeout : false );
$t->set_var( "action_value", isset( $actionValue ) ? $actionValue : false );

( isset( $isRoot ) && $isRoot == true ) ? $t->set_var( "root_checked", "checked" ) : $t->set_var( "root_checked", "" );

$t->set_var( "group_id", isset( $groupID ) ? $groupID : false );

$t->pparse( "output", "group_edit" );

?>