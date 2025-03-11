// Add by Dien Nguyen on 2025-03-11 to schedule pipeline action
<?php
require_once ('modules/com_vtiger_workflow/WorkflowScheduler.inc');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once 'modules/Users/Users.php';

class PipelineScheduler{
    public function queuePipelineActions() {
		global $default_timezone, $adb;
        $scheduleDates = array();

        $actionQueue = new ActionQueue();
		$entityCache = new VTEntityCache($this->user);

		// set the time zone to the admin's time zone, this is needed so that the scheduled workflow will be triggered
		// at admin's time zone rather than the systems time zone. This is specially needed for Hourly and Daily scheduled workflows
		$admin = Users::getRootAdminUser();
		$adminTimeZone = $admin->time_zone;
		@date_default_timezone_set($adminTimeZone);
		$currentTimestamp  = date("Y-m-d H:i:s");
		@date_default_timezone_set($default_timezone);

		$activePipelines = PipelineAction::getActivePipeline();
		$noOfActivePipelines = count($activePipelines);
		for ($i = 0; $i < $noOfActivePipelines; ++$i) {
			$pipeline = $activePipelines[$i];
            $moduleName = $pipeline['module'];
			$stages = PipelineAction::getStageForPipeline($pipeline['pipelineid']);
			if ($stages) {
                $noOfStages = count($stages);
                for ($i = 0; $i < $noOfStages; ++$i) {
                    $stage = $stages[$i];
                    $page = 0;
                    do {
                        $records = $this->getEligibleStageRecords($stage, $page++, 100, $moduleName);
                        $noOfRecords = count($records);
                        
                        if ($noOfRecords < 1) break;
                        
                        for ($j = 0; $j < $noOfRecords; ++$j) {
                            $recordId = $records[$j];
                            // We need to pass proper module name to get the webservice
                            $wsEntityId = vtws_getWebserviceEntityId($moduleName, $recordId);
                            $entityData = $entityCache->forId($wsEntityId);
                            $data = $entityData->getData();

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

                                // check if action is onceAction and is executed then skip
                                if ($action['frequency' === 'onceAction']){
                                    // code
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
                                    $delay = strtotime($data['stage_changing_time']) + $delayTime;
                                }

                                // If action is scheduled then we have to schedule CronTx with that specified time
                                $time = time();
                                if($delay > 0 && $delay >= $time){
                                    $scheduleDates[] = gmdate('Y-m-d H:i:s',$delay);
                                } else{
                                    $delay = 0;
                                }

                                // If action is immediate then process the action
                                if ($action['action_time_type'] === 'immediate') {
                                    try {
                                        // process action
                                        // PipelineAction::processActions();
                                    }
                                    catch (Throwable $ex) {
                                        echo "".$ex;
                                    }
                                } else {
                                    $actionQueue->queueAction($action, $entityData->getId(), $delay);
                                }
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
		$query = $this->getPipelineStageQuery($stage, $start, $limit);
        $stageid = $stage['stageid'];
		$result = $adb->query($query, array($stageid));
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
        $query = 'SELECT * FROM $tableName WHERE stageid = ?';
		if($limit) {
			$query .= ' LIMIT '. ($start * $limit) . ',' .$limit;
		}
		return $query;
	}
}