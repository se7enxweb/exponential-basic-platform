<?php
//
// $Id: datasupplier.php 6974 2001-09-05 12:51:13Z jhe $
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

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZCalendarMain", "DefaultSection" );

$action              = eZHTTPTool::getVar( 'Action' );
$allDay              = eZHTTPTool::getVar( 'AllDay' );
$appointmentArrayID  = $_POST['AppointmentArrayID'] ?? [];
$appointmentID       = eZHTTPTool::getVar( 'AppointmentID' );
$cancel              = eZHTTPTool::getVar( 'Cancel' );
$changeView          = eZHTTPTool::getVar( 'ChangeView' );
$dateError           = eZHTTPTool::getVar( 'DateError' );
$deleteAppointments  = eZHTTPTool::getVar( 'DeleteAppointments' );
$description         = eZHTTPTool::getVar( 'Description' );
$getByUser           = eZHTTPTool::getVar( 'GetByUser' );
$getByUserID         = eZHTTPTool::getVar( 'GetByUserID' );
$goDay               = eZHTTPTool::getVar( 'GoDay' );
$goMonth             = eZHTTPTool::getVar( 'GoMonth' );
$goToday             = eZHTTPTool::getVar( 'GoToday' );
$goYear              = eZHTTPTool::getVar( 'GoYear' );
$intervalStr         = eZHTTPTool::getVar( 'IntervalStr' );
$isPrivate           = eZHTTPTool::getVar( 'IsPrivate' );
$locale              = eZHTTPTool::getVar( 'Locale' );
$name                = eZHTTPTool::getVar( 'Name' );
$priority            = eZHTTPTool::getVar( 'Priority' );
$start               = eZHTTPTool::getVar( 'Start' );
$startTime           = eZHTTPTool::getVar( 'StartTime' );
$startTimeError      = eZHTTPTool::getVar( 'StartTimeError' );
$startTimeStr        = eZHTTPTool::getVar( 'StartTimeStr' );
$stop                = eZHTTPTool::getVar( 'Stop' );
$stopTimeError       = eZHTTPTool::getVar( 'StopTimeError' );
$stopTimeStr         = eZHTTPTool::getVar( 'StopTimeStr' );
$storeSiteCache      = eZHTTPTool::getVar( 'StoreSiteCache' );
$titleError          = eZHTTPTool::getVar( 'TitleError' );
$trusteeUser         = eZHTTPTool::getVar( 'TrusteeUser' );
$trusteesList        = $_POST['TrusteesList'] ?? [];
$userError           = eZHTTPTool::getVar( 'UserError' );
$viewType            = eZHTTPTool::getVar( 'ViewType' );

switch ( $url_array[2] )
{
    case "yearview" :
    {
        $year = $url_array[3];

        include( "kernel/ezcalendar/user/yearview.php" );
    }
    break;

    case "monthview" :
    {
        $year = $url_array[3];
        $month = $url_array[4];

        include( "kernel/ezcalendar/user/monthview.php" );
    }
    break;

    case "dayview" :
    {
        $year = $url_array[3];
        $month = $url_array[4];
        $day = $url_array[5];

        include( "kernel/ezcalendar/user/dayview.php" );
    }
    break;
    
    case "appointmentedit" :
    {
        switch ( $url_array[3] )
        {
            case "new" :
            {
                $action = "New";
                $year = $url_array[4];
                $month = $url_array[5];
                $day = $url_array[6];
                $startTime = $url_array[7];
            }
            break;

            case "edit" :
            {
                $action = "Edit";
                $appointmentID = $url_array[4];
            }
            break;

            case "update" :
            {
                $action = "Update";
                $appointmentID = $url_array[4];
            }
            break;

            case "insert" :
            {
                $action = "Insert";
                $appointmentID = 0;
            }
            break;

            default :
            {
                $action = $url_array[3];
            }
        }
        if ( isset( $changeView ) )
            $action = "New";
        include( "kernel/ezcalendar/user/appointmentedit.php" );
    }
    break;

    case "appointmentview" :
    {
        $appointmentID = $url_array[3];
        include( "kernel/ezcalendar/user/appointmentview.php" );
    }
    break;

    case "trustees":
    {
        switch ( $url_array[3] )
        {
            case "edit":
            {
                $action = "edit";
                include( "kernel/ezcalendar/user/trustees.php" );
                break;
            }
            default:
            {
                include( "kernel/ezcalendar/user/trustees.php" );
            }
            break;
        }
    }
    break;
}

?>