<?php
class Settings_PipelineConfig_PipelineOrConditionRowAjax_View extends CustomView_Base_View{

    function __construct() {
        parent::__construct($isFullView = false);
    }

    function checkPermission(Vtiger_Request $request){
        $moduleName = $request->getModule();

        //Write your own logic to check for access permission
        $allowAccess  = true; // Set this to false if a user 's role is not permitted

        if (!$allowAccess){
            throw new AppException(vtranslate($moduleName, $moduleName) . ' ' . vtranslate('LBL_NOT_ACCESSIBLE'));
        }
    }

    function process(Vtiger_Request $request){
        $viewer = new Vtiger_Viewer();
        $moduleName = $request->getModule(false);
		$currentModuleName = $request->get('currentNameModule');
		// Respond
		$pipelineModule = !empty($currentModuleName) ? $currentModuleName : "Potentials";
		$module = 'CustomView';
		// $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$moduleModel = Vtiger_Module_Model::getInstance($pipelineModule);
		$viewer = $this->getViewer($request);
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

        $result = $viewer->fetch('modules/Settings/PipelineConfig/tpls/PipelineOrConditionRow.tpl');
        echo $result;
    }
}
?>