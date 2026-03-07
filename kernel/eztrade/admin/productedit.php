<?php
//
// $id: productedit.php 9904 2004-07-09 11:44:47Z br $
//
// Created on: <19-Sep-2000 10:56:05 bf>
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
// include_once( "classes/ezcachefile.php" );
// include_once( "classes/ezhttptool.php" );
// include_once( "ezuser/classes/ezobjectpermission.php" );
// include_once( "eztrade/classes/ezpricegroup.php" );
// include_once( "eztrade/classes/ezproductpermission.php" );
// include_once( "eztrade/classes/ezproductpricerange.php" );

// include_once( "ezxml/classes/ezxml.php" );

function deleteCache( $productID, $categoryID, $categoryArray, $hotdeal )
{
    $product = new eZProduct( $productID );
    if ( is_a( $product, "eZProduct" ) )
    {
        $categoryID = $product->categoryDefinition( false );
        $categoryArray = $product->categories( false );
        $hotdeal = $product->isHotDeal();
        $productID = $product->id();
    }

    $files = eZCacheFile::files( "kernel/eztrade/cache/", array( array( "productview", "productprint" ),
                                                          $productID, $categoryID ),
                                 "cache", "," );
    foreach ( $files as $file )
    {
        $file->delete();
    }
    $files = eZCacheFile::files( "kernel/eztrade/cache/", array( "productlist",
                                                          array_merge( array( $categoryID ), $categoryArray ) ),
                                 "cache", "," );

    foreach ( $files as $file )
    {
        $file->delete();
    }
    if ( $hotdeal )
    {
        $files = eZCacheFile::files( "kernel/eztrade/cache/", array( "hotdealslist", NULL ),
                                     "cache", "," );
        foreach ( $files as $file )
        {
            $file->delete();
        }
    }
    $files = eZCacheFile::files( "kernel/ezarticle/cache/",
                                  array( "articlefrontpage",
                                         NULL,
                                         NULL),
                                  "cache", "," );
    foreach( $files as $file )
    {
        $file->delete();
    }

}

$ini = eZINI::instance( 'site.ini' );

$language = $ini->variable( "eZTradeMain", "Language" );
$showPriceGroups = $ini->variable( "eZTradeMain", "PriceGroupsEnabled" ) == "true";
$showQuantity = $ini->variable( "eZTradeMain", "ShowQuantity" ) == "true";
$showModuleLinker = $ini->variable( "eZTradeMain", "ShowModuleLinker" ) == "true";

$csvDelimiter = $ini->variable( "eZTradeMain", "CSVDelimiter" );
$defaultDealerPriceGroup = $ini->variable( "eZTradeMain", "DefaultDealerPriceGroup" );

// include_once( "eztrade/classes/ezproduct.php" );
// include_once( "eztrade/classes/ezproductcategory.php" );
// include_once( "eztrade/classes/ezvattype.php" );
// include_once( "eztrade/classes/ezshippinggroup.php" );

// must have to generate XML
// include_once( "ezarticle/classes/ezarticlegenerator.php" );

if ( isset($csvImport) )
{
	// include_once( "eztrade/classes/ezcsvimport.php" );
	//get default target category ID
	$categoryID = $ini->variable( "eZTradeMain", "CSVImportCat" );
	//read CSV file
	$productImport = new eZCSVImport();
	$csvArray =  $productImport->csvFileToArray($importCSVDir, $csvDelimiter, 'none', TRUE, TRUE, FALSE);
//	print_r($csvArray); exit();
	foreach ( $csvArray as $row )
	{
		// make a new product
		// build the product
		/*
			$row[0] = Name
			$row[1] = Product_Number
			$row[2] = Keywords
			$row[3] = Lead_In
			$row[4] = Description
			$row[5] = External_Link
			$row[6] = Quantity
			$row[7] = StockDate
			$row[8] = Shipping_group
			$row[9] = Weight
			$row[10] = BoxType
			$row[11] = Duration
			$row[12] = IsHotDeal
			$row[13] = Discontinued
			$row[14] = Tax_Amount
			$row[15] = IncludesVAT
			$row[16] = Price
			$row[17] = ListPrice
			$row[18] = DealerPrice
			$row[19] = Show_Product
			$row[20] = Show_Price
			$row[21] = FlatUPS
			$row[22] = FlatUSPS
			$row[23] = FlatCombine
		*/
		
		$product = new eZProduct();
		$generator = new eZArticleGenerator();		
		
		// check to see if product already exists
		$item = $product->findProductNumber($row[1]);
		// update fields if true
		if ( $item )
		{
		    if ( eZXML::domTree( $contents ) )
    		{
    			if ( $item->name() != $row[0] )
					$item->setName( $row[0] );

				$contentsArray = $generator->decodeXML( $item->contents() );

				if ( $contentsArray[0] != $row[3] )
					$item->setBrief( $row[3] );
				if ( $contentsArray[1] != $row[4] )
					$item->setDescription( $row[4] );
				
				if ( ($contentsArray[0] != $row[3]) || ($contentsArray[1] != $row[4]) )
				{
					$contents = array( $row[3], $row[4] );
				    $contents = $generator->generateXML( $contents );
					$item->setContents( $contents );
				}
				
    	    	if ( $item->keywords() != $row[2] )
					$item->setKeywords( $row[2]  );
				if ( $item->productNumber != $row[1] )
				   	$item->setProductNumber( $row[1] );
				if ( $item->externalLink != $row[5] )
	        		$item->setExternalLink( $row[5] );
				//set delivery date if quantity = 0
				if ( is_numeric($row[6]) && $row[6] == 0 && $row[7] != "" )
					{
						$dateArray = explode('/', $row[7]);
			            $dateInStock = new eZDate( $dateArray[2], $dateArray[1], $dateArray[0] );
						$date = $dateInStock->timeStamp();
						if ( $item->stockDate != $date )
							$item->setStockDate( $date );
					}
				
				if ( $row[6] > 0 || $row[7] == "" )
					$item->setStockDate('');
				
		        $vattype = $item->vatType();
				if ( $vattype->id() != $row[14] && $row[14] > 0 )
					{
						$newVATType = new eZVATType( $row[14] );
	    	    		$item->setVATType( $newVATType );
					}
			
				$boxtype = $item->boxType();
				if ($boxtype && $boxtype->id() != $row[10] && $row[10] > 0 )
				{
		        	$newBoxType = new eZBoxType( $row[10] );
    	    		$item->setBoxType( $newBoxType );
				}
				
				if ($boxtype && $row[10]=='' )
				{
		        	$newBoxType = new eZBoxType();
    	    		$item->setBoxType( $newBoxType );
				}
				
				if ( !$item->shippingGroup() )
					$item->setShippingGroup( $row[8] );
				
				$shippingGroup = $item->shippingGroup();

				if ( $shippingGroup && $shippingGroup->id() != $row[8] )
	    	    {
					$newShippingGroup = new eZShippingGroup( $row[8] );
    			    $item->setShippingGroup( $newShippingGroup );
				}
    
    		    if ( $row[20] == "on" )
    	    	   	$item->setShowPrice( true );
	       		else if ( $row[20] == "off" )
    	    	    $item->setShowPrice( false );
        		if ( $item->weight() != $row[9] )
					$item->setWeight( $row[9] );
				//product is not a voucher
				$item->setProductType( 1 );
    	        $item->setShowProduct( $row[19] == "on" );
	        	$item->setDiscontinued( $row[13] == "on" );

	    	    if ( $row[12] == "on" )
    		    	$item->setIsHotDeal( true );
	        	else if ( $row[12] == "off" )
     				$item->setIsHotDeal( false );
           		$item->setPrice( $row[16] );
				$item->setListPrice( $row[17] );
	    	    if ( $row[15] == "true" )
    		  	    $item->setIncludesVAT( true );
        	    else if ( $row[15] == "false" )
        			$item->setIncludesVAT( false );
				if ( $row[11] > 0 )
	            	$item->setExpiryTime( $row[11] );
	            // Flat Rate 	
				  if ( is_numeric( $row[21] ) )
				   $item->setFlatUPS( $row[21] );
				  else
				   $item->setFlatUPS( 'off' );
				   
				  if ( is_numeric( $row[22] ) )
				   $item->setFlatUSPS( $row[22] );
				  else
				   $item->setFlatUSPS( 'off' );
				
				$item->setFlatCombine( $row[23] );

				//store product
    		    $item->store();
   		     	$itemID = $item->id();
				//set dealer price
				if ( $row[18] != '' )
				{
		            eZPriceGroup::removePrices( $itemID, -1, -1, $defaultDealerPriceGroup );
					eZPriceGroup::addPrice( $itemID, $defaultDealerPriceGroup, $row[18] );
				}
				
				if ( $row[18] == '' )
				    eZPriceGroup::removePrices( $itemID, -1, -1, $defaultDealerPriceGroup );
										
				//set quantity data
				$item->setTotalQuantity( is_numeric( $row[6] ) ? $row[6] : false );
				// setup the category
			//	$category = new eZProductCategory( $categoryID );
			//	$item->setCategoryDefinition( $category );
			//	eZProductCategory::addProduct( $item, $categoryID );
				// set write permissions
    		/*    eZObjectPermission::removePermissions( $itemID, "trade_product", 'w' );
	        	eZObjectPermission::setPermission( 1, $itemID, "trade_product", 'w' );
            	// set read permissions
    		    eZObjectPermission::removePermissions( $itemID, "trade_product", 'r' );
    	    	eZObjectPermission::setPermission( -1, $itemID, "trade_product", 'r' );  */
			}
		
		}

	
		if (!$item)
		{	
			$contents = array( $row[3], $row[4] );
//    	$generator = new eZArticleGenerator();
    		$contents = $generator->generateXML( $contents );

	   		if ( eZXML::domTree( $contents ) )
    		{
    			$product->setName( $row[0] );	
				$product->setContents( $contents );
				$product->setBrief( $row[3] );
				$product->setDescription( $row[4] );
        		$product->setKeywords( $row[2]  );
        		$product->setProductNumber( $row[1] );
        		$product->setExternalLink( $row[5] );
				//set delivery date if quantity = 0
				if ( is_numeric($row[6]) && $row[6] == 0 )
					{
						$dateArray = explode('/', $row[7]);
		            	$dateInStock = new eZDate( $dateArray[2], $dateArray[1], $dateArray[0] );
						$date = $dateInStock->timeStamp();
						$product->setStockDate( $date );
					}
				
	    	    $vattype = new eZVATType( $row[14] );
    	    	$product->setVATType( $vattype );
			
				if ($row[10])
				{
	        		$boxtype = new eZBoxType( $row[10] );
    	    		$product->setBoxType( $boxtype );
				}			
	        	$shippingGroup = new eZShippingGroup( $row[8] );
    	    	$product->setShippingGroup( $shippingGroup );
    
    		    if ( $row[20] == "on" )
        		   	$product->setShowPrice( true );
       			else if ( $row[20] == "off" )
    	        	$product->setShowPrice( false );
        		if ( $row[9] )
					$product->setWeight( $row[9] );
				//product is not a voucher
				$product->setProductType( 1 );
            	$product->setShowProduct( $row[19] == "on" );
        		$product->setDiscontinued( $row[13] == "on" );

		        if ( $row[12] == "on" )
    		    	$product->setIsHotDeal( true );
        		else if ( $row[12] == "off" )
     				$product->setIsHotDeal( false );
           		$product->setPrice( $row[16] );
				$product->setListPrice( $row[17] );
	        	if ( $row[15] == "true" )
    	  		    $product->setIncludesVAT( true );
            	else if ( $row[15] == "false" )
        			$product->setIncludesVAT( false );
				if ( $row[11] > 0 )
            		$product->setExpiryTime( $row[11] );

				  if ( is_numeric( $row[21] ) )
				   $product->setFlatUPS( $row[21] );
				  else
				   $product->setFlatUPS( 'off' );
				   
				  if ( is_numeric( $row[22] ) )
				   $product->setFlatUSPS( $row[22] );
				  else
				   $product->setFlatUSPS( 'off' );
				
				$product->setFlatCombine( $row[23] );  
				//store product
    	    	$product->store();
        		$productID = $product->id();
				if ( $row[18] != '' )
				{
		            eZPriceGroup::removePrices( $productID, -1, -1, $defaultDealerPriceGroup );
					eZPriceGroup::addPrice( $productID, $defaultDealerPriceGroup, $row[18] );
				}
				
				if ( $row[18] == '' )
				    eZPriceGroup::removePrices( $productID, -1, -1, $defaultDealerPriceGroup );
				//set quantity data
				$product->setTotalQuantity( is_numeric( $row[6] ) ? $row[6] : false );
				// setup the category
				$category = new eZProductCategory( $categoryID );
				$product->setCategoryDefinition( $category );
				eZProductCategory::addProduct( $product, $categoryID );
				// set write permissions
    	    	eZObjectPermission::removePermissions( $productID, "trade_product", 'w' );
        		eZObjectPermission::setPermission( 1, $productID, "trade_product", 'w' );
            	// set read permissions
    	    	eZObjectPermission::removePermissions( $productID, "trade_product", 'r' );
        		eZObjectPermission::setPermission( -1, $productID, "trade_product", 'r' );
			}
		}
	}
	eZHTTPTool::header( "Location: /trade/categorylist/parent/$categoryID/" );	
	exit();
}

if ( isSet( $submitPrice ) )
{
    for ( $i = 0; $i < count( $productEditArrayID ); $i++ )
    {
        if ( $price[$i] != "" and is_numeric( $price[$i] ) )
        {
            $product = new eZProduct( $productEditArrayID[$i] );
            $product->setPrice( $price[$i] );
            $product->store();
            deleteCache( $product, false, false, false );
        }
    }
    if ( isset( $query ) )
        eZHTTPTool::header( "Location: /trade/search/$offset/$query" );
    else
        eZHTTPTool::header( "Location: /trade/categorylist/parent/$categoryID/$offset" );

    exit();
}


if ( isSet( $updateProducts ) )
{	
/*	echo "<pre>";
	print_r( $discontinued );
	print_r($showPrice);
	echo "</pre>";
	exit ();  */

    for ( $i = 0; $i < count( $productEditArrayID ); $i++ )
    {
        $product = new eZProduct( $productEditArrayID[$i] );
        
		if ( $name[$i] != $product->name() )
			$product->setName($name[$i]);
			
		if ( $price[$i] != "" and is_numeric( $price[$i] ) )
            $product->setPrice( $price[$i] );
			
		if ( $listPrice[$i] != "" and is_numeric( $listPrice[$i] ) )
            $product->setListPrice( $listPrice[$i] );
		
//		if ( $brief[$i] != $product->brief() || $description[$i] != $product->description() )
//		{
			$contents = array( $brief[$i], $description[$i] );
	    	$generator = new eZArticleGenerator();
    		$contents = $generator->generateXML( $contents );
//		}
		
	    if ( $contents && eZXML::domTree( $contents ) )
		{
			$product->setContents( $contents );
			$was_hotdeal = $product->isHotDeal();
		
			if ( $keywords[$i] != "" )
				$product->setKeywords( $keywords[$i]  );
		
			if ( $productNumber[$i] != $product->productNumber() )
        		$product->setProductNumber( $productNumber[$i] );
		
			if ( $externalLink[$i] != $product->externalLink() )
        		$product->setExternalLink( $externalLink[$i] );
		
			$vatType = $product->vatType();
		
			if ( $vatTypeID[$i] and $vatTypeID[$i] != $vatType->id() )
			{
        		$vattype = new eZVATType( $vatTypeID[$i] );
        		$product->setVATType( $vattype );
			}
			
			/* Flat and Free Shipping section */
			if ( $flatFeeUPS[$i] ) {
			  if (  is_numeric( $flatFeeUPS[$i] ) )
			   $product->setFlatUPS( $flatFeeUPS[$i] );
			  else
			   $product->setFlatUPS( 'off' );
			} else {
				$product->setFlatUPS('off' );	
			}
			if ( $flatFeeUSPS[$i] ) {
			  if ( is_numeric( $flatFeeUSPS[$i] ) )
			   $product->setFlatUSPS( $flatFeeUSPS[$i] );
			  else
			   $product->setFlatUSPS( 'off' );
			} else {
				$product->setFlatUSPS('off' );	
			}

			$product->setFlatCombine( $flatCombine[$i] == "on" );
//			$boxType = $product->boxType();
		
//			if ( $boxTypeID[$i] > 0 && $boxTypeID[$i] != $boxType->id() )
			if ( $boxTypeID[$i] > 0 )
			{
        		$boxtype = new eZBoxType( $boxTypeID[$i] );
        		$product->setBoxType( $boxtype );
			}
			elseif ( $boxTypeID[$i] == 0 )
        		$product->setBoxType( "" );
		
			$shippingGroup = $product->shippingGroup();
		
			if ( $shippingGroupID[$i] and $shippingGroupID[$i] != $shippingGroup->id() )
			{
        		$shippingGroup = new eZShippingGroup( $shippingGroupID[$i] );
        		$product->setShippingGroup( $shippingGroup );
    		}
		
        	if ( $showPrice[$i] == "on" )
            	$product->setShowPrice( true );
        	else
            	$product->setShowPrice( false );

			$product->setShowProduct( $active[$i] == "on" );
			$product->setDiscontinued( $discontinued[$i] == "on" );
		
/*			if ( $discontinued[$i] == "on" )
	        	$product->setDiscontinued( true );
			else
				$product->setDiscontinued( false );
			
			if ( $active[$i] == "on" )
    	        $product->setShowProduct( true );
        	else
            	$product->setShowProduct( false );
*/		
        	if ( $isHotDeal[$i] == "on" )
            	$product->setIsHotDeal( true );
        	else
            	$product->setIsHotDeal( false );
   
			if ( $showQuantity && is_numeric($quantity[$i]) && $quantity[$i] != $product->totalQuantity() )
				$product->setTotalQuantity( is_numeric( $quantity[$i] ) ? $quantity[$i] : false );
				
			if ( isset($quantity[$i]) && $quantity[$i] == 0 ) {
            	$dateInStock = new eZDate( $stockYear[$i], $stockMonth[$i], $stockDay[$i] );
				$date = $dateInStock->timeStamp();
				$product->setStockDate( $date );
			}	
			
			if ( is_numeric($quantity[$i]) && $quantity[$i] > 0 )
				$product->setStockDate( "" );
			
			if ( $weight[$i]>0 && is_numeric($weight[$i]) && $weight[$i] != $product->weight() )
            	$product->setWeight( $weight[$i] );
		
			if ( $includesVAT[$i] == "true" )
            	$product->setIncludesVAT( true );
        	else
            	$product->setIncludesVAT( false );
			$product->store();

			//update categories
		
    	    $old_maincategory = $product->categoryDefinition();
        	$old_categories = array_merge( $old_maincategory->id(), $product->categories( false ) );
        	$old_categories = array_unique( $old_categories );

	        $new_categories = array_unique( array_merge( $mainCategoryID[$i], $categoryArray[$i] ) );
       
    	    $remove_categories = array_diff( $old_categories, $new_categories );
        	$add_categories = array_diff( $new_categories, $old_categories );
		
	        foreach ( $remove_categories as $categoryItem )
    	    {
        	  eZProductCategory::removeProduct( $product, $categoryItem );
         	}
		 	// add a product to the categories
			$category = new eZProductCategory( $mainCategoryID[$i] );
        	$product->setCategoryDefinition( $category );

	        foreach ( $add_categories as $categoryItem )
    	    {
        	    eZProductCategory::addProduct( $product, $categoryItem );
        	}

		
	        // clear the cache files.
    	    deleteCache( $productID, $categoryID, $old_categories, $was_hotdeal or $product->isHotDeal() );

	    }
	}
// for array debugging use
//	echo "<pre>";
//	            print_r($categoryArray);
//				print_r($mainCategoryID);
//				echo "<br>"."specified element:"."<br>";
//				print_r($categoryArray[1]);
//	echo "</pre>";

	if ( isSet( $query ) )
        eZHTTPTool::header( "Location: /trade/search/$offset/$query" );
    else
        eZHTTPTool::header( "Location: /trade/categorylist/parent/$categoryID/$offset" );
	
    exit();


}


if ( isset( $deleteProducts ) )
{
    $action = "DeleteProducts";
}

if ( isset( $action ) && $action == "Update"  or isset( $action ) && $action == "Insert" )
{
    $parentCategory = new eZProductCategory();
    $parentCategory->get( $categoryID );

    if ( isset( $action ) && $action == "Insert" )
    {
        $product = new eZProduct();
    }
    else
    {
        $product = new eZProduct();
        $product->get( $productID );
        $was_hotdeal = $product->isHotDeal();

    }

    $product->setName( $name );

    $generator = new eZArticleGenerator();
    $contents = $generator->generateXML( $contents );

    if ( eZXML::domTree( $contents ) )
    {
        $product->setContents( $contents );

        $product->setKeywords( $keywords  );
        $product->setProductNumber( $productNumber );
        $product->setCatalogNumber( $catalogNumber );
        $product->setExternalLink( $externalLink );

        $vattype = new eZVATType( $vatTypeID );
        $product->setVATType( $vattype );

		$boxtype = new eZBoxType( $boxTypeID );
        $product->setBoxType( $boxtype );

        $shippingGroup = new eZShippingGroup( $shippingGroupID );
        $product->setShippingGroup( $shippingGroup );

        if ( $showPrice == "on" )
        {
            $product->setShowPrice( true );
        }
        else
        {
            $product->setShowPrice( false );
        }

        if ( $useVoucher == true )
        {
            $product->setProductType( 2 );
        }
        else
        {
            $product->setProductType( 1 );
        }

        $product->setShowProduct( $active == "on" );
        $product->setDiscontinued( $discontinued == "on" );

        if ( $isHotDeal == "on" )
        {
            $product->setIsHotDeal( true );
        }
        else
        {
            $product->setIsHotDeal( false );
        }

        $product->setPrice( $price );
		$product->setListPrice( $listPrice );

		if ( $weight>0 && is_numeric($weight) )
	        $product->setWeight( $weight );

        if ( $includesVAT == "true" )
        {
            $product->setIncludesVAT( true );
        }
        else
        {
            $product->setIncludesVAT( false );
        }
			/* Flat and Free Shipping section */
		if ( $flatFeeUPS ) {
			if ( is_numeric( $flatFeeUPS ) )
			$product->setFlatUPS( $flatFeeUPS );
			else {
			 $product->setFlatUPS( 'off' );
			}
			} else {
				$product->setFlatUPS('off' );	
			}
			if ( $flatFeeUSPS ) {
			  if ( is_numeric( $flatFeeUSPS ) )
			   $product->setFlatUSPS( $flatFeeUSPS );
			  else
			   $product->setFlatUSPS( 'off' );
			} else {
				$product->setFlatUSPS('off' );	
			}
			$product->setFlatCombine( $flatCombine == "on" );

        if ( $expiry > 0 )
            $product->setExpiryTime( $expiry );
		
		if ( is_numeric($quantity) && $quantity == 0 ) {
                $dateInStock = new eZDate( $stockYear, $stockMonth, $stockDay );
				$date = $dateInStock->timeStamp();

				$product->setStockDate( $date );
				}	
		
		if ( is_numeric($quantity) && $quantity > 0 )
			$product->setStockDate( "" );

        $product->store();
        // print_r($product); exit();
        $productID = $product->id();
        $productID = $product->id();

        if ( $product->productType() == 2 )
        {
            $range = $product->priceRange();
            if ( !$range )
                $range = new eZProductPriceRange();
            $range->setMin( $minPrice );
            $range->setMax( $maxPrice );
            $range->setProduct( $product );
            $range->store();
        }

        if ( $showQuantity )
        {
            $product->setTotalQuantity( is_numeric( $quantity ) ? $quantity : false );
        }

        if ( $productID )
            eZPriceGroup::removePrices( $productID, -1 );

        if( isset( $priceGroup ) && isset( $priceGroupID ) )
        {
            $count = max( count( $priceGroup ), count( $priceGroupID ) );
        }
        else
        {
            $count = false;
        }
        for ( $i = 0; $i < $count; $i++ )
        {
            if ( is_numeric( $priceGroupID[$i] ) and $priceGroup[$i] != "" )
            {
                eZPriceGroup::addPrice( $productID, $priceGroupID[$i], $priceGroup[$i] );
            }
        }


        eZObjectPermission::removePermissions( $productID, "trade_product", 'w' );
        if( isset( $writeGroupArray ) )
        {
            if( $writeGroupArray[0] == 0 )
            {
                eZObjectPermission::setPermission( -1, $productID, "trade_product", 'w' );
            }
            else
            {
                foreach ( $writeGroupArray as $groupID )
                {
                    eZObjectPermission::setPermission( $groupID, $productID, "trade_product", 'w' );
                }
            }
        }
        else
        {
            eZObjectPermission::removePermissions( $productID, "trade_product", 'w' );
        }

        /* read access thingy */
        eZObjectPermission::removePermissions( $productID, "trade_product", 'r' );
        if ( isset( $readGroupArray ) )
        {
            if( $readGroupArray[0] == 0 )
            {
                eZObjectPermission::setPermission( -1, $productID, "trade_product", 'r' );
            }
            else // some groups are selected.
            {
                foreach ( $readGroupArray as $groupID )
                {
                    eZObjectPermission::setPermission( $groupID, $productID, "trade_product", 'r' );
                }
            }
        }
        else
        {
            eZObjectPermission::removePermissions( $productID, "trade_product", 'r' );
        }

        // Calculate which categories are new and which are unused

        if ( isset( $action ) && $action == "Update" )
        {
            $old_maincategory = $product->categoryDefinition();

            $old_categories = array_merge( array( $old_maincategory->id() ), $product->categories( false ) );
            $old_categories = array_unique( $old_categories );

            if( isset( $categoryArray ) )
            {
                $new_categories = array_unique( array_merge( array( $categoryID ), $categoryArray ) );
            }
            else
            {
                $new_categories = array( $categoryID );
            }
            $remove_categories = array_diff( $old_categories, $new_categories );
            $add_categories = array_diff( $new_categories, $old_categories );

            foreach ( $remove_categories as $categoryItem )
            {
                eZProductCategory::removeProduct( $product, $categoryItem );
            }
        }
        else
        {
            if( isset( $categoryArray ) )
            {
                $add_categories = array_unique( array_merge( array( $categoryID ), $categoryArray ) );
            }
            else
            {
                $add_categories = array( $categoryID );
            }
        }

        // add a product to the categories
        $category = new eZProductCategory( $categoryID );
        $categorySetResult = $product->setCategoryDefinition( $category );

        if( empty( $add_categories ) )
        {
             $add_categories = array( $old_maincategory->id() );
        }

        foreach ( $add_categories as $categoryItem )
        {
            eZProductCategory::addProduct( $product, $categoryItem );
        }

        // clear the cache files.
        deleteCache( $productID, $categoryID, $old_categories, $was_hotdeal or $product->isHotDeal() );

        // preview
        if ( isset( $preview ) )
        {
            eZHTTPTool::header( "Location: /trade/productedit/productpreview/$productID/" );
            exit();
        }

        if( isset( $addItem ) )
        {
            switch ( $itemToAdd )
            {
                // add options
                case "Option":
                {
                    eZHTTPTool::header( "Location: /trade/productedit/optionlist/$productID/" );
                    exit();
                }
                break;
				
			    // add files
                case "File":
                    {
                        // add files
                        eZHTTPTool::header( "Location: /trade/productedit/filelist/$productID/" );
                    exit();
                }
                break;

                // add images
                case "Image":
                {
                    eZHTTPTool::header( "Location: /trade/productedit/imagelist/$productID/" );
                    exit();
                }
                break;

                // attribute
                case "Attribute":
                {
                    eZHTTPTool::header( "Location: /trade/productedit/attributeedit/$productID/" );
                    exit();
                }
                break;

                // attribute
                case "ModuleLinker":
                {
                    eZHTTPTool::header( "Location: /trade/productedit/link/list/$productID/" );
                    exit();
                }
                break;

                case "Form":
                {
                    // add form
                    eZHTTPTool::header( "Location: /trade/productedit/formlist/$productID/" );
                    exit();
                }
                break;
            }
        }

        // get the category to redirect to
        $category = $product->categoryDefinition();
        $categoryID = $category->id();
    
        eZHTTPTool::header( "Location: /trade/categorylist/parent/$categoryID" );
        exit();
    }
    else
    {
        $contentsOverride = $contents;
        if ( isset( $action ) && $action == "Update" )
        {
            $action = "Edit";
        }
        else
        {
            $action = "Insert";
        }
    }
}

if ( isset( $action ) && $action == "Cancel" )
{
    if ( is_numeric( $productID ) )
    {
        $product = new eZProduct( $productID );
        $category = $product->categoryDefinition();
        $categoryID = $category->id();
        eZHTTPTool::header( "Location: /trade/categorylist/parent/$categoryID" );
        exit();
    }
    else
    {
        eZHTTPTool::header( "Location: /trade/categorylist/parent/" );
        exit();
    }
}

if ( isset( $action ) && $action == "DeleteProducts" )
{
    if ( count ( $productArrayID ) != 0 )
    {
        foreach ( $productArrayID as $productID )
        {
            $product = new eZProduct();
            $product->get( $productID );

            $categories = $product->categories();

            $categoryArray = $product->categories();
            $categoryIDArray = array();
            foreach ( $categoryArray as $cat )
            {
                $categoryIDArray[] = $cat->id();
            }

            // clear the cache files.
            deleteCache( $productID, $categoryID, $categoryIDArray, $product->isHotDeal() );

            $category = $product->categoryDefinition( );
            $categoryID = $category->id();

            $product->delete();
            eZPriceGroup::removePrices( $productID, -1 );
        }
    }

    if ( isset( $query ) )
        eZHTTPTool::header( "Location: /trade/search/$offset/$query" );
    else
        eZHTTPTool::header( "Location: /trade/categorylist/parent/$categoryID/$offset" );
    exit();
}

if ( isset( $action ) && $action == "Delete" )
{
    $product = new eZProduct();
    $product->get( $productID );

    $categories = $product->categories();

    $categoryArray = $product->categories();
    $categoryIDArray = array();
    foreach ( $categoryArray as $cat )
    {
        $categoryIDArray[] = $cat->id();
    }

    // clear the cache files.
    deleteCache( $productID, $categoryID, $categoryIDArray, $product->isHotDeal() );

    $category = $product->categoryDefinition( );
    $categoryID = $category->id();

    $product->delete();

    eZPriceGroup::removePrices( $productID, -1 );

    eZHTTPTool::header( "Location: /trade/categorylist/parent/$categoryID/" );
    exit();
}

$t = new eZTemplate( "kernel/eztrade/admin/" . $ini->variable( "eZTradeMain", "AdminTemplateDir" ),
                     "kernel/eztrade/admin/intl/", $language, "productedit.php" );

$t->set_file( "product_edit_tpl", "productedit.tpl" );

$t->set_block( "product_edit_tpl", "value_tpl", "value" );
$t->set_block( "product_edit_tpl", "multiple_value_tpl", "multiple_value" );

$t->set_block( "product_edit_tpl", "module_linker_button_tpl", "module_linker_button" );
$t->set_block( "product_edit_tpl", "group_item_tpl", "group_item" );

$t->set_block( "product_edit_tpl", "vat_select_tpl", "vat_select" );
$t->set_block( "product_edit_tpl", "box_select_tpl", "box_select" );
$t->set_block( "product_edit_tpl", "shipping_select_tpl", "shipping_select" );
$t->set_block( "product_edit_tpl", "quantity_item_tpl", "quantity_item" );
$t->set_block( "quantity_item_tpl", "day_item_tpl", "day_item" );

$t->set_block( "product_edit_tpl", "read_group_item_tpl", "read_group_item" );
$t->set_block( "product_edit_tpl", "write_group_item_tpl", "write_group_item" );

$t->set_block( "product_edit_tpl", "price_range_tpl", "price_range" );
$t->set_block( "product_edit_tpl", "normal_price_tpl", "normal_price" );
$t->set_block( "product_edit_tpl", "list_price_tpl", "list_price" );

$t->set_block( "product_edit_tpl", "price_group_list_tpl", "price_group_list" );
$t->set_block( "price_group_list_tpl", "price_groups_item_tpl", "price_groups_item" );
$t->set_block( "price_groups_item_tpl", "price_group_header_item_tpl", "price_group_header_item" );
$t->set_block( "price_groups_item_tpl", "price_group_item_tpl", "price_group_item" );
$t->set_block( "price_group_list_tpl", "price_groups_no_item_tpl", "price_groups_no_item" );

$t->setAllStrings();

$t->set_var( "brief_value", "" );
$t->set_var( "description_value", "" );
$t->set_var( "name_value", "" );
$t->set_var( "keywords_value", "" );
$t->set_var( "product_nr_value", "" );
$t->set_var( "product_catalog_number", "" );
$t->set_var( "price_value", "" );
$t->set_var( "list_price", "" );
$t->set_var( "expiry_value", "" );

$t->set_var( "weight_value", "");
$t->set_var( "flat_fee_ups", "");
$t->set_var( "flat_fee_usps", "");

$t->set_var( "showprice_checked", "" );
$t->set_var( "showproduct_checked", "" );
$t->set_var( "discontinued_checked", "" );
$t->set_var( "is_hot_deal_checked", "" );

$t->set_var( "price_min", "0" );
$t->set_var( "price_max", "0" );

$t->set_var( "external_link", "" );

$t->set_var( "action_value", "insert" );
$t->set_var( "product_id", 0 );
$t->set_var( "flat_combine_checked", "");

$writeGroupsID = array();
$readGroupsID = array();

$priceGroup = array();
$priceGroupID = array();

$vatType = false;
$boxType = false;

// edit
if ( isset( $action ) && $action == "Edit" )
{
    $product = new eZProduct();
    $product->get( $productID );
	
    $t->set_var( "name_value", htmlspecialchars($product->name()) );
    $t->set_var( "keywords_value", $product->keywords() );
    $t->set_var( "product_nr_value", $product->productNumber() );
    $t->set_var( "product_catalog_number", $product->catalogNumber() );
    $t->set_var( "price_value", $product->price() );
    $t->set_var( "list_price", $product->listPrice() );
    $t->set_var( "expiry_value", $product->expiryTime() ? $product->expiryTime() : "" );
    $t->set_var( "external_link", $product->externalLink() );

    $generator = new eZArticleGenerator();
//    var_dump( $product->contents() ); die('funny');

    if( $product->contents() !== "" )
    {
        $productContents = $product->contents();
        $contentsArray = $generator->decodeXML( $productContents );             
    }
    else
    {
        $contentsArray = array( "", "" );
    }

    if ( isset( $contentsOverride ) && count( $contentsOverride ) == 2 )
    {
        $t->set_var( "brief_value", $contentsOverride[0] );
        $t->set_var( "description_value", $contentsOverride[1] );
    }
    else
    {
        $t->set_var( "brief_value", $contentsArray[0] );
        $t->set_var( "description_value", $contentsArray[1] );
    }


    $t->set_var( "action_value", "update" );
    $t->set_var( "product_id", $product->id() );

    if ( $product->showPrice() == true )
        $t->set_var( "showprice_checked", "checked" );

    if ( $product->showProduct() == true )
        $t->set_var( "showproduct_checked", "checked" );

    if ( $product->discontinued() == true )
        $t->set_var( "discontinued_checked", "checked" );

    if ( $product->isHotDeal() == true )
        $t->set_var( "is_hot_deal_checked", "checked" );
		
    if ( $product->weight() > 0 )
        $t->set_var( "weight_value", $product->weight() );
	else
        $t->set_var( "weight_value", "" );	

    if ( $product->productType() == 2 )
        $t->set_var( "mark_as_voucher", "checked" );

    if ( $product->includesVAT() == true )
        $t->set_var( "include_vat", "checked" );

    if ( $product->excludedVAT() == true )
        $t->set_var( "exclude_vat", "checked" );
	
	if ($product->FlatUPS() == 'off')
	 $t->set_var( "flat_fee_ups", '' );
	else
	 $t->set_var( "flat_fee_ups", $product->FlatUPS ); 
	 
	if ($product->FlatUSPS() == 'off')
	 $t->set_var( "flat_fee_usps", '' );
	else
	 $t->set_var( "flat_fee_usps", $product->FlatUSPS ); 
	 
	if ($product->FlatCombine())
		$t->set_var( "flat_combine_checked", 'checked' );	

    $vatType = $product->vatType();
	
    $boxType = $product->BoxType();

    $quantity = $product->totalQuantity();

    if ( $product->stockDate() )
    {
        $stock = new eZDate();
        $stock->setTimeStamp( $product->stockDate() );	
        $stockYear = $stock->year();
        $stockMonth = $stock->month();
        $stockDay = $stock->day();
    }
    else
    {
        $stockYear = "";
        $stockMonth = 1;
        $stockDay = 1;
    }
	
     for ( $i = 1; $i <= 31; $i++ )
    {
        $t->set_var( "day_id", $i );
        $t->set_var( "day_value", $i );
        $t->set_var( "selected", "" );
        // if ( ( $stockDay == "" and $i == 1 ) or $stockDay == $i )
        if ( $stockDay == $i )
                $t->set_var( "selected", "selected" );
        if ( $stockDay == "" and $i == date(j) )
                $t->set_var( "selected", "selected" );
            $t->parse( "day_item", "day_item_tpl", true );
    }

    $month_array = array( 1 => "select_january",
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

    foreach ( $month_array as $month )
    {
        $t->set_var( $month, "" );
    }

    $var_name =& $month_array[$stockMonth];
    if ( $var_name == "" ) {
//				$dateMonth = date(n);
        $var_name =& $month_array[date(n)];
        }
        
    $t->set_var( $var_name, "selected" );
    
    if ( $stockYear )
        $t->set_var( "stockyear", $stockYear );
    else
        $t->set_var( "stockyear", date("Y") );
    

    $prices = eZPriceGroup::prices( $productID );

    $priceGroup = array();
    $priceGroupID = array();

    foreach ( $prices as $price )
    {
        $priceGroup[] = $price["Price"];
        $priceGroupID[] = $price["PriceID"];
    }

    if ( isset( $useVoucher ) && $useVoucher )
    {
        $priceRange = $product->priceRange();
    }

    $writeGroupsID = eZObjectPermission::getGroups( $productID, "trade_product", 'w' , false );
    $readGroupsID = eZObjectPermission::getGroups( $productID, "trade_product", 'r', false );

//    $vatType = $product->vatType();    
    $shippingGroup = $product->shippingGroup();
}

if ( isset( $useVoucher ) && $useVoucher )
{
    if ( isset( $priceRange ) && $priceRange )
    {
        $t->set_var( "price_max", $priceRange->max() );
        $t->set_var( "price_min", $priceRange->min() );
    }
    else
    {
        $t->set_var( "price_max", "0" );
        $t->set_var( "price_min", "0" );
    }

    $t->set_var( "url_action", "voucher" );
    $t->set_var( "normal_price", "" );
    $t->set_var( "list_price", "" );
    $t->parse( "price_range", "price_range_tpl" );
}
else
{
    $t->set_var( "url_action", "productedit" );
    $t->set_var( "price_range", "" );
    $t->parse( "normal_price", "normal_price_tpl" );
	$t->parse( "list_price", "list_price_tpl" );
}

$category = new eZProductCategory();
$categoryArray = $category->getTree();

foreach ( $categoryArray as $catItem )
{
    if ( isset( $action ) && $action == "Edit" )
    {
        $defCat = $product->categoryDefinition();
        if ( $product->existsInCategory( $catItem[0] ) &&
             ( $defCat->id() != $catItem[0]->id() ) )
        {
            $t->set_var( "multiple_selected", "selected" );
        }
        else
        {
            $t->set_var( "multiple_selected", "" );
        }

        if ( $defCat->id() == $catItem[0]->id() )
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
        $t->set_var( "multiple_selected", "" );
    }

//      if ( isset( $action ) && $action == "Edit" )
//      {
//          if ( $product->existsInCategory( $catItem ) )
//              $t->set_var( "selected", "selected" );
//          else
//              $t->set_var( "selected", "" );
//      }
//      else
//      {
//              $t->set_var( "selected", "" );
//      }

    $t->set_var( "option_value", $catItem[0]->id() );
    $t->set_var( "option_name", $catItem[0]->name() );

    if ( $catItem[1] > 0 )
        $t->set_var( "option_level", str_repeat( "&nbsp;", $catItem[1] ) );
    else
        $t->set_var( "option_level", "" );

    $t->parse( "value", "value_tpl", true );
    $t->parse( "multiple_value", "multiple_value_tpl", true );
}

// show the VAT values

$vat = new eZVATType();
$vatTypes = $vat->getAll();

foreach ( $vatTypes as $type )
{
    if ( $vatType  and  ( $vatType->id() == $type->id() ) )
    {
        $t->set_var( "vat_selected", "selected" );
    }
    else
    {
        $t->set_var( "vat_selected", "" );
    }

    $t->set_var( "vat_id", $type->id() );
    $t->set_var( "vat_name", $type->name() . " (" . $type->value() . ")%" );

    $t->parse( "vat_select", "vat_select_tpl", true );
}

// show the box values

$box = new eZBoxType();
$boxTypes = $box->getAll();

foreach ( $boxTypes as $type )
{
    if ( $boxType  and  ( $boxType->id() == $type->id() ) )
    {
        $t->set_var( "box_selected", "selected" );
    }
    else
    {
        $t->set_var( "box_selected", "" );
    }
        
    $t->set_var( "box_id", $type->id() );
    $t->set_var( "box_name", $type->name()." (".$type->length()."x".$type->width()."x".$type->height()." in)" );

    $t->parse( "box_select", "box_select_tpl", true );
}

// show shipping groups

$group = new eZShippingGroup();

$groups = $group->getAll();

foreach ( $groups as $group )
{
    if ( isset( $shippingGroup ) && $shippingGroup and $shippingGroup->id() == $group->id() )
    {
        $t->set_var( "selected", "selected" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }

    $t->set_var( "shipping_group_id", $group->id() );

    $t->set_var( "shipping_group_name", $group->name() );

    $t->parse( "shipping_select", "shipping_select_tpl", true );
}

// Show quantity
$t->set_var( "quantity_item", "" );
$t->set_var( "quantity_value", isset( $quantity ) ? $quantity : false );
if ( isset( $showQuantity ) && $showQuantity )
{
    $t->parse( "quantity_item", "quantity_item_tpl" );
}

// Show price groups

$t->set_var( "price_group_list", "" );
$t->set_var( "price_groups_item", "" );
$t->set_var( "price_groups_no_item", "" );

if ( $showPriceGroups )
{
    $price_groups = eZPriceGroup::getAll();
    $count = max( count( $priceGroup ), count( $priceGroupID ) );

    $newPriceGroup = array();
    for ( $i = 0; $i < $count; $i++ )
    {
        $newPriceGroup[$priceGroupID[$i]] = $priceGroup[$i];
    }

    $prices = array();
    $price_ids = array();
    $price_names = array();
    foreach ( $price_groups as $price_group )
    {
        $price_id = $price_group->id();

	if ( isset( $newPriceGroup[$price_id] ) )
            $prices[] = $newPriceGroup[$price_id];

        $price_ids[] = $price_id;
        $price_names[] = $price_group->name();
    }
    $priceGroup = $prices;
    $priceGroupID = $price_ids;
    $t->set_var( "price_group_header_item", "" );
    $t->set_var( "price_group_item", "" );

    for ( $i = 0; $i < count( $priceGroup ); $i++ )
    {
        $t->set_var( "price_group_name", $price_names[$i] );
        $t->parse( "price_group_header_item", "price_group_header_item_tpl", true );
        $t->set_var( "price_group_value", $priceGroup[$i] );
        $t->set_var( "price_group_id", $priceGroupID[$i] );
        $t->parse( "price_group_item", "price_group_item_tpl", true );
    }

    if ( count( $price_groups ) > 0 )
    {
        $t->parse( "price_groups_item", "price_groups_item_tpl" );
        $t->parse( "price_group_list", "price_group_list_tpl" );
    }
//    else
//        $t->parse( "price_groups_no_item", "price_groups_no_item_tpl" );
}

    if ( isset( $shippingGroup ) && $shippingGroup and ( $shippingGroup->id() == $group->id() ) )
    {
        $t->set_var( "selected", "selected" );
    }
    else
    {
        $t->set_var( "selected", "" );
    }

    $t->set_var( "shipping_group_id", $group->id() );

    $t->set_var( "shipping_group_name", $group->name() );

$t->set_var( "module_linker_button", "" );
if ( $showModuleLinker )
    $t->parse( "module_linker_button", "module_linker_button_tpl" );

// group selector
$group = new eZUserGroup();
$groupList = $group->getAll();

$t->set_var( "selected", "" );
foreach ( $groupList as $groupItem )
{
    // for the group owner selector
    $t->set_var( "read_id", $groupItem->id() );
    $t->set_var( "read_name", $groupItem->name() );
    
    if ( in_array( $groupItem->id(), $readGroupsID ) )
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );

	if ( in_array( "-1", $readGroupsID ) )
	    $t->set_var( "all_selected", "selected" );
	else
		$t->set_var( "all_selected", "" );
		
    $t->parse( "read_group_item", "read_group_item_tpl", true );
    
    // for the read access groups selector
        $t->set_var( "write_name", $groupItem->name() );
        $t->set_var( "write_id", $groupItem->id() );
        if ( in_array( $groupItem->id(), $writeGroupsID ) )
            $t->set_var( "is_selected", "selected" );
        else
            $t->set_var( "is_selected", "" );

	if ( in_array( "-1", $writeGroupsID ) )
	    $t->set_var( "all_write_selected", "selected" );
    else
		$t->set_var( "all_write_selected", "" );
		
    $t->parse( "write_group_item", "write_group_item_tpl", true );
}

$t->pparse( "output", "product_edit_tpl" );

?>