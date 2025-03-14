<?php
/*+**********************************************************************************
Add by Dien Nguyen on 2025-03-06 to create new project task
 ************************************************************************************/
require_once('include/Webservices/Utils.php');
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/Webservices/ModuleTypes.php';
require_once('include/Webservices/Create.php');
require_once 'include/Webservices/DescribeObject.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';

require_once("modules/Users/Users.php");

class VTCreateNewProjectTask extends VTTask{
	public $executeImmediately = true;

    // Add by Dien Nguyen on 2025-03-03 to define fiels of a project task
	public function getFieldNames(){
		return [
            "assigned_user_id", "description", "projectid", "projecttaskname", 
            "projecttaskstatus", "projecttasktype", "startdate",
            "enddate" 
        ];
	}

	function getAdmin(){
		$user = Users::getRootAdminUser();
		global $current_user;
		$this->originalUser = $current_user;
		$current_user = $user;
		return $user;
	}

	public function doTask($entityData){
		global $adb, $current_user;
		$userId = $entityData->get('assigned_user_id');
		if($userId===null){
			$userId = vtws_getWebserviceEntityId('Users', Users::getRootAdminId());
		}

		$moduleName = 'ProjectTask';
		$parentModuleName = $entityData->getModuleName();
		$adminUser = $this->getAdmin();

		$endDate = $this->calculateDate($entityData, $this->endDays,
															$this->endDayDirection, $this->endDatefield);

		// Added to check if the user/group is active
		if(!empty($this->assigned_user_id)) {
			$userExists = $adb->pquery('SELECT 1 FROM vtiger_users WHERE id = ? AND status = ?', array($this->assigned_user_id, 'Active'));
			if($adb->num_rows($userExists)) {
				$assignedUserId = vtws_getWebserviceEntityId('Users', $this->assigned_user_id);
				$userId = $assignedUserId;
			} else {
				$groupExist = $adb->pquery('SELECT 1 FROM vtiger_groups WHERE groupid = ?', array($this->assigned_user_id));
				if($adb->num_rows($groupExist)) {
					$assignedGroupId = vtws_getWebserviceEntityId('Groups', $this->assigned_user_id);
					$userId = $assignedGroupId;
				}
				else{ 
					if($this->assigned_user_id == 'copyParentOwner'){ 
						$userId = $entityData->get('assigned_user_id'); 
					}
				}
			}
		}

        $this->projectid = vtws_getWebserviceEntityId('Project', $this->projectid);
		
		$fields = array(
			"assigned_user_id"   => $userId, // Example: "19x1"
            "description"        => $this->description,
            "projectid"          => $this->projectid, // ID của dự án liên quan
            "projecttaskname"    => $this->projecttaskname,
            "projecttaskstatus"  => $this->projecttaskstatus,
            "projecttasktype"    => $this->projecttasktype,
            "startdate"          => date("Y-m-d", strtotime($this->startdate)), // Convert to format YYYY-MM-DD
            "enddate"            => date("Y-m-d", strtotime($this->enddate)), // Convert to format YYYY-MM-DD
            "end_date_plan"      => date("Y-m-d", strtotime($endDate)) // Convert to YYYY-MM-DD
		);

        // handle save value for owner field
        $tempEntify = clone $entityData;

        if ($this->assign_parent_record_owners == 1) {
            list($ownerModuleId, $ownerId) = explode('x', $entityData->get('assigned_user_id'));
            $this->assigned_user_id = $ownerId;

            // Set default main owner id to admin user when the parent record is assigned to a group
            if ($ownerModuleId == 20) {
                $this->assigned_user_id = $current_user->id;
                $this->main_owner_id = $current_user->id;

                if ($entityData->get('main_owner_id') != -1) {
                    $ownerId = end(explode('x', $entityData->get('main_owner_id')));
                    $this->assigned_user_id = $ownerId;
                    $this->main_owner_id = $ownerId;
                }
            }
        }
        
        $tempEntify->set('assigned_user_id', $this->assigned_user_id);  // Borrow this object to set owner
        $tempEntify->set('main_owner_id', $this->main_owner_id);
        Vtiger_CustomOwnerField_Helper::setOwner($tempEntify);
        $fields['assigned_user_id'] = $tempEntify->get('assigned_user_id');
        $fields['main_owner_id'] = $tempEntify->get('main_owner_id');
        // End Hieu Nguyen
		
		//Setting visibility value		
		$id = $entityData->getId();

		// Modified by Hieu Nguyen on 2022-02-21 to set the right related field value
		if ($parentModuleName == 'Contacts'){
			$fields['contact_id'] = $id;
		}
		else if ($parentModuleName == 'Accounts') {
			$fields['related_account'] = $id;
		}
		else if ($parentModuleName == 'Leads') {
			$fields['related_lead'] = $id;
		}
		// End Hieu Nguyen
		else{
			$data = vtws_describe('ProjectTask', $adminUser);
			$fieldInfo = $data['fields'];
			foreach($fieldInfo as $field){
				if($field['name']=='parent_id'){
					$parentIdField = $field;
				}
			}
			$refersTo = $parentIdField['type']['refersTo'];

			if(in_array($parentModuleName, $refersTo)){
				$fields['parent_id'] = $id;
			}
		}
		
		$entityModuleHandler = vtws_getModuleHandlerFromName($moduleName, $current_user);
		$handlerMeta = $entityModuleHandler->getMeta();
		$moduleFields = $handlerMeta->getModuleFields();
		foreach ($moduleFields as $name => $fieldModel) {
			if(!empty($fields[$name])) {
				continue;
			} else if(!empty($this->$name)) {
				$fields[$name] = $this->$name;
			}
		}
		
		$mandatoryFields = $handlerMeta->getMandatoryFields();
		foreach ($mandatoryFields as $fieldName) {
			$fieldInstance = $moduleFields[$fieldName];
			$fieldDataType = $fieldInstance->getFieldDataType();
			if(!empty($fields[$fieldName])) {
				continue;
			} else {
				$fieldValue = $this->$fieldName;
				if(empty($fieldValue)) {
					$defaultValue = $fieldInstance->getDefault();
					$fieldValue = $defaultValue;
				}
				if(empty($fieldValue)) {
                    $utilHelper = new Vtiger_Util_Helper();
					$fieldValue = $utilHelper->getDefaultMandatoryValue($fieldDataType);
					if($fieldDataType == 'picklist' || $fieldDataType == 'multipicklist') {
						$picklistValues = $fieldInstance->getPickListOptions();
						$fieldValue = $picklistValues[0]['label'];
					}
				}
				$fields[$fieldName] = $fieldValue;
			}
		}

		try {
			$fields['source'] = 'PIPELINE';
			$projectTask = vtws_create($moduleName, $fields, $adminUser);
		} catch (Exception $e) {
            echo "".$e;
            // Added by Hieu nguyen on 2020-10-26 to save error log
            VTTask::saveLog('[VTCreateProjectTask::doTask] Error: ' . $e->getMessage(), $e->getTrace());
            // End Hieu Nguyen
		}
		global $current_user;
		$current_user = $this->originalUser;
	}
	
	private function calculateDate($entityData, $days, $direction, $datefield){
		$baseDate = $entityData->get($datefield);
		if($baseDate == '') {
			$baseDate = date('Y-m-d');
		}
		if($days == '') {
			$days = 0;
		}
		preg_match('/\d\d\d\d-\d\d-\d\d/', $baseDate, $match);
		$baseDate = strtotime($match[0]);
		$date = strftime('%Y-%m-%d', $baseDate+$days*24*60*60*
										 (strtolower($direction)=='before'?-1:1));
		return $date;
	}
}
?>