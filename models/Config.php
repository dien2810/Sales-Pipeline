<?php
/*
	Config_Model
	Author: Team
	Date: 22/1/2025
	Purpose: Provide utility functions for managing pipeline configurations
*/
class Settings_PipelineConfig_Config_Model extends Vtiger_Base_Model {

    // Implemented by The Vi to retrieves a list of pipelines with optional filtering. 
    public static function getPipelineList($nameModule = null, $name = null, $roleId = null) {
        $db = PearDatabase::getInstance();
        
        // Main query with stage count and role permissions
        $query = "SELECT 
            vp.*,
            COUNT(DISTINCT vs.stageid) AS stage_count,
            GROUP_CONCAT(DISTINCT vrp.roleid) as role_permissions
        FROM vtiger_pipeline vp
        LEFT JOIN vtiger_stage vs ON vp.pipelineid = vs.pipelineid
        LEFT JOIN vtiger_rolepipeline vrp ON vp.pipelineid = vrp.pipelineid";
        
        $params = [];
        $whereClauses = [];
    
        // Filter by module
        if (!empty($nameModule)) {
            $whereClauses[] = 'vp.module = ?';
            $params[] = $nameModule;
        }
    
        // Filter by pipeline name
        if (!empty($name)) {
            $whereClauses[] = 'vp.name LIKE ?';
            $params[] = '%' . $name . '%';
        }
    
        // Filter by role ID - Modified to use EXISTS subquery
        if (!empty($roleId)) {
            $whereClauses[] = 'EXISTS (
                SELECT 1 FROM vtiger_rolepipeline vrp2 
                WHERE vrp2.pipelineid = vp.pipelineid 
                AND vrp2.roleid = ?
            )';
            $params[] = $roleId;
        }
    
        // Add WHERE clauses if any
        if (!empty($whereClauses)) {
            $query .= ' WHERE ' . implode(' AND ', $whereClauses);
        }
    
        // Group and order results
        $query .= ' GROUP BY vp.pipelineid ORDER BY vp.pipelineid ASC';
    
        $result = $db->pquery($query, $params);
        $pipelines = array();
        
        // Format results
        while ($row = $db->fetch_array($result)) {
            // Convert role permissions string to array and ensure unique values
            $rolePermissions = !empty($row['role_permissions']) 
                ? array_unique(explode(',', $row['role_permissions'])) 
                : [];
    
            $pipeline = array(
                'pipelineid' => $row['pipelineid'],
                'module' => $row['module'],
                'name' => $row['name'],
                'stage' => $row['stage'],
                'status' => $row['status'],
                'auto_move' => $row['auto_move'],
                'duration' => $row['duration'],
                'time_unit' => $row['time_unit'],
                'description' => $row['description'],
                'is_default' => $row['is_default'],
                'create_by' => $row['create_by'],
                'created_at' => $row['created_at'],
                'stage_count' => $row['stage_count'],
                'permissions' => $rolePermissions
            );
            $pipelines[] = $pipeline;
        }
    
        return $pipelines;
    }
    // Implemented by The Vi to retrieves active pipelines with optional filtering. 

    public static function getPipelineStatusList($roleId, $nameModule = null, $name = null) {
        $db = PearDatabase::getInstance();
    
        $query = 'SELECT DISTINCT vp.* 
                  FROM vtiger_pipeline vp
                  INNER JOIN vtiger_rolepipeline vrp ON vp.pipelineid = vrp.pipelineid 
                  WHERE vp.status = 1 AND vrp.roleid = ?';
        
        $params = [$roleId];
    
        if (!empty($nameModule)) {
            $query .= ' AND vp.module = ?';
            $params[] = $nameModule;
        }
    
        if (!empty($name)) {
            $query .= ' AND vp.name LIKE ?';
            $params[] = '%' . $name . '%';
        }
    
        $query .= ' ORDER BY vp.pipelineid ASC';
        $result = $db->pquery($query, $params);
        return $result;
    }

    // Implemented by The Vi to retrieves pipelines excluding a specified pipeline ID. 
    public static function getPipelineListExcluding($nameModule = null, $name = null, $idpipeline = null) {
        $db = PearDatabase::getInstance();
        $query = 'SELECT * FROM vtiger_pipeline';
        $params = [];
        $conditions = [];
        if (!empty($nameModule)) {
            $conditions[] = 'module = ?';
            $params[] = $nameModule;
        }
    
        if (!empty($name)) {
            $conditions[] = 'name LIKE ?';
            $params[] = '%' . $name . '%';
        }
        if (!empty($idpipeline)) {
            $conditions[] = 'pipelineid <> ?';
            $params[] = $idpipeline;
        }
            if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $query .= ' ORDER BY pipelineid ASC';
        $result = $db->pquery($query, $params);
        return $result;
    }
    // Implemented by The Vi to retrieves all stages for a given pipeline ID. 

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
    //Implemented by The Vi updates the status of a specified pipeline. 
     
    public static function updatePipelineStatus($id, $status) {
        $db = PearDatabase::getInstance();
        if (empty($id)) {
            throw new Exception("ID cannot be empty");
        }
        $query = 'UPDATE vtiger_pipeline SET status = ? WHERE pipelineid = ?';
        $params = [$status, $id];
        $result = $db->pquery($query, $params);
        if ($result === false) {
            throw new Exception("Error executing SQL query");
        }
        $affectedRows = $db->getAffectedRowCount($result);
    
        if ($affectedRows > 0) {
            return [
                'success' => true,
                'message' => 'Update successful',
                'affectedRows' => $affectedRows,
                'data' => [
                    'id' => $id,
                    'status' => $status
                ]
            ];
        } else {
            return [
                'success' => false, 
                'message' => 'No record found to update',
                'affectedRows' => 0,
                'data' => [
                    'id' => $id,
                    'status' => $status
                ]
            ];
        }
    }
    

    //Implemented by The Vi to deletes a pipeline and updates related records.
    public static function deletePipelineRecordExist($idPipeline, $idPipelineReplace, $stageReplace) {
        $db = PearDatabase::getInstance();
    
        $resultPipeline = $db->pquery("SELECT name FROM vtiger_pipeline WHERE pipelineid = ?", array($idPipelineReplace));
        if ($db->num_rows($resultPipeline) > 0) {
            $pipelineNameReplace = $db->query_result($resultPipeline, 0, 'name');
        } else {
            throw new Exception("Replacement pipeline not found with ID: " . $idPipelineReplace);
        }
    
        $updatePipelineSQL = "UPDATE vtiger_potential 
                              SET pipelineid = ?, pipelinename = ? 
                              WHERE pipelineid = ?";
        $db->pquery($updatePipelineSQL, array($idPipelineReplace, $pipelineNameReplace, $idPipeline));
    
        if (is_array($stageReplace) && count($stageReplace) > 0) {
            foreach ($stageReplace as $map) {
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
    
        $deleteResult = self::deletePipelineById($idPipeline);
        return $deleteResult;
    }

    //Implemented by Minh Hoang to replace pipeline in records.
    public static function replacePipelineAndStageInRecord($idRecord, $idPipelineReplace, $idStageReplace) {
        try {
            $db = PearDatabase::getInstance();
    
            $resultPipeline = $db->pquery("SELECT name FROM vtiger_pipeline WHERE pipelineid = ?", array($idPipelineReplace));

            if ($db->num_rows($resultPipeline) > 0) {
                $pipelineNameReplace = $db->query_result($resultPipeline, 0, 'name');
            } else {
                throw new Exception("Pipeline not found with ID: " . $idPipelineReplace);
            }

            $resultStage = $db->pquery("SELECT name, success_rate, value FROM vtiger_stage WHERE stageid = ?", array($idStageReplace));

            if ($db->num_rows($resultStage) > 0) {
                $stageNameReplace = $db->query_result($resultStage, 0, 'name');
                $successRate = $db->query_result($resultStage, 0, 'success_rate');
                $stageValueReplace = $db->query_result($resultStage, 0, 'value');
            } else {
                throw new Exception("Stage not found with ID: " . $idStageReplace);
            }
            
            return [
                'success' => true,
                'message' => 'Pipeline and stage updated successfully',
                'data' => [
                    'idRecord' => $idRecord,
                    'idPipelineReplace' => $idPipelineReplace,
                    'pipelineNameReplace' => $pipelineNameReplace,
                    'idStageReplace' => $idStageReplace,
                    'stageNameReplace' => $stageNameReplace,
                    'successRate' => $successRate,
                    'stageValueReplace' => $stageValueReplace
                ]
            ];
        } catch(Throwable $th) {
            // throw $th;
            return [
                'success' => false,
                'message' => 'Error updating pipeline and stage: ' . $th->getMessage()
            ];
        }
    }
    
    // Implemented by The Vi deletes a pipeline by ID with transaction handling. 
    public static function deletePipelineById($id) {
        $db = PearDatabase::getInstance();
    
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
                'message' => 'Pipeline deleted successfully',
                'deletedPipelineId' => $id
            ];
        } catch (Exception $e) {
            $db->rollbackTransaction();
            return [
                'success' => false,
                'message' => 'Error deleting pipeline: ' . $e->getMessage()
            ];
        }
    }
    
    // Implemented by The Vi to checks if a pipeline record exists. 
    public static function isPipelineRecordExist($pipelineId, $sourceModule) {
        $db = PearDatabase::getInstance();
        
        // Get table name for module
        $focus = CRMEntity::getInstance($sourceModule);
    
        $tableName = $focus->table_name;   
        
        $query = "SELECT 1 FROM $tableName WHERE pipelineid = ? LIMIT 1";
        $result = $db->pquery($query, [$pipelineId]);
        
        return ($result && $db->num_rows($result) > 0);
    }

    
    // Implemented by The Vi to checks if a pipeline is the default pipeline.
    public static function checkPipelineDefault($pipelineId) {
        $db = PearDatabase::getInstance();
        
        $query = "SELECT 1 FROM vtiger_pipeline 
                  WHERE pipelineid = ? 
                  AND is_default = 1 
                  AND status = 1 
                  LIMIT 1";
                  
        $result = $db->pquery($query, array($pipelineId));
        
        return ($result && $db->num_rows($result) > 0);
    }
}