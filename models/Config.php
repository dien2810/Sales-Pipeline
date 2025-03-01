<?php
/*
	Config_Model
	Author: The Vi
	Date: 22/1/2025
	Purpose: Provide utility functions for managing pipeline configurations
*/
class Settings_PipelineConfig_Config_Model extends Vtiger_Base_Model {

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

     //Begin By The Vi 28-2-2025
     public static function deletePipelineRecordExist($idPipeline, $stageReplace) {
        $db = PearDatabase::getInstance();
       


    







    }
    
     //End By The Vi 28-2-2025


    //Begin By The Vi 28-2-2025
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
    
     //End By The Vi 28-2-2025


    //Begin The Vi 28/2/2025
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
    //End The Vi 28/2/2025
}