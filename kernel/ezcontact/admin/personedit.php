<?php
//
// $id: personedit.php 9829 2003-06-03 05:56:24Z jhe $
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

/*
  Edit a person
*/

// include_once( "classes/INIFile.php" );

$ini = eZINI::instance( 'site.ini' );
$language = $ini->variable( "eZContactMain", "Language" );

// include_once( "classes/eztemplate.php" );
// include_once( "classes/ezlog.php" );
// include_once( "classes/eztexttool.php" );

// include_once( "ezaddress/classes/ezcountry.php" );
// include_once( "ezcontact/classes/ezperson.php" );
// include_once( "ezcontact/classes/ezcompany.php" );
// include_once( "ezcontact/classes/ezprojecttype.php" );
// include_once( "ezmail/classes/ezmail.php" );
// include_once( "ezuser/classes/ezusergroup.php" );
// include_once( "ezuser/classes/ezpermission.php" );

// deletes the dayview cache file for a given day
function deleteCache( $siteDesign )
{
    unlinkWild( "./kernel/ezcalendar/user/cache/", "monthview.tpl-$siteDesign-*" );
}

function unlinkWild( $dir, $rege )
{
    $d = eZPBFile::dir( $dir );
    while ( $f = $d->read() )
    {
        if ( preg_match( $rege, $f ) )
        {
            eZPBFile::unlink( $dir . $f );
        }
    }
}

$user = eZUser::currentUser();

if ( isset( $companyEdit ) && $companyEdit )
{
    $item_type = "company";
    $item_id = $companyID;
}
else
{
    $item_type = "person";
    $item_id = $personID;
}

if ( !is_a( $user, "eZUser" ) )
{
    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/nopermission/login" );
    exit();
}

if ( isset( $buyButton ) )
{
    include( "kernel/ezcontact/admin/buy.php" );
}

if ( isset( $ok ) )
{
    if ( $companyEdit )
    {
        if ( isset( $action ) && $action == "edit" || isset( $action ) && $action == "update" )
        {
            if ( !eZPermission::checkPermission( $user, "eZContact", "CompanyModify" ) )
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/nopermission/company/edit" );
                exit();
            }
        }
        else if ( isset( $action ) && $action == "new" || isset( $action ) && $action == "insert" )
        {
            if ( !eZPermission::checkPermission( $user, "eZContact", "CompanyAdd" ) )
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/nopermission/company/new" );
                exit();
            }
        }
    }
    else
    {
        if ( isset( $action ) && $action == "edit" || isset( $action ) && $action == "update" )
        {
            if ( !eZPermission::checkPermission( $user, "eZContact", "PersonModify" ) )
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/nopermission/person/edit" );
                exit();
            }
        }
        else if ( isset( $action ) && $action == "new" || isset( $action ) && $action == "insert" )
        {
            if ( !eZPermission::checkPermission( $user, "eZContact", "PersonAdd" ) )
            {
                // include_once( "classes/ezhttptool.php" );
                eZHTTPTool::header( "Location: /contact/nopermission/person/new" );
                exit();
            }
        }
    }
}

if ( isset( $listConsultation ) )
{
    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/consultation/$item_type/list/$item_id" );
    exit;
}

if ( isset( $newConsultation ) )
{
    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/consultation/$item_type/new/$item_id" );
    exit;
}

if ( isset( $fileButton ) )
{
    include( "kernel/ezcontact/admin/folder.php" );
}

if ( isset( $back ) )
{
    if ( $companyEdit )
    {
        $company = new eZCompany( $companyID );
        $categories = $company->categories( false, false );
        $id = $categories[0];
    }
    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/$item_type/list/$id" );
    exit;
}

if ( isset( $delete ) )
{
    $action = "delete";
}

if ( isset( $action ) && $action == "delete" )
{
    if ( $companyEdit )
    {
        if ( !eZPermission::checkPermission( $user, "eZContact", "CompanyDelete" ) )
        {
            // include_once( "classes/ezhttptool.php" );
            eZHTTPTool::header( "Location: /contact/nopermission/company/delete" );
            exit();
        }
    }
    else
    {
        if ( !eZPermission::checkPermission( $user, "eZContact", "PersonDelete" ) )
        {
            // include_once( "classes/ezhttptool.php" );
            eZHTTPTool::header( "Location: /contact/nopermission/person/delete" );
            exit();
        }
    }

    if ( $companyEdit )
    {
        $categories = (new eZCompany())->categories( $companyID, false, 1 );
        $id =& $categories[0];
        $item_type = "company";
        foreach ( $contactArrayID as $contactItem )
        {
            eZCompany::delete( $contactItem );
        }
    }
    else
    {
        $item_type = "person";
        foreach ( $contactArrayID as $contactItem )
        {
            eZPerson::delete( $contactItem );
        }
    }

    deleteCache( "default" );
    // include_once( "classes/ezhttptool.php" );
    eZHTTPTool::header( "Location: /contact/$item_type/list/$id" );
    exit;
}

if ( isset( $ok ) )
{
    if ( $action == "new" )
        $action = "insert";
    else if ( $action == "edit" )
        $action = "update";
}

$error = false;

if ( $companyEdit )
{
    $template_file = "companyedit.tpl";
    $language_file = "companyedit.php";
}
else
{
    $template_file = "personedit.tpl";
    $language_file = "personedit.php";
}

$t = new eZTemplate( "kernel/ezcontact/admin/" . $ini->variable( "eZContactMain", "AdminTemplateDir" ),
                     "kernel/ezcontact/admin/intl", $language, $language_file );
$t->setAllStrings();

$t->set_file( "person_edit", $template_file );

$t->set_block( "person_edit", "edit_tpl", "edit_item" );
$t->set_block( "person_edit", "confirm_tpl", "confirm_item" );
$t->set_block( "person_edit", "image_item_tpl", "image_item" );

if ( $companyEdit )
{
    $t->set_block( "edit_tpl", "company_item_tpl", "company_item" );
    $t->set_block( "company_item_tpl", "company_type_select_tpl", "company_type_select" );
    $t->set_block( "edit_tpl", "logo_item_tpl", "logo_item" );
    $t->set_block( "edit_tpl", "image_item_tpl", "image_item" );
}
else
{
    $t->set_block( "edit_tpl", "person_item_tpl", "person_item" );
    $t->set_block( "person_item_tpl", "day_item_tpl", "day_item" );
    $t->set_block( "person_item_tpl", "company_select_tpl", "company_select" );
}

$t->set_block( "edit_tpl", "address_table_item_tpl", "address_table_item" );
$t->set_block( "address_table_item_tpl", "address_item_tpl", "address_item" );
$t->set_block( "address_item_tpl", "address_item_select_tpl", "address_item_select" );

$t->set_block( "address_item_tpl", "country_item_select_tpl", "country_item_select" );

$t->set_block( "edit_tpl", "phone_table_item_tpl", "phone_table_item" );
$t->set_block( "phone_table_item_tpl", "phone_item_tpl", "phone_item" );
$t->set_block( "phone_item_tpl", "phone_item_select_tpl", "phone_item_select" );

$t->set_block( "edit_tpl", "online_table_item_tpl", "online_table_item" );
$t->set_block( "online_table_item_tpl", "online_item_tpl", "online_item" );
$t->set_block( "online_item_tpl", "online_item_select_tpl", "online_item_select" );

$t->set_block( "edit_tpl", "project_item_tpl", "project_item" );
$t->set_block( "project_item_tpl", "project_contact_item_tpl", "project_contact_item" );
$t->set_block( "project_contact_item_tpl", "contact_group_item_select_tpl", "contact_group_item_select" );
$t->set_block( "project_contact_item_tpl", "contact_item_select_tpl", "contact_item_select" );
$t->set_block( "project_item_tpl", "project_item_select_tpl", "project_item_select" );

$t->set_block( "person_edit", "delete_item_tpl", "delete_item" );

$t->set_block( "edit_tpl", "errors_tpl", "errors_item" );

if ( $companyEdit )
{
    $t->set_block( "errors_tpl", "error_name_item_tpl", "error_name_item" );
}
else
{
    $t->set_block( "errors_tpl", "error_firstname_item_tpl", "error_firstname_item" );
    $t->set_block( "errors_tpl", "error_lastname_item_tpl", "error_lastname_item" );
    $t->set_block( "errors_tpl", "error_birthdate_item_tpl", "error_birthdate_item" );
}

$t->set_block( "errors_tpl", "error_address_item_tpl", "error_address_item" );
$t->set_block( "errors_tpl", "error_phone_item_tpl", "error_phone_item" );
$t->set_block( "errors_tpl", "error_online_item_tpl", "error_online_item" );
$t->set_block( "errors_tpl", "error_logo_item_tpl", "error_logo_item" );
$t->set_block( "errors_tpl", "error_image_item_tpl", "error_image_item" );

$confirm = false;

if( isset( $action ) && $action == "new" )
{
    if( !isset( $birthDay ) )
        $birthDay = false;
    if( !isset( $birthYear ) )
        $birthYear = false;
    if( !isset( $birthMonth ) )
        $birthMonth = false;
    if( !isset( $comment ) )
        $comment = false;

    $t->set_var( "image_caption", '' );
}

if ( isset( $action ) && $action == "delete" )
{
    if ( !isset( $confirm ) )
    {
        $confirm = true;

        if ( $companyEdit )
        {
            $t->set_var( "company_id", $companyID );
            $company = new eZCompany( $companyID );
            $t->set_var( "name", $company->name() );
        }
        else
        {
            $t->set_var( "person_id", $personID );
            $person = new eZPerson( $personID );
            $t->set_var( "firstname", $person->firstName() );
            $t->set_var( "lastname", $person->lastName() );
        }
        $t->set_var( "edit_item", "" );
        $t->set_var( "action_value", $action );
        $t->set_var( "delete_item", "" );
        $t->parse( "confirm_item", "confirm_tpl" );
    }
}

if ( !$confirm )
{
    $t->set_var( "confirm_item", "" );

    if ( $companyEdit )
    {
        $t->set_var( "name", "" );
        $t->set_var( "companyno", "" );
    }
    else
    {
        $t->set_var( "firstname", "" );
        $t->set_var( "lastname", "" );
        $t->set_var( "birthdate", "" );
        $t->set_var( "comment", "" );
        $t->set_var( "person_id", "" );
    }

    $t->set_var( "user_id", isset( $userID ) ? $userID : false );

    $t->set_var( "contact_group_item_select", "" );
    $t->set_var( "contact_item_select", "" );

/* End of the pre-defined values */
    if ( ( isset( $action ) && $action == "insert" || isset( $action ) && $action == "update" ) )
    {
        if ( $action == "update" )
        {
            deleteCache( "default" );
        }

        if ( $companyEdit )
        {
            $t->set_var( "error_name_item", "" );
        }
        else
        {
            $t->set_var( "error_firstname_item", "" );
            $t->set_var( "error_lastname_item", "" );
            $t->set_var( "error_birthdate_item", "" );
        }

        $t->set_var( "error_address_item", "" );
        $t->set_var( "error_phone_item", "" );
        $t->set_var( "error_online_item", "" );
        $t->set_var( "error_logo_item", "" );
        $t->set_var( "error_image_item", "" );

        if ( $companyEdit )
        {
            if ( $name == "" )
            {
                $t->parse( "error_name_item", "error_name_item_tpl" );
                $error = true;
            }
        }
        else
        {
            if ( $firstName == "" )
            {
                $t->parse( "error_firstname_item", "error_firstname_item_tpl" );
                $error = true;
            }

            if ( $lastName == "" )
            {
                $t->parse( "error_lastname_item", "error_lastname_item_tpl" );
                $error = true;
            }

            if ( $birthYear != "" )
            {
                $birth = new eZDate( $birthYear, $birthMonth, $birthDay );
                if ( !$birth->isValid() )
                {
                    $t->parse( "error_birthdate_item", "error_birthdate_item_tpl" );
                    $error = true;
                }
            }
        }

        if( !isset( $addressID ) )
            $addressID = array();

        $count = max( count( $addressTypeID ), count( $addressID ),
                      count( $street1 ), count( $street2 ),
                      count( $zip ), count( $place ), 1 );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( $addressTypeID[$i] != -1 )
            {
                if ( $street1[$i] == "" || $place[$i] == "" || $country[$i] == "" )
                {
                    $t->set_var( "error_address_position", $i + 1 );
                    $t->parse( "error_address_item", "error_address_item_tpl", true );
                    $error = true;
                }
            }
            else
            {
                if ( $street1[$i] != "" || $street2[$i] != "" || $place[$i] != "" ||
                   ( $country[$i] != -1 and $country[$i] != "" ) )
                {
                    $t->set_var( "error_address_position", $i + 1 );
                    $t->parse( "error_address_item", "error_address_item_tpl", true );
                    $error = true;
                }
            }
        }

        $count = max( count( $phoneTypeID ), count( $phoneID ), count( $phone ) );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( $phoneTypeID[$i] != -1 )
            {
                if ( $phone[$i] == "" )
                {
                    $t->set_var( "error_phone_position", $i + 1 );
                    $t->parse( "error_phone_item", "error_phone_item_tpl", true );
                    $error = true;
                }
            }
            else
            {
                if ( $phone[$i] != "" )
                {
                    $t->set_var( "error_phone_position", $i + 1 );
                    $t->parse( "error_phone_item", "error_phone_item_tpl", true );
                    $error = true;
                }
            }
        }

        $count = max( count( $onlineTypeID ), count( $onlineID ), count( $online ) );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( $onlineTypeID[$i] != -1 )
            {
                if ( $online[$i] == "" )
                {
                    $t->set_var( "error_online_position", $i + 1 );
                    $t->parse( "error_online_item", "error_online_item_tpl", true );
                    $error = true;
                }
            }
            else
            {
                if ( $online[$i] != "" )
                {
                    $t->set_var( "error_online_position", $i + 1 );
                    $t->parse( "error_online_item", "error_online_item_tpl", true );
                    $error = true;
                }
            }
        }

        // Check uploaded logo image
        $file = new eZPBImageFile();
        if ( $file->getUploadedFile( "logo" ) )
        {
            $logo = new eZImage();
            if ( !$logo->checkImage( $file ) )
            {
                $t->parse( "error_logo_item", "error_logo_item_tpl", true );
                $error = true;
            }
        }

        // Check uploaded image
        $file = new eZPBImageFile();
        if ( $file->getUploadedFile( "image" ) )
        {
            $image = new eZImage();
            if ( !$image->checkImage( $file ) )
            {
                $t->parse( "error_image_item", "error_image_item_tpl", true );
                $error = true;
            }
        }

        if ( is_numeric( $companyID ) )
        {
            if ( isset( $deleteImage ) )
            {
                print( "deleteimage $companyID" );
                eZCompany::deleteImage( $companyID );
            }

            if ( isset( $deleteLogo ) )
            {
                print( "deletelogo $companyID" );
                eZCompany::deleteLogo( $companyID );
            }
        }

        if ( $error && isset( $ok ) )
        {
            $t->set_var( "action_value", $action );
            $t->parse( "errors_item", "errors_tpl" );
        }
    }

    if ( $error == false || isset( $refreshUsers ) )
    {
        $t->set_var( "errors_item", "" );
    }
    else
    {
        $action = "formdata";
    }

    if ( ( $action == "insert" || $action == "update" ) && !$error && isset( $ok ) )
    {
        if ( $companyEdit )
        {
            if ( $action == "insert" )
                $company = new eZCompany();
            else
                $company = new eZCompany( $companyID );

            $company->setName( $name );

            $company->setCompanyNo( $companyNo );
            if ( $contactPersonType == "ezperson" )
                $company->setPersonContact( $contactID );
            else
                $company->setContact( $contactID );
            $company->setComment( $comment );
            $company->store();

            $item_id = $company->id();
            $companyID = $item_id;

            // Update categories
            $company->removeCategories();
            $category = new eZCompanyType();
            if ( count( $companyCategoryID ) > 0 )
            {
                for ( $i = 0; $i < count( $companyCategoryID ); $i++ )
                {
                    $category->get( $companyCategoryID[$i] );
                    $category->addCompany( $company );
                }
            }
            else
            {
                $category->get( 0 );
                $category->addCompany( $company );
            }
            $item_cat_id = $companyCategoryID[0];

            // Upload images
            $file = new eZPBImageFile();
            if ( $file->getUploadedFile( "logo" ) )
            {
                $logo = new eZImage();
                $logo->setName( "Logo" );
                if ( $logo->checkImage( $file ) and $logo->setImage( $file ) )
                {
                    $logo->store();
                    $company->setLogoImage( $logo );
                }
                else
                {
                    $company->deleteLogo();
                }
            }
            else
            {
                print( $file->name() . " not uploaded successfully" );
            }

            // Upload images
            $file = new eZPBImageFile();
            if ( $file->getUploadedFile( "image" ) )
            {
                $image = new eZImage( );
                $image->setName( "Image" );
                if ( $image->checkImage( $file ) and $image->setImage( $file ) )
                {
                    $image->store();
                    $company->setCompanyImage( $image );
                }
                else
                {
                    $company->deleteImage();
                }
            }
            else
            {
                print( $file->name() . " not uploaded successfully" );
            }
            $item =& $company;
        }
        else
        {
            $person = new eZPerson( $personID, true );
            $person->setFirstName( $firstName );
            $person->setLastName( $lastName );

            if ( $birthYear != "" )
            {
                $birth = new eZDate( $birthYear, $birthMonth, $birthDay );
                $person->setBirthDay( $birth->timeStamp() );
            }
            else
            {
                $person->setNoBirthDay();
            }
//              $person->setContact( $contactID );
            $person->setComment( $comment );

            if ( $deleteImage == "on" )
                $person->setImage(0);

            $person->store();

            // Upload images
            $file = new eZPBImageFile();
            if ( $file->getUploadedFile( "ImageFile" ) )
            {
                $image = new eZImage( );
                $image->setName( "Image" );
                if ( $image->checkImage( $file ) and $image->setImage( $file ) )
                {
                    $image->store();
                    $person->setImage( $image, $person->id() );
                }
            }
            $person->store();

            $person->removeCompanies();

            if( !isset( $companyID ) )
                $companyID = array();
            for ( $i = 0; $i < count( $companyID ); $i++ )
            {
                (new eZCompany())->addPerson( $person->id(), $companyID[$i] );
            }

            $item_id = $person->id();
            $personID = $item_id;
            $item_cat_id = "";

            $item =& $person;
            //var_dump($item);
        }

        $item->setProjectState( $projectID, $item->id() );

        // address
        $item->removeAddresses();
        $count = max( count( $addressTypeID ), count( $addressID ),
                      count( $street1 ), count( $street2 ),
                      count( $zip ), count( $place ) );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( $street1[$i] != "" && $place[$i] != "" &&
                 $country[$i] != "" && $addressTypeID != -1 )
            {
                if ( isset( $addressDelete ) && !in_array( $i + 1, $addressDelete ) && $addressTypeID[$i] != -1 )
                {
                    $address = new eZAddress();
                    $address->setStreet1( $street1[$i] );
                    $address->setStreet2( $street2[$i] );
                    $address->setZip( $zip[$i] );
                    $address->setPlace( $place[$i] );
                    $address->setAddressType( $addressTypeID[$i] );
                    $address->setCountry( $country[$i] );
                    $address->store();

                    $item->addAddress( $address );
                }
            }
        }

        $item->removePhones();
        $count = max( count( $phoneID ), count( $phone ) );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( isset( $phoneDelete ) && !in_array( $i + 1, $phoneDelete ) && $phone[$i] != "" )
            {
                $phone = new eZPhone( false, true );
                $phone->setNumber( $phone[$i] );
                $phone->setPhoneTypeID( $phoneTypeID[$i] );
                $phone->store();

                $item->addPhone( $phone );
            }
        }

        $item->removeOnlines();
        $count = max( count( $onlineID ), count( $online ) );
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( isset( $onlineDelete ) && !in_array( $i + 1, $onlineDelete ) && $online[$i] != "" )
            {
                $online = new eZOnline( false, true );
                $online->setURL( $online[$i] );
                $online->setOnlineTypeID( $onlineTypeID[$i] );
                $online->store();

                $item->addOnline( $online );
            }
        }

        if ( $companyEdit )
        {
            $companyID = $company->id();
            $item_cat_id = $companyID;
        }
        else
        {
            $personID = $person->id();
            $item_cat_id = $personID;
        }

        $t->set_var( "user_id", $userID );
        $t->set_var( "person_id", $personID );
        $t->set_var( "company_id", $companyID );

        // include_once( "classes/ezhttptool.php" );
        eZHTTPTool::header( "Location: /contact/$item_type/view/$item_cat_id" );
    }

/*
    The user wants to edit an existing person.

    We fetch the appropriate variables.
*/

    if ( $action == "edit" )
    {
        if ( $companyEdit )
        {
            $company = new eZCompany( $companyID, true );
            $item =& $company;

            $name = $company->name();
            $comment = $company->comment();
            $companyNo = $company->companyNo();
        }
        else
        {
            $person = new eZPerson( $personID, true );
            $item =& $person;

            $firstName = $person->firstName();
            $lastName = $person->lastName();
            if ( $person->hasBirthDate() )
            {
                $birth = new eZDate();
                $birth->setTimeStamp( $person->birthDate() );
                $birthYear = $birth->year();
                $birthMonth = $birth->month();
                $birthDay = $birth->day();
            }
            else
            {
                $birthYear = "";
                $birthMonth = 1;
                $birthDay = 1;
            }
            $comment = $person->comment();
            $image = $person->image( $person->id() );
            if ( get_class( $image ) == "ezimage" && $image->id() != 0 )
            {
                $imageWidth = $ini->variable( "eZContactMain", "PersonImageWidth" );
     	        $imageHeight = $ini->variable( "eZContactMain", "PersonImageHeight" );
                $variation = $image->requestImageVariation( $imageWidth, $imageHeight );
                $imageURL = "/" . $variation->imagePath();
                $imageWidth = $variation->width();
                $imageHeight = $variation->height();
                $imageCaption = $image->caption();
                $t->set_var( "image_width", $imageWidth );
                $t->set_var( "image_height", $imageHeight );
                $t->set_var( "image_url", $imageURL );
                $t->set_var( "image_caption", $imageCaption );
                $t->parse( "image_item", "image_item_tpl" );
            }
            else
            {
                $t->set_var( "image_caption", '' );
                $t->parse( "image_item", "image_item_tpl" );
            }
        }

        $addresses = $item->addresses();
        $i = 1;
        foreach ( $addresses as $address )
        {
            $addressTypeID[$i - 1] = $address->addressTypeID();
            $addressID[$i - 1] = $i;
            $street1[$i - 1] = $address->street1();
            $street2[$i - 1] = $address->street2();
            $zip[$i - 1] = $address->zip();
            $place[$i - 1] = $address->place();
            $country = $address->country();
            if ( $country )
                $country[$i - 1] = $country->id();
            else
                $country[$i - 1] = -1;
            $i++;
        }

        $phones = $item->phones();
        $i = 1;
        foreach ( $phones as $phone )
        {
            $phoneTypeID[$i - 1] = $phone->phoneTypeID();
            $phoneID[$i - 1] = $i;
            $phone[$i - 1] = $phone->number();
            $i++;
        }

        $onlines = $item->onlines();
        $i = 1;
        foreach ( $onlines as $online )
        {
            $onlineTypeID[$i - 1] = $online->onlineTypeID();
            $onlineID[$i - 1] = $i;
            $online[$i - 1] = $online->url();
            $i++;
        }

        $contactID = $item->contact();
        if ( is_a( $item, "eZCompany" ) )
            $contactType = $item->contactType();
        else
            $contactType = "eZUser";
        $projectID = $item->projectState();
    }

/*
    The user wants to create a new person/company.

    We present an empty form.
 */
    if ( ( $action == "new" || $action == "formdata" || $action == "edit" || isset( $refreshUsers ) ) )
    {
        if ( isset( $ok ) )
        {
            if ( $action == "edit" )
                $action = "update";
            else if ( $action == "new" )
                $action = "insert";
        }

        if ( $companyEdit )
        {
            $t->set_var( "company_id", $companyID );
            $t->set_var( "user_id", $user->id() );

            if( isset( $name ) && isset( $companyNo ) && isset( $comment ) )
            {
                $t->set_var( "name", eZTextTool::htmlspecialchars( $name ) );
                $t->set_var( "comment", eZTextTool::htmlspecialchars( $comment ) );
                $t->set_var( "companyno", eZTextTool::htmlspecialchars( $companyNo ) );
            }
            else
            {
                $t->set_var( "name", '' );
                $t->set_var( "comment", '' );
                $t->set_var( "companyno", '' );
            }

            // Company type selector
            $companyTypeList = eZCompanyType::getTree();
            $categoryList = array();

            if ( $action != "new" )
            {
                if ( !isset( $companyCategoryID ) )
                    $categoryList = (new eZCompany())->categories( $companyID, false );
                else
                    $categoryList = array( $companyCategoryID );
            }
            if ( isset( $newCompanyCategory ) and !is_numeric( $newCompanyCategory ) )
                $newCompanyCategory = 0;
            if ( isset( $newCompanyCategory ) and is_numeric( $newCompanyCategory ) )
                $categoryList = array_unique( array_merge( array( $newCompanyCategory ), $categoryList ) );
            if ( isset( $companyCategoryID ) )
                $categoryList = array_unique( array_merge( array( $companyCategoryID ), $categoryList ) );
            if ( isset( $categoryList ) && count( $categoryList ) > 0 )
                $category_values = array_values( $categoryList );
            else
                $category_values = array();

            $t->set_var( "is_top_selected", in_array( 0, $category_values ) ? "selected" : "" );
            foreach ( $companyTypeList as $companyTypeItem )
            {
                $t->set_var( "company_type_name", eZTextTool::htmlspecialchars( $companyTypeItem[0]->name() ) );
                $t->set_var( "company_type_id", $companyTypeItem[0]->id() );

                if ( $companyTypeItem[1] > 0 )
                    $t->set_var( "company_type_level", str_repeat( "&nbsp;", $companyTypeItem[1] ) );
                else
                    $t->set_var( "company_type_level", "" );

                $t->set_var( "is_selected", in_array( $companyTypeItem[0]->id(), $category_values )
                                            ? "selected" : "" );

                $t->parse( "company_type_select", "company_type_select_tpl", true );
            }

            $t->parse( "company_item", "company_item_tpl" );
        }
        else
        {
            $t->set_var( "person_id", $personID );

            $t->set_var( "user_id", $user->id() );
            if ( isset( $firstName ) )
                $t->set_var( "firstname", eZTextTool::htmlspecialchars( $firstName ) );
            if ( isset( $lastName ) )
                $t->set_var( "lastname", eZTextTool::htmlspecialchars( $lastName ) );

            $top_name = $t->get_var( "intl-top_category" );
            if ( !is_string( $top_name ) )
                $top_name = "";
            $companyTypeList = eZCompanyType::getTree( 0, 0, true, $top_name );
            $categoryList = array();
            $categoryList = eZPerson::companies( $personID, false );
            $category_values = array_values( $categoryList );
            $t->set_var( "is_top_selected", in_array( 0, $category_values ) ? "selected" : "" );
            foreach ( $companyTypeList as $companyTypeItem )
            {
                $t->set_var( "company_name", "[" . eZTextTool::htmlspecialchars( $companyTypeItem[0]->name() ) . "]" );
                $t->set_var( "company_id", "-1" );

                $level = $companyTypeItem[1] > 0 ? str_repeat( "&nbsp;", $companyTypeItem[1] ) : "";
                $t->set_var( "company_level", $level );
                $t->set_var( "is_selected", "" );
                $t->parse( "company_select", "company_select_tpl", true );

                $level = str_repeat( "&nbsp;", $companyTypeItem[1] + 1 );
                $t->set_var( "company_level", $level );

                $companies = eZCompany::getByCategory( $companyTypeItem[0]->id() );
                foreach ( $companies as $companyItem )
                {
                    $t->set_var( "company_name", eZTextTool::htmlspecialchars( $companyItem->name() ) );
                    $t->set_var( "company_id", $companyItem->id() );
                    $t->set_var( "is_selected", in_array( $companyItem->id(), $category_values )
                                 ? "selected" : "" );
                    $t->parse( "company_select", "company_select_tpl", true );
                }
            }

            for ( $i = 1; $i <= 31; $i++ )
            {
                $t->set_var( "day_id", $i );
                $t->set_var( "day_value", $i );
                $t->set_var( "selected", "" );
                if ( ( $birthDay == "" and $i == 1 ) or $birthDay == $i )
                    $t->set_var( "selected", "selected" );
                $t->parse( "day_item", "day_item_tpl", true );
            }

            $birth_array = array( 1 => "select_january",
                                  2 => "select_february",
                                  3 => "select_march",
                                  4 => "select_april",
                                  5 => "select_may",
                                  6 => "select_june",
                                  7 => "select_july",
                                  8 => "select_august",
                                  9 => "select_september",
                                  10 => "select_october",
                                  11 => "select_november",
                                  12 => "select_december" );

            foreach ( $birth_array as $month )
            {
                $t->set_var( $month, "" );
            }

            $var_name =& $birth_array[$birthMonth];
            if ( $var_name == "" )
                $var_name =& $birth_array[1];

            $t->set_var( $var_name, "selected" );
            $t->set_var( "birthyear", $birthYear );
            $t->set_var( "comment", $comment );

            $t->parse( "person_item", "person_item_tpl" );
        }

        $phone_types = eZPhoneType::getAll();
        $online_types = eZOnlineType::getAll();
        $address_types = eZAddressType::getAll();
        $countries = eZCountry::getAllArray();
        if ( !isset( $phoneDelete ) )
        {
            $phoneDelete = array();
        }
        if ( !isset( $onlineDelete ) )
        {
            $onlineDelete = array();
        }
        if ( !isset( $addressDelete ) )
        {
            $addressDelete = array();
        }

        $addressMinimum = $ini->variable( "eZContactMain", "AddressMinimum" );
        $phoneMinimum = $ini->variable( "eZContactMain", "PhoneMinimum" );
        $onlineMinimum = $ini->variable( "eZContactMain", "OnlineMinimum" );
        $addressWidth = $ini->variable( "eZContactMain", "AddressWidth" );
        $phoneWidth = $ini->variable( "eZContactMain", "PhoneWidth" );
        $onlineWidth = $ini->variable( "eZContactMain", "OnlineWidth" );

        if ( isset( $newAddress ) )
        {
            $addressTypeID[] = "";
            $addressID[] = count( $addressID ) > 0 ? $addressID[count( $addressID ) - 1] + 1 : 1;
            $street1[] = "";
            $street2[] = "";
            $zip[] = "";
            $place[] = "";
            $country[] = count( $country ) > 0 ? $country[count( $country ) - 1] : "";

            $count = max( count( $addressTypeID ), count( $addressID ),
                count( $street1 ), count( $street2 ),
                count( $zip ), count( $place ) );
        }
        else
        {
            $count = false;
            $zip = array();
        }
        $item = 0;
        $addressDeleteValues = array_values( $addressDelete );
        $last_id = 0;
        for ( $i = 0; $i < $count || $item < $addressMinimum; $i++ )
        {
            if ( ( $item % $addressWidth == 0 ) && $item > 0 )
            {
                $t->parse( "address_table_item", "address_table_item_tpl", true );
                $t->set_var( "address_item" );
            }
            if ( !isset( $addressID[$i] ) or !is_numeric( $addressID[$i] ) )
                 $addressID[$i] = ++$last_id;
            if ( !in_array( $addressID[$i], $addressDeleteValues ) )
            {
                $last_id = $addressID[$i];
                if( isset( $street1[$i] ) )
                    $t->set_var( "street1", eZTextTool::htmlspecialchars( $street1[$i] ) );
                else
                    $t->set_var( "street1", '' );

                if( isset( $street2[$i] ) )
                    $t->set_var( "street2", eZTextTool::htmlspecialchars( $street2[$i] ) );
                else
                    $t->set_var( "street2", '' );
                if( isset( $zip[$i] ) )
                    $t->set_var( "zip", eZTextTool::htmlspecialchars( $zip[$i] ) );
                else
                    $t->set_var( "zip", '' );
                if( isset( $place[$i] ) )
                    $t->set_var( "place", eZTextTool::htmlspecialchars( $place[$i] ) );
                else
                    $t->set_var( "place", '' );
                $t->set_var( "address_id", $addressID[$i] );
                $t->set_var( "address_index", $addressID[$i] );
                $t->set_var( "address_position", $i + 1 );

                $t->set_var( "address_item_select", "" );

                foreach ( $address_types as $address_type )
                {
                    $t->set_var( "type_id", $address_type->id() );
                    $t->set_var( "type_name", eZTextTool::htmlspecialchars( $address_type->name() ) );
                    $t->set_var( "selected", "" );
                    if ( isset( $addressTypeID ) && $address_type->id() == $addressTypeID[$i] )
                        $t->set_var( "selected", "selected" );
                    $t->parse( "address_item_select", "address_item_select_tpl", true );
                }
                $t->set_var( "country_item_select", "" );
                $t->set_var( "no_country_selected", "" );
                foreach ( $countries as $country )
                {
                    $t->set_var( "type_id", $country["ID"] );
                    $t->set_var( "type_name", eZTextTool::htmlspecialchars( $country["Name"] ) );
                    $t->set_var( "selected", "" );
                    if ( isset( $country ) && $country[$i] == -1 )
                        $t->set_var( "no_country_selected", "selected" );
                    else if ( isset( $country ) && $country["ID"] == $country[$i] )
                        $t->set_var( "selected", "selected" );
                    $t->parse( "country_item_select", "country_item_select_tpl", true );
                }

                $t->parse( "address_item", "address_item_tpl", true );
                $item++;
            }
            else
                $addressDeleteValues = array_diff( $addressDeleteValues, array( $addressID[$i] ) );
        }
        $t->parse( "address_table_item", "address_table_item_tpl", true );

//          $t->parse( "address_item", "address_item_tpl" );

        if ( isset( $newPhone ) )
        {
            $phoneTypeID[] = "";
            $phoneID[] = count( $phoneID ) > 0 ? $phoneID[count( $phoneID ) - 1] + 1 : 1;
            $phone[] = "";
            $count = max( count( $phoneTypeID ), count( $phoneID ), count( $phone ) );
        }
        else
        {
            $count = false;
        }
        $item = 0;
        $last_id = 0;
        $phoneDeleteValues = array_values( $phoneDelete );
        for ( $i = 0; $i < $count || $item < $phoneMinimum; $i++ )
        {
            if ( ( $item % $phoneWidth == 0 ) && $item > 0 )
            {
                $t->parse( "phone_table_item", "phone_table_item_tpl", true );
                $t->set_var( "phone_item" );
            }
            if ( !isset( $phoneID[$i] ) or !is_numeric( $phoneID[$i] ) )
                 $phoneID[$i] = ++$last_id;
            if ( !in_array( $phoneID[$i], $phoneDeleteValues ) )
            {
                $last_id = $phoneID[$i];
                if( isset( $phone[$i] ) )
                {
                    $t->set_var( "phone_number", eZTextTool::htmlspecialchars( $phone[$i] ) );
                    $t->set_var( "phone_id", $phoneID[$i] );
                    $t->set_var( "phone_index", $phoneID[$i] );
                }
                else
                {
                    $t->set_var( "phone_number", '' );
                    $t->set_var( "phone_id", '' );
                    $t->set_var( "phone_index", '' );
                }
                $t->set_var( "phone_position", $i + 1 );

                $t->set_var( "phone_item_select", "" );

                foreach ( $phone_types as $phone_type )
                {
                    $t->set_var( "type_id", $phone_type->id() );
                    $t->set_var( "type_name", eZTextTool::htmlspecialchars( $phone_type->name() ) );
                    $t->set_var( "selected", "" );
                    if ( isset( $phoneTypeID ) && $phone_type->id() == $phoneTypeID[$i] )
                        $t->set_var( "selected", "selected" );
                    $t->parse( "phone_item_select", "phone_item_select_tpl", true );
                }

                $t->parse( "phone_item", "phone_item_tpl", true );
                $item++;
            }
            else
                $phoneDeleteValues = array_diff( $phoneDeleteValues, array( $phoneID[$i] ) );
        }
        $t->parse( "phone_table_item", "phone_table_item_tpl", true );

        if ( isset( $newOnline ) )
        {
            $onlineTypeID[] = "";
            $onlineID[] = count( $onlineID ) > 0 ? $onlineID[count( $onlineID ) - 1] + 1 : 1;
            $online[] = "";
            $count = max( count( $onlineTypeID ), count( $onlineID ), count( $online ) );
        }
        else
        {
            $count = false;
        }
        $item = 0;
        $last_id = 0;
        $onlineDeleteValues = array_values( $onlineDelete );
        for ( $i = 0; $i < $count || $item < $onlineMinimum; $i++ )
        {
            if ( ( $item % $onlineWidth == 0 ) && $item > 0 )
            {
                $t->parse( "online_table_item", "online_table_item_tpl", true );
                $t->set_var( "online_item" );
            }
            if ( !isset( $onlineID[$i] ) or !is_numeric( $onlineID[$i] ) )
                 $onlineID[$i] = ++$last_id;
            if ( !in_array( $onlineID[$i], $onlineDeleteValues ) )
            {
                $last_id = $onlineID[$i];
                if( isset( $online[$i] ) )
                {
                    $t->set_var( "online_value", eZTextTool::htmlspecialchars( $online[$i] ) );
                    $t->set_var( "online_id", $onlineID[$i] );
                    $t->set_var( "online_index", $onlineID[$i] );
                }
                else
                {
                    $t->set_var( "online_value", '' );
                    $t->set_var( "online_id", '' );
                    $t->set_var( "online_index", '' );
                }
                $t->set_var( "online_position", $i + 1 );

                $t->set_var( "online_item_select", "" );

                foreach ( $online_types as $online_type )
                {
                    $t->set_var( "type_id", $online_type->id() );
                    $t->set_var( "type_name", eZTextTool::htmlspecialchars( $online_type->name() ) );
                    $t->set_var( "selected", "" );
                    if ( isset( $onlineTypeID ) && $online_type->id() == $onlineTypeID[$i] )
                        $t->set_var( "selected", "selected" );
                    $t->parse( "online_item_select", "online_item_select_tpl", true );
                }

                $t->parse( "online_item", "online_item_tpl", true );
                $item++;
            }
            else
                $onlineDeleteValues = array_diff( $onlineDeleteValues, array( $onlineID[$i] ) );
        }
        $t->parse( "online_table_item", "online_table_item_tpl", true );

        $groups = eZUserGroup::getAll();
        foreach ( $groups as $group )
        {
            $t->set_var( "type_id", $group->id() );
            $t->set_var( "type_name", eZTextTool::htmlspecialchars( $group->name() ) );
            $t->set_var( "selected", "" );
            if ( isset( $contactGroupID ) && $contactGroupID == $group->id() )
                $t->set_var( "selected", "selected" );
            $t->parse( "contact_group_item_select", "contact_group_item_select_tpl", true );
        }

        $t->set_var( "project_contact_item", "" );
        if ( $companyEdit )
        {
            if( isset( $userSearch ) )
                $t->set_var( "user_search", eZTextTool::htmlspecialchars( $userSearch ) );
            else
                $t->set_var( "user_search", '' );

            $users = array();
            if ( isset( $contactGroupID ) && $contactGroupID == -1 )
            {
                $users = eZUser::getAll( "name", true, $userSearch );
            }
            else if ( isset( $contactGroupID ) && $contactGroupID == -3 )
            {
                $users = eZPerson::getAll( $userSearch, 0, -1 );
            }
            else if ( isset( $contactGroupID ) && $contactGroupID < 1 )
            {
                if ( is_numeric( $contactID ) and $contactID > 0 )
                {
                    if ( $contactType == "eZPerson" )
                        $contact = new eZPerson( $contactID );
                    else
                        $contact = new eZUser( $contactID );
                    $users[] = $contact;
                }
            }
            else
            {
                $group = new eZUserGroup();
                $users = $group->users( isset( $contactGroupID ) ? $contactGroupID : false, "name", isset( $userSearch ) ? $userSearch : false );
            }
            foreach ( $users as $contact )
            {
                if ( is_a( $contact, "eZUser" ) ||
                     is_a( $contact, "eZPerson" ) )
                {
                    $t->set_var( "type_id", $contact->id() );
                    $t->set_var( "type_firstname", eZTextTool::htmlspecialchars( $contact->firstName() ) );
                    $t->set_var( "type_lastname", eZTextTool::htmlspecialchars( $contact->lastName() ) );
                    $t->set_var( "selected", "" );
                    if ( $contactID == $contact->id() )
                        $t->set_var( "selected", "selected" );
                }
                $t->parse( "contact_item_select", "contact_item_select_tpl", true );
            }
            if ( count( $users ) > 0 )
                $t->set_var( "contact_person_type", is_a( $users[0], "eZUser" )? "eZUser" : "eZPerson" );
            else
                $t->set_var( "contact_person_type", "" );

            $t->set_var( "none_selected", "" );
            $t->set_var( "all_selected", "" );
            $t->set_var( "persons_selected", "" );
            if ( isset( $contactGroupID ) && $contactGroupID == -1 )
            {
                $t->set_var( "all_selected", "selected" );
            }
            else if ( isset( $contactGroupID ) && $contactGroupID == -3 )
            {
                $t->set_var( "persons_selected", "selected" );
            }
            else if ( isset( $contactGroupID ) && $contactGroupID < 1 )
            {
                $t->set_var( "none_selected", "selected" );
            }

            $t->parse( "project_contact_item", "project_contact_item_tpl" );
        }

        $t->set_var( "project_item_select", "" );
        $project_types = eZProjectType::findTypes();
        foreach ( $project_types as $project_type )
        {
            $t->set_var( "type_id", $project_type->id() );
            $t->set_var( "type_name", eZTextTool::htmlspecialchars( $project_type->name() ) );
            $t->set_var( "selected", "" );
            if ( isset( $projectID ) && $projectID == $project_type->id() )
                $t->set_var( "selected", "selected" );
            $t->parse( "project_item_select", "project_item_select_tpl", true );
        }

        $t->parse( "project_item", "project_item_tpl", true );

        if ( $companyEdit )
        {
            // View logo.
            $logoImage = eZCompany::logoImage( $companyID );
            if ( isset( $logoImageID ) && is_numeric( $logoImageID ) )
            {
                $logoImage = new eZImage( $logoImageID );
            }

            $t->set_var( "logo_item", "&nbsp;" );
            if ( ( is_a( $logoImage, "eZImage" ) ) && ( $logoImage->id() != 0 ) )
            {
                $variation = $logoImage->requestImageVariation( 150, 150 );
                if ( is_a( $variation, "eZImageVariation" ) )
                {
                    $t->set_var( "logo_image_src", "/" . $variation->imagePath() );

                    $t->set_var( "logo_image_width", $variation->width() );
                    $t->set_var( "logo_image_height", $variation->height() );
                    $t->set_var( "logo_image_alt", eZTextTool::htmlspecialchars( $logoImage->caption() ) );
                    $t->set_var( "logo_name", eZTextTool::htmlspecialchars( $logoImage->name() ) );
                    $t->set_var( "logo_id", $logoImage->id() );

                    $t->parse( "logo_item", "logo_item_tpl" );
                }
            }
            else
            {
                $t->set_var( "logo_id", "" );
                $t->set_var( "image_id", "" );
            }

            // View company image.
            $companyImage = eZCompany::companyImage( $companyID );
            if ( isset( $companyImageID ) && is_numeric( $companyImageID ) )
            {
                $companyImage = new eZImage( $companyImageID );
            }

            $t->set_var( "image_item", "&nbsp;" );
            if ( ( is_a( $companyImage, "eZImage" ) ) && ( $companyImage->id() != 0 ) )
            {
                $variation = $companyImage->requestImageVariation( 150, 150 );
                if ( is_a( $variation, "eZImageVariation" ) )
                {
                    $t->set_var( "image_src", "/" . $variation->imagePath() );
                    $t->set_var( "image_width", $variation->width() );
                    $t->set_var( "image_height", $variation->height() );
                    $t->set_var( "image_alt", eZTextTool::htmlspecialchars( $companyImage->caption() ) );
                    $t->set_var( "image_name", eZTextTool::htmlspecialchars( $companyImage->name() ) );
                    $t->set_var( "image_id", $companyImage->id() );
                    $t->set_var( "image_caption", '' );

                    $t->parse( "image_item", "image_item_tpl" );
                }
            }
        }
    }

// Template variables.

    if ( isset( $companyID ) && is_numeric( $companyID ) || isset( $personID ) && is_numeric( $personID ) )
        $t->parse( "delete_item", "delete_item_tpl" );
    else
        $t->set_var( "delete_item", "" );

    if ( !$error )
        $t->set_var( "action_value", $action );

    $t->parse( "edit_item", "edit_tpl" );
}

$t->pparse( "output", "person_edit" );

?>