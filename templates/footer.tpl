<!-- Begin Footer -->
<a name="bottom">
<br>
<div id="rowFooter">

	<div class="textFooter">
		{t}Server response time:{/t} {php}echo sprintf('%01.3f',microtime(true)-$this->_tpl_vars['global_script_start_time']);{/php} {t}seconds.{/t}
		<br>
		Copyright &copy; {$smarty.now|date_format:"%Y"} <a href="http://www.timetrex.com" class="footerLink">{$APPLICATION_NAME|default:"TimeTrex"}</a>.
		{if getTTProductEdition() == 10}
			The Program is provided AS IS, without warranty. Licensed under <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">AGPLv3.</a><br>
			This program is free software; you can redistribute it and/or modify it under the terms of the<br>
			<a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">GNU Affero General Public License version 3</a> as published by the Free Software Foundation including the additional permission set forth in the source code header.
		{else}
			All Rights Reserved.
		{/if}
		<br>
		<br>
		{* REMOVING OR CHANGING THIS LOGO IS IN STRICT VIOLATION OF THE LICENSE AGREEMENT *}
		<a href="http://www.timetrex.com"><img src="{$IMAGES_URL}powered_by.jpg" alt="Time and Attendance"></a>
	</div>
</div>

<div>
	{php}
		Debug::writeToLog();
		Debug::Display();
		if (Debug::getEnableDisplay() == TRUE AND Debug::getVerbosity() >= 10) {
			{/php}
			{$profiler->printTimers(TRUE)}
			{php}
		}
	{/php}
</div>

</div>
		{if $config_vars.debug.production == 1 AND $config_vars.other.disable_google_analytics != 1}<script src="http{if $smarty.server.HTTPS == TRUE}s://ssl{else}://www{/if}.google-analytics.com/urchin.js" type="text/javascript"></script><script type="text/javascript">_uacct="UA-333702-3"; __utmSetVar('Company: {if is_object($current_company)}{$current_company->getName()|escape}{else}N/A{/if}'); __utmSetVar('Edition: {if getTTProductEdition() == 20}Professional{elseif getTTProductEdition() == 15}Business{else}Standard{/if}'); __utmSetVar('Key: {if isset($system_settings)}{$system_settings.registration_key}{else}N/A{/if}'); __utmSetVar('Host: {$smarty.server.HTTP_HOST}'); __utmSetVar('Version: {$APPLICATION_VERSION}'); urchinTracker();</script><img src="{$IMAGES_URL}spacer.gif">{/if}
</body>
</html>