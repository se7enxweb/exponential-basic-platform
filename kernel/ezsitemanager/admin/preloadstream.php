<?php
//
// ezsitemanager/admin/preloadstream.php
//
// Server-Sent Events endpoint — streams `grok preload` output to the browser.
// Reached via: /sitemanager/preload/stream
// Included by datasupplier.php AFTER eZPermission check.
//

// ── 1. Permission guard ───────────────────────────────────────────────────────
if ( !isset( $user ) || eZPermission::checkPermission( $user, "eZSiteManager", "ModuleEdit" ) == false )
{
    while ( ob_get_level() > 0 ) ob_end_clean();
    http_response_code( 403 );
    header( 'Content-Type: text/event-stream; charset=utf-8' );
    echo "data: [403] Forbidden — you must be logged in as an admin\n\n";
    echo "data: __DONE__\n\n";
    flush();
    eZExecution::setCleanExit();
    exit();
}

// ── 2. Kill every output buffer — we own this HTTP response entirely ──────────
while ( ob_get_level() > 0 )
    ob_end_clean();

// Disable any further output buffering PHP might apply
if ( function_exists( 'ob_implicit_flush' ) )
    ob_implicit_flush( true );

set_time_limit( 300 );
ignore_user_abort( false );

// ── 3. SSE response headers ───────────────────────────────────────────────────
header( 'Content-Type: text/event-stream; charset=utf-8' );
header( 'Cache-Control: no-cache, no-store, must-revalidate' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );
header( 'X-Accel-Buffering: no' );
header( 'Connection: keep-alive' );

// ── Helper: emit one SSE data line and flush immediately ─────────────────────
function sse( $line )
{
    echo 'data: ' . $line . "\n\n";
    // Pad to 4KB to defeat any transparent proxy/fcgid buffering
    $pad = 4096 - ( strlen( $line ) + 7 ) % 4096;
    if ( $pad < 4096 )
        echo ':' . str_repeat( ' ', $pad ) . "\n";
    if ( ob_get_level() > 0 )
        ob_flush();
    flush();
}

// ── 4. Announce startup immediately ──────────────────────────────────────────
sse( '── grok preload starting ──' );
sse( 'Server time: ' . date( 'Y-m-d H:i:s T' ) );
sse( 'PHP SAPI: ' . php_sapi_name() . '  |  PHP_BINARY: ' . PHP_BINARY );

// ── 5. Resolve the correct CLI PHP binary ────────────────────────────────────
// In a mod_fcgid/fpm context PHP_BINARY is the fpm binary, not the CLI.
// Try candidates in order of preference.
$phpCli     = null;
$candidates = [
    dirname( PHP_BINARY ) . '/php',   // sibling of fpm binary (most accurate)
    '/usr/bin/php',                    // system symlink (→ Plesk PHP on this host)
    '/opt/plesk/php/8.5/bin/php',     // absolute Plesk 8.5 path
    '/usr/bin/php8',
    '/usr/local/bin/php',
];
foreach ( $candidates as $c )
{
    if ( is_executable( $c ) )
    {
        $phpCli = $c;
        break;
    }
}

if ( $phpCli === null )
{
    sse( '[ERROR] Could not locate a CLI php binary.' );
    sse( '        Tried: ' . implode( ', ', $candidates ) );
    sse( '        Set $phpCli manually in preloadstream.php or fix PATH.' );
    sse( '__DONE__' );
    flush();
    eZExecution::setCleanExit();
    exit();
}

sse( 'CLI binary: ' . $phpCli );

// ── 6. Locate grok.php ───────────────────────────────────────────────────────
// __DIR__ == kernel/ezsitemanager/admin  (3 levels below site root)
$siteRoot = dirname( dirname( dirname( __DIR__ ) ) );
$grok     = $siteRoot . '/bin/shell/php/grok.php';

sse( 'Site root: ' . $siteRoot );
sse( 'grok.php:  ' . $grok );

if ( !file_exists( $grok ) )
{
    sse( '[ERROR] grok.php not found at: ' . $grok );
    sse( '        Run: ls ' . dirname( $grok ) );
    sse( '__DONE__' );
    flush();
    eZExecution::setCleanExit();
    exit();
}
if ( !is_readable( $grok ) )
{
    sse( '[ERROR] grok.php exists but is not readable (permissions?).' );
    sse( '        Run: chmod +r ' . $grok );
    sse( '__DONE__' );
    flush();
    eZExecution::setCleanExit();
    exit();
}

// ── 7. Spawn grok preload ─────────────────────────────────────────────────────
$cmd = $phpCli . ' ' . escapeshellarg( $grok ) . ' preload 2>&1';
sse( 'Command: ' . $cmd );
sse( '──────────────────────────────────────────' );
flush();

$handle = popen( $cmd, 'r' );

if ( $handle === false )
{
    sse( '[ERROR] popen() failed — the web server user may not have permission to run PHP.' );
    sse( '        Web user: ' . trim( shell_exec( 'whoami 2>&1' ) ) );
    sse( '__DONE__' );
    flush();
    eZExecution::setCleanExit();
    exit();
}

// ── 8. Stream output line-by-line ────────────────────────────────────────────
$lineCount = 0;
$lastFlush = microtime( true );

while ( !feof( $handle ) )
{
    $raw = fgets( $handle, 8192 );
    if ( $raw === false )
        break;

    $line = rtrim( $raw, "\r\n" );
    if ( $line === '' )
        continue;

    sse( $line );
    $lineCount++;

    // Heartbeat every 5s if grok is slow/silent (keeps connection alive)
    $now = microtime( true );
    if ( $now - $lastFlush > 5.0 )
    {
        echo ": heartbeat " . date( 'H:i:s' ) . "\n\n";
        flush();
        $lastFlush = $now;
    }
}

$exitCode = pclose( $handle );

sse( '──────────────────────────────────────────' );
sse( 'Lines received: ' . $lineCount );
sse( 'Exit code: ' . $exitCode . ( $exitCode === 0 ? ' (success)' : ' (non-zero — check output above)' ) );
sse( 'Server time: ' . date( 'Y-m-d H:i:s T' ) );
sse( '__DONE__' );
flush();

eZExecution::setCleanExit();
exit();


// ── 1. Extra permission guard (defense-in-depth) ─────────────────────────────
if ( !isset( $user ) || eZPermission::checkPermission( $user, "eZSiteManager", "ModuleEdit" ) == false )
{
    while ( ob_get_level() > 0 ) ob_end_clean();
    http_response_code( 403 );
    header( 'Content-Type: text/event-stream; charset=utf-8' );
    echo "data: [403] Forbidden\n\ndata: __DONE__\n\n";
    flush();
    eZExecution::setCleanExit();
    exit();
}

// ── 2. Kill every buffering layer (the admin kernel already buffered the    ───
//       header/menuboxes above us — discard all of it, we own this response). ─
while ( ob_get_level() > 0 )
    ob_end_clean();
ob_implicit_flush( true );
set_time_limit( 0 );           // spider can take a while
ignore_user_abort( false );    // stop streaming if client disconnects

// ── 3. SSE headers ───────────────────────────────────────────────────────────
header( 'Content-Type: text/event-stream; charset=utf-8' );
header( 'Cache-Control: no-cache, no-store, must-revalidate' );
header( 'Pragma: no-cache' );
header( 'Expires: 0' );
header( 'X-Accel-Buffering: no' );   // nginx: disable proxy buffering
header( 'Connection: keep-alive' );

// ── 4. Locate grok.php ───────────────────────────────────────────────────────
// __DIR__ inside an included file == directory of *this* file:
//   kernel/ezsitemanager/admin  →  3 dirname() calls up == site root
$siteRoot = dirname( dirname( dirname( __DIR__ ) ) );
$grok     = $siteRoot . '/bin/php/grok.php';

if ( !file_exists( $grok ) )
{
    echo "data: [ERROR] grok.php not found: {$grok}\n\n";
    echo "data: __DONE__\n\n";
    flush();
    eZExecution::setCleanExit();
    exit();
}

// ── 5. Padding flush — fills Apache/fcgid send-buffer so first bytes arrive ──
//       before the whole output is produced (avoids blank page while waiting).
echo ': padding ' . str_repeat( ' ', 2048 ) . "\n\n";
flush();

// ── 6. Spawn grok preload and stream its stdout/stderr ───────────────────────
$cmd    = PHP_BINARY . ' ' . escapeshellarg( $grok ) . ' preload 2>&1';
$handle = popen( $cmd, 'r' );

if ( $handle === false )
{
    echo "data: [ERROR] popen() failed — could not start grok preload\n\n";
    echo "data: __DONE__\n\n";
    flush();
    eZExecution::setCleanExit();
    exit();
}

while ( !feof( $handle ) )
{
    $line = fgets( $handle, 4096 );
    if ( $line === false )
        break;

    // rtrim strips \r\n — SSE data fields must not contain bare newlines
    $line = rtrim( $line, "\r\n" );
    if ( $line === '' )
        continue;

    echo 'data: ' . $line . "\n\n";
    flush();
}

$exitCode = pclose( $handle );

echo "data: \n\n";
echo "data: \xe2\x94\x80\xe2\x94\x80 Finished (exit: {$exitCode}) \xe2\x94\x80\xe2\x94\x80\n\n";
echo "data: __DONE__\n\n";
flush();

eZExecution::setCleanExit();
exit();

?>