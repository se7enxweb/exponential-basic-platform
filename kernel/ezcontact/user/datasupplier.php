<?php
//
// $Id: datasupplier.php 7816 2001-10-12 12:27:35Z jhe $
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

// include_once( "classes/ezuritool.php" );

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZContactMain", "DefaultSection" );

$url_array = eZURITool::split($_SERVER["REQUEST_URI"] );
$url_array_count = count( $url_array );

for( $i = $url_array_count; $i <= 25; $i++ )
{
    $url_array[$i] = false;
}

if ( is_object( $user ) )
{
    $userID = $user->id();
}

if ( isset( $userID ) && $userID > 0 )
{
    $Add_User = false;
}
else
{
    $Add_User = true;
}

$action             = eZHTTPTool::getVar( 'Action' );
$add                = eZHTTPTool::getVar( 'Add' );
$companyEdit        = eZHTTPTool::getVar( 'CompanyEdit' );
$companyID          = eZHTTPTool::getVar( 'CompanyID' );
$consultationID     = eZHTTPTool::getVar( 'ConsultationID' );
$consultationList   = $_POST['ConsultationList'] ?? [];
$contactArrayID     = $_POST['ContactArrayID'] ?? [];
$mailButton         = eZHTTPTool::getVar( 'MailButton' );
$moduleName         = eZHTTPTool::getVar( 'ModuleName' );
$newCompany         = eZHTTPTool::getVar( 'NewCompany' );
$newCompanyCategory = eZHTTPTool::getVar( 'NewCompanyCategory' );
$newParentID        = eZHTTPTool::getVar( 'NewParentID' );
$newPerson          = eZHTTPTool::getVar( 'NewPerson' );
$offset             = eZHTTPTool::getVar( 'Offset' );
$personID           = eZHTTPTool::getVar( 'PersonID' );
$personOffset       = eZHTTPTool::getVar( 'PersonOffset' );
$searchCategory     = eZHTTPTool::getVar( 'SearchCategory' );
$searchObject       = eZHTTPTool::getVar( 'SearchObject' );
$searchResult       = eZHTTPTool::getVar( 'SearchResult' );
$searchText         = eZHTTPTool::getVar( 'SearchText' );
$searchType         = eZHTTPTool::getVar( 'SearchType' );
$sendMail           = eZHTTPTool::getVar( 'SendMail' );
$showStats          = eZHTTPTool::getVar( 'ShowStats' );
$subAction          = eZHTTPTool::getVar( 'SubAction' );
$type               = eZHTTPTool::getVar( 'Type' );
$typeID             = eZHTTPTool::getVar( 'TypeID' );

switch ( $url_array[2] )
{
    case "nopermission":
    {
        $type = $url_array[3];
        switch ( $type )
        {
            case "company":
            {
                $action = $url_array[4];
                include( "kernel/ezcontact/admin/nopermission.php" );
                break;
            }
            case "category":
            {
                $action = $url_array[4];
                include( "kernel/ezcontact/admin/nopermission.php" );
                break;
            }
            case "person":
            {
                $action = $url_array[4];
                include( "kernel/ezcontact/admin/nopermission.php" );
                break;
            }
            case "login":
            case "consultation":
            {
                include( "kernel/ezcontact/admin/nopermission.php" );
                break;
            }
            case "type":
            {
                $action = $url_array[4];
                include( "kernel/ezcontact/admin/nopermission.php" );
                break;
            }
            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/error?Type=404&Uri=$_SERVER['REQUEST_URI']&Query=$QUERY_STRING&BackUrl=$HTTP_REFERER" );
                break;
            }
        }
        break;
    }

    case "search":
    {
        $searchType = $url_array[3];
        switch ( $searchType )
        {
            case "company":
            {
                include( "kernel/ezcontact/user/companysearch.php" );
                break;
            }
            case "person":
            {
                include( "kernel/ezcontact/user/personsearch.php" );
                break;
            }
        }
        break;
    }

    case "person":
    {
        $action = $url_array[3];
        $personID = $url_array[4];
        switch ( $action )
        {
            // intentional fall through
            case "new":
            case "edit":
            case "update":
            case "delete":
            case "insert":
            {
                $companyEdit = false;
                if ( isset( $sendMail ) )
                {
                    include( "kernel/ezcontact/admin/sendmail.php" );
                }
                else if ( isset( $mailButton ) )
                {
                    $contactArrayID = array( $personID );
                    include( "kernel/ezcontact/admin/sendmail.php" );
                }
                else
                {
                    if ( isset( $newPerson ) )
                        $action = "new";
                    include( "kernel/ezcontact/admin/personedit.php" );
                }
                break;
            }
            case "list":
            {
                if ( is_numeric( $url_array[4] ) )
                    $offset = $url_array[4];
                include( "kernel/ezcontact/admin/personlist.php" );
                break;
            }
            case "search":
            {
                if ( is_numeric( $url_array[4] ) )
                    $offset = $url_array[4];
                if ( count( $url_array ) >= 5 && !isset( $searchText ) )
                {
                    $searchText = eZURITool::decode( $url_array[5] );
                }
                include( "kernel/ezcontact/admin/personlist.php" );
                break;
            }
            case "view":
            {
                include( "kernel/ezcontact/admin/personview.php" );
                break;
            }
            case "folder":
            {
                $item_id = $url_array[4];
                include( "kernel/ezcontact/admin/folder.php" );
                break;
            }
            case "buy":
            {
                include( "kernel/ezcontact/admin/buy.php" );
                break;
            }
            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/error?Type=404&Uri=$_SERVER['REQUEST_URI']&Query=$QUERY_STRING&BackUrl=$HTTP_REFERER" );
                break;
            }
        }
        break;
    }

    case "company":
    {
        $action = $url_array[3];
        $companyID = $url_array[4];
        switch ( $action )
        {
            case "new":
            case "edit":
            case "update":
            case "delete":
            case "insert":
            {
                $companyEdit = true;
                if ( isset( $sendMail ) )
                {
                    include( "kernel/ezcontact/admin/sendmail.php" );
                }
                else if ( isset( $mailButton ) )
                {
                    $contactArrayID = array( $companyID );
                    include( "kernel/ezcontact/admin/sendmail.php" );
                }
                else
                {
                    if ( isset( $newCompany ) )
                    {
                        // include_once( "classes/ezhttptool.php" );
                        eZHTTPTool::header( "Location: /contact/company/new/$companyID" );
                        exit;
                    }
                    if ( $action == "new" )
                        if ( isset( $url_array[4] ) and is_numeric( $url_array[4] ) )
                            $newCompanyCategory = $url_array[4];
//                        else if ( !isset( $companyID ) and isset( $url_array[4] ) and is_numeric( $url_array[4] ) )
//                            $companyID = $url_array[4];
                    include( "kernel/ezcontact/admin/companyedit.php" );
                }
                break;
            }
            case "view":
            {
                if ( !isset( $companyID ) and isset( $url_array[4] ) and is_numeric( $url_array[4] ) )
                    $companyID = $url_array[4];
                $personOffset = $url_array[5];
                // include_once( "ezcontact/classes/ezcompany.php" );
                eZCompany::addViewHit( $companyID );
                include( "kernel/ezcontact/admin/companyview.php" );
                break;
            }
            case "list":
            {
                $typeID = $url_array[4];
                $offset = $url_array[5];
                $showStats = false;
                include( "kernel/ezcontact/admin/companytypelist.php" );
                break;
            }
            case "folder":
            {
                $item_id = $url_array[4];
                $companyEdit = true;
                include( "kernel/ezcontact/admin/folder.php" );
                break;
            }
            case "buy":
            {
                include( "kernel/ezcontact/admin/buy.php" );
                break;
            }
            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/error?Type=404&Uri=$_SERVER['REQUEST_URI']&Query=$QUERY_STRING&BackUrl=$HTTP_REFERER" );
                break;
            }
        }
        break;
    }

    case "companycategory" :
    {
        $typeID = $url_array[4];
        $action = $url_array[3];
        switch ( $action )
        {
            // intentional fall through
            case "new":
            {
                $newParentID = $url_array[4];
                unset( $typeID );
                include( "kernel/ezcontact/admin/companytypeedit.php" );
                break;
            }
            case "edit":
            case "update":
            case "delete":
            case "insert":
            {
                include( "kernel/ezcontact/admin/companytypeedit.php" );
                break;
            }
            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/error?Type=404&Uri=$_SERVER['REQUEST_URI']&Query=$QUERY_STRING&BackUrl=$HTTP_REFERER" );
                break;
            }
        }
        break;
    }

    case "consultation":
    {
        if ( isset( $url_array[4] ) && ( !isset( $consultationID ) or !is_numeric( $consultationID ) ) )
            $consultationID = $url_array[4];
        if ( isset( $new_consultation ) )
        {
            // include_once( "classes/ezhttptool.php" );
            eZHTTPTool::header( "Location: /contact/consultation/new" );
            exit();
        }
        $action = $url_array[3];
        switch ( $action )
        {
            // intentional fall through
            case "new":
            case "edit":
            case "update":
            case "delete":
            case "insert":
            {
                include( "kernel/ezcontact/admin/consultationedit.php" );
                break;
            }
            case "view":
            {
                include( "kernel/ezcontact/admin/consultationview.php" );
                break;
            }
            case "list":
            {
                include( "kernel/ezcontact/admin/consultationlist.php" );
                break;
            }
            case "company":
            {
                $subAction = $url_array[3];
                $action = $url_array[4];
                if ( !isset( $companyID ) or !is_numeric( $companyID ) )
                    $companyID = $url_array[5];
                switch ( $action )
                {
                    // intentional fall through
                    case "delete":
                    {
                        $consultationID = $url_array[5];
                    }
                    case "new":
                    case "edit":
                    case "update":
                    case "insert":
                    {
                        include( "kernel/ezcontact/admin/consultationedit.php" );
                        break;
                    }
                    case "list":
                    {
                        $consultationList = true;
                        include( "kernel/ezcontact/admin/consultationlist.php" );
                        break;
                    }
                    case "view":
                    {
                        include( "kernel/ezcontact/admin/consultationview.php" );
                        break;
                    }
                }
                break;
            }
            case "person":
            {
                $subAction = $url_array[3];
                $action = $url_array[4];
                if ( !isset( $personID ) or !is_numeric( $personID ) )
                    $personID = $url_array[5];
                switch ( $action )
                {
                    // intentional fall through
                    case "delete":
                    {
                        $consultationID = $url_array[5];
                    }
                    case "new":
                    case "edit":
                    case "update":
                    case "insert":
                    {
                        include( "kernel/ezcontact/admin/consultationedit.php" );
                        break;
                    }
                    case "list":
                    {
                        $consultationList = true;
                        include( "kernel/ezcontact/admin/consultationlist.php" );
                        break;
                    }
                    case "view":
                    {
                        include( "kernel/ezcontact/admin/consultationview.php" );
                        break;
                    }
                }
                break;
            }

            default:
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/error?Type=404&Uri=$_SERVER['REQUEST_URI']&Query=$QUERY_STRING&BackUrl=$HTTP_REFERER" );
                break;
            }
        }
        break;
    }

    default :
        print( "<h1>Contact</h1><h2>Thank you for your interest.</h2><p>Sorry, This page isn't for you. Please go back and make another selection. <a href='' onclick='history.back()'>Go back</a></p>" );
        break;
}

?>