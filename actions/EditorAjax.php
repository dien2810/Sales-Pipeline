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

class Settings_PipelineConfig_EditorAjax_Action extends Vtiger_Action_Controller {

	function __construct() {
		$this->exposeMethod('getRoleList');
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
    public function getRoleList(Vtiger_Request $request) {
        $roleList = Settings_Roles_Record_Model::getAll();
                $result = [];
        foreach ($roleList as $roleId => $roleRecord) {
            $result[] = [
                'roleid'   => $roleId,
                'rolename' => $roleRecord->get('rolename'), 
            ];
        }

        $response = new Vtiger_Response();

        try {
            $response->setResult($result);
        } catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
}