<?php
// Add by Dien Nguyen on 2025-03-11 to schedule pipeline action
require_once ('data/CRMEntity.php');
require_once ('modules/com_vtiger_workflow/VTEntityCache.inc');
require_once 'modules/Users/Users.php';
require_once 'modules/Settings/PipelineConfig/models/ActionQueue.php';
require_once 'modules/Settings/PipelineConfig/models/PipelineAction.php';

class PipelineScheduler{
    public function queuePipelineActions() {
		global $default_timezone, $adb;
        $scheduleDates = array();

        $actionQueue = new ActionQueue();
		// set the time zone to the admin's time zone, this is needed so that the scheduled workflow will be triggered
		// at admin's time zone rather than the systems time zone. This is specially needed for Hourly and Daily scheduled workflows
		$admin = Users::getRootAdminUser();
		$adminTimeZone = $admin->time_zone;
		@date_default_timezone_set($adminTimeZone);
		$currentTimestamp  = date("Y-m-d H:i:s");
		@date_default_timezone_set($default_timezone);

		$activePipelines = PipelineAction::getActivePipeline();
		$noOfActivePipelines = count($activePipelines);
		for ($i = 0; $i < $noOfActivePipelines; $i++) {
			$pipeline = $activePipelines[$i];
            $moduleName = $pipeline['module'];
			$stages = PipelineAction::getStageForPipeline($pipeline['pipelineid']);
			if ($stages) {
                $noOfStages = count($stages);
                for ($s = 0; $s < $noOfStages; ++$s) {
                    $stage = $stages[$s];
                    $page = 0;
                    do {
                        $records = $this->getEligibleStageRecords($stage, $page++, 100, $moduleName);
                        $noOfRecords = count($records);
                        if ($noOfRecords < 1) break;
                        for ($j = 0; $j < $noOfRecords; ++$j) {
                            $recordId = $records[$j];
                            // We need to pass proper module name to get the webservice
                            $wsEntityId = vtws_getWebserviceEntityId($moduleName, $recordId);
                            $entityData = vtws_retrieve($wsEntityId, $admin);
                            $data = $entityData;

                            // get action from stageid
                            $actions = PipelineAction::getActions($stage['stageid']);
                            foreach ($actions as $action) {
                                $delay = 0;
                                $actionType = $action['action_type'];

                                //Check whether action is sendEmail and then check emailoptout value 
                                //if enabled don't queue the email 
                                if($actionType == 'sendEmail'){
                                    if($data['emailoptout'] == 1) continue;
                                }

                                // check if action is onceAction and was executed then skip
                                if ($action['frequency'] === 'onceAction') {
                                    if ($this->isActionExcecuted($wsEntityId, $action)) {
                                        continue;
                                    }
                                    else {
                                        $this->storeAction($wsEntityId, $action);
                                    }
                                }

                                // convert delayTime and delayTimeUnit to seconds
                                $delayTime = $action['time'];
                                $delayTimeUnit = $action['time_unit'];
                                if ($delayTime != null && $action['action_time_type'] === 'scheduled') {
                                    if ($delayTimeUnit == 'minutes'){
                                        $delayTime*=60;
                                    }
                                    else if ($delayTimeUnit == 'hours'){
                                        $delayTime*=3600;
                                    }
                                    else if ($delayTimeUnit == 'days'){
                                        $delayTime*=86400;
                                    }
                                    $delay = $data['stage_changing_time'] + $delayTime;
                                    
                                }

                                // If action is scheduled then we have to schedule CronTx with that specified time
                                $time = time();
                                if($delay > 0 && $delay >= $time){
                                    $scheduleDates[] = gmdate('Y-m-d H:i:s',$delay);
                                    $actionQueue->queueAction($action, $wsEntityId, $delay);
                                } else{
                                    $delay = 0;
                                }

                                // If action is immediate then process the action
                                // if ($action['action_time_type'] === 'immediate') {
                                //     try {
                                //         // process immediate action
                                //         PipelineAction::processActions($action, $entityData->getId(), $entityData->getModuleName());
                                //     }
                                //     catch (Throwable $ex) {
                                //         echo "".$ex;
                                //     }
                                // } else if ($delay > 0){
                                //     $actionQueue->queueAction($action, $entityData->getId(), $delay);
                                    
                                // }
                                // If action is scheduled then queue action
                                // echo "t".$time;
                                // echo "d".$delay;
                            }
                        }
                    } while(true);
                }
			}
		}
		$activePipelines = null;
	}

    public function getEligibleStageRecords($stage, $start=0, $limit=0, $moduleName='Potentials') {
		global $adb;
		$query = $this->getPipelineStageQuery($stage, $start, $limit, $moduleName);
        $stageid = $stage['stageid'];
		$result = $adb->pquery($query, array($stageid));
		$noOfRecords = $adb->num_rows($result);
		$recordsList = array();
		for ($i = 0; $i < $noOfRecords; ++$i) {
			$recordsList[] = $adb->query_result($result, $i, 0);
		}
		$result = null;
		return $recordsList;
	}

    public function getPipelineStageQuery($stage, $start=0, $limit=0, $moduleName) {
        //Get the entity instance
        $entity = CRMEntity::getInstance($moduleName);
        $tableName = $entity->table_name;
        
        $primaryKey = $tableName->tab_name_index[$tableName];
        $query = 'SELECT * FROM '.$tableName.' WHERE stageid = ?';
		if($limit) {
			$query .= ' LIMIT '. ($start * $limit) . ',' .$limit;
		}
		return $query;
	}

    // Add by Dien Nguyen on 2025-03-12 to check if once-action is executed 
    public function isActionExcecuted($wsEntityId, $action){
        global $adb;
        $actionContents = json_encode($action);
        $query = "SELECT COUNT(*) as count FROM vtiger_pipelineaction_skip WHERE entity_id=? AND action_contents=?";
        $params = array($wsEntityId, $actionContents);
        $result = $adb->pquery($query, $params);
        $row = $adb->fetchByAssoc($result);
        return $row['count'] > 0;
    }

    // Add by Dien Nguyen on 2025-03-12 to store once-action to skip next time
    public function storeAction($wsEntityId, $action){
        global $adb;
        $actionContents = json_encode($action);
        $query = "INSERT INTO vtiger_pipelineaction_skip(entity_id, action_contents) VALUES(?, ?)";
        $params = array($wsEntityId, $actionContents);
        $adb->pquery($query, $params);
    }
}