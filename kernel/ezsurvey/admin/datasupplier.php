<?php

$action         = eZHTTPTool::getVar( 'Action' );
$addValue       = eZHTTPTool::getVar( 'AddValue' );
$back           = eZHTTPTool::getVar( 'Back' );
$clear          = eZHTTPTool::getVar( 'Clear' );
$deleteSelected = eZHTTPTool::getVar( 'DeleteSelected' );
$eMail          = eZHTTPTool::getVar( 'EMail' );
$info           = eZHTTPTool::getVar( 'Info' );
$newElement     = eZHTTPTool::getVar( 'NewElement' );
$nextPage       = eZHTTPTool::getVar( 'NextPage' );
$ok             = eZHTTPTool::getVar( 'OK' );
$page           = eZHTTPTool::getVar( 'Page' );
$preview        = eZHTTPTool::getVar( 'Preview' );
$previousPage   = eZHTTPTool::getVar( 'PreviousPage' );
$public         = eZHTTPTool::getVar( 'Public' );
$questionID     = eZHTTPTool::getVar( 'QuestionID' );
$section        = eZHTTPTool::getVar( 'Section' );
$size           = eZHTTPTool::getVar( 'Size' );
$stats          = eZHTTPTool::getVar( 'Stats' );
$status         = eZHTTPTool::getVar( 'Status' );
$store          = eZHTTPTool::getVar( 'Store' );
$subTitle       = eZHTTPTool::getVar( 'SubTitle' );
$surveyID       = eZHTTPTool::getVar( 'SurveyID' );
$thankBody      = eZHTTPTool::getVar( 'ThankBody' );
$thankHead      = eZHTTPTool::getVar( 'ThankHead' );
$title          = eZHTTPTool::getVar( 'Title' );
$update         = eZHTTPTool::getVar( 'Update' );
$value          = eZHTTPTool::getVar( 'Value' );
$valueDeleteID  = eZHTTPTool::getVar( 'ValueDeleteID' );
$valueID        = eZHTTPTool::getVar( 'ValueID' );

    switch ( $url_array[2] )
    {
        case "surveylist":
        {
            include ( "kernel/ezsurvey/admin/surveylist.php" );
        }
        break;
        
        case "surveyedit":
        {
            $action = $url_array[3];
            
            if ( isset($back) )
            {
                $url_array[4] = "";
                include ( "kernel/ezsurvey/admin/surveylist.php" );
            }
            else
            {
                include ( "kernel/ezsurvey/admin/surveyedit.php" );
            }
        }
        break;
        
        case "preview":
        {
            include( "kernel/ezsurvey/admin/preview.php" );
        }
        break;
        
        case "stats":
        {
            include( "kernel/ezsurvey/admin/stats.php" );
        }
        break;
        
        case "values":
        {
            include ( "kernel/ezsurvey/admin/values.php" );
        }
        break;
        
        case "default":
        {
            include ( "kernel/ezsurvey/admin/default.php" );
        }
        break;
    }
    
?>