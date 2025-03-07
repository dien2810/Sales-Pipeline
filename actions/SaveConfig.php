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
		$module = $request->get('moduleName');

		$response = new Vtiger_Response();

		try {
			$result = Settings_PipelineConfig_Config_Model::getPipelineStatusList($module);
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
		$stageReplace = $request->get('stageReplace');
		
		$deleteResult = Settings_PipelineConfig_Config_Model::deletePipelineRecordExist($idPipeline, $idPipelineReplace, $stageReplace);
		$response = new Vtiger_Response();
		$response->setResult([
			'success' => $deleteResult['success'],
			'data' => $idPipelineReplace,
			'message' => $deleteResult['message']
		]);
		$response->emit();
	}

	//End The Vi 28-02-2025

	// Begin Dien Nguyen
	public static function clonePipeline($id) {
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
        $newPipelineId = $db->getUniqueID('vtiger_pipeline');
        $query = "INSERT INTO vtiger_pipeline(pipelineid,module,name,
            stage,status,auto_move, duration,time_unit,description, 
            is_default, create_by, created_at) values(?,?,?,?,?,?,?,?,?,?,?,?)";        
        $params = array($newPipelineId, $row['module'], $row['name'], 
            $row['stage'], $row['status'], $row['auto_move'],
            $row['duration'], $row['time_unit'], $row['description'], 0, 
            $row['create_by'], $row['created_at']);
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
        while ($row = $db->fetchByAssoc($stageResult)) {
            $oldStageId = $row['stageid'];
            $newStageId = $db->getUniqueID('vtiger_stage');
            $query = 'INSERT INTO vtiger_stage
                VALUES(?,?,?,?,?,?,?,?,?)';
            $params = array($newStageId, $newPipelineId, $row['name'], $row['success_rate'], 
                $row['time'], $row['time_unit'], $row['is_mandatory'], $row['color_code'],
                $row['sequence']);
            $db->pquery($query, $params);
            // Clone vtiger_allowedmoveto table
            $query = 'SELECT * FROM vtiger_allowedmoveto WHERE stageid = ?';
            $params = array($oldStageId);
            $result = $db->pquery($query, $params);
            if ($result === false) {
                throw new Exception("Lỗi thực thi câu lệnh SQL khi lấy dữ liệu từ vtiger_allowedmoveto");
            }
            while ($row = $db->fetchByAssoc($result)) {
                $newAllowedMovetoId = $db->getUniqueID('vtiger_allowedmoveto');
                $query = 'INSERT INTO vtiger_allowedmoveto
                            VALUES (?, ?, ?)';
                $params = array($newAllowedMovetoId, $newStageId, $row['allowedstageid']);
                $db->pquery($query, $params);
            }
            // Clone các record liên quan trong bảng vtiger_rolestage
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
        return [
            'success' => true,
            'message' => 'Clone thành công',
            'newPipelineId' => $newPipelineId,
        ];
    }
	// End Dien Nguyen

}