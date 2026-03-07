<?php
include_once( "kernel/ezgroupeventcalendar/classes/ezgroupevent.php" );

$ini = eZINI::instance( 'site.ini' );
$GlobalSectionID = $ini->variable( "eZGroupEventCalendarMain", "DefaultSection" );
$userComments = $ini->variable( "eZGroupEventCalendarMain", "UserComments" );

$title = "Calendar";

$action             = eZHTTPTool::getVar( 'Action' );
$addFile            = eZHTTPTool::getVar( 'AddFile' );
$addFiles           = eZHTTPTool::getVar( 'AddFiles' );
$browse             = eZHTTPTool::getVar( 'Browse' );
$cancel             = eZHTTPTool::getVar( 'Cancel' );
$categoryID         = eZHTTPTool::getVar( 'CategoryID' );
$debug              = eZHTTPTool::getVar( 'Debug' );
$deleteEvents       = eZHTTPTool::getVar( 'DeleteEvents' );
$deleteSelected     = eZHTTPTool::getVar( 'DeleteSelected' );
$description        = eZHTTPTool::getVar( 'Description' );
$descriptionArray   = $_POST['DescriptionArray'] ?? [];
$encoding           = eZHTTPTool::getVar( 'Encoding' );
$eventAlarmNotice   = eZHTTPTool::getVar( 'EventAlarmNotice' );
$eventID            = eZHTTPTool::getVar( 'EventID' );
$exceptSelect       = eZHTTPTool::getVar( 'ExceptSelect' );
$fileArrayID        = $_POST['FileArrayID'] ?? [];
$fileID             = eZHTTPTool::getVar( 'FileID' );
$fileUploadFlag     = eZHTTPTool::getVar( 'FileUploadFlag' );
$forumID            = eZHTTPTool::getVar( 'ForumID' );
$getByGroup         = eZHTTPTool::getVar( 'GetByGroup' );
$getByGroupID       = eZHTTPTool::getVar( 'GetByGroupID' );
$getByTypeID        = eZHTTPTool::getVar( 'GetByTypeID' );
$getByUserID        = eZHTTPTool::getVar( 'GetByUserID' );
$goDay              = eZHTTPTool::getVar( 'GoDay' );
$goMonth            = eZHTTPTool::getVar( 'GoMonth' );
$goNew              = eZHTTPTool::getVar( 'GoNew' );
$goToday            = eZHTTPTool::getVar( 'GoToday' );
$goWeek             = eZHTTPTool::getVar( 'GoWeek' );
$goYear             = eZHTTPTool::getVar( 'GoYear' );
$group              = eZHTTPTool::getVar( 'Group' );
$groupID            = eZHTTPTool::getVar( 'GroupID' );
$host               = eZHTTPTool::getVar( 'Host' );
$intervalStr        = eZHTTPTool::getVar( 'IntervalStr' );
$isAllDay           = eZHTTPTool::getVar( 'IsAllDay' );
$isEventAlarmNotice = eZHTTPTool::getVar( 'IsEventAlarmNotice' );
$isPrivate          = eZHTTPTool::getVar( 'IsPrivate' );
$isRecurring        = eZHTTPTool::getVar( 'IsRecurring' );
$limit              = eZHTTPTool::getVar( 'Limit' );
$limitDirectionForward = eZHTTPTool::getVar( 'LimitDirectionForward' );
$link               = eZHTTPTool::getVar( 'Link' );
$locale             = eZHTTPTool::getVar( 'Locale' );
$location           = eZHTTPTool::getVar( 'Location' );
$name               = eZHTTPTool::getVar( 'Name' );
$numberOfTimes      = eZHTTPTool::getVar( 'NumberOfTimes' );
$offset             = eZHTTPTool::getVar( 'Offset' );
$printableVersion   = eZHTTPTool::getVar( 'PrintableVersion' );
$priority           = eZHTTPTool::getVar( 'Priority' );
$recurFreq          = eZHTTPTool::getVar( 'RecurFreq' );
$recurType          = eZHTTPTool::getVar( 'RecurType' );
$recurTypeMonth     = eZHTTPTool::getVar( 'RecurTypeMonth' );
$recurWeekly        = eZHTTPTool::getVar( 'RecurWeekly' );
$redirectURL        = eZHTTPTool::getVar( 'RedirectURL' );
$repeatOptions      = eZHTTPTool::getVar( 'RepeatOptions' );
$rssVersion         = eZHTTPTool::getVar( 'RssVersion' );
$sitedesign         = eZHTTPTool::getVar( 'Sitedesign' );
$siteStyle          = eZHTTPTool::getVar( 'SiteStyle' );
$start              = eZHTTPTool::getVar( 'Start' );
$startTime          = eZHTTPTool::getVar( 'StartTime' );
$startTimeError     = eZHTTPTool::getVar( 'StartTimeError' );
$startTimeStr       = eZHTTPTool::getVar( 'StartTimeStr' );
$status             = eZHTTPTool::getVar( 'Status' );
$stop               = eZHTTPTool::getVar( 'Stop' );
$stopTimeError      = eZHTTPTool::getVar( 'StopTimeError' );
$stopTimeStr        = eZHTTPTool::getVar( 'StopTimeStr' );
$storeByGroupID     = eZHTTPTool::getVar( 'StoreByGroupID' );
$templateDir        = eZHTTPTool::getVar( 'TemplateDir' );
$titleError         = eZHTTPTool::getVar( 'TitleError' );
$truncateTitle      = eZHTTPTool::getVar( 'TruncateTitle' );
$truncateTitleSize  = eZHTTPTool::getVar( 'TruncateTitleSize' );
$type               = eZHTTPTool::getVar( 'Type' );
$typeID             = eZHTTPTool::getVar( 'TypeID' );
$url                = eZHTTPTool::getVar( 'Url' ) ?? eZHTTPTool::getVar( 'URL' );
$untilDate          = eZHTTPTool::getVar( 'UntilDate' );
$week               = eZHTTPTool::getVar( 'Week' );

/*
if( $getByTypeID != 0 )
	$type = $getByTypeID;
else
	$type = 0;
*/

switch ( $url_array[2] )
{
    case "rssheadlines":
    case "listrss":
    case "rss":
    {
      include( "kernel/ezgroupeventcalendar/user/rssheadlines.php" );
    }
    break;

    case "yearview" :
    {
        $year = $url_array[3];

        include( "kernel/ezgroupeventcalendar/user/yearview.php" );
    }
    break;

    case "monthview" :
    {
        $year = $url_array[3];
        $month = $url_array[4];

        include( "kernel/ezgroupeventcalendar/user/monthview.php" );
    }
    break;

    case "dayview" :
    {
        $year = $url_array[3];
        $month = $url_array[4];
        $day = $url_array[5];

        include( "kernel/ezgroupeventcalendar/user/dayview.php" );
    }
    break;
    case "weekview" :
    {
        $year = $url_array[3];
        $month = $url_array[4];
        $day = $url_array[5];

        include( "kernel/ezgroupeventcalendar/user/weekview.php" );
    }
    break;
    
    case "eventedit" :
    {
        switch ( $url_array[3] )
        {
	    // filelist
	    case "filelist" :
	    {
	        $eventID = $url_array[4];
		include( "kernel/ezgroupeventcalendar/user/filelist.php" );
		break;
	    }

	    //files
	    case "fileedit" :
	    {
	      if ( isSet( $browse ) )
	      {
		include( "kernel/ezfilemanager/admin/browse.php" );
		break;
	      }

 	      switch ( $url_array[4] )
	      {

	      case "new" :
	      {
		  $action = "New";
		  $eventID = $url_array[5];
		  include( "kernel/ezgroupeventcalendar/user/fileedit.php" );
	      }
	      break;

	      case "edit" :
	      {
		  $action = "Edit";
		  $eventID = $url_array[6];
		  $fileID = $url_array[5];
		  include( "kernel/ezgroupeventcalendar/user/fileedit.php" );
	      }
	      break;

	      case "delete" :
	      {
		  $action = "Delete";
		  $eventID = $url_array[6];
		  $fileID = $url_array[5];
		  include( "kernel/ezgroupeventcalendar/user/fileedit.php" );
	      }
	      break;

	      default :
	      {
	          include( "kernel/ezgroupeventcalendar/user/fileedit.php" );
	      }
	      }
	  }
	  break;


            case "new" :
            {
                $action = "New";
                $year = $url_array[4];
                $month = $url_array[5];
                $day = $url_array[6];
                $startTime = $url_array[7];

                include( "kernel/ezgroupeventcalendar/user/eventedit.php" );
            }
            break;

            case "edit" :
            {
                $action = "Edit";
                $eventID = $url_array[4];

                include( "kernel/ezgroupeventcalendar/user/eventedit.php" );
            }
            break;

            case "update" :
            {
                $action = "Update";
                $eventID = $url_array[4];

                include( "kernel/ezgroupeventcalendar/user/eventedit.php" );
            }
            break;

            case "insert" :
            {
                $action = "Insert";
                $eventID = $url_array[4];

                include( "kernel/ezgroupeventcalendar/user/eventedit.php" );
            }
            break;

            default :
            {
                $action = $url_array[3];
		include( "kernel/ezgroupeventcalendar/user/eventedit.php" );
            }
        }
    }
    break;

    case "eventview" :
    {
        $eventID = $url_array[3];
        include( "kernel/ezgroupeventcalendar/user/eventview.php" );

	if  ( isset( $printableVersion ) && ( $printableVersion != "enabled" ) &&  ( $userComments == "enabled" ) )
	{
	    $redirectURL = "/groupeventcalendar/eventview/$eventID/";
	    $event = new eZGroupEvent( $eventID );
	    if ( ( $event->id() >= 1 ) )
	    {
		for ( $i = 0; $i < count( $url_array ); $i++ )
		{
		  if ( ( $url_array[$i] ) == "parent" )
		  {
		     $next = $i + 1;
		     $offset = $url_array[$next];
		   }
		}

	      $forum = $event->forum();
	      $forumID = $forum->id();
       	      include( "kernel/ezforum/user/messagesimplelist.php" );
	    }
	}
    }
    break;
	
    default:
    {
       	eZHTTPTool::header( "Location: /error/404" );
         exit();
    }
}

?>
