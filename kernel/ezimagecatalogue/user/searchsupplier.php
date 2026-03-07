<?php
//
// Created on: <08-Nov-2002 14:31:10 jhe>
//
// Copyright (C) 1999-2002 eZ systems as. All rights reserved.
//
// This source file is part of the Exponential Basic (tm) Open Source Content
// Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 as published by the Free
// Software Foundation and appearing in the file LICENSE.GPL included in
// the packaging of this file.
//
// Licencees holding valid "Exponential Basic professional licences" may use this
// file in accordance with the "Exponential Basic professional licence" Agreement
// provided with the Software.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "Exponential Basic professional licence" is available at
// http://ez.no/home/licences/professional/. For pricing of this licence
// please contact us via e-mail to licence@ez.no. Further contact
// information is available at http://ez.no/home/contact/.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html.
//
// Contact licence@ez.no if any conditions of this licencing isn't clear to
// you.
//

// include_once( "ezimagecatalogue/classes/ezimage.php" );

$moduleName = "eZ imagecatalogue";
$searchResult[0]["DetailedSearchPath"] = "/imagecatalogue/search/";
$searchResult[0]["DetailedSearchVariable"] = "SearchText";
$searchResult[0]["DetailViewPath"] = "/imagecatalogue/imageview/";
$searchResult[0]["IconPath"] = "/design/base/images/icons/document.gif";

$image = new eZImage();
$searchResult[0]["Result"] = $image->search( $searchText, 0, $limit );
$searchResult[0]["SearchCount"] = $image->searchCount( $searchText );

?>