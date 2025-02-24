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
    public static function deletePipelineById($id) {
        $db = PearDatabase::getInstance();
    
        if (empty($id)) {
            throw new Exception("ID không được để trống");
        }
        $params = [$id];
        $deleteQuery = 'DELETE FROM vtiger_pipeline WHERE pipelineid = ?';
        $deleteResult = $db->pquery($deleteQuery, $params);
        // if ($deleteResult === false) {
        //     throw new Exception("Lỗi thực thi câu lệnh SQL");
        // }
        // $affectedRows = $db->getAffectedRowCount($deleteResult);
        // return [
        //     'success' => $affectedRows > 0,
        //     'message' => $affectedRows > 0 ? 'Xóa thành công' : 'Không có bản ghi nào bị xóa',
        //     'affectedRows' => $affectedRows,
        //     'data' => $id
        // ];
        return $deleteResult;
    }
    

    


    
    
}