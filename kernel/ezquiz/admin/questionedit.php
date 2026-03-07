<?php
//
// $Id: questionedit.php 8687 2001-12-06 10:19:29Z jhe $
//
// Created on: <22-May-2001 16:17:22 ce>
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
// include_once( "classes/ezhttptool.php" );

// include_once( "ezquiz/classes/ezquizquestion.php" );

$errorMessages = array();
if ( isset( $newAlternative ) )
{
    $question = new eZQuizQuestion( $questionID );
    $alternative = new eZQuizAlternative();
    $alternative->setQuestion( $question );
    $alternative->store();
    $action = "Update";
}
else
{
    $question = new eZQuizQuestion( $questionID );
}


if ( isset( $ok ) )
{
    $question = new eZQuizQuestion( $questionID );

    if ( $question->countAlternatives() == false )
    {
        $errorMessages[] = "error_add_alternative";
    }

    if ( count( $errorMessages ) > 0 )
    {
        unset( $ok );
    }
    $action = "Update";
}

if ( isset( $cancel ) )
{
    $question = new eZQuizQuestion( $questionID);
    $game = $question->game();
    $gameID = $game->id();
    eZHTTPTool::header( "Location: /quiz/game/edit/$gameID/" );
    exit();
}

if ( isset( $delete ) )
{
    if ( count( $alternativeDeleteArray ) > 0 )
    {
        foreach ( $alternativeDeleteArray as $altID )
        {
            eZQuizAlternative::delete( $altID );
        }
    }
    $action = "";
}

$ini = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZQuizMain", "Language" );

$t = new eZTemplate( "kernel/ezquiz/admin/" . $ini->variable( "eZQuizMain", "AdminTemplateDir" ),
                     "kernel/ezquiz/admin/" . "/intl", $Language, "questionedit.php" );
$t->setAllStrings();

$t->set_file( array(
    "question_edit_page" => "questionedit.tpl"
      ) );

$t->set_block( "question_edit_page", "alternative_list_tpl", "alternative_list" );
$t->set_block( "alternative_list_tpl", "alternative_item_tpl", "alternative_item" );
$t->set_block( "question_edit_page", "error_list_tpl", "error_list" );
$t->set_block( "error_list_tpl", "error_item_tpl", "error_item" );

$t->set_var( "question_name", isset( $name ) ? $name : false );
$t->set_var( "question_description", isset( $description ) ? $description : false );

if ( isset( $action ) && $action == "Update" )
{
    if ( is_numeric( $questionID ) )
        $question = new eZQuizQuestion( $questionID );
    else
        $question = new eZQuizQuestion();

    if ( empty( $name ) )
    {
        $errorMessages[] = "error_missing_question_name";
        unset( $ok );
    }
    else
    {
        $question->setName( $name );
    }
    $question->store();
    $alternativeNameError = false;
    if ( isset( $alternativeArrayID ) && count( $alternativeArrayID ) > 0 )
    {
        for ( $i = 0; $i < count( $alternativeArrayID ); $i++ )
        {
            $alternative = new eZQuizAlternative( $alternativeArrayID[$i] );
            if ( empty( $alternativeArrayName[$i] ) )
            {
                if ( $alternativeNameError == false )
                {
                    $errorMessages[] = "error_missing_answer_name";
                    unset( $ok );
                    $alternativeNameError = true;
                }
            }
            else
            {
                $alternative->setName( $alternativeArrayName[$i] );
            }

            if ( isset( $isCorrect ) && $isCorrect == $alternativeArrayID[$i] )
                $alternative->setIsCorrect( true );
            else
                $alternative->setIsCorrect( false );
            $alternative->store();
        }
        unset( $alternative );
    }

    if ( $question->countCorrectAlternatives() == false && !isset( $newAlternative ) )
    {
        $errorMessages[] = "error_no_correct_alternative";
        unset( $ok );
    }


    if ( isset( $ok ) )
    {
        $game = $question->game();
        $gameID = $game->id();
        eZHTTPTool::header( "Location: /quiz/game/edit/$gameID" );
        exit();
    }
}

if ( isset( $action ) && $action == "Delete" )
{
    if ( count( $alternativeArrayID ) > 0 )
    {
        foreach ( $alternativeArrayID as $alternativeID )
        {
            $alternative = new eZQuizAlternative( $alternativeID );
            $alternative->delete();
        }
    }
    eZHTTPTool::header( "Location: /quiz/game/question/edit/$gameID" );
    exit();
}

if ( is_numeric( $questionID ) )
{
    if ( !is_a( $question, "eZQuizQuestion" ) )
        $question = new eZQuizQuestion( $questionID );
    $t->set_var( "question_id", $question->id() );
    $t->set_var( "question_name", $question->name() );

    $alternativeList = $question->alternatives();
}

if ( count( $alternativeList ) > 0 )
{
    foreach ( $alternativeList as $alternative )
    {
        $t->set_var( "alternative_id", $alternative->id() );
        $t->set_var( "alternative_name", $alternative->name() );

        if ( $alternative->isCorrect() == $alternative->id() )
            $t->set_var( "is_selected", "checked" );
        else
            $t->set_var( "is_selected", "" );

        $t->parse( "alternative_item", "alternative_item_tpl", true );
    }
    $t->parse( "alternative_list", "alternative_list_tpl", true );
}
else
{
    $t->set_var( "alternative_list", "" );
}

if ( count( $errorMessages ) > 0 )
{
    foreach ( $errorMessages as $errorMessage )
    {
        $errorMessage = $t->Ini->variable( "strings", $errorMessage );
        $t->set_var( "error_message", $errorMessage );
        $t->parse( "error_item", "error_item_tpl", true );
    }

    $t->set_var( "question_name", $name );
    $t->set_var( "question_id", $questionID );

    $t->parse( "error_list", "error_list_tpl" );
}
else
{
    $t->set_var( "error_list", "" );
}

$t->pparse( "output", "question_edit_page" );

?>