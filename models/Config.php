<?php
/*
	Config_Model
	Author: The Vi
	Date: 22/1/2025
	Purpose: Provide utility functions for managing pipeline configurations
*/
class Settings_PipelineConfig_Config_Model extends Vtiger_Base_Model {

    // Implemented by: The Vi - Retrieves a list of pipelines with optional filtering. 

    public static function getPipelineList($nameModule = null, $name = null) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM vtiger_pipeline';
        $params = [];
        if (!empty($nameModule)) {
            $query .= ' WHERE module = ?';
            $params[] = $nameModule;
        }
        if (!empty($name)) {
            if (!empty($params)) {
                $query .= ' AND';
            } else {
                $query .= ' WHERE';
            }
            $query .= ' name LIKE ?';
            $params[] = '%' . $name . '%'; 
        }
        $query .= ' ORDER BY pipelineid ASC';
        $result = $db->pquery($query, $params);
        return $result;
    }
    // Implemented by: The Vi - Retrieves active pipelines with optional filtering. 

    public static function getPipelineStatusList($nameModule = null, $name = null) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM vtiger_pipeline WHERE status = 1';
        $params = []; 
    
        if (!empty($nameModule)) {
            $query .= ' AND module = ?';
            $params[] = $nameModule;
        }
    
        if (!empty($name)) {
            $query .= ' AND name LIKE ?';
            $params[] = '%' . $name . '%';
        }
    
        $query .= ' ORDER BY pipelineid ASC';
        $result = $db->pquery($query, $params);
        return $result;
    }

    // Implemented by: The Vi - Retrieves pipelines excluding a specified pipeline ID. 
    public static function getPipelineListExcluding($nameModule = null, $name = null, $idpipeline = null) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM vtiger_pipeline';
        $params = [];
        $conditions = [];
    
        // Lọc theo module nếu được truyền vào
        if (!empty($nameModule)) {
            $conditions[] = 'module = ?';
            $params[] = $nameModule;
        }
    
        // Lọc theo tên (tìm kiếm dạng LIKE) nếu có giá trị name
        if (!empty($name)) {
            $conditions[] = 'name LIKE ?';
            $params[] = '%' . $name . '%';
        }
    
        // Loại trừ pipeline có pipelineid bằng với idpipeline được truyền vào
        if (!empty($idpipeline)) {
            $conditions[] = 'pipelineid <> ?';
            $params[] = $idpipeline;
        }
    
        // Nếu có điều kiện nào, nối chúng với nhau bằng AND
        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }
    
        $query .= ' ORDER BY pipelineid ASC';
    
        $result = $db->pquery($query, $params);
        return $result;
    }
    // Implemented by: The Vi - Retrieves all stages for a given pipeline ID. 

    public static function getStageList($idPipeline = null) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM vtiger_stage';
        $params = array();
        if (!empty($idPipeline)) {
            $query .= ' WHERE pipelineid = ?';
            $params[] = $idPipeline;
        }
        $query .= ' ORDER BY stageid ASC';
        $result = $db->pquery($query, $params);
        return $result;
    }
    //Implemented by: The Vi - Updates the status of a specified pipeline. 
     
    public static function updatePipelineStatus($id, $status) {
        $db = PearDatabase::getInstance();
        if (empty($id)) {
            throw new Exception("ID không được để trống");
        }
        $query = 'UPDATE vtiger_pipeline SET status = ? WHERE pipelineid = ?';
        $params = [$status, $id];
        $result = $db->pquery($query, $params);
        if ($result === false) {
            throw new Exception("Lỗi thực thi câu lệnh SQL");
        }
        $affectedRows = $db->getAffectedRowCount($result);
    
        if ($affectedRows > 0) {
            return [
                'success' => true,
                'message' => 'Cập nhật thành công',
                'affectedRows' => $affectedRows,
                'data' => [
                    'id' => $id,
                    'status' => $status
                ]
            ];
        } else {
            return [
                'success' => false, 
                'message' => 'Không tìm thấy bản ghi để cập nhật',
                'affectedRows' => 0,
                'data' => [
                    'id' => $id,
                    'status' => $status
                ]
            ];
        }
    }

    //Implemented by: The Vi - Deletes a pipeline and updates related records.

    public static function deletePipelineRecordExist($idPipeline, $idPipelineReplace, $stageReplace) {
        $db = PearDatabase::getInstance();
    
        // Lấy tên pipeline thay thế
        $resultPipeline = $db->pquery("SELECT name FROM vtiger_pipeline WHERE pipelineid = ?", array($idPipelineReplace));
        if($db->num_rows($resultPipeline) > 0) {
            $pipelineNameReplace = $db->query_result($resultPipeline, 0, 'name');
        } else {
            throw new Exception("Không tìm thấy pipeline thay thế với id: " . $idPipelineReplace);
        }
    
        // Cập nhật bảng vtiger_potential với pipeline thay thế
        $updatePipelineSQL = "UPDATE vtiger_potential 
                              SET pipelineid = ?, pipelinename = ? 
                              WHERE pipelineid = ?";
        $db->pquery($updatePipelineSQL, array($idPipelineReplace, $pipelineNameReplace, $idPipeline));
    
        // Cập nhật stage nếu có truyền vào
        if (is_array($stageReplace) && count($stageReplace) > 0) {
            foreach($stageReplace as $map) {
                $idCurrently = $map['idCurrently'];
                $idReplace = $map['idReplace'];
    
                $sqlStage = "SELECT name, value FROM vtiger_stage WHERE stageid = ?";
                $resultStage = $db->pquery($sqlStage, array($idReplace));
    
                if ($db->num_rows($resultStage) > 0) {
                    $newStageName = $db->query_result($resultStage, 0, 'name');
                    $newSalesStage = $db->query_result($resultStage, 0, 'value');
    
                    $updateStageSQL = "UPDATE vtiger_potential 
                                       SET stageid = ?, stagename = ?, sales_stage = ? 
                                       WHERE stageid = ?";
                    $db->pquery($updateStageSQL, array($idReplace, $newStageName, $newSalesStage, $idCurrently));
                }
            }
        }
    
        // Sau khi cập nhật, xóa pipeline cũ
        $deleteResult = self::deletePipelineById($idPipeline);
        return $deleteResult;
    }
    // Implemented by: The Vi - Deletes a pipeline by ID with transaction handling. 
    public static function deletePipelineById($id) {
        $db = PearDatabase::getInstance();

        if (empty($id)) {
            throw new Exception("ID không được để trống");
        }
        try {
            $db->startTransaction();
                $queryStages = "SELECT stageid FROM vtiger_stage WHERE pipelineid = ?";
            $resultStages = $db->pquery($queryStages, [$id]);
            $stageIds = [];

            while ($row = $db->fetch_array($resultStages)) {
                $stageIds[] = $row['stageid'];
            }

            if (!empty($stageIds)) {
                $stageIdsPlaceholder = implode(',', array_fill(0, count($stageIds), '?'));
    
                $queryDeleteRoleStage = "DELETE FROM vtiger_rolestage WHERE stageid IN ($stageIdsPlaceholder)";
                $db->pquery($queryDeleteRoleStage, $stageIds);
    
                $queryDeleteAllowedMove = "DELETE FROM vtiger_allowedmoveto WHERE stageid IN ($stageIdsPlaceholder) OR allowedstageid IN ($stageIdsPlaceholder)";
                $db->pquery($queryDeleteAllowedMove, array_merge($stageIds, $stageIds));
    
                $queryDeleteStages = "DELETE FROM vtiger_stage WHERE pipelineid = ?";
                $db->pquery($queryDeleteStages, [$id]);
    
                $queryUpdatePotential = "UPDATE vtiger_potential SET pipelineid = NULL, stageid = NULL WHERE pipelineid = ?";
                $db->pquery($queryUpdatePotential, [$id]);
            }

            $queryDeleteRolePipeline = "DELETE FROM vtiger_rolepipeline WHERE pipelineid = ?";
            $db->pquery($queryDeleteRolePipeline, [$id]);
            $queryDeletePipeline = "DELETE FROM vtiger_pipeline WHERE pipelineid = ?";
            $db->pquery($queryDeletePipeline, [$id]);
            $db->completeTransaction();

            return [
                'success' => true,
                'message' => 'Xóa Pipeline thành công',
                'deletedPipelineId' => $id
            ];
        } catch (Exception $e) {
            $db->rollbackTransaction();
            return [
                'success' => false,
                'message' => 'Lỗi khi xóa Pipeline: ' . $e->getMessage()
            ];
        }
    }
    // Implemented by: The Vi - Checks if a pipeline record exists. 
    public static function isPipelineRecordExist($pipelineId) {
        $db = PearDatabase::getInstance();
        
        if (empty($pipelineId)) {
            throw new Exception("Pipeline ID không được để trống");
        }
        
        $query = "SELECT 1 FROM vtiger_potential WHERE pipelineid = ? LIMIT 1";
        $params = [$pipelineId];
        $result = $db->pquery($query, $params);
                if ($result && $db->num_rows($result) > 0) {
            return true;
        }
        return false;
    } 
  
}