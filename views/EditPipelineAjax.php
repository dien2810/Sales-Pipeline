<?php
/*
	File: EditPipelineAjax.php
	Author: The Vi
	Date: 22/1/2025
	Purpose:Display Ajax-based interfaces for EditPipeline page, including step addition modal
*/
require_once('include/utils/LangUtils.php');
class Settings_PipelineConfig_EditPipelineAjax_View extends CustomView_Base_View {

	function __construct() {
		//Begin The Vi 3/3/2025
		$this->exposeMethod('getStagePipelineModal');
		$this->exposeMethod('getStagePipelineModalNew');
		$this->exposeMethod('getDeleteStageModal');
		$this->exposeMethod('getStagePipelineList');
		//End The Vi 3/3/2025
		//Begin Dien Nguyen
		$this->exposeMethod('getAddActionSettingModal');
		$this->exposeMethod('getAddConditionModal');
		$this->exposeMethod('getActionSettingModal');
		$this->exposeMethod('getConditionModal');
		$this->exposeMethod('getAddCallModal');
		$this->exposeMethod('getAddMeetingModal');
		$this->exposeMethod('getCreateNewTaskModal');
		$this->exposeMethod('getCreateNewProjectTaskModal');
		$this->exposeMethod('getCreateNewRecordModal');
		$this->exposeMethod('getSetValuePopup');
		$this->exposeMethod('getCreateEntity');
		$this->exposeMethod('getUpdateDataFieldModal');
		$this->exposeMethod('getAddNotificationModal');
		//End Dien Nguyen

		//Begin Minh Hoang 3/3/2025
		$this->exposeMethod('getSendModal');
		$this->exposeMethod('getSendZNSModal');
		//End Minh Hoang 3/3/2025
	}

	function validateRequest(Vtiger_Request $request) {
		$request->validateWriteAccess(); 
	}

	function process(Vtiger_Request $request) {
        $mode = $request->getMode();

		if (!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
        
	}
	// Implemented by The Vi to show Modal Add New Stage

	function getStagePipelineModal(Vtiger_Request $request) {

		$sourceModule = $request->get('source_module');
		$pickFieldId = Settings_PipelineConfig_Util_Helper::getModuleIdByName($sourceModule);;
        
		if (empty($sourceModule) || empty($pickFieldId)) return; 

        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickFieldId);
		$moduleName = $request->getModule(false);
        $qualifiedName = $request->getModule(false);
        $selectedFieldAllPickListValues = Vtiger_Util_Helper::getPickListValues($fieldModel->getName());
		
		require_once('include/utils/LangUtils.php');

		$modStringsEn = LangUtils::readModStrings($sourceModule, 'en_us');
		$modStringsVn = LangUtils::readModStrings($sourceModule, 'vn_vn');
		
		foreach ($selectedFieldAllPickListValues as $key => $value) {
			$selectedFieldAllPickListValues[$key] = [
				'pickFieldId' => $pickFieldId,
				'LABEL_DISPLAY_EN' => $modStringsEn['languageStrings'][$value] ?? $value,
				'LABEL_DISPLAY_VN' => $modStringsVn['languageStrings'][$value] ?? $value,
				'value' => $value,
				'color' => Settings_Picklist_Module_Model::getPicklistColor("leadstatus", $key) 
			];
		}
        $viewer = $this->getViewer($request);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('SELECTED_PICKLIST_FIELDMODEL',$fieldModel);
		$viewer->assign('SELECTED_MODULE_NAME',$sourceModule);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('FIELD_MODEL',$fieldModel);
		$viewer->assign('FIELD_VALUE_ID',$pickFieldId);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('ROLES_LIST', Settings_Roles_Record_Model::getAll());
        $viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$selectedFieldAllPickListValues);
	
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddStagePipelineModal.tpl');
	}
	// Implemented by The Vi to show Stage Pipeline

	function getStagePipelineList(Vtiger_Request $request) {
		
        $viewer = $this->getViewer($request);
		$viewer = $this->getViewer($request);
		$viewer->display('modules/Settings/PipelineConfig/tpls/StagePipeline.tpl');
	}
	// Implemented by The Vi to show Modal Delete Stage

	function getDeleteStageModal(Vtiger_Request $request) {
    
		$idPipeline = $request->get('idPipeline');
		$moduleName = $request->getModule(false);
	
		$listStage = Settings_PipelineConfig_Config_Model::getStageList($idPipeline);
		
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PIPELINE_ID', $idPipeline);
		$viewer->assign('STAGE_LIST', $listStage);
		$viewer->assign('MODULE_NAME', $moduleName);
		
		$viewer->display('modules/Settings/PipelineConfig/tpls/DeleteStageModal.tpl');
	}
	// Implemented by The Vi to show Modal Add New Stage

	function getStagePipelineModalNew(Vtiger_Request $request) {
    
		$moduleName = $request->getModule(false);
		
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddStagePipelineModalNew.tpl');
	}

	//Begin Tran Dien
    function getAddActionSettingModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$action = $request->get('actionInfo');
		// Respond
		$viewer = $this->getViewer($request);
		if (!empty($action)) {
			$viewer->assign('ACTION', $action);
		}
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddActionSettingModal.tpl');
	}

    function getAddConditionModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		
		// Respond
		$module = 'CustomView';
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$viewer = $this->getViewer($request);
		$conditions = $request->get('conditions');
		$viewer->assign('ADVANCE_CRITERIA', $conditions);
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$recordStructure = $recordStructureInstance->getStructure();
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$advanceFilterOpsByFieldType = Vtiger_Field_Model::getAdvancedFilterOpsByFieldType();
		$viewer->assign('ADVANCED_FILTER_OPTIONS', Vtiger_Field_Model::getAdvancedFilterOptions());
		$viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', $advanceFilterOpsByFieldType);
		$dateFilters = Vtiger_Field_Model::getDateFilterTypes();
		foreach($dateFilters as $comparatorKey => $comparatorInfo) {
			$comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
			$comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
			$comparatorInfo['label'] = vtranslate($comparatorInfo['label'],$module);
			$dateFilters[$comparatorKey] = $comparatorInfo;
		}
		$viewer->assign('DATE_FILTERS', $dateFilters);
		$viewer->assign('SOURCE_MODULE',$pipelineModule);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddConditionModal.tpl');
	}

	function getActionSettingModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$action = $request->get('actionInfo');
		// Respond
		$viewer = $this->getViewer($request);
		if (!empty($action)) {
			$viewer->assign('ACTION', $action);
		}
		$viewer->display('modules/Settings/PipelineConfig/tpls/ActionSettingModal.tpl');
	}

	// Add by Minh Hoang on 2025-02-14
	function getAddCallModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$actionData = $request->get('actionData');
		$allModules = getModulesTranslatedSingleLabel();

        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$dateTimeFields = $moduleModel->getFieldsByType(array('date', 'datetime'));
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();
		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->assign('DATETIME_FIELDS', $dateTimeFields);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$viewer->assign('ACTION_DATA', $actionData);
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddCallModal.tpl');
	}

	function getAddMeetingModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$actionData = $request->get('actionData');
		$allModules = getModulesTranslatedSingleLabel();
		// Keep only specific modules
        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$dateTimeFields = $moduleModel->getFieldsByType(array('date', 'datetime'));
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();
		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->assign('DATETIME_FIELDS', $dateTimeFields);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$viewer->assign('ACTION_DATA', $actionData);
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddMeetingModal.tpl');
	}

	function getCreateNewTaskModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Keep only specific modules
        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		// Render view
		$viewer = $this->getViewer($request);

		$workflowModel = Settings_Workflows_Record_Model::getCleanInstance($pipelineModule);
		
		$taskType = 'VTCreateTodoTask';
        $taskModel = Settings_Workflows_TaskRecord_Model::getCleanInstance($workflowModel, $taskType);		

		$taskTypeModel = $taskModel->getTaskType();
		
		$viewer->assign('TASK_TYPE_MODEL', $taskTypeModel);

		$viewer->assign('TASK_TEMPLATE_PATH', $taskTypeModel->getTemplatePath());
		$recordStructureInstance = Settings_Workflows_RecordStructure_Model::getInstanceForWorkFlowModule($workflowModel,
																			Settings_Workflows_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDITTASK);
        $recordStructureInstance->setTaskRecordModel($taskModel);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$dateTimeFields = $moduleModel->getFieldsByType(array('date', 'datetime'));
		$viewer->assign('DATETIME_FIELDS', $dateTimeFields);

		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		// Modal send Update Data Field
		
		$viewer->display('modules/Settings/PipelineConfig/tpls/CreateNewTaskModal.tpl');
	}
	
	// Add by Dien Nguyen on 2025-03-06 to show Modal Create New Project Task
	function getCreateNewProjectTaskModal(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$recordModel = Vtiger_Record_Model::getCleanInstance('ProjectTask');

		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DEFAULT);
		$recordStructure = $recordStructureInstance->getStructure();
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$viewer->assign('RECORD_STRUCTURE_JSON', Vtiger_Functions::jsonEncode($recordStructure));

		$moduleName = 'ProjectTask';
		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);

		$fieldModel = Vtiger_Field_Model::getInstance('projecttasktype', $moduleModel);
		$taskTypePicklistValues = $fieldModel->getPicklistValues();
		$viewer->assign('PROJECT_TASK_TYPE_VALUES', $taskTypePicklistValues);

		$fieldModel = Vtiger_Field_Model::getInstance('projecttaskstatus', $moduleModel);
		$taskStatusPicklistValues = $fieldModel->getPicklistValues();
		$viewer->assign('PROJECT_TASK_STATUS_VALUES', $taskStatusPicklistValues);

		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('MODULE_MODEL',$moduleModel);

		$dateTimeFields = $moduleModel->getFieldsByType(array('date', 'datetime'));
		$viewer->assign('DATETIME_FIELDS', $dateTimeFields);
		
		
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/CreateNewProjectTaskModal.tpl');
	}

	function getCreateNewRecordModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Keep only specific modules
        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();
		$RELATED_MODULES_INFO = $this->getDependentModules();
		$RELATED_MODULES = array_keys($RELATED_MODULES_INFO);

		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$moduleName = $request->getModule(false);		
		$viewer->assign('QUALIFIED_MODULE', $moduleName);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$viewer->assign('RELATED_MODULES', $RELATED_MODULES);
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/CreateNewRecordModal.tpl');
	}

	function getSetValuePopup(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Keep only specific modules
        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();
		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/SetValuePopup.tpl');
	}

	function getCreateEntity(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$moduleName = $request->get('module_name');
		
		$viewer->assign('SOURCE_MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', 'Settings:Workflows');
		$allModules = getModulesTranslatedSingleLabel();
		// Keep only specific modules
        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }
		$currentModuleName = $request->get('currentNameModule');
		
		
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();
		$relatedModule = $request->get('relatedModule');

		$workflowModel = Settings_Workflows_Record_Model::getCleanInstance($relatedModule);
		$taskType = 'VTCreateEntityTask';
        $taskModel = Settings_Workflows_TaskRecord_Model::getCleanInstance($workflowModel, $taskType);

		$taskTypeModel = $taskModel->getTaskType();
		$viewer->assign('TASK_TYPE_MODEL', $taskTypeModel);

		$viewer->assign('TASK_TEMPLATE_PATH', $taskTypeModel->getTemplatePath());
		$recordStructureInstance = Settings_Workflows_RecordStructure_Model::getInstanceForWorkFlowModule($workflowModel,
																			Settings_Workflows_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDITTASK);
        $recordStructureInstance->setTaskRecordModel($taskModel);

		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$relatedModule = $request->get('relatedModule');
		$relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);

		$workflowModuleModel = $workflowModel->getModule();
		$relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
		$viewer->assign('RELATED_MODULE_MODEL', $relatedModuleModel);
		// Render view
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->assign('DATE_FILTERS', Vtiger_Field_Model::getDateFilterTypes());
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/CreateEntity.tpl');
	}

	public function getDependentModules() {
		$modulesList = Settings_LayoutEditor_Module_Model::getEntityModulesList();
		// $primaryModule = $this->getModule();
		$primaryModule = Vtiger_Module_Model::getInstance('Potentials');

		if($primaryModule->isCommentEnabled()) {
			$modulesList['ModComments'] = 'ModComments';
		}
		$createModuleModels = array();
		// List of modules which will not be supported by 'Create Entity' workflow task
		$filterModules = array('Invoice', 'Quotes', 'SalesOrder', 'PurchaseOrder', 'Emails', 'Calendar', 'Events');

		foreach ($modulesList as $moduleName => $translatedModuleName) {
			$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
			if (in_array($moduleName, $filterModules))
				continue;
			$createModuleModels[$moduleName] = $moduleModel;
		}
		return $createModuleModels;
	}

	function getUpdateDataFieldModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Keep only specific modules
        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();
		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->assign('DATE_FILTERS', Vtiger_Field_Model::getDateFilterTypes());
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/DataFieldUpdateModal.tpl');
	}

	// Implemented by Minh Hoang to show Modal Send ZNS
	function getSendZNSModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();

		// Keep only specific modules
        $allowedModules = ['Potentials', 'Leads', 'Project', 'HelpDesk'];
        foreach ($allModules as $name => $label) {
            if (!in_array($name, $allowedModules)) {
                unset($allModules[$name]);
            }
        }

		// Get current module name
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);

		// Get record structure
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();

		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ALL_MODULES', $allModules);
		$viewer->assign('MODULE_MODEL',$moduleModel);
		$viewer->assign('DATE_FILTERS', Vtiger_Field_Model::getDateFilterTypes());
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);

		// Display modal Send ZNS
		$viewer->display('modules/Settings/PipelineConfig/tpls/SendZNSModal.tpl');
	}

	// Implemented by Minh Hoang to show Modal Send SMS
	function getSendModal(Vtiger_Request $request) {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
		$qualifiedModuleName = $request->getModule(false);
		$taskData = $request->get('taskData');

		// Added by Hieu Nguyen on 2021-09-30 to workarround load selected module fields by its EditView layout in edit/update fields task, otherwise load its DetailView layout for other tasks
		$taskType = !empty($taskData) ? $taskData['taskType'] : $request->get('type');
		
		if (in_array($taskType, ['VTCreateTodoTask', 'VTCreateEventTask', 'VTUpdateFieldsTask', 'VTCreateEntityTask'])) {
			$GLOBALS['current_view'] = 'edit';
		}
		else {
			$GLOBALS['current_view'] = 'detail';
		}
		// End Hieu Nguyen

        // Modified by Hieu Nguyen on 2021-02-01 to fix bug can not show raw value from From Email and Content fields
        if ($taskData && $taskData['taskType'] == 'VTEmailTask') {
            $rawTaskData = decodeUTF8($request->getRaw('taskData', '', true));
		    $rawTaskData = json_decode($rawTaskData, true);
            $taskData['fromEmail'] = $rawTaskData['fromEmail'];
            $taskData['content'] = $rawTaskData['content'];
        }
        // End Hieu Nguyen

        // Added by Hieu Nguyen on 2020-07-31 to parse json string field_value_mapping from request into array
        if ($taskData && is_array($taskData)) {
            $taskData['field_value_mapping'] = json_decode($taskData['field_value_mapping'], true) ?? [];
        }
        // End Hieu Nguyen

		$recordId = $request->get('task_id');
		$workflowId = $request->get('for_workflow');

		if ($workflowId) {
			$workflowModel = Settings_Workflows_Record_Model::getInstance($workflowId);
			$selectedModule = $workflowModel->getModule();
			$selectedModuleName = $selectedModule->getName();
		} else {
			$selectedModuleName = $request->get('module_name');
			$selectedModule = Vtiger_Module_Model::getInstance($selectedModuleName);
			$workflowModel = Settings_Workflows_Record_Model::getCleanInstance($selectedModuleName);
		}

		$taskTypes = $workflowModel->getTaskTypes();
		if($recordId) {
			$taskModel = Settings_Workflows_TaskRecord_Model::getInstance($recordId);
		} else {
			$taskType = $request->get('type');
			if(empty($taskType)) {
				$taskType = !empty($taskTypes[0]) ? $taskTypes[0]->getName() : 'VTEmailTask';
			}
			$taskModel = Settings_Workflows_TaskRecord_Model::getCleanInstance($workflowModel, $taskType);
			if(!empty($taskData)) {
				$taskModel->set('summary', $taskData['summary']);
				$taskModel->set('status', $taskData['status']);
				$taskModel->set('tmpTaskId', $taskData['tmpTaskId']);
				$taskModel->set('active', $taskData['active']);
				$tmpTaskObject = $taskModel->getTaskObject();
				foreach ($taskData as $key => $value){
					$key = preg_replace('/\[\]/', '', $key);	// Added by Hieu Nguyen on 2021-09-28 to work arround fix issue multiselect field with '[]' in field name cause error when accessing task data attributes
					$tmpTaskObject->$key = $value;
				}
				$taskModel->setTaskObject($tmpTaskObject);
			}
		}

		$taskTypeModel = $taskModel->getTaskType();
		$viewer->assign('TASK_TYPE_MODEL', $taskTypeModel);

		$viewer->assign('TASK_TEMPLATE_PATH', $taskTypeModel->getTemplatePath());
		$recordStructureInstance = Settings_Workflows_RecordStructure_Model::getInstanceForWorkFlowModule($workflowModel,
																			Settings_Workflows_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDITTASK);
		$recordStructureInstance->setTaskRecordModel($taskModel);

		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$moduleModel = $workflowModel->getModule();
		$dateTimeFields = $moduleModel->getFieldsByType(array('date', 'datetime'));

		$taskObject = $taskModel->getTaskObject();
		$taskType = get_class($taskObject);
		
		if ($taskType === 'VTUpdateFieldsTask') {
			if($moduleModel->getName() =="Documents"){
				$restrictFields=array('folderid','filename','filelocationtype'); 
				$viewer->assign('RESTRICTFIELDS',$restrictFields); 
			}
		}

        // Added by Hieu Nguyen on 2020-07-01 to resolve owner name from owner id for Workflow Task form
        foreach ($taskObject->field_value_mapping as $i => $mapping) {
            if (in_array($mapping['fieldname'], ['assigned_user_id', 'main_owner_id', 'createdby', 'modifiedby', 'inventorymanager'])) {
                $taskObject->field_value_mapping[$i]['value'] = Vtiger_Owner_UIType::getSelectedOwnersFromOwnersString($mapping['value']);
            }
        }
        // End Hieu Nguyen

		$viewer->assign('SOURCE_MODULE',$moduleModel->getName());
		$viewer->assign('MODULE_MODEL', $moduleModel);
		$viewer->assign('TASK_ID',$recordId);
		$viewer->assign('WORKFLOW_ID',$workflowId);
		$viewer->assign('DATETIME_FIELDS', $dateTimeFields);
		$viewer->assign('WORKFLOW_MODEL', $workflowModel);
		$viewer->assign('TASK_TYPES', $taskTypes);
		$viewer->assign('TASK_MODEL', $taskModel);
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$metaVariables = Settings_Workflows_Module_Model::getMetaVariables();
		if($moduleModel->getName() == 'Invoice' || $moduleModel->getName() == 'Quotes') {
			$metaVariables['Portal Pdf Url'] = '(general : (__VtigerMeta__) portalpdfurl)';
		}

		foreach($metaVariables as $variableName => $variableValue) {
			if(strpos(strtolower($variableName), 'url') !== false) {
				$metaVariables[$variableName] = "<a href='$".$variableValue."'>".vtranslate($variableName, $qualifiedModuleName).'</a>';
			}
		}
		// Adding option Line Item block for Individual tax mode
		$individualTaxBlockLabel = vtranslate("LBL_LINEITEM_BLOCK_GROUP", $qualifiedModuleName);
		$individualTaxBlockValue = $viewer->view('LineItemsGroupTemplate.tpl', $qualifiedModuleName, $fetch = true);

		// Adding option Line Item block for group tax mode
		$groupTaxBlockLabel = vtranslate("LBL_LINEITEM_BLOCK_INDIVIDUAL", $qualifiedModuleName);
		$groupTaxBlockValue = $viewer->view('LineItemsIndividualTemplate.tpl', $qualifiedModuleName, $fetch = true);

		$templateVariables = array(
			$individualTaxBlockValue => $individualTaxBlockLabel,
			$groupTaxBlockValue => $groupTaxBlockLabel
		);

		$viewer->assign('META_VARIABLES', $metaVariables);
		$viewer->assign('TEMPLATE_VARIABLES', $templateVariables);
		$viewer->assign('TASK_OBJECT', $taskObject);
		$viewer->assign('FIELD_EXPRESSIONS', Settings_Workflows_Module_Model::getExpressions());
		$repeat_date = $taskModel->getTaskObject()->calendar_repeat_limit_date;
		if(!empty ($repeat_date)){
			$repeat_date = Vtiger_Date_UIType::getDisplayDateValue($repeat_date);
		}
		$viewer->assign('REPEAT_DATE',$repeat_date);

		$userModel = Users_Record_Model::getCurrentUserModel();
		$viewer->assign('dateFormat',$userModel->get('date_format'));
		$viewer->assign('timeFormat', $userModel->get('hour_format'));
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);

		$emailFields = $recordStructureInstance->getAllEmailFields();
		foreach($emailFields as $metaKey => $emailField) {
			$emailFieldoptions .= '<option value=",$'.$metaKey.'">'.$emailField->get('workflow_columnlabel').'</option>';
		}
		$usersModuleModel = Vtiger_Module_Model::getInstance('Users');

        // Modified by Hieu Nguyen on 2020-07-01 to get reports to of main_owner_id instead of assinged_user_id
        $mainOwnerFieldModel = $moduleModel->getField('main_owner_id');

		if ($mainOwnerFieldModel) {
            $reportsToFieldModel = $usersModuleModel->getField('reports_to_id');
			$emailFieldoptions .= '<option value=",$(general : (__VtigerMeta__) reports_to_id)"> '. vtranslate($mainOwnerFieldModel->get('label'), 'Users') 
                                    .' : (' . vtranslate('Users', 'Users') . ') '. vtranslate($reportsToFieldModel->get('label'), 'Users') .'</option>';
		}
        // End Hieu Nguyen

		$nameFields = $recordStructureInstance->getNameFields();
		$fromEmailFieldOptions = '<option value="">'. vtranslate('ENTER_FROM_EMAIL_ADDRESS', $qualifiedModuleName) .'</option>';
		$fromEmailFieldOptions .= '<option value="$(general : (__VtigerMeta__) supportName)<$(general : (__VtigerMeta__) supportEmailId)>"
									>'.vtranslate('LBL_HELPDESK_SUPPORT_EMAILID', $qualifiedModuleName).
									'</option>';

		foreach($emailFields as $metaKey => $emailField) {
			list($relationFieldName, $rest) = explode(' ', $metaKey);
			$value = '<$'.$metaKey.'>';

			if ($nameFields) {
				$nameFieldValues = '';
					foreach (array_keys($nameFields) as $fieldName) {
					if (strstr($fieldName, $relationFieldName) || (count(explode(' ', $metaKey)) === 1 && count(explode(' ', $fieldName)) === 1)) {
						$fieldName = '$'.$fieldName;
						$nameFieldValues .= ' '.$fieldName;
					}
				}
				$value = trim($nameFieldValues).$value;
			}
			if ($emailField->get('uitype') != '13') {
				$fromEmailFieldOptions .= '<option value="'.$value.'">'.$emailField->get('workflow_columnlabel').'</option>';
			}
		}

		$structure = $recordStructureInstance->getStructure();
		foreach ($structure as $fields) {
			foreach ($fields as $field) {
                if ($field->getName() == 'assinged_user_id') continue;
                
				if ($field->get('workflow_pt_lineitem_field')) {
					$allFieldoptions .= '<option value="' . $field->get('workflow_columnname') . '">' .
							$field->get('workflow_columnlabel') . '</option>';
				} else {
					$allFieldoptions .= '<option value="$' . $field->get('workflow_columnname') . '">' .
							$field->get('workflow_columnlabel') . '</option>';
				}
			}
		}

		if($taskType == 'VTEmailTask') {
			$worflowModuleName = $workflowModel->get('module_name');
			$emailTemplates = EmailTemplates_Record_Model::getAllForEmailTask($worflowModuleName);
			if(!empty($emailTemplates)) {
				$viewer->assign('EMAIL_TEMPLATES',$emailTemplates);
			}
		}

		// $viewer->assign('ASSIGNED_TO', $assignedToValues);   // Commented out by Hieu Nguyen on 2020-07-02 to boost performance
		$viewer->assign('EMAIL_FIELD_OPTION', $emailFieldoptions);
		$viewer->assign('FROM_EMAIL_FIELD_OPTION', $fromEmailFieldOptions);
		$viewer->assign('ALL_FIELD_OPTIONS',$allFieldoptions);

        // Added by Hieu Nguyen on 2020-07-24 to support custom render for each task
        if (method_exists($taskObject, 'customRenderTaskEditForm')) {
            $taskObject->customRenderTaskEditForm($viewer, $workflowModel);
        }
        // End Hieu Nguyen
	
		// Display modal Send SMS
		// $viewer->display('modules/Settings/PipelineConfig/tpls/SendSMSModal.tpl');
		if ($taskType == 'VTSMSTask') {
			$viewer->display('modules/Settings/PipelineConfig/tpls/SendSMSModal.tpl');
		} else if ($taskType == 'VTEmailTask') {
			$viewer->display('modules/Settings/PipelineConfig/tpls/SendEmailModal.tpl');
		}
	}

	function getAddNotificationModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		// Respond
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->display('modules/Settings/PipelineConfig/tpls/NotificationModal.tpl');
	}
	// End Minh Hoang

	function getConditionModal(Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->get('source_module');
		$module = $request->getModule();
		$record = $request->get('record');
		$sourceRecord = $request->get('source_viewname');

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);

		if(!empty($record)) {
			$customViewModel = CustomView_Record_Model::getInstanceById($record);
			$viewer->assign('MODE', 'edit');
		} else if(!empty($sourceRecord)) {
			$customViewModel = CustomView_Record_Model::getInstanceById($sourceRecord);
			$viewer->assign('MODE', '');
		} else {
			$customViewModel = new CustomView_Record_Model();
			$customViewModel->setModule($moduleName);
			$viewer->assign('MODE', '');
		}

		$viewer->assign('ADVANCE_CRITERIA', $customViewModel->transformToNewAdvancedFilter());
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('DATE_FILTERS', Vtiger_Field_Model::getDateFilterTypes());

		if($moduleName == 'Calendar'){
			$advanceFilterOpsByFieldType = Calendar_Field_Model::getAdvancedFilterOpsByFieldType();
		} else{
			$advanceFilterOpsByFieldType = Vtiger_Field_Model::getAdvancedFilterOpsByFieldType();
		}
		$viewer->assign('ADVANCED_FILTER_OPTIONS', Vtiger_Field_Model::getAdvancedFilterOptions());
		$viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', $advanceFilterOpsByFieldType);
		$dateFilters = Vtiger_Field_Model::getDateFilterTypes();
		foreach($dateFilters as $comparatorKey => $comparatorInfo) {
			$comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
			$comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
			$comparatorInfo['label'] = vtranslate($comparatorInfo['label'],$module);
			$dateFilters[$comparatorKey] = $comparatorInfo;
		}
		$viewer->assign('DATE_FILTERS', $dateFilters);
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$recordStructure = $recordStructureInstance->getStructure();
		// for Inventory module we should now allow item details block
		if(in_array($moduleName, getInventoryModules())){
			$itemsBlock = "LBL_ITEM_DETAILS";
			unset($recordStructure[$itemsBlock]);
		}
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		// Added to show event module custom fields
		if($moduleName == 'Calendar'){
			$relatedModuleName = 'Events';
			$relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
			$relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
			$eventBlocksFields = $relatedRecordStructureInstance->getStructure();
			$viewer->assign('EVENT_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
			$viewer->assign('EVENT_RECORD_STRUCTURE', $eventBlocksFields);
		}
		$currentUserModel = Users_Record_Model::getCurrentUserModel();

		$viewer->assign('CUSTOMVIEW_MODEL', $customViewModel);
		$viewer->assign('RECORD_ID', $record);
		$viewer->assign('MODULE', $module);
		$viewer->assign('SOURCE_MODULE',$moduleName);
		$viewer->assign('USER_MODEL', $currentUserModel);
		$viewer->assign('CV_PRIVATE_VALUE', CustomView_Record_Model::CV_STATUS_PRIVATE);
		$viewer->assign('CV_PENDING_VALUE', CustomView_Record_Model::CV_STATUS_PENDING);
		$viewer->assign('CV_PUBLIC_VALUE', CustomView_Record_Model::CV_STATUS_PUBLIC);
		$viewer->assign('MODULE_MODEL',$moduleModel);
        // End Hieu Nguyen

		$viewer->display('modules/Settings/PipelineConfig/tpls/ConditionModal.tpl');
	}
	//End Tran Dien
}