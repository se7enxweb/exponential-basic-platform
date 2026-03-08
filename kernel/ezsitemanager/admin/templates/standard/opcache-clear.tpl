<h1>{intl-opcache_clear}</h1>

<style>
{literal}
body { /* background: #f5f5f5; */ }
.box { background: #fff; border: 1px solid #ddd; padding: 1.5em 2em; max-width: 500px; border-radius: 4px; }
.ok  { color: #2a7a2a; }
.err { color: #cc0000; }
table { border-collapse: collapse; margin-top: 1em; width: 100%; }
th { background: #f0f0f0; }
{/literal}
</style>

<hr noshade="noshade" size="4" />

<form method="post" action="{www_dir}{index}/sitemanager/cache/opcache" >

<input class="stdbutton" type="submit" name="ClearOpcache" value="{intl-clear_opcache}" />

<hr noshade="noshade" size="4" />

<!-- BEGIN opcache_results_tpl -->
<div class="box">
<p class="ok"><strong>{opcache_return}</strong></p>
</div>
<!-- END opcache_results_tpl -->

</form>
