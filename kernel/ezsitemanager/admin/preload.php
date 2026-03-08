<?php
//
// ezsitemanager/admin/preload.php
//
// Preload Site admin page — renders a terminal-style UI that connects to
// preloadstream.php via Server-Sent Events and displays grok preload output.
//
// Reached via: /sitemanager/preload/
//

$ini      = eZINI::instance( 'site.ini' );
$Language = $ini->variable( "eZSiteManagerMain", "Language" );

$t = new eZTemplate( "kernel/ezsitemanager/admin/" . $ini->variable( "eZSiteManagerMain", "AdminTemplateDir" ),
                     "kernel/ezsitemanager/admin/intl", $Language, "preload.php" );
$t->setAllStrings();

$t->set_file( "preload_tpl", "preload.tpl" );

$t->pparse( "output", "preload_tpl" );
