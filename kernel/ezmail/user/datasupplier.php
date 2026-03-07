<?php
//
// $Id: datasupplier.php 8870 2002-01-04 09:26:57Z jhe $
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
// include_once( "ezmail/classes/ezmailaccount.php" );
// include_once( "ezmail/classes/ezmailfolder.php" );
// include_once( "ezuser/classes/ezuser.php" );

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZMailMain", "DefaultSection" );
$SiteDesign = $ini->variable( "site", "SiteDesign" );

$accountActiveArrayID = $_POST['AccountActiveArrayID'] ?? [];
$accountArrayID    = $_POST['AccountArrayID'] ?? [];
$accountID         = eZHTTPTool::getVar( 'AccountID' );
$addAttachment     = eZHTTPTool::getVar( 'AddAttachment' );
$attachmentArrayID = $_POST['AttachmentArrayID'] ?? [];
$autoCheckMail     = eZHTTPTool::getVar( 'AutoCheckMail' );
$autoSignature     = eZHTTPTool::getVar( 'AutoSignature' );
$back              = eZHTTPTool::getVar( 'Back' );
$bcc               = eZHTTPTool::getVar( 'Bcc' );
$bccButton         = eZHTTPTool::getVar( 'BccButton' );
$cancel            = eZHTTPTool::getVar( 'Cancel' );
$cc                = eZHTTPTool::getVar( 'Cc' );
$ccButton          = eZHTTPTool::getVar( 'CcButton' );
$checkSelect       = eZHTTPTool::getVar( 'CheckSelect' );
$companyID         = eZHTTPTool::getVar( 'CompanyID' );
$companyList       = eZHTTPTool::getVar( 'CompanyList' );
$delFromServer     = eZHTTPTool::getVar( 'DelFromServer' );
$delete            = eZHTTPTool::getVar( 'Delete' );
$deleteAccounts    = eZHTTPTool::getVar( 'DeleteAccounts' );
$deleteAttachments = eZHTTPTool::getVar( 'DeleteAttachments' );
$emptyTrash        = eZHTTPTool::getVar( 'EmptyTrash' );
$filterArrayID     = $_POST['FilterArrayID'] ?? [];
$filterID          = eZHTTPTool::getVar( 'FilterID' );
$folderArrayID     = $_POST['FolderArrayID'] ?? [];
$folderID          = eZHTTPTool::getVar( 'FolderID' );
$folderSelectID    = eZHTTPTool::getVar( 'FolderSelectID' );
$forward           = eZHTTPTool::getVar( 'Forward' );
$from              = eZHTTPTool::getVar( 'From' );
$headerSelect      = eZHTTPTool::getVar( 'HeaderSelect' );
$idList            = eZHTTPTool::getVar( 'IdList' );
$link              = eZHTTPTool::getVar( 'Link' );
$login             = eZHTTPTool::getVar( 'Login' );
$mailArrayID       = $_POST['MailArrayID'] ?? [];
$mailBody          = eZHTTPTool::getVar( 'MailBody' );
$mailID            = eZHTTPTool::getVar( 'MailID' );
$match             = eZHTTPTool::getVar( 'Match' );
$move              = eZHTTPTool::getVar( 'Move' );
$name              = eZHTTPTool::getVar( 'Name' );
$newAccount        = eZHTTPTool::getVar( 'NewAccount' );
$newFilter         = eZHTTPTool::getVar( 'NewFilter' );
$newFolder         = eZHTTPTool::getVar( 'NewFolder' );
$numMessages       = eZHTTPTool::getVar( 'NumMessages' );
$ok                = eZHTTPTool::getVar( 'OK' ) ?? eZHTTPTool::getVar( 'Ok' );
$onDelete          = eZHTTPTool::getVar( 'OnDelete' );
$parentID          = eZHTTPTool::getVar( 'ParentID' );
$password          = eZHTTPTool::getVar( 'Password' );
$personID          = eZHTTPTool::getVar( 'PersonID' );
$port              = eZHTTPTool::getVar( 'Port' );
$preview           = eZHTTPTool::getVar( 'Preview' );
$reply             = eZHTTPTool::getVar( 'Reply' );
$replyAll          = eZHTTPTool::getVar( 'ReplyAll' );
$save              = eZHTTPTool::getVar( 'Save' );
$searchText        = eZHTTPTool::getVar( 'SearchText' );
$send              = eZHTTPTool::getVar( 'Send' );
$server            = eZHTTPTool::getVar( 'Server' );
$showUnread        = eZHTTPTool::getVar( 'ShowUnread' );
$signature         = eZHTTPTool::getVar( 'Signature' );
$sortMethod        = eZHTTPTool::getVar( 'SortMethod' );
$subject           = eZHTTPTool::getVar( 'Subject' );
$to                = eZHTTPTool::getVar( 'To' );
$toButton          = eZHTTPTool::getVar( 'ToButton' );

switch ( $url_array[2] )
{
    case "foldersort" : // change the sort mode of the folder list
    {
        $folderID = $url_array[3];
        $sortMethod = $url_array[4];
        $offset = 0;
        include( "kernel/ezmail/user/maillist.php" );
    }
    break;

    case "folder" :
    {
        $folderID = $url_array[3];
        $offset = $url_array[4];
        if ( $offset == "" )
            $offset = 0;
//        if ( $folderID == "" )
//            $folderID = get INBOX.
        
        include( "kernel/ezmail/user/maillist.php" );
    }
    break;

    case "view" :
    {
        $mailID = $url_array[3];
        include( "kernel/ezmail/user/mailview.php" );
    }
    break;

    case "folderedit" :
    {
        $folderID = $url_array[3];
        if ( $folderID == "" )
            $folderID = 0;
        include( "kernel/ezmail/user/folderedit.php" );
    }
    break;

    case "folderlist" :
    {
        include( "kernel/ezmail/user/folderlist.php" );
    }
    break;
    
    case "mailedit" :
    {
        $mailID = $url_array[3];
        if ( $mailID == "" )
            $mailID = 0;
        include( "kernel/ezmail/user/mailedit.php" );
    }
    break;

    case "fileedit" :
    {
        $mailID = $url_array[3];
        if ( $mailID == "" )
            $mailID = 0;
        include( "kernel/ezmail/user/fileedit.php" );
    }
    break;
    
    case "config" :
    {
        include( "kernel/ezmail/user/configure.php" );
    }
    break;

    case "accountedit" :
    {
        $accountID = $url_array[3];
        if ( $accountID == "" )
            $accountID = 0;
        include( "kernel/ezmail/user/accountedit.php" );
    }
    break;

    case "check" : // check the mail for this user!
    {
        $user = eZUser::currentUser();
        $accounts = eZMailAccount::getByUser( $user->id() );

        foreach ( $accounts as $account )
        {
            if ( $account->isActive() )
                $account->checkMail();
        }

        eZHTTPTool::header( "Location: /mail/folderlist/" );
        exit();
//        $server = "{" . "zap.ez.no" . "/pop3:" . "110" ."}";
//        $mbox = imap_open( $server, "larson", "AcRXYJJA", OP_HALFOPEN)
//             or die("can't connect: ".imap_last_error());

//        $structure = imap_fetchstructure( $mbox, 1 );
//        echo "<pre>"; print_r( $structure ); echo "</pre>";
//        print( imap_fetchbody( $mbox, 1, 2 ) ); 
//        imap_close( $mbox );
    }
    break;

    case "filteredit" :
    {
        $filterID = $url_array[3];
        if ( $filterID == "" )
            $filterID = 0;
        include( "kernel/ezmail/user/filteredit.php" );
    }
    break;

    case "search" :
    {
        include( "kernel/ezmail/user/search.php" );
    }
    break;

    case "link" :
    {
        $mailID = $url_array[3];
        include( "kernel/ezmail/user/link.php" );
    }
    break;
    
    default:
    {
        eZHTTPTool::header( "Location: /error/404/" );
        exit();
    }
    break;
}

?>