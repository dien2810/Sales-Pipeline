<?php
/*
	Action EditorAjax
	Author: The Vi
	Date: 24/1/2025
	Purpose: Handle logic to page Edit Pipeline
*/
require_once('include/utils/CustomConfigUtils.php');
require_once ('include/utils/FileUtils.php');
require_once('modules/Settings/PipelineConfig/models/PipelineAction.php'); 

class Settings_PipelineConfig_EditorAjax_Action extends Vtiger_Action_Controller {

	function __construct() {
		$this->exposeMethod('getRoleList');
		$this->exposeMethod('getPipelineStageInfo');
		$this->exposeMethod('checkCondition');

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
	public function getPipelineStageInfo(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            $recordId = $request->get('record');
            $moduleName = $request->get('modulename');
           
            $pipelineStageInfo = PipelineAction::getPipelineStageInfo($recordId, $moduleName);
            
        
            $response->setResult(array(
                'success' => true,
                'data' =>$pipelineStageInfo
            ));
        } catch(Exception $e) {
            $response->setError($e->getMessage());
        }
        $response->emit();
    }
	public function checkCondition(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            $recordId = $request->get('recordId');
            $stageIdNext = $request->get('stageIdNext');
            $moduleName = $request->get('moduleName');

             //Begin edit by The Vi 3/4/2025
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $stageId = $recordModel->get('stageid');
           
            // End edit by The Vi 3/4/2025
            $result = true;
            if($stageId != $stageIdNext) {
                $result = PipelineAction::checkConditions($recordId, $stageId, "Potentials");
            }
            if ($result) {
                $response->setResult([
                    'status'  => true,
                    'message' => 'Điều kiện chuyển stage thỏa mãn',
                    'data'    => [
                        'recordId' => $recordId,
                        'stageId' => $stageId,
                        'result' => $result
                    ]
                ]);
            } else {
                $response->setResult([
                    'status'  => false,
                    'message' => 'Điều kiện chuyển stage không thỏa mãn',
                    'data'    => [
                        'recordId' => $recordId, 
                        'stageId' => $stageId,
                        'result' => $result

                    ]
                ]);
            }
        } catch (Exception $e) {
            $response->setResult([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
        $response->emit();
    }
}