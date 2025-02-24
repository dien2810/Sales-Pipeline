<?php
// Add By Dien Nguyen

require_once 'data/CRMEntity.php';
require_once 'modules/PipelineConfig/StagesPipelineCondition.php';
require_once 'include/Webservices/Utils.php';
require_once 'include/Webservices/RelatedModuleMeta.php';
class StageChangingQueryGenerator{
    protected $module;
	/**
	 * @var VtigerCRMObjectMeta
	 */
	protected $meta;
    protected $user;
    protected $conditions;
	protected $conditionals;
	protected $referenceModuleMetaInfo;
    protected $groupType;
	protected $whereFields;
	protected $columns;
    protected $fromClause;
	protected $whereClause;
	protected $query;
    protected $groupInfo;
    public $conditionInstanceCount;
    public static $AND = 'AND';
	public static $OR = 'OR';

    public function __construct($module, $user) {
		$db = PearDatabase::getInstance();
		$this->module = $module;
		$this->meta = $this->getMeta($module);
		$this->conditionals = array();
		$this->user = $user;
		$this->conditions = null;
		$this->referenceModuleMetaInfo = array();
		$this->groupType = self::$AND;
		$this->whereFields = array();
		$this->fromClause = null;
		$this->whereClause = null;
		$this->groupInfo = '';
		$this->conditionInstanceCount = 0;
	}

	public function reset() {
		$this->fromClause = null;
		$this->whereClause = null;
		$this->columns = null;
		$this->query = null;
	}

	public function getModule () {
		return $this->module;
	}

	public function getModuleFields() {
		$moduleFields = $this->meta->getModuleFields();

		$module = $this->getModule();
		return $moduleFields;
	}

	public function getQuery() {
		if(empty($this->query)) {
			$allFields = array_merge($this->fields, (array)$this->whereFields);
			foreach ($allFields as $fieldName) {
				if(in_array($fieldName,$this->referenceFieldList)) {
					$moduleList = $this->referenceFieldInfoList[$fieldName];
					foreach ($moduleList as $module) {
						if(empty($this->moduleNameFields[$module])) {
							$meta = $this->getMeta($module);
						}
					}
				} elseif(in_array($fieldName, $this->ownerFields )) {
					$meta = $this->getMeta('Users');
					$meta = $this->getMeta('Groups');
				}
			}

			$query = "SELECT ";    // Removed DISTINCT keyword by Hieu Nguyen on 2021-01-27 to boost performance
			$query .= $this->getSelectClauseColumnSQL();
			$query .= $this->getFromClause();
			$query .= $this->getWhereClause();
			$this->query = $query;
			return $query;
		} else {
			return $this->query;
		}
	}

    public function startGroup($groupType) {
		$this->groupInfo .= " $groupType (";
	}

	public function endGroup() {
		$this->groupInfo .= ')';
	}

	public function addConditionGlue($glue) {
		$this->groupInfo .= " $glue ";
	}

    public function initForStageChangingConditionByStageId($stageid) {
		$stagePipelineCondition = new StagePipelineCondition($this->module);
		$this->conditions = $stagePipelineCondition->getConditionsByStageID($stageid);

		if($this->conditionInstanceCount <= 0 && is_array($this->conditions) && count($this->conditions) > 0) {
			$this->startGroup('');
		} elseif($this->conditionInstanceCount > 0 && is_array($this->conditions) && count($this->conditions) > 0) {
			$this->addConditionGlue(self::$AND);
		}
		if(is_array($this->conditions) && count($this->conditions) > 0) {
			$this->parseconditions($this->conditions);
		}
		if($this->conditionInstanceCount > 0) {
			$this->endGroup();
		}
	}

    public function fixDateTimeValue($name, $value, $first = true) {
		$moduleFields = $this->getModuleFields();
		$field = $moduleFields[$name];
		$type = $field ? $field->getFieldDataType() : false;
		if($type == 'datetime') {
			if(strrpos($value, ' ') === false) {
				if($first) {
					return $value.' 00:00:00';
				}else{
					return $value.' 23:59:59';
				}
			}
		}
		return $value;
	}

	public function addCondition($fieldname,$value,$operator,$glue= null,$newGroup = false,
		$newGroupType = null, $ignoreComma = false) {
		$conditionNumber = $this->conditionInstanceCount++;
		if($glue != null && $conditionNumber > 0)
			$this->addConditionGlue ($glue);

		$this->groupInfo .= "$conditionNumber ";
		$this->whereFields[] = $fieldname;
		$this->ignoreComma = $ignoreComma;
		$this->reset();
		$this->conditionals[$conditionNumber] = $this->getConditionalArray($fieldname,
				$value, $operator);
	}

	protected function getConditionalArray($fieldname,$value,$operator) {
		if(is_string($value)) {
			$value = trim($value);
		} elseif(is_array($value)) {
			$value = array_map(trim, $value);
		}
		return array('name'=>$fieldname,'value'=>$value,'operator'=>$operator);
	}

    public function parseconditions($conditions, $glue=''){
		if(!empty($glue)) $this->addConditionGlue($glue);

		$stagePipelineCondition = new StagePipelineCondition($this->module);
		$dateSpecificConditions = $stagePipelineCondition->getStdFilterConditions();
		foreach ($conditions as $groupindex=>$groupcolumns) {
			$filtercolumns = $groupcolumns['columns'];
			if (count($filtercolumns) > 0) {
				$this->startGroup('');
				foreach ($filtercolumns as $index=>$filter) {
					$nameComponents = explode(':',$filter['columnname']);
					$name = $nameComponents[2];

					if(($nameComponents[4] == 'D' || $nameComponents[4] == 'DT') && in_array($filter['compareType'], $dateSpecificConditions)) {
						$filter['stdfilter'] = $filter['compareType'];
						$valueComponents = explode(',',$filter['value']);
						if($filter['compareType'] == 'custom') {
							if($nameComponents[4] == 'DT') {
								$startDateTimeComponents = explode(' ',$valueComponents[0]);
								$endDateTimeComponents = explode(' ',$valueComponents[1]);
								$filter['startdate'] = DateTimeField::convertToDBFormat($startDateTimeComponents[0]);
								$filter['enddate'] = DateTimeField::convertToDBFormat($endDateTimeComponents[0]);
							} else {
								$filter['startdate'] = DateTimeField::convertToDBFormat($valueComponents[0]);
								$filter['enddate'] = DateTimeField::convertToDBFormat($valueComponents[1]);
							}
						}
						$dateFilterResolvedList = $stagePipelineCondition->resolveDateFilterValue($filter);
						// If datatype is DT then we should append time also
						if($nameComponents[4] == 'DT'){
							$startdate = explode(' ', $dateFilterResolvedList['startdate']);
							if($startdate[1] == '')
								$startdate[1] = '00:00:00';
							$dateFilterResolvedList['startdate'] = $startdate[0].' '.$startdate[1];

							$enddate = explode(' ',$dateFilterResolvedList['enddate']);
							if($enddate[1] == '')
								$enddate[1] = '23:59:59';
							$dateFilterResolvedList['enddate'] = $enddate[0].' '.$enddate[1];
						}
						$value = array();
						$value[] = $this->fixDateTimeValue($name, $dateFilterResolvedList['startdate']);
						$value[] = $this->fixDateTimeValue($name, $dateFilterResolvedList['enddate'], false);
						$this->addCondition($name, $value, 'BETWEEN');
					} else if($nameComponents[4] == 'DT' && ($filter['compareType'] == 'e' || $filter['compareType'] == 'n')) {
						$filter['stdfilter'] = $filter['compareType'];
						$dateTimeComponents = explode(' ',$filter['value']);
						$filter['startdate'] = DateTimeField::convertToDBFormat($dateTimeComponents[0]);
						$filter['enddate'] = DateTimeField::convertToDBFormat($dateTimeComponents[0]);

						$startDate = $this->fixDateTimeValue($name, $filter['startdate']);
						$endDate = $this->fixDateTimeValue($name, $filter['enddate'],false);

						$value = array();
						$start = explode(' ', $startDate);
						if($start[1] == "")
							$startDate = $start[0].' '.'00:00:00';

						$end = explode(' ',$endDate);
						if($end[1] == "")
							$endDate = $end[0].' '.'23:59:59';

						$value[] = $startDate;
						$value[] = $endDate;
						if($filter['compareType'] == 'n') {
							$this->addCondition($name, $value, 'NOTEQUAL');
						} else {
							$this->addCondition($name, $value, 'BETWEEN');
						}
					} else if($nameComponents[4] == 'DT' && ($filter['compareType'] == 'a' || $filter['compareType'] == 'b')) {
						$dateTime = explode(' ', $filter['value']);
						$date = DateTimeField::convertToDBFormat($dateTime[0]);
						$value = array();
						$value[] = $this->fixDateTimeValue($name, $date, false);
						// Still fixDateTimeValue returns only date value, we need to append time because it is DT type
						for($i=0;$i<count($value);$i++){
							$values = explode(' ', $value[$i]);
							if($values[1] == ''){
								$values[1] = '00:00:00';
							}
							$value[$i] = $values[0].' '.$values[1];
						}
						$this->addCondition($name, $value, $filter['compareType']);
					} else{
						$this->addCondition($name, $filter['value'], $filter['compareType']);
					}
					$columncondition = $filter['column_condition'];
					if(!empty($columncondition)) {
						$this->addConditionGlue($columncondition);
					}
				}
				$this->endGroup();
				$groupConditionGlue = $groupcolumns['condition'];
				if(!empty($groupConditionGlue))
					$this->addConditionGlue($groupConditionGlue);
			}
		}
	}
}
?>