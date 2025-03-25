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
        $this->exposeMethod('getPickListDependencyPotential');

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
	public function getPickListDependencyPotential(Vtiger_Request $request) {
        $response = new Vtiger_Response();
        try {
            // $recordId = $request->get('record');
            $module = $request->get('sourceModule');
            $sourceField = $request->get('sourcefield');
            $targetField = $request->get('targetfield');
            $recordModel = Settings_PickListDependency_Record_Model::getInstance($module, $sourceField, $targetField);
            $valueMapping = $recordModel->getPickListDependency(); //Trả về
            $response->setResult(array(
                'success' => true,
                'data' =>[
                    "MAPPED_VALUES" => $valueMapping,
                ]
            ));
        } catch(Exception $e) {
            $response->setError($e->getMessage());
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
                $result = PipelineAction::checkConditions($recordId, $stageId, $moduleName, $stageIdNext);
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
    public function getDependencyGraph(Vtiger_Request $request) {
		$qualifiedName = $request->getModule(false);
		$module = $request->get('sourceModule');
		$sourceField = $request->get('sourcefield');
		$targetField = $request->get('targetfield');
		$recordModel = Settings_PickListDependency_Record_Model::getInstance($module, $sourceField, $targetField);
		$valueMapping = $recordModel->getPickListDependency();
		$sourcePicklistValues = $recordModel->getSourcePickListValues();
		$safeHtmlSourcePicklistValues = array();
		foreach($sourcePicklistValues as $key => $value) {
			$safeHtmlSourcePicklistValues[$key] = Vtiger_Util_Helper::toSafeHTML($key);
		}

		$targetPicklistValues = $recordModel->getTargetPickListValues();
		$safeHtmlTargetPicklistValues = array();
		foreach($targetPicklistValues as $key => $value) {
			$safeHtmlTargetPicklistValues[$key] = Vtiger_Util_Helper::toSafeHTML($key);
		}

		$viewer = $this->getViewer($request);
		$viewer->assign('MAPPED_VALUES', $valueMapping);
		$viewer->assign('SOURCE_PICKLIST_VALUES', $sourcePicklistValues);
		$viewer->assign('SAFEHTML_SOURCE_PICKLIST_VALUES', $safeHtmlSourcePicklistValues);
		$viewer->assign('TARGET_PICKLIST_VALUES', $targetPicklistValues);
		$viewer->assign('SAFEHTML_TARGET_PICKLIST_VALUES', $safeHtmlTargetPicklistValues);
		$viewer->assign('QUALIFIED_MODULE', $qualifiedName);
		$viewer->assign('RECORD_MODEL', $recordModel);

		return $viewer->view('DependencyGraph.tpl',$qualifiedName, true);
	}
}