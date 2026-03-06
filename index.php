<?php
/**
 * @copyright Copyright (C) 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

// Set a default time zone if none is given to avoid "It is not safe to rely
// on the system's timezone settings" warnings. The time zone can be overridden
// in config.php or php.ini.
if ( !ini_get( "date.timezone" ) )
{
    date_default_timezone_set( "UTC" );
}

$displayPerformanceInfoComment = false;

ignore_user_abort( true );
error_reporting ( E_ALL );

// Include composer based autoloads (new in 2.4.0.0)
require __DIR__ . '/vendor/autoload.php';

$_t0 = hrtime( true );

$kernel = new ezpbKernel( new ezpbKernelWeb() );

$_t1 = hrtime( true );

$result = $kernel->run();

$_t2 = hrtime( true );

$content = $result->getContent();

if ( $displayPerformanceInfoComment === true )
{
    $_t3 = hrtime( true );
    $_ns = static fn( $a, $b ) => round( ( $b - $a ) / 1e6, 2 );
    $content .= "\n<!--\n"
    . "  [PERF] kernel-init : " . $_ns( $_t0, $_t1 ) . " ms\n"
    . "  [PERF] kernel-run  : " . $_ns( $_t1, $_t2 ) . " ms\n"
    . "  [PERF] get-content : " . $_ns( $_t2, $_t3 ) . " ms\n"
    . "  [PERF] total       : " . $_ns( $_t0, $_t3 ) . " ms\n"
    . "  [PERF] peak-memory : " . round( memory_get_peak_usage( true ) / 1048576, 2 ) . " MB\n"
    . "  [PERF] db-queries  : " . ( $GLOBALS['_db_query_count'] ?? 0 ) . "\n"
    . "  [PERF] db-time     : " . round( $GLOBALS['_db_query_ms'] ?? 0, 2 ) . " ms\n"
    . "-->\n";
}

echo $content;

?>