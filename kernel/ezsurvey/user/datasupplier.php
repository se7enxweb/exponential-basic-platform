<?php

    // include_once( "ezsitemanager/classes/ezsection.php" );
    // include_once( "ezuser/classes/ezpermission.php" ); 
    
    $ini = eZINI::instance( 'site.ini' );
    $user = eZUser::currentUser();

    $GlobalSectionID = $ini->variable( "eZSurveyMain", "DefaultSection" );
    $currentSiteDesign = (new eZSection())->siteDesign( $GlobalSectionID );
    $hasPermission = ( eZPermission::checkPermission( $user, "eZSurvey", "ModuleAnswer" ) );

    $finish       = eZHTTPTool::getVar( 'Finish' );
    $nextPage     = eZHTTPTool::getVar( 'NextPage' );
    $page         = eZHTTPTool::getVar( 'Page' );
    $previousPage = eZHTTPTool::getVar( 'PreviousPage' );
    $questionID   = eZHTTPTool::getVar( 'QuestionID' );
    $rank         = eZHTTPTool::getVar( 'Rank' );
    $responseID   = eZHTTPTool::getVar( 'ResponseID' );
    $surveyID     = eZHTTPTool::getVar( 'SurveyID' );
    $value        = eZHTTPTool::getVar( 'Value' );

    switch ( $url_array[2] )
    {
        case "surveylist":
        {
            if ( $hasPermission )
            {
                include ( "kernel/ezsurvey/user/surveylist.php" );
            }
            else
            {
                //eZHTTPTool::header( "Location: /novisagent/login/" );
                //exit();
                include ( "kernel/ezsurvey/user/surveylist.php" );
            }
        }
        break;
        
        case "thanks":
        {
            include ( "kernel/ezsurvey/user/thanks.php" );
        }
        break;
    }
    
?>