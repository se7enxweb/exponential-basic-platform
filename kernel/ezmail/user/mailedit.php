<?php
//
// $Id: mailedit.php 9363 2002-03-23 13:24:54Z fh $
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

// include_once( "classes/INIFile.php" );
// include_once( "classes/eztemplate.php" );
// include_once( "classes/ezlocale.php" );
// include_once( "classes/ezhttptool.php" );
// include_once( "ezfilemanager/classes/ezvirtualfile.php" );
// include_once( "ezmail/classes/ezmail.php" );
// include_once( "ezuser/classes/ezuser.php" );
// include_once( "ezsession/classes/ezpreferences.php" );

if ( isset( $mailID ) && $mailID != 0 && !eZMail::isOwner( eZUser::currentUser(), $mailID ) )
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}

if ( isset( $cancel ) )
{
    if ( $mailID != 0 )
    {
        $mail = new eZMail( $mailID );
        $folderID = $mail->folder( false );
    }
    else
    {
        $inbox = eZMailFolder::getSpecialFolder( INBOX );
        $folderID = $inbox->id();
    }
    eZHTTPTool::header( "Location: /mail/folder/$folderID" );
    exit();
}

if ( isset( $toButton ) )
{
    eZHTTPTool::header( "Location: /contact/person/list" );
    exit();
}

if ( isset( $addAttachment ) )
{
    $mailID = save_mail();
    eZHTTPTool::header( "Location: /mail/fileedit/$mailID" );
    exit();
}

if ( isset( $deleteAttachments ) && count( $attachmentArrayID ) > 0 )
{
    foreach ( $attachmentArrayID as $attachmentID )
    {
        $mail = new eZMail( $mailID );
        $file = new eZVirtualFile( $attachmentID );
        $mail->deleteFile( $file );
    }
}

if ( isset( $save ) )
{
    $mailID = save_mail();
    if ( isset( $IDList ) )
    {
        $id_array = preg_split( "/;/", $IDList );
        foreach ( $id_array as $idItem )
        {
            eZMail::addContact( $mailID, $idItem, $companyList );
        }
    }
    $mail = new eZMail( $mailID );
    $mail->setStatus( 'READ', true );

    $drafts = eZMailFolder::getSpecialFolder( DRAFTS );
    $drafts->addMail( $mail );
}

if ( isset( $preview ) )
{
    $mailID = save_mail();
    eZHTTPTool::header( "Location: /mail/view/$mailID" );
    exit();
}

if ( isset( $send ) )
{
    $mailID = save_mail();
    if ( isset( $IDList ) )
    {
        $id_array = preg_split( "/;/", $IDList );
        foreach ( $id_array as $idItem )
        {
            if ( is_numeric( $idItem ) )
                eZMail::addContact( $mailID, $idItem, $companyList );
        }
    }
    // give error message if no valid users where supplied...
    $mail = new eZMail( $mailID );
    if ( $mail->to() == "" && $mail->bcc() == "" && $mail->cc() == "" )
    {
        $error = "no_address";
    }
    else
    {
        $mail->setStatus( 4, true );
        $mail->send();

        $sent = eZMailFolder::getSpecialFolder( SENT );
        $sent->addMail( $mail );
    
        $sentid = $sent->id();
        eZHTTPTool::header( "Location: /mail/folder/$sentid" );
        exit();
    }
}

if ( isset( $ccButton ) )
    $showcc = true;
if ( isset( $bccButton ) )
    $showbcc = true;

$ini = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZMailMain", "Language" ); 

$t = new eZTemplate( "kernel/ezmail/user/" . $ini->variable( "eZMailMain", "TemplateDir" ),
                     "kernel/ezmail/user/intl/", $Language, "mailedit.php" );

$languageIni = new eZINI( "kernel/ezmail/user/intl/" . $Language . "/mailedit.php.ini", false );
$t->setAllStrings();

$t->set_file( "mail_edit_page_tpl", "mailedit.tpl" );

$t->set_block( "mail_edit_page_tpl", "error_message_tpl", "error_message" );
$t->set_block( "mail_edit_page_tpl", "attachment_delete_tpl", "attachment_delete" );
$t->set_block( "mail_edit_page_tpl", "inserted_attachments_tpl", "inserted_attachments" );
$t->set_block( "mail_edit_page_tpl", "bcc_single_tpl", "bcc_single" );
$t->set_block( "mail_edit_page_tpl", "cc_single_tpl", "cc_single" );
$t->set_block( "inserted_attachments_tpl", "attachment_tpl", "attachment" );
$t->set_var( "inserted_attachments", "" );
$t->set_var( "attachment_delete", "" );

$t->set_var( "error_message", "" );
$t->set_var( "site_style", $siteDesign );

$to_string = "";
$id_string = "";
$company_list = false;

if( isset( $toArray["Email"] ) )
for ( $i = 0; $i < count( $toArray["Email"] ); $i++ )
{
    $to_string .= $toArray["Email"][$i];
    $id_string .= $toArray["ID"][$i];
    if ( ( $i + 1 ) < count( $toArray["Email"] ) )
    {
        $to_string .= "; ";
        $id_string .= ";";
    }
    else
    {
        $company_list = $toArray["CompanyEdit"];
    }
}

$t->set_var( "to_value", $to_string );
$t->set_var( "id_value", $id_string );
$t->set_var( "company_value", $company_list );
$t->set_var( "from_value", "" );
$t->set_var( "cc_value", "" );
$t->set_var( "bcc_value", "" );
$t->set_var( "subject_value", "" );
$t->set_var( "mail_body", "" );
$t->set_var( "current_mail_id", "" );
$t->set_var( "cc_single", "" );
$t->set_var( "bcc_single", "" );

// New mail, lets insert some default values 
if ( isset( $mailID ) && $mailID == 0 )
{
    $auto_signature = eZPreferences::variable( "eZMail_AutoSignature" );
    $signature = eZPreferences::variable( "eZMail_Signature" );
    if( $auto_signature && $auto_signature == "true" && $signature != "" )
    {
        $comp_sign = "--\n$signature";
        $t->set_var( "mail_body", htmlspecialchars( $comp_sign  ) );
    }
}
$user = eZUser::currentUser();
$t->set_var( "from_value", $user->email() );

// We are editing an allready existant mail... lets insert it's values 
if ( isset( $mailID ) && $mailID != 0 && eZMail::isOwner( $user, $mailID ) ) // load values from disk!, check that this is really current users mail
{
    $t->set_var( "current_mail_id", $mailID );
    
    $mail = new eZMail( $mailID );
    $t->set_var( "to_value", htmlspecialchars( $mail->to() ) );

    if ( $mail->from() != "" )
        $t->set_var( "from_value", htmlspecialchars( $mail->from() ) );
    $t->set_var( "subject_value", htmlspecialchars( $mail->subject() ) );

    if( isset( $signature ) )
    {
        $signature = eZPreferences::variable( "eZMail_Signature" );
        $mail_body = $mail->body();
        $comp_sign = "$mail_body\n\n--\n$signature";
        $t->set_var( "mail_body", htmlspecialchars( $comp_sign  ) );
    }
    else
    {
        $t->set_var( "mail_body", htmlspecialchars( $mail->body() ) );
    }
    
    if ( $mail->cc() != ""  )
    {
        $showcc = true;
        $t->set_var( "cc_value", htmlspecialchars( $mail->cc() ) );
    }

    if ( $mail->bcc() != "" )
    {
        $showbcc = true;
        $t->set_var( "bcc_value", htmlspecialchars( $mail->bcc() ) );
    }

    $files = $mail->files();
    $i = 0;
    foreach ( $files as $file )
    {
        $t->set_var( "file_name", htmlspecialchars( $file->originalFileName() ) );
        $t->set_var( "file_id", $file->id() );

        $size = $file->siFileSize();
        $t->set_var( "file_size", $size["size-string"] . $size["unit"] );

        ( $i % 2 ) ? $t->set_var( "td_class", "bgdark" ) : $t->set_var( "td_class", "bglight" );
        
        $t->parse( "attachment", "attachment_tpl", true );
        $i++;
    }
    if ( $i > 0 )
    {
        $t->parse( "attachment_delete", "attachment_delete_tpl" );
        $t->parse( "inserted_attachments", "inserted_attachments_tpl", false );
    }
}
else if ( isset( $mailID ) && $mailID == 0 && ( isset( $showcc ) && $showcc || isset( $showbcc) && $showbcc || isset( $signature ) && $signature ) ) //mail not saved, but there is data
{
    $t->set_var( "to_value", htmlspecialchars( $to ) );
    $t->set_var( "id_value", $IDList );
    $t->set_var( "company_value", $companyList );
    $t->set_var( "from_value", htmlspecialchars( $from ) );
    $t->set_var( "cc_value", htmlspecialchars( $cc ) );
    $t->set_var( "bcc_value", htmlspecialchars( $bcc ) );
    $t->set_var( "subject_value",  htmlspecialchars( $subject ) );
    if( $signature )
    {
        $signature = eZPreferences::variable( "eZMail_Signature" );
        if( $signature != "" )
        {
            $comp_sign = "$mailBody\n\n--\n$signature";
            $t->set_var( "mail_body", htmlspecialchars( $comp_sign  ) );
        }
    }
    else
    {
        $t->set_var( "mail_body", htmlspecialchars( $mailBody ) );
    }
    if ( $cc != "" )
        $showcc = true;
    if ( $bcc != "" )
        $showbcc = true;
}

// check if we have any errors... if yes. show them to the user
if ( isset( $error ) )
{
    $t->set_var( "mail_error_message", $languageIni->variable( "strings", "address_error" ) );
    $t->parse( "error_message", "error_message_tpl", true );
}

if ( isset( $showcc ) )
        $t->parse( "cc_single", "cc_single_tpl", false );
if ( isset( $showbcc ) )
        $t->parse( "bcc_single", "bcc_single_tpl", false );

$t->pparse( "output", "mail_edit_page_tpl" );

/*********************** FUNCTIONS ***************************************/

/*
  Saves the mail and returns the ID of the saved mail.
 */
function save_mail()
{
    global $to, $from, $cc, $bcc, $subject, $mailBody, $mailID; // instead of passing them as arguments..

    if ( $mailID == 0 )
    {
        $mail = new eZMail();
        $mail->setOwner( eZUser::currentUser() );
    }
    else
    {
        $mail = new eZMail( $mailID );
    }
    $mail->setTo( $to );
    $mail->setFrom( $from  ); // from NAME
    $mail->setCc( $cc );
    $mail->setBcc( $bcc );
    $mail->setStatus( 0, true );
//    $mail->setReferences( );
//    $mail->setReplyTo( $ );
    $mail->setSubject( $subject );
    $mail->setBodyText( $mailBody );
    $mail->calculateSize();
    
    $mail->store();
    $folder = eZMailFolder::getSpecialFolder( DRAFTS );
    $folder->addMail( $mail );

    return $mail->id();
}

?>