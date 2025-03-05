<?php
/*
	File: Config.php
	Author: The Vi
	Date: 22/1/2025
	Purpose: Display the interface of the Pipeline Config page
*/

class Settings_PipelineConfig_Config_View extends Settings_Vtiger_BaseConfig_View {
    function __construct() {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
       
        $sourceModule = $request->get('source_module');
		$allModules = Settings_Picklist_Module_Model::getPicklistSupportedModules();
		$allowedModules = ['Potentials', 'Leads', 'HelpDesk', 'Project'];
		
		$pickListSupportedModules = array_filter($allModules, function($module) use ($allowedModules) {
			return in_array($module->getName(), $allowedModules);
		});
		
		$pickListSupportedModules = array_values($pickListSupportedModules);
        
		if(empty($sourceModule)) {
            $sourceModule = $pickListSupportedModules[0]->name;
        }

        $moduleModel = Settings_Picklist_Module_Model::getInstance($sourceModule);
        $viewer = $this->getViewer($request);
		$qualifiedName = $request->getModule(FALSE);
        $viewer->assign('PICKLIST_MODULES',$pickListSupportedModules);
		$qualifiedName = $request->getModule(false);
		$configEditorModel = Settings_Vtiger_ConfigEditor_Model::getInstance();
		$viewer = $this->getViewer($request);
		
		$viewer->assign('MODEL', $configEditorModel);
		$viewer->assign('MODULE_NAME', $qualifiedName);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedName);

		$viewer->display('modules/Settings/PipelineConfig/tpls/Config.tpl');
    }
    public function getPageTitle(Vtiger_Request $request) {
		return "Pipeline";
	}
    public function getHeaderScripts(Vtiger_Request $request) {
		$headerScriptInstances = parent::getHeaderScripts($request);
		$moduleName = $request->getModule();
		$viewName = $request->get('view');
		$jsFileNames = array(
			"modules.Settings.{$moduleName}.resources.{$viewName}",
		);

		$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
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