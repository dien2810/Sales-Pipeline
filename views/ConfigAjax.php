<?php
/*
	File: ConfigAjax.php
	Author: The Vi
	Date: 22/1/2025
	Purpose: Display Ajax-based interfaces for Pipeline management, including Pipeline listing and Pipeline deletion modal
*/
require_once('include/utils/LangUtils.php');
class Settings_PipelineConfig_ConfigAjax_View extends CustomView_Base_View {

	function __construct() {
		$this->exposeMethod('getPipelineList');
		$this->exposeMethod('getDeletePipelineModal');
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
	// Implemented by The Vi to show List Pipeline

	function getPipelineList(Vtiger_Request $request) {
            $nameModule = $request->get('nameModule');
            $namePipeline = $request->get('namePipeline');
			$moduleName = $request->getModule(false);
			$currentUserModel = Users_Record_Model::getCurrentUserModel();
			$roleId = $currentUserModel->get('roleid');

            $pipelineList =  Settings_PipelineConfig_Config_Model::getPipelineList($nameModule, $namePipeline, $roleId);
	
			$viewer = $this->getViewer($request);
            $viewer->assign('PIPELINE_LIST', $pipelineList);
			$viewer->assign('MODULE_NAME', $moduleName);
            $result = $viewer->fetch('modules/Settings/PipelineConfig/tpls/PipelineList.tpl');
            echo $result;
	}
    // Implemented by The Vi to show Delete Pipeline Modal

	function getDeletePipelineModal(Vtiger_Request $request) {
		$pipelineId = $request->get('pipelineId');
        $moduleNamePipeline = $request->get('moduleName');
		$moduleName = $request->getModule(false);
		
        $checkPipelineEmpty = Settings_PipelineConfig_Config_Model::isPipelineRecordExist($pipelineId,  $moduleNamePipeline);
        
		if(!$checkPipelineEmpty){

			$viewer = $this->getViewer($request);
			$moduleName = $request->getModule(false);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PIPELINE_ID', $pipelineId);
			$viewer->assign('MODULE_NAME', $moduleName);
			$result = $viewer->fetch('modules/Settings/PipelineConfig/tpls/DeletePipelineEmptyModal.tpl');
			
			echo $result;
		}else{
			$pipelineListReplace =  Settings_PipelineConfig_Config_Model::getPipelineListExcluding($moduleNamePipeline, null, $pipelineId);
            $stageCurrentList =  Settings_PipelineConfig_Config_Model::getStageList($pipelineId);
		
			$viewer = $this->getViewer($request);
			$moduleName = $request->getModule(false);
			$viewer->assign('MODULE_NAME', $moduleName);
			$viewer->assign('PIPELINE_ID', $pipelineId);
			$viewer->assign('PIPELINE_REPLACE_LIST', $pipelineListReplace);
			$viewer->assign('STAGE_CURRENT_LIST', $stageCurrentList);
			$viewer->assign('MODULE_NAME', $moduleName);
			
			$result = $viewer->fetch('modules/Settings/PipelineConfig/tpls/DeletePipelineModal.tpl');
			
			echo $result;
		}
	}
}