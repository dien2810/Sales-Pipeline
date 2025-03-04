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
		//Begin Tran Dien
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
		//End Tran Dien

		//Begin Minh Hoang 3/3/2025
		$this->exposeMethod('getSendSMSModal');
		$this->exposeMethod('getSendZNSModal');
		$this->exposeMethod('getSendEmailModal');
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
		// $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
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
		$allModules = getModulesTranslatedSingleLabel();
		// Chỉ giữ lại các module cụ thể
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
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddCallModal.tpl');
	}

	function getAddMeetingModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Chỉ giữ lại các module cụ thể
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
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddMeetingModal.tpl');
	}

	function getCreateNewTaskModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Chỉ giữ lại các module cụ thể
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
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/CreateNewTaskModal.tpl');
	}
	
	function getCreateNewProjectTaskModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Chỉ giữ lại các module cụ thể
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
		$viewer->display('modules/Settings/PipelineConfig/tpls/CreateNewProjectTaskModal.tpl');
	}

	function getCreateNewRecordModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$allModules = getModulesTranslatedSingleLabel();
		// Chỉ giữ lại các module cụ thể
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
		// $RELATED_MODULE_MODEL_NAME = $TASK_OBJECT->entity_type;

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
		// Chỉ giữ lại các module cụ thể
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
		// Chỉ giữ lại các module cụ thể
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
		// Chỉ giữ lại các module cụ thể
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
	function getSendSMSModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);

		// Get record structure
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();

		$allFieldOptions = '';
		$phoneFieldOptions = '';

		// Generate options for all fields and phone fields
		foreach ($recordStructure as $block => $fields) {
			foreach ($fields as $fieldName => $fieldModel) {
				if ($fieldModel->getName() != 'assigned_user_id') {
					$allFieldOptions .= '<option value="$' . $fieldModel->get('name') . '">' . vtranslate($fieldModel->get('label'), $currentModuleName) . '</option>';
				}
				if ($fieldModel->getFieldDataType() === 'phone') {
					$phoneFieldOptions .= '<option value="$' . $fieldName . '">' . vtranslate($fieldModel->get('label'), $currentModuleName) . '</option>';
				}
			}
		}

		// Get task object from request
		$taskObject = $request->get('taskObject');

		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('ALL_FIELD_OPTIONS', $allFieldOptions);
		$viewer->assign('TASK_OBJECT', $taskObject);
		$viewer->assign('PHONE_FIELD_OPTIONS', $phoneFieldOptions);
	
		// Display modal Send SMS
		$viewer->display('modules/Settings/PipelineConfig/tpls/SendSMSModal.tpl');
	}

	// Implemented by Minh Hoang to show Modal Send Email
	function getSendEmailModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		$qualifiedModuleName = $request->getModule(false);
		$currentModuleName = $request->get('currentNameModule');
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);

		// Get record structure
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
		$recordStructure = $recordStructureInstance->getStructure();

		$emailFieldOptions = '';
		$allFieldOptions = '';

		$fromEmailFieldOptions = '<option value="">'. vtranslate('ENTER_FROM_EMAIL_ADDRESS', $moduleName) .'</option>';
		$fromEmailFieldOptions .= '<option value="$(general : (__VtigerMeta__) supportName)<$(general : (__VtigerMeta__) supportEmailId)>">' . vtranslate('LBL_HELPDESK_SUPPORT_EMAILID', $moduleName) . '</option>';

		// Generate options for email fields
		foreach ($recordStructure as $block => $fields) {
			foreach ($fields as $fieldName => $fieldModel) {
				if ($fieldModel->getFieldDataType() === 'email') {
					$emailFieldOptions .= '<option value=",$' . str_replace(';', ':', $fieldName) . '">' . vtranslate($fieldModel->get('label'), $currentModuleName) . '</option>';
					$fromEmailFieldOptions .= '<option value="$' . str_replace(';', ':', $fieldName) . '">' . vtranslate($fieldModel->get('label'), $currentModuleName) . '</option>';
				}
			}
		}
	
		// Generate options for all fields
		foreach ($recordStructure as $fields) {
			foreach ($fields as $fieldModel) {
				if ($fieldModel->getName() == 'assigned_user_id') continue;
				$allFieldOptions .= '<option value="$' . str_replace(';', ':', $fieldModel->get('name')) . '">' . vtranslate($fieldModel->get('label'), $currentModuleName) . '</option>';
			}
		}

		// Get meta variables
		$metaVariables = Settings_Workflows_Module_Model::getMetaVariables();
		if ($moduleModel->getName() == 'Invoice' || $moduleModel->getName() == 'Quotes') {
			$metaVariables['Portal Pdf Url'] = '(general : (__VtigerMeta__) portalpdfurl)';
		}

		// Convert URL meta variables to links
		foreach($metaVariables as $variableName => $variableValue) {
			if(strpos(strtolower($variableName), 'url') !== false) {
				$metaVariables[$variableName] = "<a href='$".$variableValue."'>".vtranslate($variableName, $qualifiedModuleName).'</a>';
			}
		}

		// Get email templates
		$emailTemplates = EmailTemplates_Record_Model::getAllForEmailTask($pipelineModule);

		// Get task object from request
		$taskObject = $request->get('taskObject');

		// Render view
		$viewer = $this->getViewer($request);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('EMAIL_FIELD_OPTION', $emailFieldOptions);
		$viewer->assign('FROM_EMAIL_FIELD_OPTION', $fromEmailFieldOptions);
		$viewer->assign('ALL_FIELD_OPTIONS', $allFieldOptions);
		$viewer->assign('META_VARIABLES', $metaVariables);
		$viewer->assign('EMAIL_TEMPLATES', $emailTemplates);
		$viewer->assign('TASK_OBJECT', $taskObject);

		// Display modal Send Email
		$viewer->display('modules/Settings/PipelineConfig/tpls/SendEmailModal.tpl');
	}

	function getAddNotificationModal(Vtiger_Request $request) {
		$moduleName = $request->getModule(false);
		// Respond
		$viewer = $this->getViewer($request);
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