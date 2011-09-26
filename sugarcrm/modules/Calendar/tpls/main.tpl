<link type="text/css" href="modules/Calendar/Cal.css" rel="stylesheet" />
	
<script type="text/javascript">
	YAHOO.util.Event.onAvailable('cal_loaded',function(){literal}{{/literal}
	
		CAL.pview = "{$pview}";
		CAL.t_step = {$t_step};
		CAL.current_user_id = "{$current_user_id}";	
		CAL.current_user_name = "{$current_user_name}";
		CAL.time_format = "{$time_format}";
		CAL.items_draggable = "{$items_draggable}";
		CAL.item_text = "{$item_text}";
		CAL.mouseover_expand = "{$mouseover_expand}";
		CAL.celcount = {$celcount};	
		CAL.cells_per_day = {$cells_per_day};	
		CAL.current_params = {literal}{}{/literal};
		CAL.dashlet = "{$dashlet}";		

		CAL.lbl_create_new = "{$MOD.LBL_CREATE_NEW_RECORD}";
		CAL.lbl_edit = "{$MOD.LBL_EDIT_RECORD}";
		CAL.lbl_saving = "{$MOD.LBL_SAVING}";
		CAL.lbl_loading = "{$MOD.LBL_LOADING}";
		CAL.lbl_confirm_remove = "{$MOD.LBL_CONFIRM_REMOVE}";
		CAL.lbl_error_saving = "{$MOD.LBL_ERROR_SAVING}";
		CAL.lbl_error_loading = "{$MOD.LBL_ERROR_LOADING}";
		CAL.lbl_another_browser = "{$MOD.LBL_ANOTHER_BROWSER}";
		CAL.lbl_remove_participants = "{$MOD.LBL_REMOVE_PARTICIPANTS}";	
		CAL.lbl_desc = "{$MOD.LBL_I_DESC}";
		CAL.lbl_start_t = "{$MOD.LBL_I_START_DT}";
		CAL.lbl_due_t = "{$MOD.LBL_I_DUE_DT}";
		CAL.lbl_duration = "{$MOD.LBL_I_DURATION}";
		CAL.lbl_name = "{$MOD.LBL_I_NAME}";
		CAL.lbl_title = "{$MOD.LBL_I_TITLE}";
		CAL.lbl_related = "{$MOD.LBL_I_RELATED_TO}";					
	
		CAL.img_edit_inline = "{$img_edit_inline}";
		CAL.img_view_inline = "{$img_view_inline}";
		CAL.img_close = "{$img_close}";		
		CAL.scroll_slot = {$scroll_slot};

		CAL.fit_grid();
		
		{literal}
		var scrollable = CAL.get("cal-scrollable");
		if(scrollable){
			scrollable.scrollTop = 15 * CAL.scroll_slot;
		}
		{/literal}
					
		
				
		
		{if $pview == "shared"}
			{counter name="un" start=0 print=false assign="un"}
			{foreach name="shared" from=$shared_ids key=k item=member_id}				
				CAL.shared_users['{$member_id}'] = '{$un}';
				{counter name="un" print=false}
			{/foreach}
			CAL.shared_users_count = "{$shared_users_count}";
		{/if}
	
		CAL.field_list = new Array();
		CAL.field_disabled_list = new Array();			

		CAL.activity_colors = [];				
		{foreach name=colors from=$activity_colors key=module item=v}
			CAL.activity_colors['{$module}'] = [];
			CAL.activity_colors['{$module}']['border'] = '{$v.border}';
			CAL.activity_colors['{$module}']['body'] = '{$v.body}'
		{/foreach}

		CAL.act_types = [];
		CAL.act_types['Meetings'] = 'meeting';
		CAL.act_types['Calls'] = 'call';
		CAL.act_types['Tasks'] = 'task';
	
		var d_param = "{$d_param}";
		
		{literal}
		var nodes = CAL.query("#cal-grid div.left_cell:nth-child("+d_param+"), #cal-grid div.slot:nth-child("+d_param+")");
		CAL.each(nodes,function(i,v){
			if(!YAHOO.util.Dom.hasClass(nodes[i],"odd_border"))
				YAHOO.util.Dom.addClass(nodes[i],"odd_border");
		});		

		if(CAL.items_draggable){			
			var target_slots = [];			
			var slots = CAL.query('#cal-grid div.slot');
			CAL.each(
				slots,
				function(i,v){
					target_slots[i] = new YAHOO.util.DDTarget(slots[i].id,"cal"); 
				}
			);			
		}
		
			
		
		var nodes = CAL.query("#cal-grid div.slot");
		CAL.each(nodes, function(i,v){
			YAHOO.util.Event.on(nodes[i],"mouseover",function(){
				if(CAL.records_openable)
					this.style.backgroundColor = "#D1DCFF";							
				if(!this.childNodes.length)	
					this.setAttribute("title",this.getAttribute("dur"));
			});
			YAHOO.util.Event.on(nodes[i],"mouseout",function(){
				this.style.backgroundColor = "";
				this.removeAttribute("title");
			});
			YAHOO.util.Event.on(nodes[i],"click",function(){
				if(!CAL.disable_creating){							
					CAL.dialog_create(this);
				}
			});
		});				
		
		CAL.init_record_dialog(
				{
					width: "{/literal}{$editview_width}{literal}",
					height: "{/literal}{$editview_height}{literal}"
				}
		);
		
		YAHOO.util.Event.on(window, 'resize', function(){
			CAL.fit_grid();
		});		
				
		YAHOO.util.Event.on("btn_save","click",function(){																		
			if(!(check_form('CalendarEditView') && cal_isValidDuration()))
				return false;								
			CAL.dialog_save();	
		});
		
		YAHOO.util.Event.on("btn_send_invites","click",function(){																		
			if(!(check_form('CalendarEditView') && cal_isValidDuration()))
				return false;				
			CAL.get("send_invites").value = "1";							
			CAL.dialog_save();	
		});		
		

		YAHOO.util.Event.on("btn_apply","click",function(){
			if(!(check_form('CalendarEditView') && cal_isValidDuration()))
				return false;
			CAL.dialog_apply();
		});	
				
		YAHOO.util.Event.on("btn_delete","click",function(){
			if(CAL.get("record").value != "")
				if(confirm(CAL.lbl_confirm_remove))
					CAL.dialog_remove();				
						
		});	
	
		YAHOO.util.Event.on("btn_cancel","click",function(){			
			CAL.recordDialog.cancel();						
		}); 

		CAL.select_tab("record_tabs-1");

		YAHOO.util.Event.on(CAL.get("btn_cancel_settings"), 'click', function(){
			CAL.settingsDialog.cancel();	
		});
		
		YAHOO.util.Event.on(CAL.get("btn_save_settings"), 'click', function(){			
			CAL.get("form_settings").submit();
		});
		
		{/literal}
				

		var ActRecords = [
			{$a_str}		
		];
			
		{literal}
		CAL.each(
			ActRecords,
			function(i,v){				
				CAL.AddRecordToPage(ActRecords[i]);				
			}			
		);
		{/literal}
		
	});
</script>
			
<div id="record_dialog" style="display: none;">
	
	<div class="dialog_titlebar hd" id="dialog_titlebar"><span id="title-record_dialog"></span></div>
	<div class="dialog_content bd" id="dialog_content">
		<div id="record_tabs" class="yui-navset yui-navset-top yui-content" style="height: auto; padding: 0 2px;">
			<ul class="yui-nav">
				<li id="tab_general"><a tabname="record_tabs-1"><em>{$MOD.LBL_GENERAL_TAB}</em></a></li>
				<li id="tab_invitees"><a tabname="record_tabs-2"><em>{$MOD.LBL_PARTICIPANTS_TAB}</em></a></li>
			</ul>
			<div id="record_tabs-1" class="yui-content">
				{include file=$details}
			</div>				
			<div id="record_tabs-2" class="yui-content">
				<div class="h3Row" id="scheduler"></div>
			</div>
		</div>
	</div>	
	<div id="cal_record_buttons" class="ft">
		<button id="btn_save" class="button" type="button">{$MOD.LBL_SAVE_BUTTON}</button>&nbsp;
		<button id="btn_delete" class="button" type="button">{$MOD.LBL_DELETE_BUTTON}</button>&nbsp;
		<button id="btn_apply" class="button" type="button">{$MOD.LBL_APPLY_BUTTON}</button>&nbsp;
		<button id="btn_send_invites" class="button" type="button">{$MOD.LBL_SEND_INVITES}</button>&nbsp;
		<button id="btn_cancel" class="button" type="button" style="float: right;">{$MOD.LBL_CANCEL_BUTTON}</button>&nbsp;
	</div>
</div>

{if $settings}
{include file=$settings}
{/if}
	
<script type="text/javascript">	        	
{$GRjavascript}
</script>
	
<script type="text/javascript">	
	{literal}
	var schedulerLoader = new YAHOO.util.YUILoader({
		require : ["jsclass_scheduler"],
		onSuccess: function(){
			var root_div = document.getElementById('scheduler');
			var sugarContainer_instance = new SugarContainer(document.getElementById('scheduler'));
			sugarContainer_instance.start(SugarWidgetScheduler);
		}
	});
	schedulerLoader.addModule({
		name :"jsclass_scheduler",
		type : "js",
		fullpath: "modules/Meetings/jsclass_scheduler.js",
		varName: "global_rpcClient",
		requires: []
	});
	schedulerLoader.insert();	
	{/literal}
</script>
	
{if !$sugar_body_only}
<script type="text/javascript" src="include/javascript/jsclass_base.js"></script>
<script type="text/javascript" src="include/javascript/jsclass_async.js"></script>	
<script type="text/javascript" src="include/javascript/overlibmws.js"></script>	
<script type="text/javascript" src="modules/Calendar/Cal.js"></script>
{/if}
	
{if $hide_whole_day}
<script type="text/javascript">
	{literal}
	var wd = document.getElementById("whole_day_button");
	if(typeof wd != "undefined"){
		wd.style.display = "none";
	}
	{/literal}
</script>
{/if}	
<style type="text/css">
{literal}
	.schedulerDiv h3{
		display: none;
	}
{/literal}
</style>	
{if $pview == 'day'}
<style type="text/css">
{literal}
	.day_col, .left_time_col{
		border-top: 1px solid silver;	
	}
{/literal}
</style>
{/if}

