<?php
/*
	Action SaveEdit
	Author: The Vi
	Date: 24/1/2025
	Purpose: Handle logic to save Edit Pipeline configuration
*/
require_once('include/utils/CustomConfigUtils.php');
require_once ('include/utils/FileUtils.php');
require_once('modules/Settings/PipelineConfig/models/PipelineAction.php'); 

class Settings_PipelineConfig_Detail_Action extends Vtiger_Action_Controller {

	function __construct() {
        $this->exposeMethod('getDetailPipeline');
	}
    public function checkPermission(Vtiger_Request $request) {
		$hasPermission = true;

		if (!$hasPermission) {
			throw new Exception(vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}
	public function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    public function getDetailPipeline(Vtiger_Request $request) {
        $idPipeline = $request->get('id');
       
        $response = new Vtiger_Response();
        
        $result =  Settings_PipelineConfig_Detail_Model::getDetailPipeline($idPipeline);
       
        $result['request_id'] = $idPipeline;
        
        $response->setResult($result);
        $response->emit();
    
    }
}