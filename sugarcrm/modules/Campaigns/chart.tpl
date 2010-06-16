{*

/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: chart.tpl 16320 2006-08-23 00:15:55Z awu $

*}


<!-- BEGIN: main -->
<graphData title="{GRAPHTITLE}">

        <yData defaultAltText="{Y_DEFAULT_ALT_TEXT}">
                <!-- BEGIN: row -->
                <dataRow title="{Y_ROW_TITLE}" endLabel="{Y_ROW_ENDLABEl}">
                        <!-- BEGIN: bar -->
                        <bar id="{Y_BAR_ID}" totalSize="{Y_BAR_SIZE}" altText="{Y_BAR_ALTTEXT}" url="{Y_BAR_URL}"/>
                        <!-- END: bar -->
                </dataRow>
                <!-- END: row -->
        </yData>
        <xData min="{XMIN}" max="{XMAX}" length="{XLENGTH}" kDelim="{XKDELIM}" prefix="{XPREFIX}" suffix="{XSUFFIX}"/>
        <colorLegend status="on">
                <mapping id="'.$outcome.'" name="'.$outcome_translation.'" color="'.$color.'"/>
        </colorLegend>
        <graphInfo><![CDATA[{GRAPH_DATA}]]></graphInfo>
        <chartColors {COLOR_DEFS}/>
</graphData>
<!-- END: main -->
