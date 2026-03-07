<?php
//
// $Id: linkedit.php 9200 2002-02-12 12:06:00Z br $
//
// Created on: <26-Oct-2000 14:58:57 ce>
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
  linkedit.php - edit a link.
*/

// include_once( "classes/INIFile.php" );
// include_once( "classes/ezhttptool.php" );

$ini = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZLinkMain", "Language" );
$error = new eZINI( "kernel/ezuser/admin/intl/" . $Language . "/useredit.php.ini", false );
$error_msg = false;
// include_once( "classes/eztemplate.php" );

// include_once( "ezlink/classes/ezlinkcategory.php" );
// include_once( "ezlink/classes/ezlink.php" );
// include_once( "ezlink/classes/ezhit.php" );

// include_once( "ezlink/classes/ezlinktype.php" );
// include_once( "ezlink/classes/ezlinkattribute.php" );

include_once( "kernel/ezlink/classes/ezmeta.php" );
require( "kernel/ezuser/admin/admincheck.php" );


if ( isset( $accepted ) && $accepted == "1" || !isset( $accepted ) )
{
    $yes_selected = "selected";
    $no_selected = "";
}
else
{
    $yes_selected = "";
    $no_selected = "selected";
}

if ( isset( $deleteLinks ) )
{
    $action = "DeleteLinks";
}

if ( isset( $delete ) )
{
    $action = "delete";
}

if( isset( $update ) )
{
    $tname = $name;
    $turl = $url;
    $tkeywords = $keywords;
    $tdescription = $description;
}

if ( isset( $back ) )
{
    if ( $linkID != "" )
    {
        $link = new eZLink( $linkID );
        $catDef = $link->categoryDefinition();
        $linkCategoryID = $catDef->id();
    }
    else
    {
        $linkCategoryID = 0;
    }

    eZHTTPTool::header( "Location: /link/category/$linkCategoryID/" );
    exit();
}

if ( isset( $categoryArray ) && count( $categoryArray ) > 0 )
    $linkCategoryIDArray = $categoryArray;
else
    $linkCategoryIDArray = [];

if ( $action == "new" )
{
    if ( !eZPermission::checkPermission( $user, "eZLink", "LinkAdd" ) )
    {
        eZHTTPTool::header( "Location: /link/norights" );
    }

    $action_value = "new";

    $linkID = 0;
    $error_msg = false;
    $tdescription = false;
    $tkeywords = false;
    $tname = false;
    $turl = false;
    $linkType = false;
    $tLinkCategoryID = false;

    if ( isset( $ok ) || isset( $browse ) )
    {
        $action = "insert";
    }
}

if ( $action == "edit" )
{
    $action_value = "edit";
    if( isset( $ok ) )
    {
        $action = "update";
    }
}

// Get images from the image browse function.
if ( ( isset( $addImages ) ) and ( is_numeric( $linkID ) ) and ( is_numeric( $linkID ) ) )
{
    $image = new eZImage( $imageID );
    $link = new eZLink( $linkID );
    $link->setImage( $image );
    $link->update();
    $action = "edit";
}

if ( isset( $getSite ) && $getSite )
{
    if ( $url )
    {
        if ( !preg_match( "%^([a-z]+://)%", $url ) )
            $real_url = "http://" . $url;
        else
            $real_url = $url;

        $metaList = fetchURLInfo( $real_url );
        if ( $metaList == false )
        {
            // Change this to use an external message
            $error_msg = "The site does not exists";
        }
        /* else if( count( $metaList ) == 0 )
        {
            $inierror = new eZINI( "kernel/ezlink/user/" . "/intl/" . $Language . "/suggestlink.php.ini", false );
            $terror_msg = $inierror->variable( "strings", "nometa" );
        } */
        if ( $metaList["description"] )
            $tdescription = $metaList["description"];
        else
            $tdescription = "";

        if ( isset( $metaList["keywords"] ) && $metaList["keywords"] )
            $tkeywords = $metaList["keywords"];
        else
            $tkeywords = "";

        if ( $metaList["title"] )
            $tname = $metaList["title"];
        else if ( $metaList["abstract"] )
            $tname = $metaList["abstract"];
        else
            $tname = "";

        $turl = $url;
    }
    else
    {
        $tname = $name;
        $turl = $url;
        $tkeywords = $keywords;
        $tdescription = $description;
    }

    $action_value = $action;
    if ( $action != "edit" )
        $action = "";

}

// Update a link.
if ( $action == "update" )
{
    if ( eZPermission::checkPermission( $user, "eZLink", "LinkModify" ) )
    {
        if ( $name != "" &&
             $linkCategoryID != "" &&
             $accepted != "" &&
             $url != "" )
        {
            $link = new eZLink( $linkID );

            $link->setName( $name );
            $link->setDescription( $description );
            $link->setKeyWords( $keywords );
            $link->setUrl( $url );

            // Calculate new and unused categories
            $old_maincategory = $link->categoryDefinition();
            $old_categories = array_unique( array_merge( array( $old_maincategory->id() ),
                                                          $link->categories( false ) ) );
            $new_categories = array_unique( array_merge( array( $linkCategoryID ), $categoryArray ) );
            $remove_categories = array_diff( $old_categories, $new_categories );
            $add_categories = array_diff( $new_categories, $old_categories );

            foreach ( $remove_categories as $categoryItem )
            {
                eZLinkCategory::removeLink( $link, $categoryItem );
            }

            // add to categories
            $category = new eZLinkCategory( $linkCategoryID );
            $link->setCategoryDefinition( $category );

            foreach ( $add_categories as $categoryItem )
            {
                eZLinkCategory::addLink( $link, $categoryItem );
            }

            if ( $accepted == "1" )
                $link->setAccepted( true );
            else
                $link->setAccepted( false );

            $link->setUrl( $url );

            $file = new eZPBImageFile();
            if ( $file->getUploadedFile( "ImageFile" ) )
            {
                $image = new eZImage();
                $image->setName( "LinkImage" );
                $image->setImage( $file );

                $image->store();

                $link->setImage( $image );
            }

            if ( $typeID == -1 )
            {
                $link->removeType();
            }
            else
            {
                $link->removeType();

                $link->setType( new eZLinkType( $typeID ) );

                $i = 0;
                if ( count( $attributeValue ) > 0 )
                {
                    foreach ( $attributeValue as $attribute )
                    {
                        $att = new eZLinkAttribute( $attributeID[$i] );

                        $att->setValue( $link, $attribute );

                        $i++;
                    }
                }
            }

            $link->update();

            if ( $deleteImage )
            {
                $link->deleteImage();
            }
            if ( isset ( $browse ) )
            {
                $linkID = $link->id();
                $session = eZSession::globalSession();
                $session->setVariable( "SelectImages", "single" );
                $session->setVariable( "ImageListReturnTo", "/link/linkedit/edit/$linkID/" );
                $session->setVariable( "NameInBrowse", $link->name() );
                eZHTTPTool::header( "Location: /imagecatalogue/browse/" );
                exit();
            }

            if ( isset( $attributes ) )
            {
                $linkID = $link->id();
                eZHTTPTool::header( "Location: /link/linkedit/attributeedit/$linkID/" );
                exit();
            }

            eZHTTPTool::header( "Location: /link/category/$linkCategoryID" );
            exit();
        }
        else
        {
            $error_msg = $error->variable( "strings", "error_missingdata" );
            $action_value = "edit";

            $tname = $name;
            $turl = $url;
            $tkeywords = $keywords;
            $tdescription = $description;
        }
    }
    else
    {
        eZHTTPTool::header( "Location: /link/norights" );
        exit();
    }
}

// Delete a link.
if ( $action == "delete" )
{
    if ( eZPermission::checkPermission( $user, "eZLink", "LinkDelete" ) )
    {
        $deletelink = new eZLink();
        $deletelink->get( $linkID );
        $deletelink->delete();

        if ( $deletelink->accepted() == false )
        {
            eZHTTPTool::header( "Location: /link/category/incoming" );
            exit();
        }
    }
    else
    {
        eZHTTPTool::header( "Location: /link/norights" );
    }
}

if ( $action == "DeleteLinks" )
{
    if ( count ( $linkArrayID ) != 0 )
    {
        foreach( $linkArrayID as $linkIDItem )
        {
            $deletelink = new eZLink();
            $deletelink->get( $linkIDItem );
            $deletelink->delete();

        }
        if ( $deletelink )
        {
            if ( $deletelink->accepted() == false )
            {
                eZHTTPTool::header( "Location: /link/category/incoming" );
                exit();
            }
        }
        eZHTTPTool::header( "Location: /link/category/$linkCategoryID" );
        exit();
    }
}

// Insert a link.
if ( $action == "insert" )
{
    if ( eZPermission::checkPermission( $user, "eZLink", "LinkAdd") )
    {
        if ( isset( $name ) && $name != "" &&
        isset( $linkCategoryID ) && $linkCategoryID != "" &&
        isset( $accepted ) && $accepted != "" &&
        isset( $url ) && $url != "" )
        {
            $link = new eZLink();

            $link->setName( $name );
            $link->setDescription( $description );
            $link->setKeyWords( $keywords );
            if ( $accepted == "1" )
                $link->setAccepted( true );
            else
                $link->setAccepted( false );

            $link->setUrl( $url );

            $tname = $name;
            $turl = $url;
            if ( isset( $getSite) && !$getSite )
            {
                $tkeywords = $keywords;
                $tdescription = $description;
            }
            $file = new eZPBImageFile();
            if ( $file->getUploadedFile( "ImageFile" ) )
            {
                $image = new eZImage( );
                $image->setName( "LinkImage" );
                $image->setImage( $file );

                $image->store();

                $link->setImage( $image );
            }
            $link->store();
            if ( $typeID == -1 )
            {
                $link->removeType();
            }
            else
            {
                $link->setType( new eZLinkType( $typeID ) );

                $i = 0;
                if ( count( $attributeValue ) > 0 )
                {
                    foreach ( $attributeValue as $attribute )
                    {
                        $att = new eZLinkAttribute( $attributeID[$i] );

                        $att->setValue( $link, $attribute );

                        $i++;
                    }
                }
            }

            // Add to categories.
            $cat = new eZLinkCategory( $linkCategoryID );
            eZLinkCategory::addLink( $link, $linkCategoryID );
            $link->setCategoryDefinition( $cat );
            if ( isset( $categoryArray ) && count( $categoryArray ) > 0 )
            {
                foreach ( $categoryArray as $categoryItem )
                {
                    if ( $categoryItem != $cat->id() )
                    {
                        eZLinkCategory::addLink( $link, $categoryItem );
                    }
                }
            }
            $linkID = $link->id();

            if ( isset( $browse ) )
            {
                $linkID = $link->id();
                $session = eZSession::globalSession();
                $session->setVariable( "SelectImages", "single" );
                $session->setVariable( "ImageListReturnTo", "/link/linkedit/edit/$linkID/" );
                $session->setVariable( "NameInBrowse", $link->name() );
                eZHTTPTool::header( "Location: /imagecatalogue/browse/" );
                exit();
            }

            if ( isset( $attributes ) )
            {
                $linkID = $link->id();
                eZHTTPTool::header( "Location: /link/linkedit/attributeedit/$linkID/" );
                exit();
            }

            eZHTTPTool::header( "Location: /link/category/$linkCategoryID" );
            exit();
        }
        else if ( !isset( $update ) && !isset( $getSite ) )
        {
            $error_msg = $error->variable( "strings", "error_missingdata" );
            $action_value = "new";

            $tname = $name;
            $turl = $url;
            $tkeywords = $keywords;
            $tdescription = $description;
        }
    }
    else
    {
        eZHTTPTool::header( "Location: /link/norights" );
    }
}

// set the template files.

$t = new eZTemplate( "kernel/ezlink/admin/" . $ini->variable( "eZLinkMain", "AdminTemplateDir" ),
"kernel/ezlink/admin/" . "/intl", $Language, "linkedit.php" );
$t->setAllStrings();

$t->set_file( "link_edit", "linkedit.tpl" );

$t->set_block( "link_edit", "link_category_tpl", "link_category" );

$t->set_block( "link_edit", "image_item_tpl", "image_item" );
$t->set_block( "link_edit", "no_image_item_tpl", "no_image_item" );

$t->set_block( "link_edit", "multiple_category_tpl", "multiple_category" );

$t->set_block( "link_edit", "type_tpl", "type" );

$t->set_block( "link_edit", "attribute_list_tpl", "attribute_list" );
$t->set_block( "attribute_list_tpl", "attribute_tpl", "attribute" );


$languageIni = new eZINI( "kernel/ezlink/admin/intl/" . $Language . "/linkedit.php.ini", false );
$headline = $languageIni->variable( "strings", "headline_insert" );

$linkselect = new eZLinkCategory();

$linkCategoryList = $linkselect->getTree();

// Template variables.

// $action_value = "update";


$t->set_var( "image_item", "" );
$t->set_var( "no_image_item", "" );

// set accepted link as default.
// $yes_selected = "selected";
// $no_selected = "";

// editere
if ( $action == "edit" )
{

    $languageIni = new eZINI( "kernel/ezlink/admin/intl/" . $Language . "/linkedit.php.ini", false );
    $headline =  $languageIni->variable( "strings", "headline_edit" );

    if ( !eZPermission::checkPermission( $user, "eZLink", "LinkModify" ) )
    {
        eZHTTPTool::header( "Location: /link/norights" );
    }
    else
    {
        if ( !isset( $editLink ) )
        {
            $editLink = new eZLink();
            $editLink->get( $linkID );
        }

        $cateDef = $editLink->categoryDefinition();
        $linkCategoryID = $cateDef->id();
        $linkCategoryIDArray = $editLink->categories( false );

        $action_value = "edit";

        if ( !isset( $update ) )
        {
            $tname = $editLink->name();
            $tdescription = $editLink->description();
            $tkeywords = $editLink->keywords();
            $turl = $editLink->url();
        }

        $linkType = $editLink->type();

        $image = $editLink->image();

        if ( $image )
        {
            $imageWidth = $ini->variable( "eZLinkMain", "CategoryImageWidth" );
            $imageHeight = $ini->variable( "eZLinkMain", "CategoryImageHeight" );

            $variation = $image->requestImageVariation( $imageWidth, $imageHeight );

            $imageURL = "/" . $variation->imagePath();
            $imageWidth = $variation->width();
            $imageHeight = $variation->height();
            $imageCaption = $image->caption();

            $t->set_var( "image_width", $imageWidth );
            $t->set_var( "image_height", $imageHeight );
            $t->set_var( "image_url", $imageURL );
            $t->set_var( "image_caption", $imageCaption );
            $t->set_var( "no_image", "" );
            $t->parse( "image_item", "image_item_tpl" );

            $t->set_var( "no_image_item", "" );
        }
        else
        {
            $t->parse( "no_image_item", "no_image_item_tpl" );
            $t->set_var( "image_item", "" );
        }


        if ( $editLink->accepted() == true )
        {
            $yes_selected = "selected";
            $no_selected = "";
        }
        else
        {
            $yes_selected = "";
            $no_selected = "selected";
        }

        if ( isset( $browse ) )
        {
            $linkID = $editLink->id();
            $session = eZSession::globalSession();
            $session->setVariable( "SelectImages", "single" );
            $session->setVariable( "ImageListReturnTo", "/link/linkedit/edit/$linkID/" );
            $session->setVariable( "NameInBrowse", $editLink->name() );
            eZHTTPTool::header( "Location: /imagecatalogue/browse/" );
            exit();
        }
    }
}

if ( $action == "AttributeList" )
{
    $tname = $name;
    $tkeywords = $keywords;
    $tdescription = $description;
    $turl = $url;

    $action_value = "update";

    $t->parse( "no_image_item", "no_image_item_tpl" );
    $t->set_var( "image_item", "" );

    if ( $accepted == true )
    {
        $yes_selected = "selected";
        $no_selected = "";
    }
    else
    {
        $yes_selected = "";
        $no_selected = "selected";
    }

    $action_value = $url_array[3];
}

// Selector
$link_select_dict = "";
$catCount = count( $linkCategoryList );
$t->set_var( "num_select_categories", min( $catCount, 10 ) );
$i = 0;

$t->set_var( "link_category", "" );
$t->set_var( "multiple_category", "" );

foreach( $linkCategoryList as $linkCategoryItem )
{
    $t->set_var("link_category_id", $linkCategoryItem[0]->id() );
    $t->set_var("link_category_name", $linkCategoryItem[0]->name() );

    if ( isset( $linkCategoryID ) && (int) $linkCategoryID == $linkCategoryItem[0]->id() )
    {
        $t->set_var( "is_selected", "selected" );
    }
    else
    {
        $t->set_var( "is_selected", "" );
    }

    if ( $linkCategoryItem[1] > 0 )
        $t->set_var( "option_level", str_repeat( "&nbsp;", $linkCategoryItem[1] ) );
    else
        $t->set_var( "option_level", "" );

    $link_select_dict[ $linkCategoryItem[0]->id() ] = $i;
    if ( in_array( $linkCategoryItem[0]->id(), $linkCategoryIDArray )
         and ( $linkCategoryID != $linkCategoryItem[0]->id() ) )
    {
        $t->set_var( "multiple_selected", "selected" );
        $i++;
    }
    else
    {
        $t->set_var( "multiple_selected", "" );
    }

    $t->parse( "link_category", "link_category_tpl", true );
    $t->parse( "multiple_category", "multiple_category_tpl", true );
}


$type = new eZLinkType();
$types = $type->getAll();

if ( isset( $typeID ) )
        $linkType = new eZLinkType( $typeID );

$t->set_var( "type", "" );

foreach ( $types as $typeItem )
{
    if ( is_a( $linkType, "eZLinkType"  ) )
    {
        if ( $linkType->id() == $typeItem->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }
    else
    {
        $t->set_var( "selected", "" );
    }

    $t->set_var( "type_id", $typeItem->id( ) );
    $t->set_var( "type_name", $typeItem->name( ) );

    $t->parse( "type", "type_tpl", true );
}


$i = 0;
if ( is_a( $linkType, "eZLinkType") )
{
    $attributes = $linkType->attributes();
    foreach ( $attributes as $attribute )
    {

        if ( ( $i %2 ) == 0 )
            $t->set_var( "td_class", "bglight" );
        else
            $t->set_var( "td_class", "bgdark" );

        $t->set_var( "attribute_id", $attribute->id( ) );
        $t->set_var( "attribute_name", $attribute->name( ) );

        if ( isset( $attributeValue[$i] ) && $attribute->id() == $attributeID[$i] )
            $t->set_var( "attribute_value", $attributeValue[$i] );
        else
            $t->set_var( "attribute_value", $attribute->value( $editLink ) );

        $t->parse( "attribute", "attribute_tpl", true );
        $i++;
    }
}

if ( isset( $attributes ) && count( $attributes ) > 0 || !isset( $type ) )
{
    $t->parse( "attribute_list", "attribute_list_tpl" );
}
else
{
    $t->set_var( "attribute_list", "" );
}


$t->set_var( "yes_selected", $yes_selected );
$t->set_var( "no_selected", $no_selected );

$t->set_var( "action_value", $action_value );


$t->set_var( "name", $tname );
$t->set_var( "url", $turl );
$t->set_var( "keywords", $tkeywords );
$t->set_var( "description", $tdescription );
// $t->set_var( "accepted", $taccepted );

$t->set_var( "headline", $headline );

$t->set_var( "error_msg", $error_msg );

$t->set_var( "link_id", $linkID );
$t->pparse( "output", "link_edit" );

?>