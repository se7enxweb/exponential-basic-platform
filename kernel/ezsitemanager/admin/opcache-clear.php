<?php
//
// ezsitemanager/admin/opcache-clear.php
//
// Op Cache Clear admin page — resets PHP OPcache via opcache_reset().
//
// Reached via: /sitemanager/cache/opcache
//

$ini      = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZSiteManagerMain", "Language" );

$t = new eZTemplate( "kernel/ezsitemanager/admin/" . $ini->variable( "eZSiteManagerMain", "AdminTemplateDir" ),
                     "kernel/ezsitemanager/admin/intl", $Language, "opcache-clear.php" );
$t->setAllStrings();

$t->set_file( "opcache_clear_tpl", "opcache-clear.tpl" );
$t->set_block( "opcache_clear_tpl", "opcache_results_tpl", "opcache_results" );

$t->set_var( "opcache_results", "" );

if ( isset( $clearOpcache ) )
{
    if ( function_exists( 'opcache_reset' ) )
    {
        $ok  = opcache_reset();
        $ret = $ok ? "OPcache cleared successfully." : "OPcache reset() returned false — cache may not have been cleared.";
    }
    else
    {
        $ret = "OPcache extension is not available on this server.";
    }

    $t->set_var( "opcache_return", $ret );
    $t->parse( "opcache_results", "opcache_results_tpl" );
}

$t->pparse( "output", "opcache_clear_tpl" );
