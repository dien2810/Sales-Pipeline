<?php
/*
	File: Detail.php
	Author: The Vi
	Date: 22/1/2025
	Purpose: Display the interface of the Pipeline Detail page
*/

class Settings_PipelineConfig_Detail_View extends Settings_Vtiger_BaseConfig_View {
    function __construct() {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
       
        $viewer = $this->getViewer($request);
		$moduleName = $request->getModule(false);
		$viewer->assign('MODULE_NAME', $moduleName);
     
        $recordId = $request->get('record');
        
        $pipeline = Settings_PipelineConfig_Detail_Model::getDetailPipeline($recordId);
    //    var_dump(value: $pipeline);
	echo '<pre>' . json_encode($pipeline, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

		$viewer->assign('PIPELINE_DETAIL', $pipeline);
		$viewer->display('modules/Settings/PipelineConfig/tpls/DetailPipeline.tpl');
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