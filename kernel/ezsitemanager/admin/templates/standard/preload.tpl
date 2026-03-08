<h1>{intl-preload_site}</h1>

<hr noshade="noshade" size="4" />

<div id="preload-controls" style="margin-bottom:10px;">
  <button class="stdbutton" id="btnRunPreload" onclick="startPreload(this)">&gt; Run grok preload</button>
  &nbsp;&nbsp;<button class="stdbutton" id="btnStopPreload" onclick="stopPreload()" style="display:none;">&#9632; Stop</button>
  &nbsp;&nbsp;<span id="preload-status" style="font-size:11px; font-weight:bold;"></span>
  &nbsp;&nbsp;<span id="preload-timer" style="font-size:10px; color:#888; font-family:monospace;"></span>
</div>

<div id="preload-debug" style="font-size:10px; color:#888; font-family:monospace; margin-bottom:6px; display:none;">
  Stream URL: <span id="preload-url" style="color:#bbb;"></span>
</div>

<div id="preload-output" style="display:none; margin-top:8px;">
  <div style="background:#1a1a1a; border:1px solid #555; border-radius:3px; padding:0;">
    <div style="background:#2a2a2a; color:#aaa; font-size:10px; font-family:monospace; padding:4px 10px; border-bottom:1px solid #444; display:flex; justify-content:space-between; align-items:center;">
      <span>grok preload &mdash; live output</span>
      <span id="preload-linecnt" style="color:#666;">0 lines</span>
    </div>
    <pre id="preload-log" style="
      background:#1a1a1a;
      color:#c8ffa7;
      font-family:'Courier New', Courier, monospace;
      font-size:11px;
      line-height:1.5;
      padding:10px 14px;
      margin:0;
      max-height:560px;
      overflow-y:auto;
      white-space:pre-wrap;
      word-break:break-all;
    "></pre>
  </div>
</div>

<script type="text/javascript">
(function() {

    var STREAM_URL = window.location.protocol + '//' + window.location.host + '/sitemanager/preload/stream';
    var es         = null;
    var timerID    = null;
    var startTime  = null;
    var lineCount  = 0;

    // ── DOM refs (resolved lazily after page load) ────────────────────────────
    function ui( id ) { return document.getElementById( id ); }

    // ── Timer display ─────────────────────────────────────────────────────────
    function tickTimer()
    {
        if ( startTime === null ) return;
        var elapsed = Math.round( ( Date.now() - startTime ) / 1000 );
        var m = Math.floor( elapsed / 60 );
        var s = elapsed % 60;
        ui( 'preload-timer' ).textContent = 'elapsed: ' + m + ':' + ( s < 10 ? '0' : '' ) + s;
    }

    function startTimer()
    {
        startTime = Date.now();
        tickTimer();
        timerID = setInterval( tickTimer, 1000 );
    }

    function stopTimer()
    {
        if ( timerID ) { clearInterval( timerID ); timerID = null; }
    }

    // ── Append a line to the terminal ─────────────────────────────────────────
    function appendLine( text, cssColor )
    {
        var log = ui( 'preload-log' );
        if ( cssColor )
        {
            // Wrap in a span so we can colorize error/warn lines
            var span = document.createElement( 'span' );
            span.style.color = cssColor;
            span.textContent = text + '\n';
            log.appendChild( span );
        }
        else
        {
            log.textContent += text + '\n';
        }
        log.scrollTop = log.scrollHeight;
        lineCount++;
        ui( 'preload-linecnt' ).textContent = lineCount + ' line' + ( lineCount !== 1 ? 's' : '' );
    }

    // ── Public: start ─────────────────────────────────────────────────────────
    window.startPreload = function( btn )
    {
        if ( es !== null ) return;    // already running

        lineCount   = 0;
        var log     = ui( 'preload-log' );
        log.textContent = '';
        // Clear any old colored spans
        while ( log.firstChild ) log.removeChild( log.firstChild );

        ui( 'preload-output' ).style.display = 'block';
        ui( 'preload-debug' ).style.display   = 'block';
        ui( 'preload-url' ).textContent        = STREAM_URL;
        ui( 'preload-linecnt' ).textContent    = '0 lines';
        ui( 'btnStopPreload' ).style.display   = 'inline';
        btn.disabled = true;

        setStatus( '&#9203; Connecting to stream\u2026', '#e07800' );
        startTimer();

        if ( typeof EventSource === 'undefined' )
        {
            appendLine( '[BROWSER ERROR] This browser does not support Server-Sent Events.', '#ff6b6b' );
            appendLine( 'Please use Chrome, Firefox, Safari, or Edge (not IE).', '#ff9944' );
            done( btn, false );
            return;
        }

        appendLine( '> Connecting: ' + STREAM_URL, '#888' );

        es = new EventSource( STREAM_URL );

        // Connection opened — server accepted the SSE request
        es.onopen = function()
        {
            setStatus( '&#9654; Streaming\u2026', '#00aa44' );
            appendLine( '> Connection established', '#aaaaaa' );
        };

        es.onmessage = function( e )
        {
            if ( e.data === '__DONE__' )
            {
                done( btn, true );
                return;
            }

            // Color-code known line patterns
            var text  = e.data;
            var color = null;

            if ( text.indexOf( '[ERROR]' )   !== -1 ) color = '#ff6b6b';
            else if ( text.indexOf( 'ERROR' ) !== -1 ) color = '#ff9944';
            else if ( text.indexOf( 'HTTP 5' ) !== -1 ) color = '#ff6b6b';
            else if ( text.indexOf( 'HTTP 4' ) !== -1 ) color = '#ffcc00';
            else if ( text.indexOf( 'HTTP 200' ) !== -1 ) color = '#88ff88';
            else if ( text.indexOf( '===' )   !== -1 ) color = '#88ccff';
            else if ( text.indexOf( '\u2500\u2500' ) !== -1 ) color = '#aaaaaa';   // ── lines

            appendLine( text, color );
        };

        es.onerror = function( evt )
        {
            // EventSource readyState values: 0=CONNECTING, 1=OPEN, 2=CLOSED
            var rs = es ? es.readyState : -1;

            if ( rs === EventSource.CLOSED )
            {
                // Server closed the stream cleanly after __DONE__
                return;
            }

            // Distinguish network/auth failure from server crash
            var msg;
            if ( rs === EventSource.CONNECTING )
                msg = '[STREAM ERROR] Connection lost — server may have crashed or timed out.';
            else
                msg = '[STREAM ERROR] Could not connect to: ' + STREAM_URL +
                      '\n  Make sure you are logged in and the admin is reachable.' +
                      '\n  readyState=' + rs;

            appendLine( msg, '#ff6b6b' );
            done( btn, false );
        };
    };

    // ── Public: stop ──────────────────────────────────────────────────────────
    window.stopPreload = function()
    {
        if ( es )
        {
            es.close();
            es = null;
        }
        appendLine( '> Stopped by user', '#ffcc00' );
        var btn = ui( 'btnRunPreload' );
        done( btn, false );
    };

    // ── Internal: finish ────────────────────────────────────────────────────—
    function done( btn, success )
    {
        if ( es ) { es.close(); es = null; }
        stopTimer();

        ui( 'btnStopPreload' ).style.display = 'none';
        btn.disabled = false;

        if ( success )
            setStatus( '&#10004; Done!', 'green' );
        else
            setStatus( '&#10006; Finished with errors — see output above', '#cc0000' );
    }

    function setStatus( html, color )
    {
        var el = ui( 'preload-status' );
        el.style.color   = color;
        el.innerHTML     = html;
    }

}());
</script>


<div id="preload-output" style="display:none; margin-top:8px;">
  <div style="background:#1a1a1a; border:1px solid #555; border-radius:3px; padding:0;">
    <div style="background:#333; color:#aaa; font-size:10px; font-family:monospace; padding:3px 8px; border-bottom:1px solid #555;">
      grok preload &mdash; live output
    </div>
    <pre id="preload-log" style="
      background:#1a1a1a;
      color:#c8ffa7;
      font-family:'Courier New', Courier, monospace;
      font-size:11px;
      line-height:1.5;
      padding:10px 14px;
      margin:0;
      max-height:520px;
      overflow-y:auto;
      white-space:pre-wrap;
      word-break:break-all;
    "></pre>
  </div>
</div>

<script type="text/javascript">
// ── Preload Site — SSE terminal ────────────────────────────────────────────
(function() {

    var STREAM_URL = window.location.protocol + '//' + window.location.host + '/sitemanager/preload/stream';
    var es         = null;

    window.startPreload = function( btn ) {
        if ( es !== null ) {
            // Already running — ignore double-clicks
            return;
        }

        var status = document.getElementById( 'preload-status' );
        var output = document.getElementById( 'preload-output' );
        var log    = document.getElementById( 'preload-log' );

        btn.disabled         = true;
        btn.innerHTML        = '&#9654; {intl-run_preload}';
        status.style.color   = '#e07800';
        status.innerHTML     = '&#9203; {intl-preload_running}';
        log.textContent      = '';
        output.style.display = 'block';

        if ( typeof EventSource === 'undefined' ) {
            log.textContent  = '[ERROR] Your browser does not support Server-Sent Events.\n'
                             + 'Please use a modern browser (Chrome, Firefox, Safari, Edge).';
            btn.disabled     = false;
            status.innerHTML = '';
            return;
        }

        es = new EventSource( STREAM_URL );

        es.onmessage = function( e ) {
            if ( e.data === '__DONE__' ) {
                done( btn, status, log, true );
                return;
            }
            // Ignore the SSE keep-alive padding comment lines (start with ':')
            log.textContent += e.data + '\n';
            // Auto-scroll to bottom
            log.scrollTop    = log.scrollHeight;
        };

        es.onerror = function() {
            // EventSource fires onerror both for network issues AND normal close.
            // If readyState is CLOSED, the server ended the stream intentionally.
            if ( es !== null && es.readyState === EventSource.CLOSED ) {
                return;
            }
            done( btn, status, log, false );
        };
    };

    function done( btn, status, log, success ) {
        if ( es !== null ) {
            es.close();
            es = null;
        }
        btn.disabled        = false;
        btn.innerHTML       = '&#9654; {intl-run_preload}';
        log.scrollTop       = log.scrollHeight;

        if ( success ) {
            status.style.color = 'green';
            status.innerHTML   = '&#10004; {intl-preload_done}';
        } else {
            status.style.color = '#cc0000';
            status.innerHTML   = '&#10006; {intl-preload_error}';
            log.textContent   += '\n[Connection lost or server error — check the server log]\n';
        }
    }

}());
</script>
