<?php
//
// $Id: datasupplier.php 9407 2002-04-10 11:49:02Z br $
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

$PageCaching = $ini->variable( "eZTradeMain", "PageCaching");

// include_once( "ezuser/classes/ezuser.php" );
// include_once( "eztrade/classes/ezpricegroup.php" );
// include_once( "classes/ezhttptool.php" );

$user = eZUser::currentUser();
$ini = eZINI::instance( 'site.ini' );
$SiteDesign = $ini->variable( "site", "SiteDesign" );

$RequireUser = $ini->variable( "eZTradeMain", "RequireUserLogin" ) == "enabled" ? true : false;
$ShowPrice = $RequireUser ? is_a( $user, "eZUser" ) : true;
$userReviews = $ini->variable( "eZTradeMain", "UserReviews" );

$priceGroup = 0;
if ( is_a( $user, "eZUser" ) )
{
    $priceGroup = eZPriceGroup::correctPriceGroup( $user->groups( false ) );
}
if ( !$ShowPrice )
    $priceGroup = -1;

$GlobalSectionID = $ini->variable( "eZTradeMain", "DefaultSection" );

$accountNumber     = eZHTTPTool::getVar( 'AccountNumber' );
$action            = eZHTTPTool::getVar( 'Action' );
$adminSiteURL      = eZHTTPTool::getVar( 'AdminSiteURL' );
$available         = eZHTTPTool::getVar( 'Available' );
$back              = eZHTTPTool::getVar( 'Back' );
$billingAddressID  = eZHTTPTool::getVar( 'BillingAddressID' );
$ccNumber          = eZHTTPTool::getVar( 'CcNumber' );
$cacheFile         = eZHTTPTool::getVar( 'CacheFile' );
$cancel            = eZHTTPTool::getVar( 'Cancel' );
$capitalizeHeadlines = eZHTTPTool::getVar( 'CapitalizeHeadlines' );
$cartCountArray    = $_POST['CartCountArray'] ?? [];
$cartIDArray       = $_POST['CartIDArray'] ?? [];
$cartItemID        = eZHTTPTool::getVar( 'CartItemID' );
$cartSelectArray   = $_POST['CartSelectArray'] ?? [];
$categoryArray     = $_POST['CategoryArray'] ?? [];
$categoryArrayID   = $_POST['CategoryArrayID'] ?? [];
$categoryID        = eZHTTPTool::getVar( 'CategoryID' );
$chargeTotal       = eZHTTPTool::getVar( 'ChargeTotal' );
$chargeVATTotal    = eZHTTPTool::getVar( 'ChargeVATTotal' );
$comment           = eZHTTPTool::getVar( 'Comment' );
$description       = eZHTTPTool::getVar( 'Description' );
$email             = eZHTTPTool::getVar( 'Email' );
$emailError        = eZHTTPTool::getVar( 'EmailError' );
$expireMonth       = eZHTTPTool::getVar( 'ExpireMonth' );
$expireYear        = eZHTTPTool::getVar( 'ExpireYear' );
$forumID           = eZHTTPTool::getVar( 'ForumID' );
$generateStaticPage = eZHTTPTool::getVar( 'GenerateStaticPage' );
$hotDealColumns    = eZHTTPTool::getVar( 'HotDealColumns' );
$hotDealsPage      = eZHTTPTool::getVar( 'HotDealsPage' );
$limit             = eZHTTPTool::getVar( 'Limit' );
$mail              = eZHTTPTool::getVar( 'Mail' );
$mailMethod        = eZHTTPTool::getVar( 'MailMethod' );
$message           = eZHTTPTool::getVar( 'Message' );
$moduleName        = eZHTTPTool::getVar( 'ModuleName' );
$ok                = eZHTTPTool::getVar( 'OK' ) ?? eZHTTPTool::getVar( 'Ok' );
$offset            = eZHTTPTool::getVar( 'Offset' );
$optionIDArray     = $_POST['OptionIDArray'] ?? [];
$optionValueArray  = $_POST['OptionValueArray'] ?? [];
$orderBy           = eZHTTPTool::getVar( 'OrderBy' );
$orderID           = eZHTTPTool::getVar( 'OrderID' );
$paymentSuccess    = eZHTTPTool::getVar( 'PaymentSuccess' );
$paymentType       = eZHTTPTool::getVar( 'PaymentType' );
$paypalEmail       = eZHTTPTool::getVar( 'PaypalEmail' );
$paypalMode        = eZHTTPTool::getVar( 'PaypalMode' );
$preOrderID        = eZHTTPTool::getVar( 'PreOrderID' );
$price             = eZHTTPTool::getVar( 'Price' );
$productID         = eZHTTPTool::getVar( 'ProductID' );
$purchaseProduct   = eZHTTPTool::getVar( 'PurchaseProduct' );
$quantity          = eZHTTPTool::getVar( 'Quantity' );
$query             = eZHTTPTool::getVar( 'Query' );
$queryText         = eZHTTPTool::getVar( 'QueryText' );
$redirectURL       = eZHTTPTool::getVar( 'RedirectURL' );
$removeVoucher     = eZHTTPTool::getVar( 'RemoveVoucher' );
$searchText        = eZHTTPTool::getVar( 'SearchText' );
$sendOrder         = eZHTTPTool::getVar( 'SendOrder' );
$shippingTypeID    = eZHTTPTool::getVar( 'ShippingTypeID' );
$stock             = eZHTTPTool::getVar( 'Stock' );
$subTotalsColumns  = eZHTTPTool::getVar( 'SubTotalsColumns' );
$text              = eZHTTPTool::getVar( 'Text' );
$update            = eZHTTPTool::getVar( 'Update' );
$urlQueryString    = eZHTTPTool::getVar( 'UrlQueryString' );
$voucher           = eZHTTPTool::getVar( 'Voucher' );
$wishList          = eZHTTPTool::getVar( 'WishList' );
$wishListItemID    = eZHTTPTool::getVar( 'WishListItemID' );

if ( $user )
{
    $groupIDArray = $user->groups( false );
    sort( $groupIDArray );
}
else
{
    $groupIDArray = null;
}


switch ( $url_array[2] )
{

    case "hotdealsgallery" :
    {
        // $redirectURL = $redirectURL;
        $session->setVariable( "RedirectURL", $_SERVER['REQUEST_URI'] );
        $categoryID = $url_array[3];
        $offset = $url_array[4];
        if ( !is_numeric( $offset ) )
            $offset = 0;
        if ( $PageCaching == "enabled" )
        {
            //include_once( "classes/ezcachefile.php" );
            $cacheFile = new eZCacheFile( "kernel/eztrade/cache/",
                                            array( "hotdealsgallery", $categoryID, $groupIDArray, $offset, $priceGroup ),
                                            "cache", "," );
            if ( $cacheFile->exists() )
            {
                include( $cacheFile->filename( true ) );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/eztrade/user/hotdealsgallery.php" );
            }
        }
        else
        {
            include( "kernel/eztrade/user/hotdealsgallery.php" );
        }
        break;
    }

    case "hotdealslist" :
    {
        // $redirectURL = $redirectURL;
        $session->setVariable( "RedirectURL", $_SERVER['REQUEST_URI'] );
        $categoryID = $url_array[3];
        $offset = $url_array[4];
        if ( !is_numeric( $offset ) )
            $offset = 0;
        if ( $PageCaching == "enabled" )
        {
            //include_once( "classes/ezcachefile.php" );
            $cacheFile = new eZCacheFile( "kernel/eztrade/cache/",
                                            array( "hotdealslist", $categoryID, $groupIDArray, $offset, $priceGroup ),
                                            "cache", "," );
            if ( $cacheFile->exists() )
            {
                include( $cacheFile->filename( true ) );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/eztrade/user/hotdealslist.php" );
            }
        }
        else
        {
            include( "kernel/eztrade/user/hotdealslist.php" );
        }
        break;
    }

    case "productgallery" :
    {
    // $redirectURL = $redirectURL;
    $session->setVariable( "RedirectURL", $_SERVER['REQUEST_URI'] );
        $categoryID = $url_array[3];
        $offset = $url_array[4];
        if ( !is_numeric( $offset ) )
            $offset = 0;
        if ( $PageCaching == "enabled" )
        {
            // include_once( "classes/ezcachefile.php" );
            $cacheFile = new eZCacheFile( "kernel/eztrade/cache/",
                                            array( "productgallery", $categoryID, $groupIDArray, $offset, $priceGroup ),
                                            "cache", "," );
            if ( $cacheFile->exists() )
            {
                include( $cacheFile->filename( true ) );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/eztrade/user/productgallery.php" );
            }
        }
        else
        {
            include( "kernel/eztrade/user/productgallery.php" );
        }
        break;
    }
    
    case "productlist" :
    {
        $categoryID = $url_array[3];
        $offset = $url_array[4];
        if ( !is_numeric( $offset ) )
            $offset = 0;
        if ( $PageCaching == "enabled" )
        {
            // include_once( "classes/ezcachefile.php" );
            $cacheFile = new eZCacheFile( "kernel/eztrade/cache/",
                                          array( "productlist", $categoryID, $groupIDArray, $offset, $priceGroup ),
                                          "cache", "," );
            if ( $cacheFile->exists() )
            {
                include( $cacheFile->filename( true ) );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/eztrade/user/productlist.php" );
            }
        }
        else
        {
            //$generateStaticPage = "false";
            $generateStaticPage = "false";
            include( "kernel/eztrade/user/productlist.php" );
        }
        break;
    }

    case "sitemap" :
    {
        include( "kernel/eztrade/user/sitemap.php" );
        break;
    }
    
    case "productview" :
        $PrintableVersion = "disabled";
        if ( $PageCaching == "enabled" )
        {
            $productID = $url_array[3];
            $categoryID = $url_array[4];

            // include_once( "classes/ezcachefile.php" );
            $cacheFile = new eZCacheFile( "kernel/eztrade/cache/",
                                          array( "productview", $productID, $groupIDArray, $priceGroup ),
                                          "cache", "," );
            if ( $cacheFile->exists() )
            {
                include( $cacheFile->filename( true ) );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/eztrade/user/productview.php" );
            }
        }
        else
        {
            $productID = $url_array[3];
            $categoryID = $url_array[4];
            include( "kernel/eztrade/user/productview.php" );
        }

        if  ( ( $PrintableVersion != "enabled" ) && ( $userReviews == "enabled" ) )
        {
            $redirectURL = "/trade/productview/$productID/$categoryID/";
            $product = new eZProduct( $productID );
            if ( ( $product->id() >= 1 ) )    //  && $product->discuss() )
            {
                for ( $i = 0; $i < count( $url_array ); $i++ )
                {
                    if ( ( $url_array[$i] ) == "parent" )
                    {
                        $next = $i + 1;
                        $offset = $url_array[$next];
                    }
                }
                $forum = $product->forum();
                $forumID = $forum->id();
                include( "kernel/ezforum/user/messagereviewlist.php" );
            }
        }

        break;

    case "print" :
    case "productprint" :
        if ( $PageCaching == "enabled" )
        {
            $PrintableVersion = "enabled";
            $productID = $url_array[3];
            $categoryID = $url_array[4];

            // include_once( "classes/ezcachefile.php" );
            $cacheFile = new eZCacheFile( "kernel/eztrade/cache/",
                                          array( "productprint", $productID, $groupIDArray, $priceGroup ),
                                          "cache", "," );
            if ( $cacheFile->exists() )
            {
                include( $cacheFile->filename( true ) );
            }
            else
            {
                $generateStaticPage = "true";
                include( "kernel/eztrade/user/productview.php" );
            }
        }
        else
        {
            $PrintableVersion = "enabled";
            $productID = $url_array[3];
            $categoryID = $url_array[4];
            include( "kernel/eztrade/user/productview.php" );
        }

        break;

    case "cart" :
    {
        if ( $url_array[3] == "add" )
        {
            $action = "AddToBasket";
            $productID = $url_array[4];
        }

        if ( $url_array[3] == "remove" )
        {
            $action = "RemoveFromBasket";
            $cartItemID = $url_array[4];
        }

        if ( isset( $wishList ) )
        {
            include( "kernel/eztrade/user/wishlist.php" );

//               eZHTTPTool::header( "Location: /trade/wishlist/add/$productID" );
//              exit();
        }
        else
        {
            include( "kernel/eztrade/user/cart.php" );
        }
    }
        break;

    case "wishlist" :
    {
        if ( $url_array[3] == "add" )
        {
            $action = "AddToBasket";
            $productID = $url_array[4];
        }

        if ( $url_array[3] == "movetocart" )
        {
            $action = "MoveToCart";
            $wishListItemID = $url_array[4];
        }

        if ( $url_array[3] == "remove" )
        {
            $action = "RemoveFromWishlist";
            $wishListItemID = $url_array[4];
        }

        include( "kernel/eztrade/user/wishlist.php" );
    }
    break;

    case "viewwishlist" :
    {
        if ( $url_array[3] == "movetocart" )
        {
            $action = "MoveToCart";
            $wishListItemID = $url_array[4];
        }

        include( "kernel/eztrade/user/viewwishlist.php" );
    }
    break;

    case "sendwishlist" :
    {
        include( "kernel/eztrade/user/sendwishlist.php" );
    }
    break;

    case "voucherview" :
    {
        include( "kernel/eztrade/user/voucherview.php" );
    }
    break;

    case "vouchermain" :
    {
        include( "kernel/eztrade/user/vouchermain.php" );
    }
    break;

    case "voucheremailsample" :
    {
        include( "kernel/eztrade/user/voucheremailsample.php" );
    }
    break;

    case "orderview" :
    {
        $orderID = $url_array[3];
        include( "kernel/eztrade/user/orderview.php" );
    }
    break;

    case "findwishlist" :
    {
        include( "kernel/eztrade/user/findwishlist.php" );
    }
    break;

    case "customerlogin" :
        include( "kernel/eztrade/user/customerlogin.php" );
    break;

    case "precheckout" :
    {
        include( "kernel/eztrade/user/precheckout.php" );
    }
    break;

    case "checkout" :
    {
        include( "kernel/eztrade/user/checkout.php" );
    }
    break;

    case "payment" :
    {
        include( "kernel/eztrade/user/payment.php" );
    }
    break;

	case "paypal" :
    {
        $orderID = $url_array[3];
        $sessionID = $url_array[4];
        include( "kernel/eztrade/user/paypalnotify.php" );

    }
    break;

    case "confirmation" :
    {
        include( "kernel/eztrade/user/confirmation.php" );
    }
    break;

    case "voucherinformation" :
    {
        $productID = $url_array[3];
        $PriceRange = $url_array[4];
        $mailMethod = $url_array[5];

        include( "kernel/eztrade/user/voucherinformation.php" );
    }
    break;

    case "ordersendt" :
    {
        $orderID = $url_array[3];
        include( "kernel/eztrade/user/ordersendt.php" );
    }
    break;

    case "search" :
    {
        if ( $url_array[3] == "move" )
        {
            $query = urldecode( $url_array[4] );
            $offset = urldecode ( $url_array[5] );
        }
        include( "kernel/eztrade/user/productsearch.php" );
    }
    break;

    case "orderlist" :
    {
        if ( $url_array[3] != "" )
            $offset = $url_array[3];
        else
            $offset = 0;

        include( "kernel/eztrade/user/orderlist.php" );
    }
    break;

    case "extendedsearch" :
    {
        $limit = 10;
        if ( $url_array[3] == "move" )
        {
            $text = urldecode( $url_array[4] );
            $PriceRange = urldecode( $url_array[5] );
            $MainCategories = urldecode ( $url_array[6] );
            $categoryArray = urldecode ( $url_array[7] );
            $offset = urldecode ( $url_array[8] );

            $action = "SearchButton";
            $Next = true;
        }

        include( "kernel/eztrade/user/extendedsearch.php" );
    }
    break;
    
    case "export" :
    {
      if ( $url_array[3] == 'froogle' )
      {
        if ( $url_array[4] == 'download' )
	{
	  $action = "export";
	}
        else 
	{
          $action = "export-cron";
        }
        include( "kernel/eztrade/admin/export_froogle.php" );
      }
      elseif ( $url_array[3] == 'yahoo' )
      {
        if ( $url_array[4] == 'download' )
	{
	  $action = "export";
	}
        else 
	{
          $action = "export-cron";
        }
        include( "kernel/eztrade/admin/export_yahoo.php" );
      }
      else {
	$action = "export-cron";
	include( "kernel/eztrade/admin/export_froogle.php" );
      }
    }
    break;

    // XML rpc interface
    case "xmlrpc" :
    {
        include( "kernel/eztrade/xmlrpc/xmlrpcserver.php" );
    }
    break;

    // XML rpc interface
    case "xmlrpcimport" :
    {
        include( "kernel/eztrade/xmlrpc/xmlrpcserverimport.php" );
    }
    break;


    default :
    {
        eZHTTPTool::header( "Location: /error/404" );
        exit();
    }
    break;
}

?>