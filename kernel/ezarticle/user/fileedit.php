<?php
// 
// $id: fileedit.php 7497 2001-09-27 08:00:49Z jhe $
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

// include_once( "classes/INIFile.php" );
// include_once( "classes/eztemplate.php" );
// include_once( "classes/ezlog.php" );

// include_once( "classes/ezfile.php" );

// include_once( "ezfilemanager/classes/ezvirtualfile.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZArticleMain", "Language" );

// include_once( "ezarticle/classes/ezarticlecategory.php" );
// include_once( "ezarticle/classes/ezarticle.php" );

if ( isset( $deleteSelected ) )
    $action = "Delete";

if ( $action == "Insert" )
{
    $file = new eZFile();

    if ( $file->getUploadedFile( "userfile" ) )
    { 
        $article = new eZArticle( $articleID );

        $uploadedFile = new eZVirtualFile();
        $uploadedFile->setName( $name );
        $uploadedFile->setDescription( $description );

        $uploadedFile->setFile( $file );
        
        $uploadedFile->store();

        $article->addFile( $uploadedFile );

        eZPBLog::writeNotice( "File added to article $articleID from IP: {$_SERVER['REMOTE_ADDR']}" );
    }
    else
    {
        print( $file->name() . " not uploaded successfully" );
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /article/articleedit/filelist/" . $articleID . "/" );
    exit();
}

if ( $action == "Update" )
{
    $file = new eZFile();

    if ( $file->getUploadedFile( "userfile" ) )
    {
        $article = new eZArticle( $articleID );

        $oldFile = new eZFile( $fileID );
        $article->deleteFile( $oldFile );

        $uploadedFile = new eZVirtualFile();
        $uploadedFile->setName( $name );
        $uploadedFile->setDescription( $description );

        $uploadedFile->setFile( $file );

        $uploadedFile->store();

        $article->addFile( $uploadedFile );
    }
    else
    {
        $uploadedFile = new eZVirtualFile( $fileID );
        $uploadedFile->setName( $name );
        $uploadedFile->setDescription( $description );
        $uploadedFile->store();
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /article/articleedit/filelist/" . $articleID . "/" );
    exit();
}


if ( $action == "Delete" )
{
    $article = new eZArticle( $articleID );

    if ( count( $fileArrayID ) != 0 )
    {
        foreach ( $fileArrayID as $fileID )
        {
            $file = new eZVirtualFile( $fileID );
            $article->deleteFile( $file );
        }
    }

    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /article/articleedit/filelist/" . $articleID . "/" );
    exit();    
}

$t = new eZTemplate( "kernel/ezarticle/user/" . $ini->variable( "eZArticleMain", "TemplateDir" ),
                     "kernel/ezarticle/user/intl/", $language, "fileedit.php" );

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
    $article = new eZArticle( $articleID );
    $file = new eZVirtualFile( $fileID );

    $t->set_var( "article_name", $article->name() );

    $t->set_var( "file_id", $file->id() );
    $t->set_var( "name_value", $file->name() );
    $t->set_var( "description_value", $file->description() );
    $t->set_var( "action_value", "Update" );
}

$article = new eZArticle( $articleID );
    
$t->set_var( "article_name", $article->name() );
$t->set_var( "article_id", $article->id() );

$t->pparse( "output", "file_edit_page" );

?>