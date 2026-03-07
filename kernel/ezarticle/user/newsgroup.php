<?php
// 
// $id: newsgroup.php 7197 2001-09-13 14:45:41Z bf $
//
// Created on: <30-May-2001 14:06:59 bf>
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
// include_once( "classes/ezlist.php" );

// include_once( "ezarticle/classes/ezarticlecategory.php" );
// include_once( "ezarticle/classes/ezarticle.php" );
// include_once( "ezarticle/classes/ezarticlerenderer.php" );
// include_once( "ezuser/classes/ezobjectpermission.php" );

$ini = eZINI::instance( 'site.ini' );

$language = $ini->variable( "eZArticleMain", "Language" );
$imageDir = $ini->variable( "eZArticleMain", "ImageDir" );
$capitalizeHeadlines = $ini->variable( "eZArticleMain", "CapitalizeHeadlines" );
$defaultLinkText =  $ini->variable( "eZArticleMain", "DefaultLinkText" );
$grayScaleImageList = $ini->variable( "eZArticleMain", "GrayScaleImageList" );

$templateDir = $ini->variable( "eZArticleMain", "TemplateDir" );

$t = new eZTemplate( "kernel/ezarticle/user/" . $templateDir,
                     "kernel/ezarticle/user/intl/", $language, "newsgroup.php" );

$articleLimit = 2;

$t->setAllStrings();


$globalSectionID = eZArticleCategory::sectionIDStatic( $categoryID );

// override template for the current category
$override = "_override_$categoryID";

if ( file_exists( "kernel/ezarticle/user/$templateDir/newsgroup" . $override  . ".tpl" ) )
{
    $t->set_file( "news_group_tpl", "newsgroup" . $override  . ".tpl"  );
}
else
{    
    $t->set_file( "news_group_tpl", "newsgroup.tpl" );
}

$t->set_block( "news_group_tpl", "category_item_tpl", "category_item" );

$t->set_block( "category_item_tpl", "article_item_tpl", "article_item" );

$t->set_block( "category_item_tpl", "start_with_break_tpl", "start_with_break" );
$t->set_block( "category_item_tpl", "start_without_break_tpl", "start_without_break" );

$t->set_block( "category_item_tpl", "end_with_break_tpl", "end_with_break" );
$t->set_block( "category_item_tpl", "end_without_break_tpl", "end_without_break" );

$t->set_block( "article_item_tpl", "article_image_tpl", "article_image" );
$t->set_block( "article_item_tpl", "no_image_tpl", "no_image" );

// makes the section ID available in articleview template
$t->set_var( "section_id", $globalSectionID );

// image dir
$t->set_var( "image_dir", $imageDir );

$category = new eZArticleCategory( $categoryID );

$categoryList = $category->getByParent( $category, true, "placement", 0, 4 );

$locale = new eZLocale( $language );

$i = 0;
foreach( $categoryList as $category )
{
    $t->set_var( "start_with_break", "" );
    $t->set_var( "start_without_break", "" );
    $t->set_var( "end_with_break", "" );
    $t->set_var( "end_without_break", "" );

    if ( $i%2 == 0 )
    {
        $t->parse( "start_with_break", "start_with_break_tpl");
        $t->parse( "end_without_break", "end_without_break_tpl");        
    }
    else
    {
        $t->parse( "end_with_break", "end_with_break_tpl");
        $t->parse( "start_without_break", "start_without_break_tpl");
    }
    
    $t->set_var( "category_id", $category->id() );
    $t->set_var( "category_name", $category->name() );

    $articles = $category->articles( "time", false, true, 0, $articleLimit );

    $t->set_var( "article_item", "" );
    $j=0;
    foreach( $articles as $article )
    {
        $t->set_var( "article_name", $article->name() );
        $t->set_var( "article_id", $article->id() );

        $renderer = new eZArticleRenderer( $article );
        $t->set_var( "article_intro", $renderer->renderIntro(  ) );
        
        $published = $article->published();
        $published = $published->date();        

        $t->set_var( "article_published", $locale->format( $published ) );
        
        $t->set_var( "article_image", "" );
        $t->set_var( "no_image", "" );    
        if ( $j == 0 )
        {
            // preview image
            $thumbnailImage = $article->thumbnailImage();
            if ( $thumbnailImage )
            {
                if ( $grayScaleImageList == "enabled" )
                    $convertToGray = true;
                else
                    $convertToGray = false;
                
                $variation = $thumbnailImage->requestImageVariation( $ini->variable( "eZArticleMain", "ThumbnailGroupImageWidth" ),
                $ini->variable( "eZArticleMain", "ThumbnailGroupImageHeight" ), $convertToGray );
                
                $t->set_var( "thumbnail_image_uri", "/" . $variation->imagePath() );
                $t->set_var( "thumbnail_image_width", $variation->width() );
                $t->set_var( "thumbnail_image_height", $variation->height() );
                $t->set_var( "thumbnail_image_caption", $thumbnailImage->caption() );
                
                $t->parse( "article_image", "article_image_tpl" );
            }
            else
            {
                $t->parse( "no_image", "no_image_tpl" );
            }
        }
        else
        {
            $t->parse( "no_image", "no_image_tpl" );
        }


        $t->parse( "article_item", "article_item_tpl", true );
        $j++;
    }
                     

    $t->parse( "category_item", "category_item_tpl", true );
    $i++;
}

if ( isset( $generateStaticPage ) and $generateStaticPage == "true" and $cachedFile != "" )
{
    $fp = eZPBFile::fopen( $cachedFile, "w+");

    $output = $t->parse( "output", "news_group_tpl" );
    
    // print the output the first time while printing the cache file.
    print( $output );
    fwrite ( $fp, $output );
    fclose( $fp );
}
else
{
    $t->pparse( "output", "news_group_tpl" );
}

?>