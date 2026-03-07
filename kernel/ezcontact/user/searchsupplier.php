<?php
//
// $Id: searchsupplier.php 7713 2001-10-09 08:58:34Z jhe $
//
// Created on: <09-Oct-2001 11:46:44 jhe>
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

$moduleName = "eZ contact";
$searchResult[0]["DetailedSearchPath"] = "/contact/search/company/";
$searchResult[0]["DetailedSearchVariable"] = "SearchText";
$searchResult[0]["DetailViewPath"] = "/contact/company/view/";
$searchResult[0]["IconPath"] = "/design/base/images/icons/document.gif";

// include_once( "ezcontact/classes/ezcompany.php" );

$searchResult[0]["Result"] = eZCompany::search( $searchText );
$searchResult[0]["SearchCount"] = count( $searchResult[0]["Result"] );
$searchResult[0]["SubModuleName"] = "Company";

$searchResult[1]["DetailedSearchPath"] = "/contact/search/person/";
$searchResult[1]["DetailedSearchVariable"] = "SearchText";
$searchResult[1]["DetailViewPath"] = "/contact/person/view/";
$searchResult[1]["IconPath"] = "/design/base/images/icons/document.gif";

// include_once( "ezcontact/classes/ezperson.php" );

$searchResult[1]["Result"] = eZPerson::search( $searchText );
$searchResult[1]["SearchCount"] = count( $searchResult[1]["Result"] );
$searchResult[1]["SubModuleName"] = "Person";

?>