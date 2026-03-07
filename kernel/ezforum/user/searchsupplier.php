<?php
//
// $Id: searchsupplier.php 7750 2001-10-10 11:38:41Z bf $
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


$moduleName = "eZ forum";
$searchResult[0]["DetailedSearchPath"] = "/forum/search/";
$searchResult[0]["DetailedSearchVariable"] = "QueryString";
$searchResult[0]["DetailViewPath"] = "/forum/message/";
$searchResult[0]["IconPath"] = "/design/base/images/icons/message.gif";

// include_once( "ezforum/classes/ezforum.php" );

$forum = new eZForum();

$searchResult[0]["Result"] = $forum->search( $searchText, 0, $limit, $searchResult["SearchCount"] );
$searchResult[0]["SearchCount"] = count( $searchResult[0]["Result"] );
// $searchResult["SearchCount"] = $forum->getQueryCount( $searchText );


?>