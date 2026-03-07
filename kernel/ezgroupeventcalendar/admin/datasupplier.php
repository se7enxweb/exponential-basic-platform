<?php

// Explicit POST/GET extraction — replaces the kernel register_globals hack for this module.
$action             = eZHTTPTool::getVar( 'Action' );
$cancel             = eZHTTPTool::getVar( 'Cancel' );
$categoryArrayID    = eZHTTPTool::getVar( 'CategoryArrayID' ) ?? [];
$categoryID         = eZHTTPTool::getVar( 'CategoryID' );
$deleteEditor       = eZHTTPTool::getVar( 'DeleteEditor' );
$description        = eZHTTPTool::getVar( 'Description' );
$groupID            = eZHTTPTool::getVar( 'GroupID' );
$idArray            = eZHTTPTool::getVar( 'IDArray' ) ?? [];
$name               = eZHTTPTool::getVar( 'Name' );
$newEditor          = eZHTTPTool::getVar( 'NewEditor' );
$noDisplayIDArray   = eZHTTPTool::getVar( 'NoDisplayIDArray' ) ?? [];
$parentID           = eZHTTPTool::getVar( 'ParentID' );
$removeMemberIdArray = eZHTTPTool::getVar( 'RemoveMemberIdArray' ) ?? [];
$store              = eZHTTPTool::getVar( 'Store' );
$typeArrayID        = eZHTTPTool::getVar( 'TypeArrayID' ) ?? [];
$typeID             = eZHTTPTool::getVar( 'TypeID' );
// URL-routing variables are set below inside the switch and override the above POST defaults.

switch ( $url_array[2] )
{
    case "typelist":
    {
        include( "kernel/ezgroupeventcalendar/admin/typelist.php" );
    }
    break;

    case "typeedit" :
    {
        if ( $url_array[3] == "edit" )
        {
            $action = "Edit";
            $typeID = $url_array[4];
        }
        else if ( $url_array[3] == "delete" )
        {
            $action = "Delete";
            $typeID = $url_array[4];
        }
        else if ( $url_array[3] == "new" )
        {
            $action = "New";
        }
        
        include( "kernel/ezgroupeventcalendar/admin/typeedit.php" );
    }
    break;

 case "categorylist":
   {
     include( "kernel/ezgroupeventcalendar/admin/categorylist.php" );
   }
   break;

 case "categoryedit" :
   {
     if ( $url_array[3] == "edit" )
       {
	 $action = "Edit";
	 $categoryID = $url_array[4];
       }
     else if ( $url_array[3] == "delete" )
       {
	 $action = "Delete";
	 $categoryID = $url_array[4];
       }
     else if ( $url_array[3] == "new" )
       {
	 $action = "New";
       }

     include( "kernel/ezgroupeventcalendar/admin/categoryedit.php" );
   }
   break;

	case "grpdspl" :
	{
		include( "kernel/ezgroupeventcalendar/admin/groupdisplay.php" );
	}
	break;

	case "editor" :
	{
		switch ( $url_array[3] )
		{
			case "edit" :
			{
				$action  = "Edit";
				$groupID = $url_array[4];
				include( "kernel/ezgroupeventcalendar/admin/groupeditor.php" );
			}
			break;

			case "" :
			{
				$action = "Display";
				include( "kernel/ezgroupeventcalendar/admin/groupeditor.php" );
			}
			break;
		}
	}
	break;

    default :
    {
        // go to default module page or show an error message
        print( "Error: your page request was not found" );
    }
}

?>
