<?php
//
// $id: datasupplier.php 9891 2003-09-04 16:13:04Z br $
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

// include_once( "ezarticle/classes/ezarticle.php" );
// include_once( "ezarticle/classes/ezarticlecategory.php" );
// include_once( "ezuser/classes/ezusergroup.php" );
// include_once( "ezuser/classes/ezobjectpermission.php" );
// include_once( "ezuser/classes/ezuser.php" );

$pageCaching = $ini->variable( "eZArticleMain", "PageCaching" );
$userComments = $ini->variable( "eZArticleMain", "UserComments" );

$globalSectionID = $ini->variable( "eZArticleMain", "DefaultSection" );

// --- Explicit POST/GET extraction (replaces register_globals hack for this module) ---
// Variables from HTML form submissions (POST) or query string (GET)
$action              = eZHTTPTool::getVar( 'Action' );
$cancel              = eZHTTPTool::getVar( 'Cancel' );
$deleteSelected      = eZHTTPTool::getVar( 'DeleteSelected' );
$printableVersion    = eZHTTPTool::getVar( 'PrintableVersion' );
$category            = eZHTTPTool::getVar( 'Category' );
$searchText          = eZHTTPTool::getVar( 'SearchText' );
$name                = eZHTTPTool::getVar( 'Name' );
$postedContents      = eZHTTPTool::getVar( 'Contents' );
$authorText          = eZHTTPTool::getVar( 'AuthorText' );
$linkText            = eZHTTPTool::getVar( 'LinkText' );
$categoryIDSelect    = eZHTTPTool::getVar( 'CategoryIDSelect' );
$addItem             = eZHTTPTool::getVar( 'AddItem' );
$itemToAdd           = eZHTTPTool::getVar( 'ItemToAdd' );
$sectionIDOverride   = eZHTTPTool::getVar( 'SectionIDOverride' );
$sendTo              = eZHTTPTool::getVar( 'SendTo' );
$from                = eZHTTPTool::getVar( 'From' );
$sender              = eZHTTPTool::getVar( 'Sender' );
$realName            = eZHTTPTool::getVar( 'RealName' );
$anonymous           = eZHTTPTool::getVar( 'Anonymous' );
$submit              = eZHTTPTool::getVar( 'Submit' );
$caption             = eZHTTPTool::getVar( 'Caption' );
$imageArrayID        = eZHTTPTool::getVar( 'ImageArrayID' );
$fileArrayID         = eZHTTPTool::getVar( 'FileArrayID' );
$addImages           = eZHTTPTool::getVar( 'AddImages' );
$addFiles            = eZHTTPTool::getVar( 'AddFiles' );
$newImage            = eZHTTPTool::getVar( 'NewImage' );
$newPhotographerName = eZHTTPTool::getVar( 'NewPhotographerName' );
$newPhotographerEmail= eZHTTPTool::getVar( 'NewPhotographerEmail' );
$photoID             = eZHTTPTool::getVar( 'PhotoID' );
$description         = eZHTTPTool::getVar( 'Description' );
$textarea            = eZHTTPTool::getVar( 'Textarea' );
$addMedia            = eZHTTPTool::getVar( 'AddMedia' );
// URL-routing vars are set explicitly in the switch below and override extracted values
// -----------------------------------------------------------------------------------

switch ( $url_array[2] )
{
    case "mailtofriend":
    {
        $articleID = $url_array[3];
        include( "kernel/ezarticle/user/mailtofriend.php" );
    }
    break;

    case "topiclist":
    {
        $topicID = $url_array[3];
        include( "kernel/ezarticle/user/topiclist.php" );
    }
    break;

    case "sitemap":
    {
        if ( isset( $url_array[3] ) && $url_array[3] != '' )
            $categoryID = $url_array[3];
        else
            $categoryID = 0;
        include( "kernel/ezarticle/user/sitemap.php" );
    }
    break;

    case "frontpage":
    {
        if ( isset( $url_array[3] ) ) 
        {
            $globalSectionID = $url_array[3];
            $categoryID = $url_array[3];

        }
        elseif ( !isset( $url_array[3] ) )
        {
            $globalSectionID = 1;
            $categoryID = 1;
        }

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();

        if( is_a( $user, "eZUser" ) )
            $userID = $user->id();
        else
            $userID = null;

        $groupstr = "";
        $cacheFile = false;

        if ( $user && get_class( $user ) == "eZUser" )
        {
            $groupIDArray = $user->groups( false );
            sort( $groupIDArray );
            $first = true;
            foreach ( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = null;

        //$userID = ( $user && is_object( $user ) ) ? $user->id() : null;

        if ( $pageCaching == "enabled" )
        {
            //include_once( "classes/ezcachefile.php" );
            $file = new eZCacheFile( "kernel/ezarticle/cache/", array( "articlefrontpage", $globalSectionID, $groupstr ),
                                     "cache", "," );

            $cachedFile = $file->filename( true );

            if ( $file->exists() && $userID !== null )
            {
                include( $cachedFile );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/ezarticle/user/frontpage.php" );
            }
        }
        else
        {
            $generateStaticPage = false;
            include( "kernel/ezarticle/user/frontpage.php" );
        }

    }
    break;

    case "homepage":
    {
        if ( isset( $url_array[3] ) )
            $globalSectionID = $url_array[3];

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if ( is_a( $user, "eZUser" ) )
        {
            $groupIDArray = $user->groups( false );
            sort( $groupIDArray );
            $first = true;
            foreach ( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;

        if ( $pageCaching == "enabled" )
        {
            // include_once( "classes/ezcachefile.php" );
            $file = new eZCacheFile( "kernel/ezarticle/cache/", array( "articlefrontpage", $globalSectionID, $groupstr ),
                                     "cache", "," );

            $cachedFile = $file->filename( true );

            if ( $file->exists() )
            {
                $generateStaticPage = "false";
                include( $cachedFile );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/ezarticle/user/homepage.php" );
            }
        }
        else
        {
            $generateStaticPage = "false";
            include( "kernel/ezarticle/user/homepage.php" );
        }

    }
    break;

    case "newsgroup":
    {
        if ( isset( $url_array[3] ) )
            $categoryID = $url_array[3];
        else
            $categoryID = "";

        include( "kernel/ezarticle/user/newsgroup.php" );
    }
    break;

    case "author":
    {
        $action = $url_array[3];
        switch ( $action )
        {
            case "list":
            {
                if ( isset( $url_array[4] ) )
                    $sortOrder = $url_array[4];
                else
                    $sortOrder = "Name";
                include( "kernel/ezarticle/user/authorlist.php" );
                break;
            }
            case "view":
            {
                $authorID = $url_array[4];
                $sortOrder = $url_array[5];
                $offset = $url_array[6];
                include( "kernel/ezarticle/user/authorview.php" );
                break;
            }
        }
        break;
    }

    case "archive":
    {
        $categoryID = $url_array[3];
        if ( !isset( $categoryID ) || ( $categoryID == "" ) )
            $categoryID = 0;

	if ( isset( $url_array[4] ) )
          $offset = $url_array[4];
	else
	  $offset = 0;
	  
        if ( !is_numeric( $offset ) )
            $offset = 0;


        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if ( is_a( $user, "eZUser" ) )
        {
            $groupstr = $user->groupString();
        }
        else
            $user = 0;

//        print( "Checking category: $categoryID <br>" );

        if ( $pageCaching == "enabled" )
        {
            //$categoryID = $url_array[3];

            // include_once( "classes/ezcachefile.php" );
            $file = new eZCacheFile( "kernel/ezarticle/cache/", array( "articlelist", $categoryID, $offset, $groupstr ),
                                     "cache", "," );

            $cachedFile = $file->filename( true );
//            print( "Cache file name: $cachedFile" );

            $articleCategoryTest = new eZArticleCategory( $categoryID );
            $isOwner = $articleCategoryTest->isOwner( $user, $categoryID);

            if ( $file->exists() )
            {
                include( $cachedFile );
            }
            else if ( $categoryID == 0 || eZObjectPermission::hasPermission( $categoryID, "article_category", 'r' ) ||
                $isOwner )// check if user really has permissions to browse this category
            {
                $generateStaticPage = "true";

                include( "kernel/ezarticle/user/articlelist.php" );
            }
            else
            {
                eZHTTPTool::header( "Location: /error/403" );
                exit();

            }
        }
        else if ( $categoryID == 0 || eZObjectPermission::hasPermission( $categoryID, "article_category", 'r' )
        || eZArticleCategory::isOwner( $user, $categoryID ) )
        {
            include( "kernel/ezarticle/user/articlelist.php" );
        }
        else
        {
            eZHTTPTool::header( "Location: /error/403" );
            exit();
        }
    }
    break;


    case "search":
    {
        if ( $url_array[3] == "advanced" )
        {
            include( "kernel/ezarticle/user/searchform.php" );
        }
        else
        {
            $offset = 0;
            if ( $url_array[3] == "parent" )
            {
                $searchText = urldecode( $url_array[4] );
                if ( $url_array[5] != urlencode( "+" ) )
                    $startStamp = urldecode( $url_array[5] );
                if ( $url_array[6] != urlencode( "+" ) )
                    $stopStamp = urldecode( $url_array[6] );
                if ( $url_array[7] != urlencode( "+" ) )
                    $categoryArray = explode( "-", urldecode( $url_array[7] ) );
                if ( $url_array[8] != urlencode( "+" ) )
                    $contentsWriterID = urldecode( $url_array[8] );
                if ( $url_array[9] != urlencode( "+" ) )
                    $photographerID = urldecode( $url_array[9] );
                $offset = $url_array[10];
            }

            /*
                echo "<pre>";
                print_r ( $url_array );
                echo "</pre>";
                exit();
            */			
			
            include( "kernel/ezarticle/user/search.php" );
        }
    }
    break;

    case "index":
    {
        $currentIndex = urldecode( isset($url_array[3])?$url_array[3]:'' );

        $user = eZUser::currentUser();
        $groupstr = "";
        if ( is_a( $user, "eZUser" ) )
        {
            $groupIDArray = $user->groups( false );
            sort( $groupIDArray );
            $first = true;
            foreach ( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= $groupID : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        // include_once( "classes/ezcachefile.php" );
        $file = new eZCacheFile( "kernel/ezarticle/cache/", array( "articleindex", $groupstr, $currentIndex ),
                                 "cache", "," );

        $cachedFile = $file->filename( true );
        if ( $file->exists() )
        {
            include( $cachedFile );
        }
        else
        {
            $generateStaticPage = "true";
            include( "kernel/ezarticle/user/index.php" );
        }
    }
    break;

    case "extendedsearch":
    {
        if ( !isset( $category ) and count( $url_array ) > 5 )
        {
            $category = trim( urldecode( $url_array[4] ) );
        }
        if ( !isset( $searchText ) and count( $url_array ) > 5 )
        {
            $searchText = trim( urldecode( $url_array[3] ) );
        }
        if ( count( $url_array ) > 5 )
            $offset = $url_array[5];
        if ( count( $url_array ) > 5 )
            $search = true;
        include( "kernel/ezarticle/user/extendedsearch.php" );
    }
    break;

    case "articleheaderlist":
    {
        $categoryID = $url_array[3];
        if ( !isset( $categoryID ) || ( $categoryID == "" ) )
            $categoryID = 0;

        include( "kernel/ezarticle/user/articleheaderlist.php" );
    }
    break;

    case "view":
    case "articleview":
    {
        $staticRendering = false;
        $articleID = $url_array[3];
        $pageNumber= $url_array[4];
        $categoryID = isset($url_array[5])?$url_array[5]:-1;
        if ( $pageNumber != -1 )
            if ( !isset( $pageNumber ) || ( $pageNumber == "" ) || ( $pageNumber < 1 ) )
                $pageNumber= 1;

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if ( is_a( $user, "eZUser" ) )
        {
            $groupIDArray = $user->groups( false );
            sort( $groupIDArray );
            $first = true;
            foreach ( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;

        $article = new eZArticle( $articleID );
        $definition = $article->categoryDefinition( false );

        $showComments = false;
        if ( $pageCaching == "enabled" )
        {
            $cachedFile = "kernel/ezarticle/cache/articleview," . $articleID . ",". $pageNumber . "," . $categoryID . "," . ( isset( $printableVersion ) && $printableVersion == "enabled" )  . "," . $groupstr  .".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
                $showComments = true;
            }
            else if ( eZObjectPermission::hasPermissionWithDefinition( $articleID, "article_article", 'r', false, $definition )
                      || eZArticle::isAuthor( $user, $articleID ) )
            {
                $generateStaticPage = "true";

                include( "kernel/ezarticle/user/articleview.php" );
                $showComments = true;
            }
            else
            {
            }
        }
        else if ( eZObjectPermission::hasPermissionWithDefinition( $articleID, "article_article", 'r', false, $definition )
                  || eZArticle::isAuthor( $user, $articleID ) )
        {
            include( "kernel/ezarticle/user/articleview.php" );
            $showComments = true;
        }

        /* Should there be permissions here? */
        if ( $showComments == true )
        {
            if  ( ( !isset($printableVersion) || $printableVersion != "enabled" ) && ( $userComments == "enabled" ) )
            {
                $redirectURL = "/article/view/$articleID/$pageNumber/";
                $article = new eZArticle( $articleID );
                if ( ( $article->id() >= 1 ) && $article->discuss() )
                {
                    for ( $i = 0; $i < count( $url_array ); $i++ )
                    {
                        if ( ( $url_array[$i] ) == "parent" )
                        {
                            $next = $i + 1;
                            $offset = $url_array[$next];
                        }
                    }
                    $forum = $article->forum();
                    $forumID = $forum->id();
					$messageCount = $forum->messageCount( false, true );
                    // Compatibility aliases for ezforum/messagesimplelist.php (not yet refactored)
                    $ForumID = $forumID;
                    $MessageCount = $messageCount;
                    $Offset = $offset ?? 0;
                    $RedirectURL = $redirectURL;
                    include( "kernel/ezforum/user/messagesimplelist.php" );
                }
            }
        }
    }
    break;

    case "articleuncached":
    {
        $viewMode = "static";

        $staticRendering = true;
        $articleID = $url_array[3];
        $pageNumber= $url_array[4];
        $categoryID = $url_array[5];

        $user = eZUser::currentUser();

        $article = new eZArticle( $articleID );
        $definition = $article->categoryDefinition( false );

        if ( eZObjectPermission::hasPermissionWithDefinition( $articleID, "article_article", 'r', false, $definition )
                  || eZArticle::isAuthor( $user, $articleID ) )
        {
            if ( !isset( $pageNumber ) || ( $pageNumber == "" ) || ( $pageNumber < 1 ) )
                $pageNumber= 1;

            include( "kernel/ezarticle/user/articleview.php" );
        }
    }
    break;

    case "print":
    case "articleprint":
    {
        $printableVersion = "enabled";

        $staticRendering = false;
        $articleID = $url_array[3];
        $pageNumber= $url_array[4];
        $categoryID = $url_array[5];

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if ( is_a( $user, "eZUser" ) )
        {
            $groupIDArray = $user->groups( false );
            sort( $groupIDArray );
            $first = true;
            foreach ( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;

        if ( $pageNumber != -1 )
        {
            if ( !isset( $pageNumber ) || ( $pageNumber == "" ) )
                $pageNumber = -1;
            else if ( $pageNumber < 1 )
                $pageNumber = 1;
        }

        $article = new eZArticle( $articleID );
        $definition = $article->categoryDefinition( true );
        $definition = $definition->id();

        if ( $pageCaching == "enabled" )
        {
             $cachedFile = "kernel/ezarticle/cache/articleprint," . $articleID . ",". $pageNumber . "," . $categoryID . "," . $groupstr  .".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else if ( eZObjectPermission::hasPermissionWithDefinition( $articleID, "article_article", 'r', false, $definition )
                      || eZArticle::isAuthor( $user, $articleID ) )
            {
                $generateStaticPage = "true";

                include( "kernel/ezarticle/user/articleview.php" );
            }
        }
        else if ( eZObjectPermission::hasPermissionWithDefinition( $articleID, "article_article", 'r', false, $definition )
                  || eZArticle::isAuthor( $user, $articleID ) )
        {
            include( "kernel/ezarticle/user/articleview.php" );
        }
    }
    break;

    case "static":
    case "articlestatic":
    {
        $viewMode = "static";

        $staticRendering = true;
        $articleID = $url_array[3];
		if ( isset( $url_array[4] ) )
	        $pageNumber = $url_array[4];
		else
			$pageNumber = "";

        // if file exists... evrything is ok..
        // if not.. check permission, then run page if ok
        $user = eZUser::currentUser();
        $groupstr = "";
        if ( is_a( $user, "eZUser" ) )
        {
            $groupIDArray = $user->groups( false );
            sort( $groupIDArray );
            $first = true;
            foreach ( $groupIDArray as $groupID )
            {
                $first ? $groupstr .= "$groupID" : $groupstr .= "-$groupID";
                $first = false;
            }
        }
        else
            $user = 0;

        if ( !isset( $categoryID ) )
            $categoryID = eZArticle::categoryDefinitionStatic( $articleID );

        $globalSectionID = eZArticleCategory::sectionIDStatic( $categoryID );

        if ( !isset( $pageNumber ) || ( $pageNumber == "" ) || ( $pageNumber < 1 ) )
            $pageNumber= 1;

        $article = new eZArticle( $articleID );
        $definition = $article->categoryDefinition( true );
        $definition = $definition->id();

        if ( $pageCaching == "enabled" )
        {
            $cachedFile = "kernel/ezarticle/cache/articleview," . $articleID . ",". $pageNumber . "," . $categoryID . "," . $groupstr  .".cache";
            if ( file_exists( $cachedFile ) )
            {
                include( $cachedFile );
            }
            else if ( eZObjectPermission::hasPermissionWithDefinition( $articleID, "article_article", 'r', false, $definition )
                      || eZArticle::isAuthor( $user, $articleID ) )
            {
                $generateStaticPage = "true";

                include( "kernel/ezarticle/user/articleview.php" );
            }
        }
        else if ( eZObjectPermission::hasPermissionWithDefinition( $articleID, "article_article", 'r', false, $definition )
                  || eZArticle::isAuthor( $user, $articleID ) )
        {

            include( "kernel/ezarticle/user/articleview.php" );
        }
    }
    break;

    case "rss":
    case "rssheadlines":
    {
        include( "kernel/ezarticle/user/articlelistrss.php" );
    }
    break;

    case "articleedit":
    {
        if ( eZUser::currentUser() != false &&
             $ini->variable( "eZArticleMain", "UserSubmitArticles" ) == "enabled" )
        {
            switch ( $url_array[3] )
            {
                case "new":
                {
                    $action = "New";
                    include( "kernel/ezarticle/user/articleedit.php" );
                    break;
                }
                case "edit":
                {
                    $action = "Edit";
                    //PBo added this
                    $articleID = $url_array[4];
                    //end pbo mods
                    include( "ezarticle/user/articleedit.php" );
                    break;
                }
                case "insert":
                {
                    $action = "Insert";
                    $articleID = $url_array[4];
                    include( "kernel/ezarticle/user/articleedit.php" );
                    break;
                }
                case "update":
                {
                    $action = "Update";
                    $articleID = $url_array[4];
                    include( "kernel/ezarticle/user/articleedit.php" );
                    break;
                }
                case "cancel":
                {
                    $action = "Cancel";
                    $articleID = $url_array[4];
                    include( "kernel/ezarticle/user/articleedit.php" );
                    break;
                }
                case "imagelist" :
                {
                    $articleID = $url_array[4];
                    if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                         || eZArticle::isAuthor( $user, $articleID ) )
                        include( "kernel/ezarticle/user/imagelist.php" );
                    break;
                }
                case "filelist" :
                {
                    $articleID = $url_array[4];
                    if ( eZObjectPermission::hasPermission(  $articleID, "article_article", 'w' )
                         || eZArticle::isAuthor( $user, $articleID ) )
                        include( "kernel/ezarticle/user/filelist.php" );
                    break;
                }
                case "imageedit" :
                {
                    switch ( $url_array[4] )
                    {
                        case "new" :
                        {
                            $action = "New";
                            $articleID = $url_array[5];
                            if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                                 || eZArticle::isAuthor( $user, $articleID ) )
                                include( "kernel/ezarticle/user/imageedit.php" );
                        }
                        break;

                        case "edit" :
                        {
                            $action = "Edit";
                            $articleID = $url_array[6];
                            $imageID = $url_array[5];
                            if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                                 || eZArticle::isAuthor( $user, $articleID ) )
                                include( "kernel/ezarticle/user/imageedit.php" );
                        }
                        break;

                        case "storedef" :
                        {
                            $action = "StoreDef";
                            if ( isset( $deleteSelected ) )
                                $action = "Delete";
                            $articleID = $url_array[5];
                            if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                                 || eZArticle::isAuthor( $user, $articleID ) )
                                include( "kernel/ezarticle/user/imageedit.php" );
                        }
                        break;

                        default :
                        {
                            if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                                 || eZArticle::isAuthor( $user, $articleID ) )
                                include( "kernel/ezarticle/user/imageedit.php" );
                        }
                    }
                }
                break;

                case "fileedit" :
                {
                    switch ( $url_array[4] )
                    {
                        case "new" :
                        {
                            $action = "New";
                            $articleID = $url_array[5];
                            if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                                 || eZArticle::isAuthor( $user, $articleID ) )
                                include( "kernel/ezarticle/user/fileedit.php" );
                        }
                        break;

                        case "delete" :
                        {
                            $action = "Delete";
                            $articleID = $url_array[6];
                            $fileID = $url_array[5];
                            if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                                 || eZArticle::isAuthor( $user, $articleID ) )
                                include( "kernel/ezarticle/user/fileedit.php" );
                        }
                        break;

                        default :
                        {
                            if ( eZObjectPermission::hasPermission( $articleID, "article_article", 'w' )
                                 || eZArticle::isAuthor( $user, $articleID ) )
                                include( "kernel/ezarticle/user/fileedit.php" );
                        }
                    }
                }
            }
        }
        else
        {
            // include_once( "classes/ezhttptool.php" );
            eZHTTPTool::header( "Location: /article/archive/" );
            exit();
        }
    }
    break;

    // XML rpc interface
    case "xmlrpc" :
    {
        include( "ezarticle/xmlrpc/xmlrpcserver.php" );
    }
    break;
}

// Expose lowerCamelCase var as kernel interface variable (kernel reads $GlobalSectionID after include)
$GlobalSectionID = $globalSectionID;

?>