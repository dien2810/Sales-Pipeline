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
		$this->exposeMethod('getStagePipelineModal');
		$this->exposeMethod('getStagePipelineModalNew');
		$this->exposeMethod('getDeleteStageModal');
		$this->exposeMethod('getStagePipelineList');
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
		$this->exposeMethod('getAddSMSModal');
		$this->exposeMethod('getAddZNSModal');
		$this->exposeMethod('getAddNotificationModal');

		//End Tran Dien
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
	function getStagePipelineModal(Vtiger_Request $request) {
		$sourceModule = $request->get('source_module');
		$pickFieldId = Settings_PipelineConfig_Util_Helper::getModuleIdByName($sourceModule);;
        if (empty($sourceModule) || empty($pickFieldId)) return; 
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickFieldId);
		$moduleName = $request->getModule();
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
		echo "<script>";
		echo "console.log('Source Module:', " . json_encode($sourceModule) . ");";
		echo "console.log('fieldModel', " . json_encode($fieldModel) . ");";
		echo "console.log('Picklist Field ID:', " . json_encode($pickFieldId) . ");";
		echo "console.log('Selected Picklist Values:', " . json_encode($selectedFieldAllPickListValues) . ");";
		echo "</script>";

        $viewer = $this->getViewer($request);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('SELECTED_PICKLIST_FIELDMODEL',$fieldModel);
		$viewer->assign('SELECTED_MODULE_NAME',$sourceModule);
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('FIELD_MODEL',$fieldModel);
		$viewer->assign('FIELD_VALUE_ID',$pickFieldId);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('ROLES_LIST', Settings_Roles_Record_Model::getAll());
        $viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$selectedFieldAllPickListValues);
		$viewer = $this->getViewer($request);
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddStagePipelineModal.tpl');
	}

	function getStagePipelineList(Vtiger_Request $request) {
		$sourceModule = $request->get('source_module');
		
        $viewer = $this->getViewer($request);
		$viewer = $this->getViewer($request);
		$viewer->display('modules/Settings/PipelineConfig/tpls/StagePipeline.tpl');
	}

	function getDeleteStageModal(Vtiger_Request $request) {
    
		$idPipeline = $request->get('idPipeline');
		// Respond
		$listStage = Settings_PipelineConfig_Config_Model::getStageList($idPipeline);
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PIPELINE_ID', $idPipeline);
		$viewer->assign('STAGE_LIST', $listStage);
		// echo '<pre>' . json_encode($listStage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
		$viewer->display('modules/Settings/PipelineConfig/tpls/DeleteStageModal.tpl');
	}

	function getStagePipelineModalNew(Vtiger_Request $request) {
    
		$moduleName = $request->getModule(false);
		
		// Respond
		$viewer = $this->getViewer($request);

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
		$viewer->assign('FIELD_EXPRESSIONS', Settings_PipelineConfig_Edit_Model::getExpressions());
		// $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		// $viewer->assign('RECORD_STRUCTURE', $recordStructure);
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
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructure);
		$viewer->assign('DATE_FILTERS', Vtiger_Field_Model::getDateFilterTypes());
		// Modal send Update Data Field
		$viewer->display('modules/Settings/PipelineConfig/tpls/DataFieldUpdateModal.tpl');
	}

	function getAddZNSModal(Vtiger_Request $request) {
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
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddZNSModal.tpl');
	}

	function getAddSMSModal(Vtiger_Request $request) {
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
		$viewer->display('modules/Settings/PipelineConfig/tpls/AddSMSModal.tpl');
	}

	function getAddNotificationModal(Vtiger_Request $request) {
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
		$viewer->display('modules/Settings/PipelineConfig/tpls/NotificationModal.tpl');
	}

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