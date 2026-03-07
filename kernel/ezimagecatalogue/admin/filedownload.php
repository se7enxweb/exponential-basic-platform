<?php
// 
// $id: filedownload.php 7514 2001-09-27 11:48:27Z br $
//
// Created on: <10-Dec-2000 16:39:10 bf>
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

$file = new eZImage( $imageID );
$originalFileName = $file->originalFileName();
$relFilePath = $file->filePath( true );

$siteRoot = '/var/www/vhosts/latest.basic.demo.ezpublish.one/public_html/';
$absFilePath = $siteRoot . $relFilePath;

$user = eZUser::currentUser();
if ( eZObjectPermission::hasPermission( $file->id(), "imagecatalogue_image", "r", $user ) == false )
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}

if ( !file_exists( $absFilePath ) || !is_file( $absFilePath ) )
{
    eZHTTPTool::header( "Location: /error/404" );
    exit();
}

// Redirect to the user-facing download handler which sends the file directly
// without the admin kernel template wrapper interfering with binary output.
$siteINI = eZINI::instance( 'site.ini' );
$userSiteURL = $siteINI->variable( 'site', 'UserSiteURL' );
$safeFilename = rawurlencode( basename( $originalFileName ) );
header( "Location: https://" . $userSiteURL . "/imagecatalogue/download/" . (int)$imageID . "/" . $safeFilename . "/" );

// Prevent the kernel's fatal-error shutdown handler from appending HTML output.
eZExecution::setCleanExit();

exit();

?> 