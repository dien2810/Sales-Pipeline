<?php
// StagePipelineCondition.php
// Add by Dien Nguyen on 2025-02-18 to get condition of pipeline stage
require_once('data/CRMEntity.php');
require_once('include/utils/utils.php');
require_once 'include/Webservices/Utils.php';
class StagePipelineCondition extends CRMEntity {
    var $module;
    var $escapemodule;
	var $smownerid;
    function StagePipelineCondition($module = "") {
		global $current_user, $adb;
		$this->module = $module;
		$this->escapemodule[] = $module . "_";
		$this->escapemodule[] = "_";
		$this->smownerid = $current_user->id;
		
		// $this->moduleMetaInfo = array();
		// if ($module != "" && $module != 'Calendar') {
		// 	$this->meta = $this->getMeta($module, $current_user);
		// }
	}
    /**
	 *  Function which will give condition list for date fields
	 * @return array of std filter conditions
	 */
	function getStdFilterConditions() {
		return Array("custom","prevfy" ,"thisfy" ,"nextfy","prevfq",
			"thisfq","nextfq","yesterday","today","tomorrow",
			"lastweek","thisweek","nextweek","lastmonth","thismonth",
			"nextmonth","last7days","last14days","last30days","last60days","last90days",
			"last120days","next30days","next60days","next90days","next120days",
		);
	}

    function getConditionsByStageID($stageid, $conditions) {
        global $adb, $log, $default_charset;
        if(is_array($conditions)){
            $i = 1;
            $j = 0;
            foreach ($conditions as $conditionGroup) {
                $groupInfo = $conditionGroup[$i]["columns"];
                foreach ($groupInfo as $conditionRow) {
                    $condition = array();
                    $condition['columnname'] = html_entity_decode($conditionRow["columnname"], ENT_QUOTES, $default_charset);
                    $condition['compareType'] = $conditionRow["compareType"];
                    $conditionVal = html_entity_decode($conditionRow["value"], ENT_QUOTES, $default_charset);
                    $col = explode(":", $conditionRow["columnname"]);
                    $temp_val = explode(",", $conditionRow["value"]);

                    $columnCondition = $condition['columnname'];
                    $columnInfo = explode(":", $columnCondition);
                    $fieldName = $columnInfo[2];
                    $moduleName = $this->module;
                    // $moduleName = "Potentials";
                    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
                    preg_match('/(\w+) ; \((\w+)\) (\w+)/', $fieldName, $matches);

                    if (count($matches) != 0) {
                        list($full, $referenceParentField, $referenceModule, $referenceFieldName) = $matches;
                    }
                    if ($referenceParentField) {
                        $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
                        $fieldModel = $referenceModuleModel->getField($referenceFieldName);
                    } else {
                        $fieldModel = $moduleModel->getField($fieldName);
                    }
                    if($fieldModel) {
                        $fieldType = $fieldModel->getFieldDataType();
                    }

                    if ($fieldType == 'currency') {
                        if ($fieldModel->get('uitype') == '72') {
                            // Some of the currency fields like Unit Price, Totoal , Sub-total - doesn't need currency conversion during save
                            $conditionVal = CurrencyField::convertToUserFormat($conditionVal, null, true);
                        } else {
                            $conditionVal = CurrencyField::convertToUserFormat($conditionVal);
                        }
                    }

                    $specialDateTimeConditions = Vtiger_Functions::getSpecialDateTimeCondtions();
                    if (($col[4] == 'D' || ($col[4] == 'T' && $col[1] != 'time_start' && $col[1] != 'time_end') || ($col[4] == 'DT')) && !in_array($condition['comparator'], $specialDateTimeConditions)) {
                        $val = Array();
                        for ($x = 0; $x < count($temp_val); $x++) {
                            if(empty($temp_val[$x])) {
                                $val[$x] = '';
                            } else if ($col[4] == 'D') {
                                $date = new DateTimeField(trim($temp_val[$x]));
                                $val[$x] = $date->getDisplayDate();
                            } elseif ($col[4] == 'DT') {
                                $comparator = array('e','n','b','a');
                                if(in_array($condition['compareType'], $comparator)) {
                                    $originalValue = $temp_val[$x];
                                    $dateTime = explode(' ',$originalValue);
                                    $temp_val[$x] = $dateTime[0];
                                }
                                $date = new DateTimeField(trim($temp_val[$x]));
                                $val[$x] = $date->getDisplayDateTimeValue();
                            } else {
                                $date = new DateTimeField(trim($temp_val[$x]));
                                $val[$x] = $date->getDisplayTime();
                            }
                        }
                        $conditionVal = implode(",", $val);
                    }
                    $condition['value'] = $conditionVal;
                    // $condition['column_condition'] = $conditionRow["column_condition"];

                    $conditions[$i]['columns'][$j] = $condition;
                    $conditions[$i]['condition'] = $i == 1 ? "and" : "or";
                    $j++;
                }
                if (!empty($conditions[$i]['columns'][$j - 1]['column_condition'])) {
                    $conditions[$i]['columns'][$j - 1]['column_condition'] = '';
                }
                $i++;
            }
            // Clear the condition (and/or) for last group, if any.
            if (!empty($conditions[$i - 1]['condition']))
                $conditions[$i - 1]['condition'] = '';

            $conditions = $conditions ? $conditions : null;
        }
        return $conditions ? $conditions : array();
    }

    function resolveDateFilterValue ($dateFilterRow) {
		$stdfilterlist = array();
		$stdfilterlist["columnname"] = $dateFilterRow["columnname"];
		$stdfilterlist["stdfilter"] = $dateFilterRow["stdfilter"];

		if ($dateFilterRow["stdfilter"] == "custom" || $dateFilterRow["stdfilter"] == "" || $dateFilterRow["stdfilter"] == "e" || $dateFilterRow["stdfilter"] == "n") {
			if ($dateFilterRow["startdate"] != "0000-00-00" && $dateFilterRow["startdate"] != "") {
				$startDateTime = new DateTimeField($dateFilterRow["startdate"]);
				$stdfilterlist["startdate"] = $startDateTime->getDisplayDate();
			}
			if ($dateFilterRow["enddate"] != "0000-00-00" && $dateFilterRow["enddate"] != "") {
				$endDateTime = new DateTimeField($dateFilterRow["enddate"]);
				$stdfilterlist["enddate"] = $endDateTime->getDisplayDate();
			}
		} else { //if it is not custom get the date according to the selected duration
			$specialDateFilters = array('yesterday','today','tomorrow');
			$datefilter = $this->getDateforStdFilterBytype($dateFilterRow["stdfilter"]);

			if(in_array($dateFilterRow["stdfilter"], $specialDateFilters)) {
				$currentDate = DateTimeField::convertToUserTimeZone(date('Y-m-d H:i:s'));
				$startDateTime = new DateTimeField($datefilter[0] . ' ' . $currentDate->format('H:i:s'));
				$endDateTime = new DateTimeField($datefilter[1] . ' ' . $currentDate->format('H:i:s'));
			} else {
				$startDateTime = new DateTimeField($datefilter[0]);
				$endDateTime = new DateTimeField($datefilter[1]);
			}

			$stdfilterlist["startdate"] = $startDateTime->getDisplayDate();
			$stdfilterlist["enddate"] = $endDateTime->getDisplayDate();
		}

		return $stdfilterlist;
	}

    /** to get the date value for the given type
	 * @param $type :: type string
	 * @returns  $datevalue array in the following format
	 * $datevalue = Array(0=>$startdate,1=>$enddate)
	 */
	function getDateforStdFilterBytype($type) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$userPeferredDayOfTheWeek = $currentUserModel->get('dayoftheweek');
		$date = DateTimeField::convertToUserTimeZone(date('Y-m-d H:i:s'));
		$d = $date->format('d');
		$m = $date->format('m');
		$y = $date->format('Y');

		$thisyear = $y;
		$today = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));
		$todayName =  date('l', strtotime($today));

		$tomorrow = date("Y-m-d", mktime(0, 0, 0, $m, $d + 1, $y));
		$yesterday = date("Y-m-d", mktime(0, 0, 0, $m, $d - 1, $y));

		$currentmonth0 = date("Y-m-d", mktime(0, 0, 0, $m, "01", $y));
		$currentmonth1 = $date->format("Y-m-t");
		$lastmonth0 = date("Y-m-d", mktime(0, 0, 0, $m - 1, "01", $y));
		$lastmonth1 = date("Y-m-t", mktime(0, 0, 0, $m - 1, "01", $y));
		$nextmonth0 = date("Y-m-d", mktime(0, 0, 0, $m + 1, "01", $y));
		$nextmonth1 = date("Y-m-t", mktime(0, 0, 0, $m + 1, "01", $y));
		// (Last Week) If Today is "Sunday" then "-2 week Sunday" will give before last week Sunday date
		if($todayName == $userPeferredDayOfTheWeek)
			$lastweek0 = date("Y-m-d",strtotime("-1 week $userPeferredDayOfTheWeek"));
		else
			$lastweek0 = date("Y-m-d", strtotime("-2 week $userPeferredDayOfTheWeek"));
		$prvDay = date('l',  strtotime(date('Y-m-d', strtotime('-1 day', strtotime($lastweek0)))));
		$lastweek1 = date("Y-m-d", strtotime("-1 week $prvDay"));

		// (This Week) If Today is "Sunday" then "-1 week Sunday" will give last week Sunday date
		if($todayName == $userPeferredDayOfTheWeek)
			$thisweek0 = date("Y-m-d",strtotime("-0 week $userPeferredDayOfTheWeek"));
		else
			$thisweek0 = date("Y-m-d", strtotime("-1 week $userPeferredDayOfTheWeek"));
		$prvDay = date('l',  strtotime(date('Y-m-d', strtotime('-1 day', strtotime($thisweek0)))));
		$thisweek1 = date("Y-m-d", strtotime("this $prvDay"));

		// (Next Week) If Today is "Sunday" then "this Sunday" will give Today's date
		if($todayName == $userPeferredDayOfTheWeek)
			$nextweek0 = date("Y-m-d",strtotime("+1 week $userPeferredDayOfTheWeek"));
		else
			$nextweek0 = date("Y-m-d", strtotime("this $userPeferredDayOfTheWeek"));
		$prvDay = date('l',  strtotime(date('Y-m-d', strtotime('-1 day', strtotime($nextweek0)))));
		$nextweek1 = date("Y-m-d", strtotime("+1 week $prvDay"));

		$next7days = date("Y-m-d", mktime(0, 0, 0, $m, $d + 6, $y));
		$next30days = date("Y-m-d", mktime(0, 0, 0, $m, $d + 29, $y));
		$next60days = date("Y-m-d", mktime(0, 0, 0, $m, $d + 59, $y));
		$next90days = date("Y-m-d", mktime(0, 0, 0, $m, $d + 89, $y));
		$next120days = date("Y-m-d", mktime(0, 0, 0, $m, $d + 119, $y));

		$last7days = date("Y-m-d", mktime(0, 0, 0, $m, $d - 6, $y));
		$last14days = date("Y-m-d", mktime(0, 0, 0, $m, $d - 13, $y));
		$last30days = date("Y-m-d", mktime(0, 0, 0, $m, $d - 29, $y));
		$last60days = date("Y-m-d", mktime(0, 0, 0, $m, $d - 59, $y));
		$last90days = date("Y-m-d", mktime(0, 0, 0, $m, $d - 89, $y));
		$last120days = date("Y-m-d", mktime(0, 0, 0, $m, $d - 119, $y));

		$currentFY0 = date("Y-m-d", mktime(0, 0, 0, "01", "01", $y));
		$currentFY1 = date("Y-m-t", mktime(0, 0, 0, "12", "31", $y));
		$lastFY0 = date("Y-m-d", mktime(0, 0, 0, "01", "01", $y - 1));
		$lastFY1 = date("Y-m-t", mktime(0, 0, 0, "12", "31", $y - 1));
		$nextFY0 = date("Y-m-d", mktime(0, 0, 0, "01", "01", $y + 1));
		$nextFY1 = date("Y-m-t", mktime(0, 0, 0, "12", "31", $y + 1));

		if ($m <= 3) {
			$cFq = date("Y-m-d", mktime(0, 0, 0, "01", "01", $y));
			$cFq1 = date("Y-m-d", mktime(0, 0, 0, "03", "31", $y));
			$nFq = date("Y-m-d", mktime(0, 0, 0, "04", "01", $y));
			$nFq1 = date("Y-m-d", mktime(0, 0, 0, "06", "30", $y));
			$pFq = date("Y-m-d", mktime(0, 0, 0, "10", "01", $y - 1));
			$pFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", $y - 1));
		} else if ($m > 3 and $m <= 6) {
			$pFq = date("Y-m-d", mktime(0, 0, 0, "01", "01", $y));
			$pFq1 = date("Y-m-d", mktime(0, 0, 0, "03", "31", $y));
			$cFq = date("Y-m-d", mktime(0, 0, 0, "04", "01", $y));
			$cFq1 = date("Y-m-d", mktime(0, 0, 0, "06", "30", $y));
			$nFq = date("Y-m-d", mktime(0, 0, 0, "07", "01", $y));
			$nFq1 = date("Y-m-d", mktime(0, 0, 0, "9", "30", $y));
		} else if ($m > 6 and $m <= 9) {
			$pFq = date("Y-m-d", mktime(0, 0, 0, "04", "01", $y));
			$pFq1 = date("Y-m-d", mktime(0, 0, 0, "06", "30", $y));
			$cFq = date("Y-m-d", mktime(0, 0, 0, "07", "01", $y));
			$cFq1 = date("Y-m-d", mktime(0, 0, 0, "9", "30", $y));
			$nFq = date("Y-m-d", mktime(0, 0, 0, "10", "01", $y));
			$nFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", $y));
		} else {
			$nFq = date("Y-m-d", mktime(0, 0, 0, "01", "01", $y + 1));
			$nFq1 = date("Y-m-d", mktime(0, 0, 0, "03", "31", $y + 1));
			$pFq = date("Y-m-d", mktime(0, 0, 0, "07", "01", $y));
			$pFq1 = date("Y-m-d", mktime(0, 0, 0, "09", "30", $y));
			$cFq = date("Y-m-d", mktime(0, 0, 0, "10", "01", $y));
			$cFq1 = date("Y-m-d", mktime(0, 0, 0, "12", "31", $y));
		}

		if ($type == "today") {

			$datevalue[0] = $today;
			$datevalue[1] = $today;
		} elseif ($type == "yesterday") {

			$datevalue[0] = $yesterday;
			$datevalue[1] = $yesterday;
		} elseif ($type == "tomorrow") {

			$datevalue[0] = $tomorrow;
			$datevalue[1] = $tomorrow;
		} elseif ($type == "thisweek") {

			$datevalue[0] = $thisweek0;
			$datevalue[1] = $thisweek1;
		} elseif ($type == "lastweek") {

			$datevalue[0] = $lastweek0;
			$datevalue[1] = $lastweek1;
		} elseif ($type == "nextweek") {

			$datevalue[0] = $nextweek0;
			$datevalue[1] = $nextweek1;
		} elseif ($type == "thismonth") {

			$datevalue[0] = $currentmonth0;
			$datevalue[1] = $currentmonth1;
		} elseif ($type == "lastmonth") {

			$datevalue[0] = $lastmonth0;
			$datevalue[1] = $lastmonth1;
		} elseif ($type == "nextmonth") {

			$datevalue[0] = $nextmonth0;
			$datevalue[1] = $nextmonth1;
		} elseif ($type == "next7days") {

			$datevalue[0] = $today;
			$datevalue[1] = $next7days;
		} elseif ($type == "next30days") {

			$datevalue[0] = $today;
			$datevalue[1] = $next30days;
		} elseif ($type == "next60days") {

			$datevalue[0] = $today;
			$datevalue[1] = $next60days;
		} elseif ($type == "next90days") {

			$datevalue[0] = $today;
			$datevalue[1] = $next90days;
		} elseif ($type == "next120days") {

			$datevalue[0] = $today;
			$datevalue[1] = $next120days;
		} elseif ($type == "last7days") {

			$datevalue[0] = $last7days;
			$datevalue[1] = $today;
		} elseif ($type == "last14days") {
			$datevalue[0] = $last14days;
			$datevalue[1] = $today;
		} elseif ($type == "last30days") {

			$datevalue[0] = $last30days;
			$datevalue[1] = $today;
		} elseif ($type == "last60days") {

			$datevalue[0] = $last60days;
			$datevalue[1] = $today;
		} else if ($type == "last90days") {

			$datevalue[0] = $last90days;
			$datevalue[1] = $today;
		} elseif ($type == "last120days") {

			$datevalue[0] = $last120days;
			$datevalue[1] = $today;
		} elseif ($type == "thisfy") {

			$datevalue[0] = $currentFY0;
			$datevalue[1] = $currentFY1;
		} elseif ($type == "prevfy") {

			$datevalue[0] = $lastFY0;
			$datevalue[1] = $lastFY1;
		} elseif ($type == "nextfy") {

			$datevalue[0] = $nextFY0;
			$datevalue[1] = $nextFY1;
		} elseif ($type == "nextfq") {

			$datevalue[0] = $nFq;
			$datevalue[1] = $nFq1;
		} elseif ($type == "prevfq") {

			$datevalue[0] = $pFq;
			$datevalue[1] = $pFq1;
		} elseif ($type == "thisfq") {
			$datevalue[0] = $cFq;
			$datevalue[1] = $cFq1;
		} else {
			$datevalue[0] = "";
			$datevalue[1] = "";
		}

		return $datevalue;
	}
}
?>