<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: Chart_outcome_by_month.php 53846 2010-01-19 20:17:43Z jmertic $
 * Description:  returns HTML for client-side image map.
 ********************************************************************************/



require_once('include/charts/Charts.php');



class Chart_outcome_by_month
{
	var $modules = array('Opportunities');
	var $order = 0;
function Chart_outcome_by_month()
{

}

function draw($extra_tools)
{



global $app_list_strings, $current_language, $sugar_config, $currentModule, $action, $theme;
$current_module_strings = return_module_language($current_language, 'Charts');


if (isset($_REQUEST['obm_refresh'])) { $refresh = $_REQUEST['obm_refresh']; }
else { $refresh = false; }

$date_start = array();
$datax = array();
//get the dates to display
global $current_user;
$user_date_start = $current_user->getPreference('obm_date_start');
if (!empty($user_date_start)  && !isset($_REQUEST['obm_date_start'])) {
	$date_start =$user_date_start;
	$GLOBALS['log']->debug("USER PREFERENCES['obm_date_start'] is:");
	$GLOBALS['log']->debug($user_date_start);
}
elseif (isset($_REQUEST['obm_year']) && $_REQUEST['obm_year'] != '') {
	$date_start = $_REQUEST['obm_year'].'-01-01';
	$current_user->setPreference('obm_date_start', $date_start);
	$GLOBALS['log']->debug("_REQUEST['obm_date_start'] is:");
	$GLOBALS['log']->debug($_REQUEST['obm_date_start']);
	$GLOBALS['log']->debug("_SESSION['obm_date_start'] is:");
	$GLOBALS['log']->debug($current_user->getPreference('obm_date_start'));
}
else {
	$date_start = date('Y').'-01-01';
}
$user_date_end = $current_user->getPreference('obm_date_end');
if (!empty($user_date_end) && !isset($_REQUEST['obm_date_end'])) {
	$date_end =$user_date_end;
	$GLOBALS['log']->debug("USER PREFERENCES['obm_date_end'] is:");
	$GLOBALS['log']->debug($date_end);
}
elseif (isset($_REQUEST['obm_year']) && $_REQUEST['obm_year'] != '') {
	$date_end = $_REQUEST['obm_year'].'-12-31';
	$current_user->setPreference('obm_date_end', $date_end );
	$GLOBALS['log']->debug("_REQUEST['obm_date_end'] is:");
	$GLOBALS['log']->debug($_REQUEST['obm_date_end']);
	$GLOBALS['log']->debug("USER PREFERENCES['obm_date_end'] is:");
	$GLOBALS['log']->debug($current_user->getPreference('obm_date_end'));
}
else {
	$date_end = date('Y').'-12-31';
}

$ids = array();
//get list of user ids for which to display data
$user_ids = $current_user->getPreference('obm_ids');
if (!empty($user_ids) && count($user_ids) != 0 && !isset($_REQUEST['obm_ids'])) {
	$ids = $user_ids;
	$GLOBALS['log']->debug("USER PREFERENCES['obm_ids'] is:");
	$GLOBALS['log']->debug($user_ids);
}
elseif (isset($_REQUEST['obm_ids']) && count($_REQUEST['obm_ids']) > 0) {
	$ids = $_REQUEST['obm_ids'];
	$current_user->setPreference('obm_ids', $_REQUEST['obm_ids']);
	$GLOBALS['log']->debug("_REQUEST['obm_ids'] is:");
	$GLOBALS['log']->debug($_REQUEST['obm_ids']);
	$GLOBALS['log']->debug("USER PREFRENCES['obm_ids'] is:");
	$GLOBALS['log']->debug($current_user->getPreference('obm_ids'));
}
else {
	$ids = get_user_array(false);
	$ids = array_keys($ids);
}

//create unique prefix based on selected users for image files
$id_hash = '1';
if (isset($ids)) {
	sort($ids);
	$id_hash = crc32(implode('',$ids));
	if($id_hash < 0)
	{
        $id_hash = $id_hash * -1;
	}
}
$GLOBALS['log']->debug("ids is:");
$GLOBALS['log']->debug($ids);
$id_md5 = substr(md5($current_user->id),0,9);


// cn: format date_start|end to user's preferred
global $timedate;
$dateDisplayStart	= strftime($timedate->get_user_date_format(), strtotime($date_start));
$dateDisplayEnd   	= strftime($timedate->get_user_date_format(), strtotime($date_end));
$seps				= array("-", "/");
$dates				= array($date_start, $date_end);
$dateFileNameSafe	= str_replace($seps, "_", $dates);

$cache_file_name = sugar_cached("xml/").$current_user->getUserPrivGuid()."_outcome_by_month_".$dateFileNameSafe[0]."_".$dateFileNameSafe[1].".xml";

$GLOBALS['log']->debug("cache file name is: $cache_file_name");


global $app_strings;
$tools='<div align="right"><a href="index.php?module='.$currentModule.'&action='. $action .'&obm_refresh=true" class="tabFormAdvLink">'.SugarThemeRegistry::current()->getImage('refresh','alt="Refresh"  border="0" align="absmiddle"').'&nbsp;'.$current_module_strings['LBL_REFRESH'].'</a>&nbsp;&nbsp;<a href="javascript: toggleDisplay(\'outcome_by_month_edit\');" class="tabFormAdvLink">'.SugarThemeRegistry::current()->getImage('edit','alt="Edit"  border="0"  align="absmiddle"').'&nbsp;'. $current_module_strings['LBL_EDIT'].'</a>&nbsp;&nbsp;'.$extra_tools.'</div>';
?>
	<?php echo '<span onmouseover="this.style.cursor=\'move\'" id="chart_handle_' . $this->order . '">' . get_form_header($current_module_strings['LBL_YEAR_BY_OUTCOME'],$tools,false) . '</span>';?>

<?php
	$cal_lang = "en";
	$cal_dateformat = parse_calendardate($app_strings['NTC_DATE_FORMAT']);

if (empty($_SESSION['obm_ids'])) $_SESSION['obm_ids'] = "";
?>
<p>
<div id='outcome_by_month_edit' style='display: none;'>
<form name="outcome_by_month" action="index.php" method="post" >
<input type="hidden" name="module" value="<?php echo $currentModule;?>">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="obm_refresh" value="true">
<input type="hidden" name="obm_date_start" value="<?php if (isset($_SESSION['obm_date_start'])) echo $_SESSION['obm_date_start']?>">
<input type="hidden" name="obm_date_end" value="<?php if (isset($_SESSION['obm_date_end'])) echo $_SESSION['obm_date_end']?>">
<table cellpadding="0" cellspacing="0" border="0" class="edit view" align="center">
<tr>
	<td valign='top' nowrap ><b><?php echo $current_module_strings['LBL_YEAR']?></b><br><span class="dateFormat"><?php echo $app_strings['NTC_YEAR_FORMAT']?></span></td>
	<td valign='top' ><input class="text" name="obm_year" size='12' maxlength='10' id='obm_year'  value='<?php if (isset($date_start)) echo substr($date_start,0,4)?>'>&nbsp;&nbsp;</td>
	<td valign='top'><b><?php echo $current_module_strings['LBL_USERS'];?></b></td>
	<td valign='top'><select name="obm_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(false),$ids); ?></select></td>
	<td align="right" valign="top"><input class="button" onclick="return verify_chart_data_outcome_by_month();" type="submit" title="<?php echo $app_strings['LBL_SELECT_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SELECT_BUTTON_KEY']; ?>" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL']?>" /><input class="button" onClick="javascript: toggleDisplay('outcome_by_month_edit');" type="button" title="<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_CANCEL_BUTTON_KEY'];?>" value="<?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL']?>"/></td>
</tr>
</table>
</form>

</div>
</p>
<?php
// draw chart
echo "<p align='center'>".$this->gen_xml($date_start, $date_end, $ids, $cache_file_name, $refresh,$current_module_strings)."</p>";
echo "<P align='center'><span class='chartFootnote'>".$current_module_strings['LBL_MONTH_BY_OUTCOME_DESC']."</span></P>";



?>


<?php
	if (file_exists($cache_file_name)) {
		$file_date = $timedate->asUser($timedate->fromTimestamp(filemtime($cache_file_name)));
	}
	else {
		$file_date = '';
	}
?>

<span class='chartFootnote'>
<p align="right"><i><?php  echo $current_module_strings['LBL_CREATED_ON'].' '.$file_date; ?></i></p>
</span>
<?php
echo get_validate_chart_js();

}

	/**
	* Creates opportunity pipeline image as a VERTICAL accumlated bar graph for multiple users.
	* param $datax- the month data to display in the x-axis
	* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	* All Rights Reserved..
	* Contributor(s): ______________________________________..
	*/
	function gen_xml($date_start='1971-10-15', $date_end='2010-10-15', $user_id=array('1'), $cache_file_name='a_file', $refresh=false,$current_module_strings) {
		global $app_strings, $app_list_strings, $charset, $lang, $barChartColors, $current_user;

		$kDelim = $current_user->getPreference('num_grp_sep');
		global $timedate;

		if (!file_exists($cache_file_name) || $refresh == true) {
			$GLOBALS['log']->debug("date_start is: $date_start");
			$GLOBALS['log']->debug("date_end is: $date_end");
			$GLOBALS['log']->debug("user_id is: ");
			$GLOBALS['log']->debug($user_id);
			$GLOBALS['log']->debug("cache_file_name is: $cache_file_name");

			$where = "";
			//build the where clause for the query that matches $user
			$count = count($user_id);
			$id = array();
			if ($count>0) {
				foreach ($user_id as $the_id) {
					$id[] = "'".$the_id."'";
				}
				$ids = join(",",$id);
				$where .= "opportunities.assigned_user_id IN ($ids) ";

			}

			// cn: adding user-pref date handling
			$dateStartDisplay = $timedate->asUserDate($timedate->fromString($date_start));
			$dateEndDisplay = $timedate->asUserDate($timedate->fromString($date_end));

			$opp = new Opportunity();
			//build the where clause for the query that matches $date_start and $date_end
			$where .= "AND opportunities.date_closed >= ".db_convert("'".$date_start."'",'date')." AND opportunities.date_closed <= ".db_convert("'".$date_end."'",'date')." AND opportunities.deleted=0";
			$query = "SELECT sales_stage,".db_convert('opportunities.date_closed','date_format',array("'%Y-%m'"),array("'YYYY-MM'"))." as m, sum(amount_usdollar/1000) as total, count(*) as opp_count FROM opportunities ";
			//BEGIN SUGARCRM flav=pro ONLY
			$opp->add_team_security_where_clause($query);
			//END SUGARCRM flav=pro ONLY
			$query .= "WHERE ".$where;
			$query .= " GROUP BY sales_stage,".db_convert('opportunities.date_closed','date_format',array("'%Y-%m'"),array("'YYYY-MM'"))."ORDER BY m";
			//Now do the db queries
			//query for opportunity data that matches $datay and $user
			//_pp($query);

			$result = $opp->db->query($query, true);
			//build pipeline by sales stage data
			$total = 0;
			$div = 1;
			global $sugar_config;
			$symbol = $sugar_config['default_currency_symbol'];
			$other = $current_module_strings['LBL_LEAD_SOURCE_OTHER'];
			$rowTotalArr = array();
			$rowTotalArr[] = 0;
			global $current_user;
			$salesStages = array("Closed Lost"=>$app_list_strings['sales_stage_dom']["Closed Lost"],"Closed Won"=>$app_list_strings['sales_stage_dom']["Closed Won"],"Other"=>$other);
			if($current_user->getPreference('currency') ){

				$currency = new Currency();
				$currency->retrieve($current_user->getPreference('currency'));
				$div = $currency->conversion_rate;
				$symbol = $currency->symbol;
			}
			$months = array();
			$monthArr = array();
			while($row = $opp->db->fetchByAssoc($result, -1, false))
			{
				if($row['total']*$div<=100){
					$sum = round($row['total']*$div, 2);
				} else {
					$sum = round($row['total']*$div);
				}
				if($row['sales_stage'] == 'Closed Won' || $row['sales_stage'] == 'Closed Lost'){
					$salesStage = $row['sales_stage'];
					$salesStageT = $app_list_strings['sales_stage_dom'][$row['sales_stage']];
				} else {
					$salesStage = "Other";
					$salesStageT = $other;
				}

				$months[$row['m']] = $row['m'];
				if(!isset($monthArr[$row['m']]['row_total'])) {$monthArr[$row['m']]['row_total']=0;}
				$monthArr[$row['m']][$salesStage]['opp_count'][] = $row['opp_count'];
				$monthArr[$row['m']][$salesStage]['total'][] = $sum;
				$monthArr[$row['m']]['outcome'][$salesStage]=$salesStageT;
				$monthArr[$row['m']]['row_total'] += $sum;

				$total += $sum;
			}

			$fileContents = '     <xData length="20">'."\n";
			if (!empty($months)) {
				foreach ($months as $month){
					$rowTotalArr[]=$monthArr[$month]['row_total'];
					if($monthArr[$month]['row_total']>100)
					{
						$monthArr[$month]['row_total']=round($monthArr[$month]['row_total']);
					}
					$fileContents .= '          <dataRow title="'.$month.'" endLabel="'.currency_format_number($monthArr[$month]['row_total'], array('currency_symbol' => true)).'">'."\n";
					arsort($salesStages);
					foreach ($salesStages as $outcome=>$outcome_translation){
						if(isset($monthArr[$month][$outcome])) {
						$fileContents .= '               <bar id="'.$outcome.'" totalSize="'.array_sum($monthArr[$month][$outcome]['total']).'" altText="'.$month.': '.format_number(array_sum($monthArr[$month][$outcome]['opp_count']), 0, 0).' '.$current_module_strings['LBL_OPPS_WORTH'].' '.currency_format_number(array_sum($monthArr[$month][$outcome]['total']),array('currency_symbol' => true)).$current_module_strings['LBL_OPP_THOUSANDS'].' '.$current_module_strings['LBL_OPPS_OUTCOME'].' '.$outcome_translation.'" url="index.php?module=Opportunities&action=index&date_closed='.$month.'&sales_stage='.urlencode($outcome).'&query=true&searchFormTab=advanced_search"/>'."\n";
						}
					}
					$fileContents .= '          </dataRow>'."\n";
				}
			} else {
				$fileContents .= '          <dataRow title="" endLabel="">'."\n";
				$fileContents .= '               <bar id="" totalSize="0" altText="" url=""/>'."\n";
				$fileContents .= '          </dataRow>'."\n";
				$rowTotalArr[] = 1000;
			}
			$fileContents .= '     </xData>'."\n";
			$max = get_max($rowTotalArr);
			$fileContents .= '     <yData min="0" max="'.$max.'" length="10" prefix="'.$symbol.'" suffix="" kDelim="'.$kDelim.'" defaultAltText="'.$current_module_strings['LBL_ROLLOVER_DETAILS'].'"/>'."\n";
			$fileContents .= '     <colorLegend status="on">'."\n";
			$i=0;
			asort($salesStages);
			foreach ($salesStages as $outcome=>$outcome_translation) {
				$color = generate_graphcolor($outcome,$i);
				$fileContents .= '          <mapping id="'.$outcome.'" name="'.$outcome_translation.'" color="'.$color.'"/>'."\n";
				$i++;
			}
			$fileContents .= '     </colorLegend>'."\n";
			$fileContents .= '     <graphInfo>'."\n";
			$fileContents .= '          <![CDATA['.$current_module_strings['LBL_DATE_RANGE']." ".$dateStartDisplay." ".$current_module_strings['LBL_DATE_RANGE_TO']." ".$dateEndDisplay."<br/>".$current_module_strings['LBL_OPP_SIZE'].' '.$symbol.'1'.$current_module_strings['LBL_OPP_THOUSANDS'].']]>'."\n";
			$fileContents .= '     </graphInfo>'."\n";
			$fileContents .= '     <chartColors ';
			foreach ($barChartColors as $key => $value) {
				$fileContents .= ' '.$key.'='.'"'.$value.'" ';
			}
			$fileContents .= ' />'."\n";
			$fileContents .= '</graphData>'."\n";
			$total = round($total, 2);
			$title = '<graphData title="'.$current_module_strings['LBL_TOTAL_PIPELINE'].currency_format_number($total, array('currency_symbol' => true)).$app_strings['LBL_THOUSANDS_SYMBOL'].'">'."\n";
			$fileContents = $title.$fileContents;

			//echo $fileContents;
			save_xml_file($cache_file_name, $fileContents);
		}
		$return = create_chart('vBarF',$cache_file_name);
		return $return;

	}

	function constructQuery(){
		global $current_user;
		global $timedate;

		$user_date_start = $current_user->getPreference('obm_date_start');
		if (!empty($user_date_start)  && !isset($_REQUEST['obm_date_start'])) {
			$date_start =$user_date_start;
			$GLOBALS['log']->debug("USER PREFERENCES['obm_date_start'] is:");
			$GLOBALS['log']->debug($user_date_start);
		}
		elseif (isset($_REQUEST['obm_year']) && $_REQUEST['obm_year'] != '') {
			$date_start = $_REQUEST['obm_year'].'-01-01';
			$current_user->setPreference('obm_date_start', $date_start);
			$GLOBALS['log']->debug("_REQUEST['obm_date_start'] is:");
			$GLOBALS['log']->debug($_REQUEST['obm_date_start']);
			$GLOBALS['log']->debug("_SESSION['obm_date_start'] is:");
			$GLOBALS['log']->debug($current_user->getPreference('obm_date_start'));
		}
		else {
			$date_start = date('Y').'-01-01';
		}
		$user_date_end = $current_user->getPreference('obm_date_end');
		if (!empty($user_date_end) && !isset($_REQUEST['obm_date_end'])) {
			$date_end =$user_date_end;
			$GLOBALS['log']->debug("USER PREFERENCES['obm_date_end'] is:");
			$GLOBALS['log']->debug($date_end);
		}
		elseif (isset($_REQUEST['obm_year']) && $_REQUEST['obm_year'] != '') {
			$date_end = $_REQUEST['obm_year'].'-12-31';
			$current_user->setPreference('obm_date_end', $date_end );
			$GLOBALS['log']->debug("_REQUEST['obm_date_end'] is:");
			$GLOBALS['log']->debug($_REQUEST['obm_date_end']);
			$GLOBALS['log']->debug("USER PREFERENCES['obm_date_end'] is:");
			$GLOBALS['log']->debug($current_user->getPreference('obm_date_end'));
		}
		else {
			$date_end = date('Y').'-12-31';
		}

		$ids = array();
		//get list of user ids for which to display data
		$user_ids = $current_user->getPreference('obm_ids');
		if (!empty($user_ids) && count($user_ids) != 0 && !isset($_REQUEST['obm_ids'])) {
			$ids = $user_ids;
			$GLOBALS['log']->debug("USER PREFERENCES['obm_ids'] is:");
			$GLOBALS['log']->debug($user_ids);
		}
		elseif (isset($_REQUEST['obm_ids']) && count($_REQUEST['obm_ids']) > 0) {
			$ids = $_REQUEST['obm_ids'];
			$current_user->setPreference('obm_ids', $_REQUEST['obm_ids']);
			$GLOBALS['log']->debug("_REQUEST['obm_ids'] is:");
			$GLOBALS['log']->debug($_REQUEST['obm_ids']);
			$GLOBALS['log']->debug("USER PREFRENCES['obm_ids'] is:");
			$GLOBALS['log']->debug($current_user->getPreference('obm_ids'));
		}
		else {
			$ids = get_user_array(false);
			$ids = array_keys($ids);
		}

		$user_id = $ids;

		$where = "";
		//build the where clause for the query that matches $user
		$count = count($user_id);
		$id = array();
		if ($count>0) {
			foreach ($user_id as $the_id) {
				$id[] = "'".$the_id."'";
			}
			$ids = join(",",$id);
			$where .= "opportunities.assigned_user_id IN ($ids) ";

		}

		// cn: adding user-pref date handling
		$dateStartDisplay = $timedate->asUserDate($timedate->fromString($date_start));
		$dateEndDisplay = $timedate->asUserDate($timedate->fromString($date_end));

		$opp = new Opportunity();
		//build the where clause for the query that matches $date_start and $date_end
		$where .= "AND opportunities.date_closed >= ".db_convert("'".$date_start."'",'date')." AND opportunities.date_closed <= ".db_convert("'".$date_end."'",'date')." AND opportunities.deleted=0";
		$query = "SELECT sales_stage,".db_convert('opportunities.date_closed','date_format',array("'%Y-%m'"),array("'YYYY-MM'"))." as m, sum(amount_usdollar/1000) as total, count(*) as opp_count FROM opportunities ";
		//BEGIN SUGARCRM flav=pro ONLY
		$opp->add_team_security_where_clause($query);
		//END SUGARCRM flav=pro ONLY
		$query .= "WHERE ".$where;
		$query .= " GROUP BY sales_stage,".db_convert('opportunities.date_closed','date_format',array("'%Y-%m'"),array("'YYYY-MM'"))."ORDER BY m";
		return $query;
	}

	function constructGroupBy(){
		return array( 'm', 'sales_stage', );
	}

}

?>
