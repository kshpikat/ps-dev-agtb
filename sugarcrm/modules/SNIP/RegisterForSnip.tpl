<h2>SNIP</h2>

<table width="100%" cellspacing="0" cellpadding="0" border="0" class="edit view">
	<tr>
	<td>
		{if $SNIP_STATUS=='notpurchased'}
			{$MOD.LBL_SNIP_DESCRIPTION}. {$MOD.LBL_SNIP_DESCRIPTION_SUMMARY}.<br><br>
			{$MOD.LBL_SNIP_PURCHASE_SUMMARY}. <a href="{$SNIP_PURCHASEURL}">{$MOD.LBL_SNIP_PURCHASE}</a>.
		{else}
		
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td scope="row">
					<slot>{$MOD.LBL_SNIP_STATUS}</slot>
				</td>

				<td>
					<form name='ToggleSnipStatus' method="POST" action="index.php?module=SNIP&action=RegisterForSnip">
					<input type='hidden' id='save_config' name='save_config' value='0'/>					

					{if $SNIP_STATUS == 'purchased'}
						<div id='snip_title'><span style='color:green;font-weight:bold'>{$MOD.LBL_SNIP_STATUS_OK}</span></div>
						<div style='clear:both'></div>
						<div id='snip_summary'>{$MOD.LBL_SNIP_STATUS_OK_SUMMARY}</div>
					{elseif $SNIP_STATUS == 'down'}
						<div id='snip_title'><span id='snip_title_error'>{$MOD.LBL_SNIP_STATUS_FAIL}</span></div>
						<div style='clear:both'></div>
						<div id='snip_summary'>{$MOD.LBL_SNIP_STATUS_FAIL_SUMMARY}.</div>
						
					{elseif $SNIP_STATUS == 'purchased_error'}
						<div id='snip_title'><span id='snip_title_error'>{$MOD.LBL_SNIP_STATUS_ERROR}</span></div>
						<div style='clear:both'></div>
						<div id='snip_summary'>{$MOD.LBL_SNIP_STATUS_ERROR_SUMMARY}<br>
						<div id='snip_summary_error'>{$SNIP_ERROR_MESSAGE}</div></div>
					{/if}
					</form>
					<br>
				</td>
			</tr>
			<tr>
				<td width="15%" scope="row">
					<slot>{$MOD.LBL_SNIP_SUGAR_URL}</slot>
				</td>
				<td width="85%">
					<slot>{$SUGAR_URL}</slot>
				</td>
			</tr>
			<tr>
				<td scope="row">
					<slot>{$MOD.LBL_SNIP_CALLBACK_URL}</slot>
				</td>
				<td>
					<slot>{$SNIP_URL}</slot>
				</td>
			</tr>
		</table>
		{/if}
	</td>
	</tr>
</table>

				In order to use SNIP, you must <a href="{$SNIP_PURCHASEURL}">purchase a license</a> for your SugarCRM instance.
			{else}

			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td scope="row">
						<slot>SNIP Status</slot>

	#snip_title_error {
		color:red;
		font-weight:bold;
	}

					<td>
						<form name='ToggleSnipStatus' method="POST" action="index.php?module=SNIP&action=RegisterForSnip">
						<input type='hidden' id='save_config' name='save_config' value='0'/>


						{if $SNIP_STATUS == 'purchased'}
							<div style='float:left;margin-bottom:5px;font-size:15px;display:inline;'><span style='color:green;font-weight:bold'>Enabled (Service Online)</span></div>
							<div style='clear:both'></div>
							<div style='float:left;margin-bottom:10px'>This instance has a SNIP license, and the service is running.</div>
						{elseif $SNIP_STATUS == 'down'}
							<div style='float:left;margin-bottom:5px;font-size:15px;display:inline;'><span style='color:red;font-weight:bold'>Cannot connect to SNIP server</span></div>
							<div style='clear:both'></div>
							<div style='float:left;margin-bottom:10px'>Sorry, the SNIP service is currently unavailable (either the service is down or the connection failed on your end).</div>

						{elseif $SNIP_STATUS == 'purchased_error'}
							<div style='float:left;margin-bottom:5px;font-size:15px;display:inline;'><span style='color:red;font-weight:bold'>Error returned from SNIP server</span></div>
							<div style='clear:both'></div>
							<div style='float:left;margin-bottom:10px'>This instance has a valid SNIP license, but the SNIP server returned the following error message:<br><div style='width:100%;background-color:#ffaa99;margin-top:3px;padding:2px;font-weight:bold'>{$SNIP_ERROR_MESSAGE}</div></div>
						{/if}
						</form>
						<br>
					</td>
				</tr>
				<tr>
					<td width="15%" scope="row">
						<slot>{$MOD.LBL_SNIP_SUGAR_URL}</slot>
					</td>
					<td width="85%">
						<slot>{$SUGAR_URL}</slot>
					</td>
				</tr>
				<tr>
					<td scope="row">
						<slot>{$MOD.LBL_SNIP_CALLBACK_URL}</slot>
					</td>
					<td>
						<slot>{$SNIP_URL}</slot>
					</td>
				</tr>
			</table>
			{/if}
		</td>
		</tr>
	</table>