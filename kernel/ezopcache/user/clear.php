<?php

// Suppress any output that could interfere
ini_set( 'display_errors', '0' );

$cleared   = false;
$supported = function_exists( 'opcache_reset' );

if ( $supported )
{
    $cleared = opcache_reset();
}

$status = function_exists( 'opcache_get_status' ) ? opcache_get_status( false ) : false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>OPcache Clear</title>
<style>
body { font-family: Arial, sans-serif; padding: 2em; background: #f5f5f5; }
.box { background: #fff; border: 1px solid #ddd; padding: 1.5em 2em; max-width: 500px; border-radius: 4px; }
h2 { margin-top: 0; }
.ok  { color: #2a7a2a; }
.err { color: #cc0000; }
table { border-collapse: collapse; margin-top: 1em; width: 100%; }
td, th { text-align: left; padding: 4px 8px; border-bottom: 1px solid #eee; }
th { background: #f0f0f0; }
</style>
</head>
<body>
<div class="box">
<h2>OPcache</h2>

<?php if ( !$supported ): ?>
<p class="err">OPcache extension is not loaded / not available.</p>

<?php elseif ( $cleared ): ?>
<p class="ok"><strong>OPcache cleared successfully.</strong></p>

<?php else: ?>
<p class="err">opcache_reset() returned false — OPcache may be disabled or restricted.</p>
<?php endif; ?>

<?php if ( $status !== false ): ?>
<table>
<tr><th colspan="2">Current status</th></tr>
<tr><td>Enabled</td><td><?php echo $status['opcache_enabled'] ? 'Yes' : 'No'; ?></td></tr>
<tr><td>Cached scripts</td><td><?php echo $status['opcache_statistics']['num_cached_scripts'] ?? '—'; ?></td></tr>
<tr><td>Hits</td><td><?php echo $status['opcache_statistics']['hits'] ?? '—'; ?></td></tr>
<tr><td>Misses</td><td><?php echo $status['opcache_statistics']['misses'] ?? '—'; ?></td></tr>
<tr><td>Memory used</td><td><?php echo round( ( $status['memory_usage']['used_memory'] ?? 0 ) / 1048576, 2 ); ?> MB</td></tr>
<tr><td>Memory free</td><td><?php echo round( ( $status['memory_usage']['free_memory'] ?? 0 ) / 1048576, 2 ); ?> MB</td></tr>
</table>
<?php endif; ?>

<p style="margin-top:1.5em"><a href="/opcache/clear/">Clear again</a></p>
</div>
</body>
</html>
<?php exit(); ?>
