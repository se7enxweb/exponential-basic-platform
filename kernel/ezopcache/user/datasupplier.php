<?php

$user = eZUser::currentUser();

if ( !$user || !$user->hasRootAccess() )
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}

switch ( $url_array[2] )
{
    case "clear" :
    {
        include( "kernel/ezopcache/user/clear.php" );
    }
    break;

    default :
    {
        eZHTTPTool::header( "Location: /error/404" );
        exit();
    }
}
