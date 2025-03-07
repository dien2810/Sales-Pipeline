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

class Settings_PipelineConfig_SaveEdit_Action extends Vtiger_Action_Controller {

	function __construct() {
		$this->exposeMethod('addStagePipelineNew');
		$this->exposeMethod('saveOther');
        $this->exposeMethod('savePipeline');
        $this->exposeMethod('updatePipeline');
        $this->exposeMethod('deleteStagePipeline');
        // $this->exposeMethod('getPipelineStageInfo');
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
     // Implemented by The Vi to delete stage of pipeline in edit pipeline page

    public function deleteStagePipeline(Vtiger_Request $request) {
        $idStageDelete  = $request->get('idStageDelete');
        $idStageReplace = $request->get('idStageReplace');
        $module         = $request->get('current_module');

        $response = new Vtiger_Response();
        
        try {
            $success = Settings_PipelineConfig_Edit_Model::deleteStagePipeline($idStageDelete, $idStageReplace, $module);
            if ($success) {
                $response->setResult(['success' => true]);
            } else {
                $response->setError(500, 'Không thể xóa bước pipeline!');
            }
        } catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }

        $response->emit();
    }
    // Implemented by The Vi to add new stage of pipeline in edit pipeline page
	public function addStagePipelineNew(Vtiger_Request $request) {
        $pickListName = $request->get('picklistName');
        $moduleName = $request->get('source_module');
        $selectedColor = $request->get('selectedColor');

        $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
       
        $rolesSelected = array();

        if($fieldModel->isRoleBased()) {
            $userSelectedRoles = $request->get('rolesSelected',array());
            //selected all roles option
            if(in_array('all',$userSelectedRoles)) {
                $roleRecordList = Settings_Roles_Record_Model::getAll();
                foreach($roleRecordList as $roleRecord) {
                    $rolesSelected[] = $roleRecord->getId();
                }
            }else{
                $rolesSelected = $userSelectedRoles;
            }
        }
        $response = new Vtiger_Response();
        try{
            // Modified by Hieu Nguyen on 2021-06-15 to save label for new picklist value
            $itemValue = trim($request->get('newValue'));
            $itemLabelDisplayEn = trim($request->get('itemLabelDisplayEn'));
            $itemLabelDisplayVn = trim($request->get('itemLabelDisplayVn'));

            // Save pick list item
            $result = $moduleModel->addPickListValues($fieldModel, $itemValue, $rolesSelected, $selectedColor);

            // Save item label
            require_once('include/utils/LangUtils.php');
            $languageStrings = [$itemValue => $itemLabelDisplayEn];
        	LangUtils::writeModStrings($languageStrings, [], $moduleName, 'en_us');

        	$languageStrings = [$itemValue => $itemLabelDisplayVn];
        	LangUtils::writeModStrings($languageStrings, [], $moduleName, 'vn_vn');

			global $current_user;
            $result['labelDisplay'] = ($current_user->language == 'vn_vn') ? $itemLabelDisplayVn : $itemLabelDisplayEn;


			$response->setResult($result);
        }  catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();

	}
    // Implemented by The Vi to save information of pipeline
    public function savePipeline(Vtiger_Request $request) {
        
        $pipelineData = $request->get('dataPipeline');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        $response = new Vtiger_Response();
        $result = Settings_PipelineConfig_Edit_Model::savePipeline($pipelineData, $currentUser);
    
        if ($result['success']) {
            $response->setResult($result);
        } else {
            $response->setError($result['error_code'], $result['error_message']);
        }
        $response->emit();
    }
    // Implemented by The Vi to update information of pipeline
    public function updatePipeline(Vtiger_Request $request) {
        $pipelineData = $request->get('dataPipeline');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $response = new Vtiger_Response();

        $result = Settings_PipelineConfig_Edit_Model::updatePipeline($pipelineData, $currentUser);
      
        if ($result['success']) {
            $response->setResult($result);
        } else {
            $response->setError($result['error_code'], $result['error_message']);
        }
        // $response->setResult($pipelineData);
        $response->emit();
    }   
    
}