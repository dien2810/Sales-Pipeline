<?php
/*
	File: EditPipeline.php
	Author: The Vi
	Date: 22/1/2025
	Purpose: Display interface for adding new Pipeline and editing existing Pipeline records
*/
class Settings_PipelineConfig_EditPipeline_View extends Settings_Vtiger_BaseConfig_View {
    function __construct() {
        parent::__construct();
    }
	public function process(Vtiger_Request $request)
	{
		$sourceModule = $request->get('source_module');
		$pickListSupportedModules = Settings_Picklist_Module_Model::getPicklistSupportedModules();
	
		$filteredPickListModules = array_filter($pickListSupportedModules, function($module) {
			$moduleName = $module->get('name');
			return in_array($moduleName, ['Potentials', 'Leads', 'HelpDesk', 'Project']);
		});
		$filteredPickListModules = array_values($filteredPickListModules);
		$roleList = Settings_Roles_Record_Model::getAll();
		if(empty($sourceModule)) {
			$sourceModule = $filteredPickListModules[0]->get('name');
		}
		//Lấy record cần chỉnh sửa
		$recordId = $request->get('record');
        $pipelineDetail = Settings_PipelineConfig_Detail_Model::getDetailPipeline($recordId);
   
	    // echo '<pre>' . json_encode($pipelineDetail, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
		
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('PICKLIST_MODULES', $filteredPickListModules);
		$viewer->assign('PIPELINE_DETAIL', $pipelineDetail);
		$viewer->assign('ROLES_LIST', Settings_Roles_Record_Model::getAll());
		$viewer->display('modules/Settings/PipelineConfig/tpls/EditPipeline.tpl');
	}
	public function getPageTitle(Vtiger_Request $request) {
		return "Pipeline";
	}

    public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		$viewName = $request->get('view');
		$jsFileNames = array(
			"~modules/Settings/PipelineConfig/resources/AdvanceFilter.js",
			"modules.Settings.{$moduleName}.resources.{$viewName}",
		);
		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		// var_dump($jsScriptInstances);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
		return $headerScriptInstances;
	}

	public function getHeaderCss(Vtiger_Request $request) {
		$viewName = $request->get('view');
		$cssFileNames = array("~modules/Settings/PipelineConfig/resources/{$viewName}.css");
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = parent::getHeaderCss($request);
		$headerCssInstances = array_merge($headerCssInstances, $cssInstances);
		return $headerCssInstances;
	}
}
?>
