<?php
//
// $Id: appointmentedit.php 7511 2001-09-27 11:07:25Z jhe $
//
// Created on: <03-Jan-2001 12:47:22 bf>
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
// include_once( "classes/ezhttptool.php" );

if ( isset( $deleteAppointments ) )
{
    $action = "DeleteAppointment";
}

if ( isset( $goDay ) )
{
    // include_once( "classes/ezdate.php" );

    $session = eZSession::globalSession();
    $session->fetch();

    $year = $session->variable( "Year" );
    $month = $session->variable( "Month" );
    $day = $session->variable( "Day" );

    $date = new eZDate( $year, $month, $day );
    if ( $date->daysInMonth() < $day )
        $day = $date->daysInMonth();

    eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day" );
    exit();
}
else if ( isset( $goMonth ) )
{
    $session = eZSession::globalSession();
    $session->fetch();

    $year = $session->variable( "Year" );
    $month = $session->variable( "Month" );

    eZHTTPTool::header( "Location: /calendar/monthview/$year/$month" );
    exit();
}
else if ( isset( $goYear ) )
{
    $session = eZSession::globalSession();
    $session->fetch();

    $year = $session->variable( "Year" );

    eZHTTPTool::header( "Location: /calendar/yearview/$year" );
    exit();
}
else if ( isset( $goToday ) )
{
    $today = new eZDate();

    $year = addZero( $today->year() );
    $month = addZero( $today->month() );
    $day = addZero( $today->day() );

    eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day" );
    exit();
}

// include_once( "classes/INIFile.php" );
// include_once( "classes/eztemplate.php" );
// include_once( "classes/ezlog.php" );
// include_once( "classes/ezlocale.php" );
// include_once( "classes/ezdatetime.php" );
// include_once( "classes/ezdate.php" );
// include_once( "classes/eztime.php" );

// include_once( "ezcalendar/classes/ezappointment.php" );
// include_once( "ezcalendar/classes/ezappointmenttype.php" );

$ini = eZINI::instance( 'site.ini' );

$Language = $ini->variable( "eZCalendarMain", "Language" );
$startTimeStr = $ini->variable( "eZCalendarMain", "DayStartTime" );
$stopTimeStr = $ini->variable( "eZCalendarMain", "DayStopTime" );

$locale = new eZLocale( $Language );

$titleError = false;
$startTimeError = false;
$stopTimeError = false;
$dateError = false;

if ( isset( $trusteeUser ) )
{
    $trusteelist = array();
    foreach ( $trusteeUser as $trusted )
    {
        $trusteelist[] = new eZUser( $trusted );
    }
}

$user = eZUser::currentUser();
if ( $user == false )
    $userID = false;
else
    $userID = $user->id();

if ( $userID == false )
    $app = false;
elseif ( $action == "New" || $action == "Insert" )
    $app = new eZAppointment();
else
    $app = new eZAppointment( $appointmentID );

if ( isset( $trusteeUser ) && count( $trusteeUser ) > 0 )
    $session->setVariable( "ShowOtherCalendarUsers", $trusteeUser[0] );

$t = new eZTemplate( "kernel/ezcalendar/user/" . $ini->variable( "eZCalendarMain", "TemplateDir" ),
                     "kernel/ezcalendar/user/intl/", $Language, "appointmentedit.php" );

$t->set_file( "appointment_edit_tpl", "appointmentedit.tpl" );

$t->setAllStrings();

$t->set_block( "appointment_edit_tpl", "user_error_tpl", "user_error" );
$t->set_block( "user_error_tpl", "no_user_error_tpl", "no_user_error" );
$t->set_block( "user_error_tpl", "wrong_user_error_tpl", "wrong_user_error" );

$t->set_block( "appointment_edit_tpl", "multiple_view_tpl", "multiple_view" );
$t->set_block( "appointment_edit_tpl", "no_error_tpl", "no_error" );
$t->set_block( "no_error_tpl", "title_error_tpl", "title_error" );
$t->set_block( "no_error_tpl", "start_time_error_tpl", "start_time_error" );
$t->set_block( "no_error_tpl", "stop_time_error_tpl", "stop_time_error" );
$t->set_block( "no_error_tpl", "date_error_tpl", "date_error" );
$t->set_block( "no_error_tpl", "trustee_user_name_tpl", "trustee_user_name" );
$t->set_block( "no_error_tpl", "value_tpl", "value" );
$t->set_block( "no_error_tpl", "month_tpl", "month" );
$t->set_block( "no_error_tpl", "day_tpl", "day" );

if ( isset( $changeView ) || isset( $viewType ) && $viewType == "multiple" )
{
    //$typeID = $TypeID;
    $t->set_var( "select_multiple", "multiple" );
    $t->set_var( "multiple_view", "" );
    $t->set_var( "view_type", "multiple" );
}
else
{
    //$typeID = $TypeID;
    $t->set_var( "select_multiple", "" );
    if ( is_a( $user, "eZUser" ) && count( $user->trustees() ) > 0 )
        $t->parse( "multiple_view", "multiple_view_tpl" );
    else
        $t->set_var( "multiple_view", "" );
    $t->set_var( "view_type", "single" );
}

// no user logged on
if ( !is_a( $app, "eZAppointment" ) )
{
    $t->set_var( "no_error", "" );
    $t->set_var( "wrong_user_error", "" );
    $t->parse( "no_user_error", "no_user_error_tpl" );
    $t->parse( "user_error", "user_error_tpl" );
    $t->pparse( "output", "appointment_edit_tpl" );
    $userError = true;
}

if ( $action == "Edit" && $app != false )
{
    $trusteesAlt = new eZUser();
    $trusteesAlt = $trusteesAlt->trustees( $app->userID() );
}

// only the appointment owner or a trustee is allowed to edit or delete an appointment
if ( $action == "Edit" && $app != false &&
     ( !in_array( $userID, $trusteesAlt ) &&
         $app->userID() != $userID ) )
{
    $t->set_var( "no_error", "" );
    $t->set_var( "no_user_error", "" );

    $t->parse( "wrong_user_error", "wrong_user_error_tpl" );
    $t->parse( "user_error", "user_error_tpl" );
    $t->pparse( "output", "appointment_edit_tpl" );

    $userError = true;
}

if ( $action == "DeleteAppointment" )
{
    if ( count( $appointmentArrayID ) != 0 )
    {
        foreach ( $appointmentArrayID as $id )
        {
            $appointment = new eZAppointment( $id );
            $appUser = new eZUser( $appointment->userID() );
            if ( ( $appUser->ID() == $userID ) ||
                 in_array( $userID, $appUser->getByTrustee() ) )
            {
                $datetime = $appointment->dateTime();
                $appointment->delete();

                $year = addZero( $datetime->year() );
                $month = addZero( $datetime->month() );
                $day = addZero( $datetime->day() );
                deleteCache( "default", $Language, $year, $month, $day, $appointment->userID() );
            }
            else
            {
                $t->set_var( "no_error", "" );
                $t->set_var( "no_user_error", "" );

                $t->parse( "wrong_user_error", "wrong_user_error_tpl" );
                $t->parse( "user_error", "user_error_tpl" );
                $t->pparse( "output", "appointment_edit_tpl" );

                $userError = true;
            }
        }
    }

    eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day/" );
    exit();
}

// Allowed format for start and stop time:
// 14 14:30 14:0 1430
// the : can be replaced with any non number character
if ( $action == "Insert" || $action == "Update" )
{
    if ( isset( $cancel ) )
    {
        if ( is_numeric( $appointmentID ) )
        {
            $app = new eZAppointment( $appointmentID );
            $dt = $app->dateTime();
            $year = $dt->year();
            $month = $dt->month();
            $day = $dt->day();
       }

        eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day" );
        exit();
    }

    $datetime = new eZDateTime( $year, $month, 1 );
    if ( $datetime->daysInMonth() < $day )
    {
        $dateError = true;
    }
    $datetime->setDay( $day );
    if ( !is_numeric( $year ) )
    {
        $dateError = true;
    }

    if ( !isset( $trusteelist ) )
    {
        $trusteelist = array();
        foreach ( $trusteeUser as $trusted )
        {
            $trusteelist[] = new eZUser( $trusted );
        }
    }
    if ( !isset( $user ) )
        $user = eZUser::currentUser();

    foreach ( $trusteelist as $trusteduser )
    {
        if ( $user->ID() == $trusteduser->ID() ||
             in_array( $user->ID(), $trusteduser->getByTrustee() ) )
        {
            $type = new eZAppointmentType( $TypeID );

            if ( $action == "Update" )
            {
                $appointment = new eZAppointment( $appointmentID );
                $beginDate = $appointment->dateTime();
            }
            else
            {
                $appointment = new eZAppointment();
                $beginDate = false;
            }

            $appointment->setDescription( $description );
            $appointment->setType( $type );
            $appointment->setOwner( $trusteduser );
            $appointment->setPriority( $priority );

            if ( isset( $isPrivate ) && $isPrivate == "on" )
                $appointment->setIsPrivate( true );
            else
                $appointment->setIsPrivate( false );

            if ( isset( $name ) && $name != "" )
                $appointment->setName( $name );
            else
                $titleError = true;

            // start/stop time for the day
            $dayStartTime = new eZDateTime( $year, $month, $day );
            $dayStopTime = new eZDateTime( $year, $month, $day );

            if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $startTimeStr, $dayStartArray ) )
            {
                $hour = $dayStartArray[2];
                $dayStartTime->setHour( $hour );

                $min = $dayStartArray[3];
                $dayStartTime->setMinute( $min );

                $dayStartTime->setSecond( 0 );
            }

            if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $stopTimeStr, $dayStopArray ) )
            {
                $hour = $dayStopArray[2];
                $dayStopTime->setHour( $hour );

                $min = $dayStopArray[3];
                $dayStopTime->setMinute( $min );

                $dayStopTime->setSecond( 0 );
            }

            // start/stop time for the appointment
            $startTimestamp = strtotime( $start );
            $stopTimestamp = strtotime( $stop );

            $startTime = new eZDateTime( $year, $month, $day, date('H', $startTimestamp), date('i', $startTimestamp) );
            $stopTime = new eZDateTime( $year, $month, $day, date('H', $stopTimestamp), date('i', $stopTimestamp) );

            $startTime->setSecond( 0 );
            $stopTime->setSecond( 0 );

            if ( preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $start, $startArray ) ) {
                $hour = $startArray[2];
                settype($hour, "integer");
                $startTime->setHour($hour);
                $min = $startArray[3];
                settype($min, "integer");

                $startTime->setMinute($min);
                if (isset($allDay) && $allDay == "on") {
                    $appointment->setAllDay(true);
                } else {
                    $appointment->setAllDay(false);
                }
            }
            else
            {
                $startTimeError = true;
            }

//            if ( isset( $allDay ) && $allDay == "on" )
//            {
//                $appointment->setAllDay( true );
//            }
//            else
            if ( !preg_match( "#(^([0-9]{1,2})[^0-9]{0,1}([0-9]{0,2})$)#", $stop, $stopArray ) )
            {
                $stopTimeError = true;
            }

            $datetime->setSecondsElapsedHMS( $startTime->hour(), $startTime->minute(), 0 );

            $appointment->setDateTime( $datetime );
            //$datetime = new eZDateTime( $year, $month, $day, $startTime->hour(), $startTime->minute() );
//            echo "<hr>";
//            echo $startTime->hour();
//            echo "<hr>";
//            echo $datetime->day();
//            echo "<hr>";
//            $appointment->setDateTime( $datetime );
//            echo "<hr>";
//            echo $datetime->Time->hour() ;
//            echo "<hr>";
//            echo $appointment->Date;


            if ( $stopTime->isGreater( $startTime, true ) && $allDay != "on" )
            {
                $stopTimeError = true;
            }
            else
            {
                // $duration = new eZDateTime();
                // $duration->setTimeStamp( $stopTime->timeStamp() - $startTime->timeStamp() );
                // $duration->setTimeStamp( $stopTime->timestamp() );
                // $appointment->setDuration( $allDay == "on" ? 0 : $duration->timeStamp() );

                $appointment->setDuration( isset( $allDay ) && $allDay == "on" ? 0 : $stopTimestamp - $startTimestamp );
            }

            if ( isset( $titleError ) && $titleError == false && isset( $startTimeError ) && $startTimeError == false &&
                isset( $stopTimeError ) && $stopTimeError == false && isset( $dateError ) && $dateError == false )
            {
                $appointment->store();
                // die('Stored! All is saved but, simply not always as expected.');

                $year = addZero( $datetime->year() );
                $month = addZero( $datetime->month() );
                $day = addZero( $datetime->day() );
                deleteCache( "default", $Language, $year, $month, $day, $trusteduser->id() );
                if ( $beginDate )
                {
                    deleteCache( "default", $Language, addZero( $beginDate->year() ),
                                 addZero( $beginDate->month() ),
                                 addZero( $beginDate->day() ), $trusteduser->id() );
                }
            }
            else
            {
                break;
            }
        }
    }

    if ( $titleError == false && $startTimeError == false &&
         $stopTimeError == false && $dateError == false )
    {
        eZHTTPTool::header( "Location: /calendar/dayview/$year/$month/$day/" );
        exit();
    }
    else
    {
        $user = eZUser::currentUser();
        $userID = $user->ID();
        $t->set_var( "name_value", $appointment->name() );
        $t->set_var( "description_value", $appointment->description() );

        if ( $appointment->isPrivate() )
            $t->set_var( "is_private", "checked" );
        else
            $t->set_var( "is_private", "" );

        $appStartTime = $appointment->startTime();
        $appStopTime = $appointment->stopTime();


        if ( $appStartTime )
        {
            $t->set_var( "start_value", addZero( $appStartTime->hour() ) . addZero( $appStartTime->minute() ) );
        }
        else
        {
            $t->set_var( "start_value", "" );
        }

        if ( $appStartTime && !$appointment->allDay() )
        {
            $t->set_var( "all_day_selected", "" );
        }
        else
        {
            //$t->set_var( "start_value", "" );
            $t->set_var( "all_day_selected", "checked" );
        }

        if ( $appStopTime )
            $t->set_var( "stop_value", addZero( $appStopTime->hour() ) . addZero( $appStopTime->minute() ) );
        else
            $t->set_var( "stop_value", "" );

        if ( in_array( $userID, $trusteeUser ) )
            $t->set_var( "own_selected", "selected" );
        else
            $t->set_var( "own_selected", "" );

        $t->set_var( "own_user_id", $userID );
        $t->set_var( "own_user_name", $user->name() );
        $t->set_var( "trustee_user_name", "" );
        $t->set_var( "action_value", $action );
        $t->set_var( "appointment_id", $appointmentID );

        $trusteeArray = $user->getByTrustee( -1, true );
        foreach ( $trusteeArray as $trustee )
        {
            $t->set_var( "user_id", $trustee->ID() );
            $t->set_var( "user_name", $trustee->name() );

            if ( in_array( $trustee->id(), $trusteeUser ) )
                $t->set_var( "selected", "selected" );
            else
                $t->set_var( "selected", "" );
            $t->parse( "trustee_user_name", "trustee_user_name_tpl", true );
        }
    }
}

$t->set_var( "user_error", "" );

if ( $action == "Edit" )
{
    $t->set_var( "0_selected", "" );
    $t->set_var( "1_selected", "" );
    $t->set_var( "2_selected", "" );

    if ( isset( $priority ) && $priority == 0 )
        $t->set_var( "0_selected", "selected" );
    else if ( isset( $priority ) && $priority == 1 )
        $t->set_var( "1_selected", "selected" );
    else if ( isset( $priority ) && $priority == 2 )
        $t->set_var( "2_selected", "selected" );

    if ( isset( $isPrivate ) && $isPrivate == "on" )
        $t->set_var( "is_private", "checked" );
    else
        $t->set_var( "is_private", "" );

    $t->set_var( "action_value", $action );
    $t->set_var( "appointment_id", $appointmentID );

    if ( $app != false && $userID == $app->userID() )
        $t->set_var( "own_selected", "selected" );
    else
        $t->set_var( "own_selected", "" );

    $t->set_var( "own_user_id", $userID );
    if( $user != false )
    $t->set_var( "own_user_name", $user->name() );
    $t->set_var( "user_name", "" );

    if( $user != false )
    {
        $trusteeArray = $user->getByTrustee( -1, true );
        foreach ( $trusteeArray as $trustee )
        {
            $t->set_var( "user_id", $trustee->ID() );
            $t->set_var( "user_name", $trustee->name() );

            if ( $app->userID() == $trustee->ID() )
                $t->set_var( "selected", "selected" );
            else
                $t->set_var( "selected", "" );
            $t->parse( "trustee_user_name", "trustee_user_name_tpl", true );
        }
    }
}

if ( $action == "Edit" )
{
    $appointment = new eZAppointment( $appointmentID );
    $t->set_var( "name_value", $appointment->name() );
    $t->set_var( "appointment_id", $appointment->id() );
    $t->set_var( "description_value", $appointment->description() );

    $type = $appointment->type();
    $typeID = $type->id();
    $startTime = $appointment->startTime();
    $startHour = addZero( $startTime->hour() );
    $startMinute = addZero( $startTime->minute() );

    $stopTime = $appointment->stopTime();
    $stopHour = addZero( $stopTime->hour() );
    $stopMinute = addZero( $stopTime->minute() );

    if ( $appointment->allDay() )
    {
        $t->set_var( "start_value", $startHour . $startMinute );
        $t->set_var( "stop_value", $stopHour . $stopMinute );
        $t->set_var( "all_day_selected", "checked" );
    }
    else
    {
        $t->set_var( "all_day_selected", "" );
        $t->set_var( "start_value", $startHour . $startMinute );
        $t->set_var( "stop_value", $stopHour . $stopMinute );
    }

    $t->set_var( "0_selected", "" );
    $t->set_var( "1_selected", "" );
    $t->set_var( "2_selected", "" );

    if ( $appointment->priority() == 0 )
        $t->set_var( "0_selected", "selected" );
    else if ( $appointment->priority() == 1 )
        $t->set_var( "1_selected", "selected" );
    else if ( $appointment->priority() == 2 )
        $t->set_var( "2_selected", "selected" );

    $dt = $appointment->dateTime();

    $t->set_var( "year_value", $dt->year() );

    if ( $appointment->isPrivate() )
        $t->set_var( "is_private", "checked" );
    else
        $t->set_var( "is_private", "" );

    $t->set_var( "action_value", "update" );
}


// print out error messages
if ( isset( $titleError ) && $titleError == true )
    $t->parse( "title_error", "title_error_tpl" );
else
    $t->set_var( "title_error", "" );

if ( isset( $startTimeError ) && $startTimeError )
{
    $t->parse( "start_time_error", "start_time_error_tpl" );
    $t->set_var( "action_value", "insert" );
    $t->set_var( "appointment_id", "new" );
}
else
{
    $t->set_var( "start_time_error", "" );
}

if ( isset( $stopTimeError ) && $stopTimeError )
{
    $t->parse( "stop_time_error", "stop_time_error_tpl" );
    $t->set_var( "action_value", "insert" );
    $t->set_var( "appointment_id", "new" );
}
else
{
    $t->set_var( "stop_time_error", "" );
}

if ( isset( $dateError ) && $dateError )
{
    $t->parse( "date_error", "date_error_tpl" );
    $t->set_var( "action_value", "insert" );
    $t->set_var( "appointment_id", "new" );
}
else
{
    $t->set_var( "date_error", "" );
}

$today = new eZDate();
if ( isset( $year ) && $year != 0 )
    $year = $year;
else
    $year = $today->year();

if ( isset( $month ) && $month != 0 )
    $month = $month;
else
    $month = $today->month();

if ( isset( $day ) && $day != 0 )
    $day = $day;
else
    $day = $today->day();

$tmpdate = new eZDate( $year, $month, $day );
// $tmpdate = new eZDate( $year, $month, $day );

if ( $action == "New" )
{
    $t->set_var( "action_value", "insert" );
    $t->set_var( "appointment_id", "new" );
    

    if ( isset( $changeView ) )
    {
        $t->set_var( "name_value", $name );
        $t->set_var( "description_value", $description );
        $t->set_var( "is_private", $isPrivate == "on" ? "checked" : "" );
        $t->set_var( "all_day_selected", $allDay == "on" ? "checked" : "" );
        $t->set_var( "start_value", $start );
        $t->set_var( "stop_value", $stop );

        $t->set_var( "0_selected", "" );
        $t->set_var( "1_selected", "" );
        $t->set_var( "2_selected", "" );
        if ( $priority == 0 )
            $t->set_var( "0_selected", "selected" );
        if ( $priority == 1 )
            $t->set_var( "1_selected", "selected" );
        if ( $priority == 2 )
            $t->set_var( "2_selected", "selected" );

        $tmpdate = new eZDate( $year, $month, $day );

        $t->set_var( "own_user_id", $userID );
        $t->set_var( "own_user_name", $user->name() );
        if ( in_array( $userID, $trusteeUser ) )
            $t->set_var( "own_selected", "selected" );
        else
            $t->set_var( "own_selected", "" );
        $t->set_var( "user_name", "" );

        $trusteeArray = $user->getByTrustee( -1, true );

        foreach ( $trusteeArray as $trustee )
        {
            $t->set_var( "user_id", $trustee->ID() );
            $t->set_var( "user_name", $trustee->name() );
            if ( in_array( $trustee->ID(), $trusteeUser ) )
                $t->set_var( "selected", "selected" );
            else
                $t->set_var( "selected", "" );
            $t->parse( "trustee_user_name", "trustee_user_name_tpl", true );
        }
    }
    else
    {
        $t->set_var( "name_value", "" );
        $t->set_var( "description_value", "" );
        $t->set_var( "is_private", "" );
        $t->set_var( "start_value", "" );
        $t->set_var( "stop_value", "" );
        $t->set_var( "all_day_selected", "" );

        $t->set_var( "0_selected", "" );
        $t->set_var( "1_selected", "" );
        $t->set_var( "2_selected", "" );

        if ( $year != 0 )
            $year = $year;
        else
            $year = $today->year();

        if ( $month != 0 )
            $month = $month;
        else
            $month = $today->month();

        if ( $day != 0 )
            $day = $day;
        else
            $day = $today->day();

        $tmpdate = new eZDate( $year, $month, $day );

        if ( $startTime != 0 )
            $t->set_var( "start_value", $startTime );

        if ( $user )
        {
            $t->set_var( "own_user_id", $userID );
            $t->set_var( "own_user_name", $user->name() );
            $t->set_var( "own_selected", "" );
            $t->set_var( "user_name", "" );
            $sessionUser = $session->variable( "ShowOtherCalendarUsers" );
            $trusteeArray = $user->getByTrustee( -1, true );

            foreach ( $trusteeArray as $trustee )
            {
                $t->set_var( "user_id", $trustee->ID() );
                $t->set_var( "user_name", $trustee->name() );
                if ( $sessionUser == $trustee->id() )
                    $t->set_var( "selected", "selected" );
                else
                    $t->set_var( "selected", "" );
                $t->parse( "trustee_user_name", "trustee_user_name_tpl", true );
            }
        }
    }
}

// print the appointment types
$type = new eZAppointmentType();
$typeList = $type->getTree();

foreach ( $typeList as $type )
{
    $typeID = $type[0]->id();
    if ( $type[1] > 1 )
        $t->set_var( "option_level", str_repeat( "&nbsp;&nbsp;", $type[1] - 1 ) );
    else
        $t->set_var( "option_level", "" );

    if ( $typeID )
    {
        if ( $typeID == $type[0]->id() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }

    $t->set_var( "option_name", $type[0]->name() );
    $t->set_var( "option_value", $type[0]->id() );

    $t->parse( "value", "value_tpl", true );
}

// set day combobox
for ( $i = 1; $i <= 31; $i++ )
{
    if ( $tmpdate->day() == $i )
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );

    if ( $action == "Edit" )
    {
        if ( $dt->day() == $i )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }
    else if ( isset( $dateError ) && $dateError )
    {
        if ( $day == $i )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }

    $t->set_var( "day_id", $i );
    $t->set_var( "day_name", $i );

    $t->parse( "day", "day_tpl", true );
}

// set month combobox
$month = $tmpdate->month();
for ( $i = 1; $i <= 12; $i++ )
{
    if ( $month == $i )   // don't use $tmpdate->month() since it gets changed
        $t->set_var( "selected", "selected" );
    else
        $t->set_var( "selected", "" );

    if ( $action == "Edit" )
    {
        if ( $i == $dt->month() )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }
    else if ( isset( $dateError ) && $dateError )
    {
        if ( $i == $month )
        {
            $t->set_var( "selected", "selected" );
        }
        else
        {
            $t->set_var( "selected", "" );
        }
    }

    $tmpdate->setMonth( $i );
    $t->set_var( "month_id", $i );
    $t->set_var( "month_name", $locale->monthName( $tmpdate->monthName() ) );

    $t->parse( "month", "month_tpl", true );
}

if ( $action != "Edit" )
{
    if ( isset( $dateError ) && $dateError )
        $t->set_var( "year_value", $year );
    else
        $t->set_var( "year_value", $tmpdate->year() );
}

//if ( !isset( $dateError ) )
//{

$t->parse("no_error", "no_error_tpl");
$t->pparse("output", "appointment_edit_tpl");

//}

// deletes the dayview cache file for a given day
function deleteCache( $SiteDesign, $language, $year, $month, $day, $userID )
{
    @eZPBFile::unlink( "kernel/ezcalendar/user/cache/dayview.tpl-$SiteDesign-$language-$year-$month-$day-$userID.cache" );
    @eZPBFile::unlink( "kernel/ezcalendar/user/cache/monthview.tpl-$SiteDesign-$language-$year-$month-$userID.cache" );
    @eZPBFile::unlink( "kernel/ezcalendar/user/cache/dayview.tpl-$SiteDesign-$language-$year-$month-$day-$userID-private.cache" );
    @eZPBFile::unlink( "kernel/ezcalendar/user/cache/monthview.tpl-$SiteDesign-$language-$year-$month-$userID-private.cache" );
}

//Adds a "0" in front of the value if it's below 10.
function addZero( $value )
{
    settype( $value, "integer" );
    $ret = $value;
    if ( $ret < 10 )
    {
        $ret = "0". $ret;
    }
    return $ret;
}

?>