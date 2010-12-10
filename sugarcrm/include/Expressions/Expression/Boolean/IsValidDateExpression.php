<?php
 //FILE SUGARCRM flav=pro ONLY
/************************************
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
require_once("include/Expressions/Expression/Boolean/BooleanExpression.php");

/**
 * <b>isValidDate(String date)</b><br/>
 * Returns true if <i>date</i> is a valid date string or is empty.
 *
 */
class IsValidDateExpression extends BooleanExpression {
	/**
	 * Returns true if a passed in date string (in User format) is valid
	 */
	function evaluate() {
        global $current_user;

        $td = new TimeDate();
        $format = trim($td->get_db_date_format()) . " ";
        //echo "$format<br/>";
        $format = trim($GLOBALS['current_user']->getPreference("datef")) . " ";
        $dtStr = $this->getParameters()->evaluate();
        $part = "";
        if (!is_string($dtStr))
            return AbstractExpression::$FALSE;;
        $dateRemain = trim($dtStr);

        $m = "";
        $d = "";
        $y = "";

        for ($j = 0; $j < strlen($format); $j++) {
			$c = $format[$j];
			if ($c == ':' || $c == '/' || $c == '-' || $c == '.' || $c == " " || $c == 'a' || $c == "A") {
				$i = strpos($dateRemain, $c);
				if ($i === false)
                    $i = strlen($dateRemain);
				$v = substr($dateRemain, 0, $i);
				$dateRemain = substr($dateRemain, $i+1);
				//check the date parts, ignore Time for now
                switch ($part) {
					case 'm':
						if (!($v > 0 && $v < 13)) {
                            die("bad month $v");return AbstractExpression::$FALSE;
                        }
                        $m = $v;
                        break;
					case 'd':
						if(!($v > 0 && $v < 32)) {
                            die("bad day $v");return AbstractExpression::$FALSE;
                        }
                        $d = $v;
                        break;
					case 'Y':
                    case 'y':
						if(!($v > 0)) {
                            die("bad year $v");return AbstractExpression::$FALSE;
                        }
                        $y = $v;
                    break;
				}
				$part = "";
			} else {
				$part = $c;
			}
		}
        if (empty($m) || empty($d) || empty($y)) {
            die("something was missing");
            return  AbstractExpression::$FALSE;
        } else {
            echo "month:$m, day:$d, year:$y<br/>";
        }


        return AbstractExpression::$TRUE;

/*
	    foreach ( $date_reg_positions as $key => $index )
	    {
	        if($key == 'm') {
	           $m = $dateParts[$index];
	        } else if($key == 'd') {
	           $d = $dateParts[$index];
	        } else {
	           $y = $dateParts[$index];
	        }
	    }
	   // _pp("Y = $y, m=$m, d=$d");

	    // reject negative years
	    if ($y < 1)
	        return AbstractExpression::$FALSE;
	    // reject month less than 1 and greater than 12
	    if ($m > 12 || $m < 1)
	        return AbstractExpression::$FALSE;

	    // Check that date is real
	    $dd = cal_days_in_month(CAL_GREGORIAN, $m, $y);
	    
	    // reject days less than 1 or days not in month (e.g. February 30th)
	    if ($d < 1 || $d > $dd)
	        return AbstractExpression::$FALSE;
		return AbstractExpression::$TRUE;*/
	}

	/**
	 * Returns true is a passed in date string (in user format) is valid.
	 */
	static function getJSEvaluate() {
		return <<<EOQ
		var dtStr = this.getParameters().evaluate();
        var format = "Y-m-d";
        if (SUGAR.expressions.userPrefs)
            format = SUGAR.expressions.userPrefs.datef;
        var date = SUGAR.util.DateUtils.parse(dtStr, format);
        if(date != false && date != "Invalid Date")
		    return SUGAR.expressions.Expression.TRUE;
		return SUGAR.expressions.Expression.FALSE;
EOQ;
	}

	/**
	 * Any generic type will suffice.
	 */
	function getParameterTypes() {
		return array("string");
	}

	/**
	 * Returns the maximum number of parameters needed.
	 */
	static function getParamCount() {
		return 1;
	}

	/**
	 * Returns the opreation name that this Expression should be
	 * called by.
	 */
	static function getOperationName() {
		return "isValidDate";
	}

	/**
	 * Returns the String representation of this Expression.
	 */
	function toString() {
	}
}
?>
