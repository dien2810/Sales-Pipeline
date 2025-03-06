<?php
//  File: PipelineAction.php
//  Author:Team
//  Created Date: 04.03.2025
//  Description: This class handles the execution of pipeline steps, including checking conditions and performing actions on records.
require_once('config.inc.php');
require_once('include/database/PearDatabase.php');
require_once('modules/com_vtiger_workflow/VTWorkflowUtils.php');
require_once('modules/com_vtiger_workflow/VTTaskManager.inc');
require_once('modules/com_vtiger_workflow/VTTaskQueue.inc');
require_once('modules/com_vtiger_workflow/tasks/VTCreateEventTask.inc');
require_once('modules/com_vtiger_workflow/tasks/VTCreateTodoTask.inc');
require_once('modules/com_vtiger_workflow/tasks/VTUpdateFieldsTask.inc');
require_once('modules/com_vtiger_workflow/tasks/VTCreateEntityTask.inc');
require_once('data/CRMEntity.php');
class PipelineAction 
{
    //Begin process Actions
    //Implement by Team to process actions
	public static function processActions($idRecord, $idStageNext, $moduleName) {
		try {
			$actions = self::getActions($idStageNext);			
			if (!$actions) return true;
			foreach ($actions as $action) {
				if (!isset($action['action_type'])) continue;
				switch ($action['action_type']) {
					case 'addCall':
						self::processAddCall($action, $idRecord, $moduleName);
						break;
					case 'addMeeting':
						self::processAddMeeting($action, $idRecord, $moduleName);
						break;
					case 'createNewTask':
						self::processCreateNewTask($action, $idRecord, $moduleName);
						break;
					case 'updateDataField':
                        // Implement by The Vi to process update data fields
					    self::processUpdateDataFields($action, $idRecord,   $moduleName);
						break;
					case 'notification':
                        // Implement by The Vi to process send notifications
						self::processSendNotifications($action, $idRecord,  $moduleName);
						break;
					case 'createNewRecord':
						self::processCreateNewRecords($action, $idRecord,  $moduleName);
						break;
				}
				
			}
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	// Implement by Dien Nguyen on 2025-02-21 to process add call action
	private static function processAddCall($action, $idRecord, $moduleName) {
		// Initialize workflow utilities and get admin user context
		$util = new VTWorkflowUtils();
		$adminUser = $util->adminUser();

		// Retrieve the CRM entity using caching mechanism
		$entityCache = new VTEntityCache($adminUser);
		
		$wsEntityId = vtws_getWebserviceEntityId($moduleName, $idRecord);
		$entity = $entityCache->forId($wsEntityId);

		// Build fields for add call action
		if (isset($action['callInfo'])) {
			$callInfo = $action['callInfo'];
			$task = new VTCreateEventTask();
			$task->startDays = $callInfo['startDays'];
			$task->startDirection = $callInfo['startDirection'];
			$task->startDatefield = $callInfo['startDatefield'];
			$task->endDays = $callInfo['startDays'];
			$task->endDirection = $callInfo['startDirection'];
			$task->endDatefield = $callInfo['startDatefield'];
			$task->assigned_user_id = $callInfo['assigned_user_id'];
			$task->startTime = $callInfo['startTime'];
			$task->endTime = $callInfo['endTime'];
			$task->eventType = 'Call';
			$task->description = $callInfo['description'];
			$task->eventName = $action['action_name'];
			$task->priority = $callInfo['priority'];
			$task->status = $callInfo['status'];
			$task->recurringtype = $callInfo['recurringtype'];
			$task->assign_parent_record_owners = $callInfo['assign_parent_record_owners'];
			$task->recurringcheck = $callInfo['recurringcheck'];
			$task->calendar_repeat_limit_date = $callInfo['calendar_repeat_limit_date'];
			$task->sendNotification = $callInfo['sendNotification'];
			$task->repeat_frequency = $callInfo['repeat_frequency'];
			$task->repeatMonth = $callInfo['repeatMonth'];
			$task->repeatMonth_date = $callInfo['repeatMonth_date'];
			$task->repeatMonth_daytype = $callInfo['repeatMonth_daytype'];
			$task->repeatMonth_day = $callInfo['repeatMonth_day'];

			$task->mon_flag = $callInfo['mon_flag'];
			$task->tue_flag = $callInfo['tue_flag'];
			$task->wed_flag = $callInfo['wed_flag'];
			$task->thu_flag = $callInfo['thu_flag'];
			$task->fri_flag = $callInfo['fri_flag'];
			$task->sat_flag = $callInfo['sat_flag'];
			$task->sun_flag = $callInfo['sun_flag'];

			// Execute the task and revert the user context back
			$task->doTask($entity);
			$util->revertUser();

		}
	}
	// Implement by Dien Nguyen on 2025-02-21 to process add meeting action
	public static function processAddMeeting($action, $idRecord, $moduleName){
		// Initialize workflow utilities and get admin user context
		$util = new VTWorkflowUtils();
		$adminUser = $util->adminUser();

		// Retrieve the CRM entity using caching mechanism
		$entityCache = new VTEntityCache($adminUser);
		
		$wsEntityId = vtws_getWebserviceEntityId($moduleName, $idRecord);
		$entity = $entityCache->forId($wsEntityId);

		// Build fields for add meeting action
		if (isset($action['meetingInfo'])){
			$meetingInfo = $action['meetingInfo'];
			$task = new VTCreateEventTask();
			$task->startDays = $meetingInfo['startDays'];
			$task->startDirection = $meetingInfo['startDirection'];
			$task->startDatefield = $meetingInfo['startDatefield'];
			$task->endDays = $meetingInfo['startDays'];
			$task->endDirection = $meetingInfo['startDirection'];
			$task->endDatefield = $meetingInfo['startDatefield'];
			$task->assigned_user_id = $meetingInfo['assigned_user_id'];
			$task->startTime = $meetingInfo['startTime'];
			$task->endTime = $meetingInfo['endTime'];
			$task->eventType = 'Meeting';
			$task->description = $meetingInfo['description'];
			$task->eventName = $action['action_name'];
			$task->priority = $meetingInfo['priority'];
			$task->status = $meetingInfo['status'];
			$task->recurringtype = $meetingInfo['recurringtype'];
			$task->assign_parent_record_owners = $meetingInfo['assign_parent_record_owners'];
			$task->recurringcheck = $meetingInfo['recurringcheck'];
			$task->calendar_repeat_limit_date = $meetingInfo['calendar_repeat_limit_date'];
			$task->sendNotification = $meetingInfo['sendNotification'];
			$task->repeat_frequency = $meetingInfo['repeat_frequency'];
			$task->repeatMonth = $meetingInfo['repeatMonth'];
			$task->repeatMonth_date = $meetingInfo['repeatMonth_date'];
			$task->repeatMonth_daytype = $meetingInfo['repeatMonth_daytype'];
			$task->repeatMonth_day = $meetingInfo['repeatMonth_day'];

			$task->mon_flag = $meetingInfo['mon_flag'];
			$task->tue_flag = $meetingInfo['tue_flag'];
			$task->wed_flag = $meetingInfo['wed_flag'];
			$task->thu_flag = $meetingInfo['thu_flag'];
			$task->fri_flag = $meetingInfo['fri_flag'];
			$task->sat_flag = $meetingInfo['sat_flag'];
			$task->sun_flag = $meetingInfo['sun_flag'];

			// Execute the task and revert the user context back
			$task->doTask($entity);
			$util->revertUser();
		}
	}

	// Implement by Dien Nguyen on 2025-03-04 to process create new task
	public static function processCreateNewTask($action, $idRecord, $moduleName) {
		// Initialize workflow utilities and get admin user context
		$util = new VTWorkflowUtils();
		$adminUser = $util->adminUser();

		// Retrieve the CRM entity using caching mechanism
		$entityCache = new VTEntityCache($adminUser);
		
		$wsEntityId = vtws_getWebserviceEntityId($moduleName, $idRecord);
		$entity = $entityCache->forId($wsEntityId);

		// Build fields for create new task action
		if (isset($action['taskInfo'])){
			$taskInfo = $action['taskInfo'];
			$task = new VTCreateTodoTask();
			$task->datefield = $taskInfo['datefield'];
			$task->days = $taskInfo['days'];
			$task->direction = $taskInfo['direction'];
			$task->assigned_user_id = $taskInfo['assigned_user_id'];
			$task->time = $taskInfo['time'];
			$task->description = $taskInfo['description'];
			$task->todo = $taskInfo['todo'];
			$task->priority = $taskInfo['priority'];
			$task->status = $taskInfo['status'];
			$task->sendNotification = $taskInfo['sendNotification'];
			$task->assign_parent_record_owners = $taskInfo['assign_parent_record_owners'];
			// Execute the task and revert the user context back
			$task->doTask($entity);
			$util->revertUser();
		}
	}

	// Implement by The Vi on 2025-03-01 to process send notifications
	public static function processSendNotifications($action, $idRecord, $moduleName) {
		$idNotification = null;

		// Validate and process user list from notification info
		if (isset($action['notificationInfo']['userList']) && is_array($action['notificationInfo']['userList'])) {
			$description = isset($action['notificationInfo']['description']) ? $action['notificationInfo']['description'] : '';

			// Loop through the user list to send notifications
			foreach ($action['notificationInfo']['userList'] as $userId) {
				$data = [
					'receiver_id'           => $userId,
					'category'              => 'update',
					'image'                 => '',
					'related_record_id'     => $idRecord,
					'related_record_name'   => '', 
					'related_module_name'   => $moduleName,
					'extra_data'            => [
						'action'  => 'action_pipeline',
						'message' => $description,
					],
				];

				// Save the notification and retrieve its ID
				$idNotification = CPNotifications_Data_Model::saveNotification($data);

				// Handle notification repetition if specified
				if (isset($action['notificationInfo']['repetition'])) {
					$repetition = $action['notificationInfo']['repetition'];
					$currentTime = date("Y-m-d H:i:s");

					// Determine the next send time based on the repetition type
					if ($repetition === 'sixtyMinutes') {
						$notificationSendTime = date("Y-m-d H:i:s", strtotime("+60 minutes", strtotime($currentTime)));
					} elseif ($repetition === 'everyDay') {
						$notificationSendTime = date("Y-m-d H:i:s", strtotime("+1 day", strtotime($currentTime)));
					} elseif ($repetition === 'everyWeek') {
						$notificationSendTime = date("Y-m-d H:i:s", strtotime("+7 days", strtotime($currentTime)));
					} else {
						return;
					}

					// Insert repetition schedule into the database
					$sql = "INSERT INTO vtiger_notification_repeat (notification_id, notification_type, notification_send_time) VALUES (?, ?, ?)";
					$db = PearDatabase::getInstance();
					$params = [$idNotification, $repetition, $notificationSendTime];
					$db->pquery($sql, $params);
				}
			}
		}
	}
	
    // Implement by The Vi on 2025-03-01 to process update data fields
	public static function processUpdateDataFields($action, $idRecord, $moduleName) 
	{
			// Initialize workflow utilities and get admin user context
			$util = new VTWorkflowUtils();
			$adminUser = $util->adminUser();

			// Retrieve the CRM entity using caching mechanism
			$entityCache = new VTEntityCache($adminUser);
			$wsEntityId = vtws_getWebserviceEntityId($moduleName, $idRecord);
			$entity = $entityCache->forId($wsEntityId);

			// Build mapping array for fields to update from the action data
			$fieldValueMapping = [];
			foreach ($action['updateDataFields'] as $fieldInfo) {
				$columnParts = explode(':', $fieldInfo['column_name']);
				$fieldName = $columnParts[2]; 

				$fieldValueMapping[] = [
					'fieldname'  => $fieldName,
					'valuetype'  => 'rawtext',
					'value'      => $fieldInfo['value']
				];
			}

			// Create update task and assign the field value mapping
			$task = new VTUpdateFieldsTask();
			$task->field_value_mapping = $fieldValueMapping;
			
			// Execute the update task and revert the user context back
			$task->doTask($entity);
			$util->revertUser();
    }
	
	// Implement by The Vi on 2025-03-01 to process create new records
	public static function processCreateNewRecords($action, $idRecord, $moduleName) 
	{
		// Initialize workflow utilities and retrieve the admin user
		$util = new VTWorkflowUtils();
		$adminUser = $util->adminUser();

		// Retrieve the entity from the cache
		$entityCache = new VTEntityCache($adminUser);
		$wsEntityId = vtws_getWebserviceEntityId($moduleName, $idRecord);
		$entity = $entityCache->forId($wsEntityId);

		// Extract record creation information from the action
		$createInfo = $action['createNewRecordInfo'];
		$entityType = $createInfo['entity_type'];
		$referenceField = $createInfo['reference_field'];
		$assignParentOwners = isset($createInfo['assign_parent_record_owners']) ? $createInfo['assign_parent_record_owners'] : 0;

		// Build the field_value_mapping from valid fields
		$fieldValueMapping = [];
		$excludedKeys = ['__vtrftk', 'taskType', 'action_name', 'modulename', 'fieldname', 'fieldValue', 'valuetype', 'entity_type', 'reference_field', 'assign_parent_record_owners'];
		
		foreach ($createInfo as $key => $value) {
			if (!in_array($key, $excludedKeys)) {
				$fieldValueMapping[] = [
					'fieldname' => $key,
					'valuetype' => 'rawtext', // Default value type is rawtext
					'value' => $value
				];
			}
		}

		// Initialize and configure the task
		$task = new VTCreateEntityTask();
		$task->entity_type = $entityType;
		$task->reference_field = $referenceField;
		$task->field_value_mapping = $fieldValueMapping;
		$task->assign_parent_record_owners = $assignParentOwners;

		// Execute the task to create a new record
		$task->doTask($entity);

		// Revert to the original user
		$util->revertUser();
	}

    //End process Action
    // Implement by The Vi on 2025-03-01 to get actions
	public static function getActions($idStageNext) {
		$db = PearDatabase::getInstance();
		$query = "SELECT actions FROM vtiger_stage WHERE stageid = ?";
		$result = $db->pquery($query, array($idStageNext));
		if ($db->num_rows($result) == 0) {
			return false;
		}
		$actionsJson = $db->query_result($result, 0, 'actions');
		$actionsJson = html_entity_decode($actionsJson, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$actions = json_decode($actionsJson, true);
		return $actions;
	}
	//Implement by The Vi on 2025-03-01 to get conditions
	public static function getConditions($idStageNext) {
		$db = PearDatabase::getInstance();
		$query = "SELECT conditions FROM vtiger_stage WHERE stageid = ?";

		$result = $db->pquery($query, array($idStageNext));
		if ($db->num_rows($result) == 0) {
			return false;
		}

		$conditionsJson = $db->query_result($result, 0, 'conditions');
		$conditionsJson = html_entity_decode($conditionsJson, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		$conditions = json_decode($conditionsJson, true);

		return $conditions;
	}

    //Implement by The Vi on 2025-03-01 to check conditions
    public static function checkConditions($idRecord, $idStageNext, $moduleName) {
       $conditions = self::getConditions($idStageNext);
       return self::checkPipelineStageConditions($idRecord, $conditions, $idStageNext, $moduleName);
    }

    //Implement by The Vi on 2025-03-01 to check change stage
    public static function checkChangeStage($idRecord, $idStageNext) {
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT stageid FROM vtiger_potential WHERE potentialid = ?", array($idRecord));
        
        if ($db->num_rows($result) == 0 || $db->query_result($result, 0, "stageid") === null) {
            return true;
        }
        
        $dbStageId = $db->query_result($result, 0, "stageid");

        if ($idStageNext == $dbStageId) {
            return true;
        }
        return false;
    }
    // Implement by The Vi and Tran Dien on 2025-03-01 to check pipeline stage conditions
    public static function checkPipelineStageConditions($recordid, $conditions, $stageid, $module) {
        $moduleModel = Vtiger_Module_Model::getInstance($module);
        $moduleName = $moduleModel->getName();
		
        //Get the entity instance
        $entity = CRMEntity::getInstance($moduleName);
        $tableName = $entity->table_name;
        $idColumn = $entity->tab_name_index[$tableName];
    
        global $adb;
        $user = Users_Record_Model::getCurrentUserModel();
        $queryGenerator = new QueryGenerator($moduleName, $user);
    
        $meta = $queryGenerator->getMeta($moduleName);
        $moduleFieldNames = $meta->getModuleFields();
        $fieldArray = array_keys($moduleFieldNames);
        $fieldArray[] = 'id';
        $queryGenerator->setFields($fieldArray);
    
        $queryGenerator->initForStageChangingConditionByStageId($stageid, $conditions);
        $query = $queryGenerator->getQuery();
        $result = $adb->pquery($query, []);
    
        if ($result && $adb->num_rows($result) > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                if ($row[$idColumn] == $recordid) {
                    return true;
                }
            }
        }
        return false;
    }
    // Implement by The Vi on 2025-03-05 to get pipeline stage info
    public static function getPipelineStageInfo($recordId, $moduleName) {
        $db = PearDatabase::getInstance();
        $result = array();
    
        // Check if the module exists
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        if (!$moduleInstance) {
            return array('error' => 'Invalid module name');
        }
    
        // Retrieve entity information for the module
        $focus = CRMEntity::getInstance($moduleName);
        $tableName = $focus->table_name; 
        $primaryKey = $focus->tab_name_index[$tableName]; 
    
        // Query to retrieve pipeline and stage details using alias names
        $query = "SELECT 
                    vp.pipelineid AS pipelineid,
                    vp.name AS pipelinename,
                    vp.module AS pipemodule,
                    vs.stageid AS stageid,
                    vs.name AS stagename,
                    vs.value AS stagevalue
                  FROM $tableName vt
                  LEFT JOIN vtiger_pipeline vp ON vp.pipelineid = vt.pipelineid
                  LEFT JOIN vtiger_stage vs ON vs.stageid = vt.stageid
                  WHERE vt.$primaryKey = ?";
    
        $queryResult = $db->pquery($query, array($recordId));
    
        if ($db->num_rows($queryResult) > 0) {
            // Retrieve pipeline information
            $result['pipeline'] = array(
                'id' => $db->query_result($queryResult, 0, 'pipelineid'),
                'name' => $db->query_result($queryResult, 0, 'pipelinename'),
                'module' => $db->query_result($queryResult, 0, 'pipemodule')
            );
    
            // Retrieve stage information
            $result['stage'] = array(
                'id' => $db->query_result($queryResult, 0, 'stageid'),
                'name' => $db->query_result($queryResult, 0, 'stagename'),
                'value' => $db->query_result($queryResult, 0, 'stagevalue')
            );
        }
    
        return $result;
    }
}
?>
