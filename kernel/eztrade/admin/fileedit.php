<?php
// 
// $id: fileedit.php,v 1.7 2001/08/30 07:55:56 bf Exp $
//
// Created on: <21-Dec-2000 18:01:48 bf>
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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );

include_once( "classes/ezfile.php" );

include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezuser/classes/ezobjectpermission.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZTradeMain", "Language" );

include_once( "eztrade/classes/ezproductcategory.php" );
include_once( "eztrade/classes/ezproduct.php" );

if ( isset( $deleteSelected ) )
    $action = "Delete";

if ( $action == "Insert" )
{
    $file = new eZFile();

    if ( $file->getUploadedFile( "userfile" ) )
    { 
        $product = new eZProduct( $productID );

        $uploadedFile = new eZVirtualFile();
        $uploadedFile->setName( $name );
        $uploadedFile->setDescription( $description );

        $uploadedFile->setFile( $file );
        
        $uploadedFile->store();

		eZObjectPermission::removePermissions( $uploadedFile->id(), "filemanager_file", "r" );
		eZObjectPermission::removePermissions( $uploadedFile->id(), "filemanager_file", "w" );

		eZObjectPermission::setPermission( -1, $uploadedFile->id(), "filemanager_file", "r" );
		eZObjectPermission::setPermission( 1, $uploadedFile->id(), "filemanager_file", "w" );
		
        $product->addFile( $uploadedFile );

        eZPBLog::writeNotice( "File added to product $productID  from IP: $REMOTE_ADDR" );
    }
    else
    {
        print( $file->name() . " not uploaded successfully" );
    }

    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /trade/productedit/filelist/" . $productID . "/" );
    exit();
}

if ( $action == "Update" )
{
    $file = new eZFile();

    if ( $file->getUploadedFile( "userfile" ) )
    {
        $product = new eZTrade( $productID );

        $oldFile = new eZFile( $fileID );
        $product->deleteFile( $oldFile );

        $uploadedFile = new eZVirtualFile();
        $uploadedFile->setName( $name );
        $uploadedFile->setDescription( $description );

        $uploadedFile->setFile( $file );

        $uploadedFile->store();

        $product->addFile( $uploadedFile );
    }
    else
    {
        $uploadedFile = new eZVirtualFile( $fileID );
        $uploadedFile->setName( $name );
        $uploadedFile->setDescription( $description );
        $uploadedFile->store();
    }

    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /trade/productedit/filelist/" . $productID . "/" );
    exit();
}


if ( $action == "Delete" )
{
    $product = new eZProduct( $productID );

    if ( count ( $fileArrayID ) != 0 )
    {
        foreach( $fileArrayID as $fileID )
        {
            $file = new eZVirtualFile( $fileID );
            $product->deleteFile( $file );
        }
    }

    include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /trade/productedit/filelist/" . $productID . "/" );
    exit();    
}

$t = new eZTemplate( "eztrade/admin/" . $ini->variable( "eZTradeMain", "AdminTemplateDir" ),
                     "eztrade/admin/intl/", $language, "fileedit.php" );

$t->setAllStrings();

$t->set_file( "file_edit_page", "fileedit.tpl" );


//default values
$t->set_var( "name_value", "" );
$t->set_var( "description_value", "" );
$t->set_var( "action_value", "Insert" );
$t->set_var( "option_id", "" );
$t->set_var( "file", "" );

if ( $action == "Edit" )
{
    $product = new eZProduct( $productID );
    $file = new eZVirtualFile( $fileID );

    $t->set_var( "product_name", $product->name() );

    $t->set_var( "file_id", $file->id() );
    $t->set_var( "name_value", $file->name() );
    $t->set_var( "description_value", $file->description() );
    $t->set_var( "action_value", "Update" );
}

$product = new eZProduct( $productID );
    
$t->set_var( "product_name", $product->name() );
$t->set_var( "product_id", $product->id() );



$t->pparse( "output", "file_edit_page" );

?>
