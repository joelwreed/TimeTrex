{include file="header.tpl"}
<script	language=JavaScript>

{literal}
function viewRequest(requestID,level) {
	try {
		window.open('{/literal}{$BASE_URL}{literal}request/ViewRequest.php?id='+ encodeURI(requestID) +'&selected_level='+ encodeURI(level),"Request_"+ requestID,"toolbar=0,status=1,menubar=0,scrollbars=1,fullscreen=no,width=580,height=470,resizable=1");
	} catch (e) {
		//DN
	}
}
function viewTimeSheetVerification(timesheet_verify_id,level) {
	try {
		//window.open('{/literal}{$BASE_URL}{literal}timesheet/ViewTimeSheetVerification.php?id='+ encodeURI(timesheet_verify_id) +'&selected_level='+ encodeURI(level),"TimeSheet_"+ timesheet_verify_ID,"toolbar=0,status=1,menubar=0,scrollbars=1,fullscreen=no,width=580,height=470,resizable=1");
		window.open('{/literal}{$BASE_URL}{literal}timesheet/ViewTimeSheetVerification.php?id='+ encodeURI(timesheet_verify_id) +'&selected_level='+ encodeURI(level),"TimeSheet_"+ timesheet_verify_id,"toolbar=0,status=1,menubar=0,scrollbars=1,fullscreen=no,width=580,height=470,resizable=1");
	} catch (e) {
		//DN
	}
}

{/literal}
</script>

<div id="rowContent">
  <div id="titleTab"><div class="textTitle"><span class="textTitleSub">{$title}</span></div>
</div>
<div id="rowContentInner">
		<table class="tblList">

		<form method="get" action="{$smarty.server.SCRIPT_NAME}">
				<tr>
					<td class="tblPagingLeft" colspan="5" align="right">
						{include file="pager.tpl" pager_data=$paging_data}
					</td>
				</tr>

				{if $permission->Check('request','authorize')}
				<tr class="tblHeader">
					<td colspan="5">
						{t}Pending Requests{/t}
					</td>
				</tr>

				{foreach from=$request_levels key=request_level_display item=request_level name=request_levels}
					{if $smarty.foreach.request_levels.first}
					<tr class="tblHeader">
						<td colspan="5">
					{/if}

					{if $selected_request_level == $request_level}<span style="background-color:#33CCFF">{/if}<a href="{urlbuilder script="AuthorizationList.php" values="selected_levels[request]=$request_level_display" merge="FALSE"}">{t}Level{/t} {$request_level_display}</a>{if $selected_request_level == $request_level}</span>{/if}
					{if !$smarty.foreach.request_levels.last}
						|
					{/if}

					{if $smarty.foreach.request_levels.last}
						</td>
					</tr>
					{/if}
				{/foreach}

				{foreach from=$requests item=request name=requests}
					{if $smarty.foreach.requests.first}
					<tr class="tblHeader">
						<td>
							{capture assign=label}{t}Employee{/t}{/capture}
							{include file="column_sort.tpl" label=$label sort_column="user_id" current_column="$sort_column" current_order="$sort_order"}
						</td>
						<td>
							{capture assign=label}{t}Date{/t}{/capture}
							{include file="column_sort.tpl" label=$label sort_column="date_stamp" current_column="$sort_column" current_order="$sort_order"}
						</td>
						<td>
							{capture assign=label}{t}Type{/t}{/capture}
							{include file="column_sort.tpl" label=$label sort_column="type_id" current_column="$sort_column" current_order="$sort_order"}
						</td>
						<td>
							{t}Functions{/t}
						</td>
{*
						<td>
							<input type="checkbox" class="checkbox" name="select_all" onClick="CheckAll(this)"/>
						</td>
*}
					</tr>
					{/if}
					{cycle assign=row_class values="tblDataWhite,tblDataGrey"}
					<tr class="{$row_class}">
						<td>
							{$request.user_full_name}
						</td>
						<td>
							{getdate type="DATE" epoch=$request.date_stamp}
						</td>
						<td>
							{$request.type}
						</td>
						<td>
							{assign var="request_id" value=$request.id}
							{assign var="selected_level" value=$selected_levels.request}
							<a href="javascript:viewRequest({$request_id},{$selected_level|default:0})">{t}View{/t}</a>
						</td>
{*
						<td>
							<input type="checkbox" class="checkbox" name="ids[]" value="{$company.id}">
						</td>
*}
					</tr>
				{foreachelse}
					<tr class="tblDataWhite">
						<td colspan="5">
							{t}0 Requests found.{/t}
						</td>
					</tr>
				{/foreach}
				{/if}

				<tr>
					<td colspan="6">
						<br>
					</td>
				</tr>

				{if $permission->Check('punch','authorize')}
				<tr class="tblHeader">
					<td colspan="5">
						{t}Pending TimeSheets{/t}
					</td>
				</tr>

				{foreach from=$timesheet_levels key=timesheet_level_display item=timesheet_level name=timesheet_levels}
					{if $smarty.foreach.timesheet_levels.first}
					<tr class="tblHeader">
						<td colspan="5">
					{/if}

					{if $selected_timesheet_level == $timesheet_level}<span style="background-color:#33CCFF">{/if}<a href="{urlbuilder script="AuthorizationList.php" values="selected_levels[timesheet]=$timesheet_level_display" merge="FALSE"}">{t}Level{/t} {$timesheet_level_display}</a>{if $selected_timesheet_level == $timesheet_level}</span>{/if}
					{if !$smarty.foreach.timesheet_levels.last}
						|
					{/if}

					{if $smarty.foreach.timesheet_levels.last}
						</td>
					</tr>
					{/if}
				{/foreach}

				{foreach from=$timesheets item=timesheet name=timesheets}
					{if $smarty.foreach.timesheets.first}
					<tr class="tblHeader">
						<td>
							{capture assign=label}{t}Employee{/t}{/capture}
							{include file="column_sort.tpl" label=$label sort_column="user_id" current_column="$sort_column" current_order="$sort_order"}
						</td>
						<td colspan="2">
							{capture assign=label}{t}Pay Period{/t}{/capture}
							{include file="column_sort.tpl" label=$label sort_column="start_date" current_column="$sort_column" current_order="$sort_order"}
						</td>
						<td>
							{t}Functions{/t}
						</td>
{*
						<td>
							<input type="checkbox" class="checkbox" name="select_all" onClick="CheckAll(this)"/>
						</td>
*}
					</tr>
					{/if}
					{cycle assign=row_class values="tblDataWhite,tblDataGrey"}
					<tr class="{$row_class}">
						<td>
							{$timesheet.user_full_name}
						</td>
						<td colspan="2">
							{getdate type="DATE" epoch=$timesheet.pay_period_start_date} - {getdate type="DATE" epoch=$timesheet.pay_period_end_date}
						</td>
						<td>
							{assign var="timesheet_id" value=$timesheet.id}
							{assign var="selected_level" value=$selected_levels.timesheet}
							<a href="javascript:viewTimeSheetVerification({$timesheet_id},{$selected_level|default:0})">{t}View{/t}</a>
						</td>
{*
						<td>
							<input type="checkbox" class="checkbox" name="ids[]" value="{$company.id}">
						</td>
*}
					</tr>
				{foreachelse}
					<tr class="tblDataWhite">
						<td colspan="5">
							{t}0 TimeSheets found.{/t}
						</td>
					</tr>
				{/foreach}
				{/if}

				<tr>
					<td class="tblPagingLeft" colspan="5" align="right">
						{include file="pager.tpl" pager_data=$paging_data}
					</td>
				</tr>

			<input type="hidden" name="sort_column" value="{$sort_column}">
			<input type="hidden" name="sort_order" value="{$sort_order}">
			<input type="hidden" name="page" value="{$paging_data.current_page}">
			</table>
		</form>
	</div>
</div>
{include file="footer.tpl"}