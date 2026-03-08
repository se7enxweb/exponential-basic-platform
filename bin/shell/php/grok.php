#!/usr/bin/env php
<?php
/**
 * grok.php — site control CLI program
 *
 * Usage:
 *   ./bin/shell/php/grok.php clr       — clears all caches
 *   ./bin/shell/php/grok.php preload   — warms the four main section pages then
 *                                        spiders the entire site via wget
 */

$command = $argv[1] ?? '';

$root = dirname( dirname( dirname( __DIR__ ) ) );

require_once $root . '/autoload.php';
$_siteIni  = eZINI::instance( 'site.ini' );
$_protocol = $_siteIni->variable( 'site', 'UserSiteProtocol' );
$_host     = $_siteIni->variable( 'site', 'UserSiteURL' );
$site      = $_protocol . '://' . $_host;
unset( $_siteIni, $_protocol, $_host );

// Build section list from site.ini [site] URLTranslationKeyword.
// Each entry becomes a root-level URL to warm; entries that start with '/'
// are already absolute paths and are used as-is; bare keywords become /$keyword/.
$_siteIni2  = eZINI::instance( 'site.ini' );
$_keywords  = $_siteIni2->variable( 'site', 'URLTranslationKeyword' );
unset( $_siteIni2 );
$sections = array_map(
    function ( $kw ) use ( $site ) {
        $kw = trim( $kw, '/ ' );
        return $site . '/' . $kw . '/';
    },
    array_filter(
        explode( ';', $_keywords ),
        function ( $kw ) {
            $kw = trim( $kw );
            // skip empty entries
            return $kw !== '';
        }
    )
);
unset( $_keywords );

/*
  ##
  ## Master Control Program's Switcher
  ##
*/
switch ( $command )
{
    case 'clear':
    case 'clear:cache':
    case 'cache:clear':
    case 'clr':
        echo "Clearing caches...\n";
        passthru( "cd " . escapeshellarg( $root ) . " && ./bin/shell/clearcache.sh" );
        echo "Done.\n";
        break;

    case 'preload':
        $host    = parse_url( $site, PHP_URL_HOST );
        $tmpFile = '/tmp/grok_bench.html';

        /**
         * Fetch a URL, capture body in-memory (no file written), return metrics.
         * Body is captured via stdout; a sentinel delimiter separates it from
         * the curl write-out line so the two can be split cleanly.
         */
        $fetch = function( $url ) use ( $tmpFile )
        {
            $t0   = microtime( true );
            $meta = shell_exec(
                "curl -sk -o " . escapeshellarg( $tmpFile ) .
                " -w '%{http_code} %{time_total}'" .
                " --max-time 30 " . escapeshellarg( $url )
            );
            $wall_ms = round( ( microtime( true ) - $t0 ) * 1000 );

            [ $http, $curl_time ] = array_pad( explode( ' ', trim( $meta ) ), 2, '?' );

            $html = file_exists( $tmpFile ) ? file_get_contents( $tmpFile ) : '';

            $extract = function( $key ) use ( $html )
            {
                if ( preg_match( '/\b' . preg_quote( $key, '/' ) . '\s*:\s*([0-9.]+)/', $html, $m ) )
                    return $m[1];
                return null;
            };

            return [
                'http'         => $http,
                'wall_ms'      => $wall_ms,
                'kernel_init'  => $extract( 'kernel-init' ),
                'kernel_run'   => $extract( 'kernel-run' ),
                'get_content'  => $extract( 'get-content' ),
                'total_ms'     => $extract( 'total' ),
                'peak_memory'  => $extract( 'peak-memory' ),
                'db_queries'   => $extract( 'db-queries' ),
                'db_time'      => $extract( 'db-time' ),
                'html'         => $html,
            ];
        };

        // Step 1: warm each section page and report full metrics
        echo "=== Warming section pages ===\n";
        foreach ( $sections as $url )
        {
            echo "  GET $url\n";
            flush();

            $r = $fetch( $url );

            $flag = $r['http'] === '200' ? '' : '  <<< !!!';
            echo "    HTTP {$r['http']}  wall: {$r['wall_ms']}ms{$flag}\n";

            // Only display perf lines when the site actually emits the data.
            $hasDb   = $r['db_queries']  !== null || $r['db_time']     !== null;
            $hasKern = $r['kernel_init'] !== null || $r['kernel_run']  !== null
                    || $r['get_content'] !== null || $r['total_ms']    !== null;
            $hasMem  = $r['peak_memory'] !== null;

            if ( $hasDb )
                echo "    db-queries: " . ( $r['db_queries'] ?? '–' ) .
                     "  db-time: "     . ( $r['db_time']    ?? '–' ) . "ms\n";

            if ( $hasKern )
                echo "    kernel-init: " . ( $r['kernel_init']  ?? '–' ) . "ms" .
                     "  kernel-run: "   . ( $r['kernel_run']   ?? '–' ) . "ms" .
                     "  get-content: " . ( $r['get_content']  ?? '–' ) . "ms" .
                     "  total: "        . ( $r['total_ms']     ?? '–' ) . "ms\n";

            if ( $hasMem )
                echo "    peak-memory: {$r['peak_memory']}MB\n";

            echo "\n";

            if ( $r['http'] === '500' )
            {
                echo "    --- ERROR (first 5 lines) ---\n";
                $lines = array_slice( explode( "\n", strip_tags( $r['html'] ) ), 0, 5 );
                foreach ( $lines as $line )
                    if ( trim( $line ) !== '' )
                        echo "    " . trim( $line ) . "\n";
            }
            echo "\n";
            flush();
        }

        // Step 2: spider the whole site; -nv prints one line per URL fetched
        echo "=== Spidering $site/ (level 3) ===\n";
        flush();
        $reject = implode( ',', [
            // styles & scripts
            'js', 'css', 'map',
            // images
            'png', 'gif', 'jpg', 'jpeg', 'webp', 'svg', 'ico', 'bmp', 'tif', 'tiff',
            'avif', 'heic', 'heif', 'jxl', 'psd', 'ai', 'eps', 'raw', 'cr2', 'nef',
            // fonts
            'woff', 'woff2', 'ttf', 'eot', 'otf',
            // video
            'mp4', 'webm', 'ogv', 'ogg', 'avi', 'mov', 'mkv', 'flv', 'wmv', 'mpg',
            'mpeg', 'mp2', 'm4v', 'm2v', 'ts', 'mts', 'm2ts', 'vob', 'rm', 'rmvb',
            '3gp', '3g2', 'asf', 'divx', 'xvid', 'f4v', 'swf',
            // audio
            'mp3', 'wav', 'flac', 'aac', 'm4a', 'wma', 'aiff', 'aif', 'ape', 'opus',
            'ra', 'mid', 'midi', 'amr', 'au', 'mka',
            // documents / archives
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp',
            'rtf', 'txt', 'csv', 'epub', 'mobi',
            'zip', 'tar', 'gz', 'tgz', 'bz2', 'xz', 'zst', 'rar', '7z', 'cab',
            'iso', 'dmg', 'img', 'bin', 'deb', 'rpm', 'apk', 'exe', 'msi', 'pkg',
            // data / misc
            'json', 'xml', 'rss', 'atom', 'yaml', 'yml', 'sql', 'db', 'sqlite',
        ] );

        passthru(
            "wget --no-check-certificate --spider --recursive --level=3" .
            " --no-directories --delete-after -P /tmp -nv" .
            " --reject=" . escapeshellarg( $reject ) .
            " --reject-regex='/(stats|calendar|groupeventcalendar)/'" .
            " --domains=" . escapeshellarg( $host ) .
            " " . escapeshellarg( $site . "/" ) .
            " 2>&1 | grep -Ev '^(unlink:|Removing )'"
        );
        echo "\nDone.\n";
        break;

    default:
        echo "Usage: ./bin/shell/php/grok.php <command>\n\n";
        echo "Commands:\n";
        echo "  clr       Clear all caches (runs ./bin/shell/clearcache.sh)\n";
        echo "  preload   Warm four section pages then spider the whole site\n";
        exit( 1 );
}
