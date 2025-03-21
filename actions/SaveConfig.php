<?php
/*
	Action SaveConfig
	Author: The Vi
	Date: 24/1/2025
	Purpose: Handle logic to save Pipeline configuration
*/
require_once('include/utils/CustomConfigUtils.php');
require_once ('include/utils/FileUtils.php');
class Settings_PipelineConfig_SaveConfig_Action extends Vtiger_Action_Controller {
	function __construct() {
		$this->exposeMethod('updateStatusPipeline');
		$this->exposeMethod('deletePipelineEmpty');
		$this->exposeMethod('getStagePipeline');
		$this->exposeMethod('getListPipeline');
        $this->exposeMethod('getListPipelineStatus');
		//Begin The Vi 28-02-2025
		$this->exposeMethod('deletePipelineRecordExist');
		//End The Vi 28-02-2025

		// Begin Minh Hoang 2025-03-12
		$this->exposeMethod('replacePipelineAndStageInRecord');
		// End Minh Hoang 2025-03-12
    // Add by Dien Nguyen on 2025-03-14
		$this->exposeMethod('clonePipeline');

		//Add by The Vi 21-3-2025
		$this->exposeMethod('checkPipelineDefault');
	}
	function checkPermission(Vtiger_Request $request) {
		$hasPermission = true;

		if (!$hasPermission) {
			throw new Exception(vtranslate('LBL_NOT_ACCESSIBLE'));
		}
	}
	function process(Vtiger_Request $request) {
		$mode = $request->getMode();
		if (!empty($mode) && $this->isMethodExposed($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
	}
    // Implemented by The Vi on 2025-03-05 to update status of Pipeline

	function updateStatusPipeline(Vtiger_Request $request) {
		$idPipeline = $request->get('idPipeline');
        $statusPipeline = $request->get('statusPipeline');

        $updateStatusPipeline = Settings_PipelineConfig_Config_Model::updatePipelineStatus($idPipeline, $statusPipeline);
       
		$response = new Vtiger_Response();
        if($updateStatusPipeline) {
            $response->setResult(array('success' => $updateStatusPipeline));
        } else {
            $response->setError('Update failed');
        }
        $response->emit();
	}
	// Implemented by The Vi on 2025-03-05 to get list of stages in a pipeline

	function getStagePipeline(Vtiger_Request $request) {
		$idPipeline = $request->get('pipelineId');

		$response = new Vtiger_Response();
		try {
			$result = Settings_PipelineConfig_Config_Model::getStageList($idPipeline);
			$stages = array();
			while($row = PearDatabase::getInstance()->fetchByAssoc($result)) {
				$stages[] = array(
					'stageid' => $row['stageid'],
					'pipelineid' => $row['pipelineid'],
					'name' => $row['name'], 
					'success_rate' => $row['success_rate'],
					'time' => $row['time'],
					'time_unit' => $row['time_unit'],
					'is_mandatory' => $row['is_mandatory'],
					'color_code' => $row['color_code'],
					'sequence' => $row['sequence'],
					'value' => $row['value']
				);
			}
			
			$response->setResult([
				'data' => $stages,
				'success' => true
			]);
			
		} catch (Exception $e) {
			$response->setError('Lỗi: ' . $e->getMessage());
		}
		$response->emit();
	}
	function checkPipelineDefault(Vtiger_Request $request) {
		$idPipeline = $request->get('pipelineId');

		$response = new Vtiger_Response();
		try {
			$result = Settings_PipelineConfig_Config_Model::checkPipelineDefault($idPipeline);
			
			$response->setResult([
				'result' => $result,
			]);
			
		} catch (Exception $e) {
			$response->setError('Error: ' . $e->getMessage());
		}
		$response->emit();
	}

	// Implemented by The Vi on 2025-03-05 to get list of pipelines
	function getListPipeline(Vtiger_Request $request) {
		$module = $request->get('moduleName');

		$response = new Vtiger_Response();
		try {
			$result = Settings_PipelineConfig_Config_Model::getPipelineList($module);
			$db = PearDatabase::getInstance();
			$pipelines = [];
			while ($row = $db->fetchByAssoc($result)) {
				$pipelines[] = [
					'pipelineid' => $row['pipelineid'],
					'module'     => $row['module'],
					'name'       => $row['name'],
					'stage'      => $row['stage'],
					'status'     => $row['status'],
					'auto_move'  => $row['auto_move'],
					'duration'   => $row['duration'],
					'time_unit'  => $row['time_unit'],
					'description'=> $row['description'],
					'is_default' => $row['is_default'],
					'create_by'  => $row['create_by'],
					'created_at' => $row['created_at']
				];
			}
			$response->setResult([
				'success' => true,
				'data'    => $pipelines
			]);
		} catch (Exception $e) {
			$response->setError('Lỗi: ' . $e->getMessage());
		}
		$response->emit();
	}
	
	// Implemented by The Vi on 2025-03-05 to get list of pipelines with status
    function getListPipelineStatus(Vtiger_Request $request) {
		global $current_user;
$roleId = $current_user->roleid;
		$module = $request->get('moduleName');

		$response = new Vtiger_Response();

		try {
			$result = Settings_PipelineConfig_Config_Model::getPipelineStatusList( $roleId, $module);
			$db = PearDatabase::getInstance();

			$pipelines = [];
			
			while ($row = $db->fetchByAssoc($result)) {
				$pipelines[] = [
					'pipelineid' => $row['pipelineid'],
					'module'     => $row['module'],
					'name'       => $row['name'],
					'stage'      => $row['stage'],
					'status'     => $row['status'],
					'auto_move'  => $row['auto_move'],
					'duration'   => $row['duration'],
					'time_unit'  => $row['time_unit'],
					'description'=> $row['description'],
					'is_default' => $row['is_default'],
					'create_by'  => $row['create_by'],
					'created_at' => $row['created_at']
				];
			}
			$response->setResult([
				'success' => true,
				'data'    => $pipelines
			]);
		} catch (Exception $e) {
			$response->setError('Lỗi: ' . $e->getMessage());
		}
		$response->emit();
	}
	// Implemented by The Vi on 2025-03-05 to delete pipeline if it has no record
	function deletePipelineEmpty(Vtiger_Request $request) {
		$idPipeline = $request->get('pipelineId');

		$response = new Vtiger_Response();

		try {
			$deleteResult = Settings_PipelineConfig_Config_Model::deletePipelineById($idPipeline);
			
				$response->setResult([
					'success' => $deleteResult,
					'data' => $idPipeline,
			]);
			
		} catch (Exception $e) {
			$response->setError('Lỗi: ' . $e->getMessage());
		}
		$response->emit();
	}

	//Begin The Vi 28-02-2025
    // Implemented by The Vi to delete pipeline record exist
	function deletePipelineRecordExist(Vtiger_Request $request) {
		$idPipeline = $request->get('pipelineId');
		$idPipelineReplace = $request->get('pipelineIdReplace');
		$idStageReplace = $request->get('stageReplace');
		
		$deleteResult = Settings_PipelineConfig_Config_Model::deletePipelineRecordExist($idPipeline, $idPipelineReplace, $idStageReplace);
		$response = new Vtiger_Response();
		$response->setResult([
			'success' => $deleteResult['success'],
			'data' => $idPipelineReplace,
			'message' => $deleteResult['message']
		]);
		$response->emit();
	}

	//End The Vi 28-02-2025

	// Begin Minh Hoang 2025-03-11
	function replacePipelineAndStageInRecord(Vtiger_Request $request) { 
		$idRecord = $request->get('recordId');
		$idPipelineReplace = $request->get('pipelineIdReplace');
		$idStageReplace = $request->get('stageIdReplace');
		$moduleName = $request->get('moduleName');
	
		$editResult = Settings_PipelineConfig_Config_Model::replacePipelineAndStageInRecord($idRecord, $idPipelineReplace, $idStageReplace);
	
		if (!$editResult['success']) {
			$response = new Vtiger_Response();
			$response->setResult([
				'success' => false,
				'message' => $editResult['message']
			]);
			$response->emit();
			return;
		}
	
		if ($moduleName === 'Potentials') {
			$requestData = new Vtiger_Request([
				'module' => $moduleName,
				'record' => $idRecord,
				'pipelineid' => $editResult['data']['idPipelineReplace'],
				'pipelinename' => $editResult['data']['pipelineNameReplace'],
				'stageid' => $editResult['data']['idStageReplace'],
				'stagename' => $editResult['data']['stageNameReplace'],
				'sales_stage' => $editResult['data']['stageValueReplace'],
				'probability' => $editResult['data']['successRate']
			]);
		} else {
			$requestData = new Vtiger_Request([
				'module' => $moduleName,
				'record' => $idRecord,
				'pipelineid' => $editResult['data']['idPipelineReplace'],
				'pipelinename' => $editResult['data']['pipelineNameReplace'],
				'stageid' => $editResult['data']['idStageReplace'],
				'stagename' => $editResult['data']['stageNameReplace']
			]);
		}
	
		ob_start();
		$saveAction = new Potentials_SaveAjax_Action();
		$saveAction->process($requestData);
		ob_end_clean();
	
		$response = new Vtiger_Response();
		$response->setResult([
			'success' => true,
			'data' => $editResult['data'],
		]);
		$response->emit();
	}
	// End Minh Hoang 2025-03-17

	// Begin Dien Nguyen
	function clonePipeline(Vtiger_Request $request) {
		try{
			$id = $request->get('pipelineId');
			$db = PearDatabase::getInstance();
			if (empty($id)) {
				throw new Exception("ID không được để trống");
			}

			// Clone record in vtiger_pipeline table
			$query = "SELECT * FROM vtiger_pipeline WHERE pipelineid = ?";
			$params = [$id];
			$result = $db->pquery($query, $params);
			if ($result === false) {
				throw new Exception("Lỗi thực thi câu lệnh SQL khi lấy vtiger_pipeline");
			}
			$row = $db->fetchByAssoc($result);
			$currentTime = date('Y-m-d H:i:s');
			$newPipelineId = $db->getUniqueID('vtiger_pipeline');
			$query = "INSERT INTO vtiger_pipeline(pipelineid,module,name,
				stage,status,auto_move, duration,time_unit,description, 
				is_default, create_by, created_at) values(?,?,?,?,?,?,?,?,?,?,?,?)";
			$params = array($newPipelineId, $row['module'], $row['name'], 
				$row['stage'], $row['status'], $row['auto_move'],
				$row['duration'], $row['time_unit'], $row['description'], 0, 
				$row['create_by'], $currentTime);
			$db->pquery($query, $params);

			// Clone record in vtiger_rolepipeline table
			$query = "SELECT * FROM vtiger_rolepipeline WHERE pipelineid = ?";
			$params = array($id);
			$result = $db->pquery($query, $params);
			if ($result === false) {
				throw new Exception("Lỗi thực thi câu lệnh SQL khi clone vtiger_rolepipeline");
			}
			while ($row = $db->fetchByAssoc($result)){
				$query = 'INSERT INTO vtiger_rolepipeline values(?,?)';
				$params = array($row['roleid'], $newPipelineId);
				$db->pquery($query, $params);
			}

			// Clone vtiger_stage
			$query = 'SELECT * FROM vtiger_stage
				WHERE pipelineid = ?';
			$params = array($id);
			$stageResult = $db->pquery($query, $params);
			if ($stageResult === false) {
				throw new Exception("Lỗi thực thi câu lệnh SQL khi lấy dữ liệu từ vtiger_stage");
			}
			$allowedMovetoIdInfo = array();
			while ($row = $db->fetchByAssoc($stageResult)) {
				// Clone vtiger_stage
				$oldStageId = $row['stageid'];
				$newStageId = $db->getUniqueID('vtiger_stage');
				$query = 'INSERT INTO vtiger_stage
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?)';
				$stageName = html_entity_decode($row['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
				// convert actions
				$actionsJson = html_entity_decode($row['actions'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
				$actions = json_decode($actionsJson, true);
				foreach ($actions as $key => $action) {
					$actions[$key]['stageId'] = $newStageId;
				}				
				$actions = json_encode($actions);
				// convert conditions
				$conditions = html_entity_decode($row['conditions'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
				$params = array($newStageId, $newPipelineId, $stageName, $row['success_rate'], 
					$row['time'], $row['time_unit'], $row['is_mandatory'], $row['color_code'],
					$row['sequence'], $row['value'], $actions, $conditions);
				$db->pquery($query, $params);

				// Prepare for cloning vtiger_allowedmoveto table
				$query = 'SELECT * FROM vtiger_allowedmoveto WHERE stageid = ?';
				$params = array($oldStageId);
				$result = $db->pquery($query, $params);
				if ($result === false) {
					throw new Exception("Lỗi thực thi câu lệnh SQL khi lấy dữ liệu từ vtiger_allowedmoveto");
				}
				$allowedMovetoSequence = array();
				while ($row = $db->fetchByAssoc($result)) {
					// get all sequence of allowedstageids
					$query = 'SELECT sequence FROM vtiger_stage WHERE stageid = ?';
					$params = array($row['allowedstageid']);
					$result = $db->pquery($query, $params);
					$row = $db->fetchByAssoc($result);
					$allowedMovetoSequence[] = $row['sequence'];
				}
				$allowedMovetoIdInfo[] = array(
					$newStageId => $allowedMovetoSequence
				);

				// Clone vtiger_rolestage
				$query = 'SELECT * FROM vtiger_rolestage WHERE stageid = ?';
				$params = array($oldStageId);
				$result = $db->pquery($query, $params);
				if ($result === false) {
					throw new Exception("Lỗi thực thi câu lệnh SQL khi lấy dữ liệu từ vtiger_rolestage");
				}
				while ($row = $db->fetchByAssoc($result)) {
					$query = 'INSERT INTO vtiger_rolestage
								VALUES (?, ?)';
					$params = array($newStageId, $row['roleid']);
					$db->pquery($query, $params);
				}
			}

			// Clone vtiger_allowedmoveto
			foreach($allowedMovetoIdInfo as $stageInfo){
				foreach($stageInfo as $newStageId => $sequenceArr){
					foreach($sequenceArr as $sequence){
						// get stageid from allowedstageid
						$query = 'SELECT stageid FROM vtiger_stage WHERE pipelineid = ? AND sequence = ?';
						$params = array($newPipelineId, $sequence);
						$result = $db->pquery($query, $params);
						// insert into vtiger_allowedmoveto
						$row = $db->fetchByAssoc($result);
						$newAllowedMovetoId = $db->getUniqueID('vtiger_allowedmoveto');
						$query = 'INSERT INTO vtiger_allowedmoveto
									VALUES (?, ?, ?)';
						$params = array($newAllowedMovetoId, $newStageId, $row['stageid']);
						$db->pquery($query, $params);
					}
				}
			}
			
			$response = new Vtiger_Response();
			$response->setResult([
				'success' => true,
				'message' => vtranslate('LBL_CLONE_SUCCESS'),
				'newPipelineId' => $newPipelineId,
			]);
		
			$response->emit();
		} catch (Exception $e){
			echo "".$e;
		}
    }
	// End Dien Nguyen

}