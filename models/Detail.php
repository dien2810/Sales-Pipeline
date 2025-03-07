<?php
/*
    Detail_Model
    Author: The Vi
    Date: 22/1/2025
    Purpose: Provides functions for retrieving detailed pipeline information, including roles, stages, and permissions.
*/
class Settings_PipelineConfig_Detail_Model extends Vtiger_Base_Model {

    // Implemented by The Vi on 2025-02-20 to retrieves detailed information about a pipeline
    public static function getDetailPipeline($pipelineId) {
        // Get the pipeline main info and stages info and merge results
        $pipelineInfo = self::getPipelineInfo($pipelineId);
        $stagesList = self::getPipelineStages($pipelineId);
        $pipelineInfo['stagesList'] = $stagesList;
    
        return $pipelineInfo;
    }
    
    private static function getPipelineInfo($pipelineId) {
        $db = PearDatabase::getInstance();
    
        // Retrieve pipeline data
        $pipelineQuery = "SELECT * 
                          FROM vtiger_pipeline 
                          WHERE pipelineid = ?";
        $pipelineResult = $db->pquery($pipelineQuery, array($pipelineId));
        $pipelineData = $db->fetchByAssoc($pipelineResult);
    
        // Retrieve role data for the pipeline
        $rolesQuery = "SELECT rp.roleid, r.rolename 
                       FROM vtiger_rolepipeline rp 
                       JOIN vtiger_role r ON rp.roleid = r.roleid 
                       WHERE rp.pipelineid = ?";
        $rolesResult = $db->pquery($rolesQuery, array($pipelineId));
        $rolesSelected = array();
    
        // Loop through roles and prepare the roles array
        while ($roleRow = $db->fetchByAssoc($rolesResult)) {
            $rolesSelected[] = array(
                'role_id' => $roleRow['roleid'],
                'role_name' => $roleRow['rolename']
            );
        }
    
        // Prepare the pipeline information array
        $pipelineInfo = array(
            'id' => $pipelineData['pipelineid'],
            'name' => $pipelineData['name'],
            'time' => (int)$pipelineData['duration'],
            'time_unit' => $pipelineData['time_unit'],
            'module' => $pipelineData['module'],
            'autoTransition' => $pipelineData['auto_move'] == 1,
            'rolesSelected' => $rolesSelected,
            'description' => $pipelineData['description'],
            'status' => $pipelineData['status']
        );
    
        return $pipelineInfo;
    }
    
    private static function getPipelineStages($pipelineId) {
        $db = PearDatabase::getInstance();
    
        // Retrieve all stages for the pipeline ordered by sequence ascending
        $stagesQuery = "SELECT * FROM vtiger_stage WHERE pipelineid = ? ORDER BY sequence ASC";
        $stagesResult = $db->pquery($stagesQuery, array($pipelineId));
        $stagesList = array();
    
        // Loop through each stage
        while ($stageRow = $db->fetchByAssoc($stagesResult)) {
            $stageId = $stageRow['stageid'];
    
            // Retrieve allowed next stages for the current stage
            $nextStagesQuery = "SELECT allowedstageid FROM vtiger_allowedmoveto WHERE stageid = ?";
            $nextStagesResult = $db->pquery($nextStagesQuery, array($stageId));
            $nextStages = array();
    
            // Loop through each allowed next stage
            while ($nextStageRow = $db->fetchByAssoc($nextStagesResult)) {
                $allowedStageId = $nextStageRow['allowedstageid'];
                $allowedStageQuery = "SELECT name FROM vtiger_stage WHERE stageid = ?";
                $allowedStageResult = $db->pquery($allowedStageQuery, array($allowedStageId));
                $allowedStageData = $db->fetchByAssoc($allowedStageResult);
    
                if ($allowedStageData) {
                    $nextStages[] = array(
                        'id' => $allowedStageId,
                        'name' => $allowedStageData['name']
                    );
                }
            }
    
            // Retrieve permissions (roles allowed) for the stage
            $permissionsQuery = "SELECT r.roleid, r.rolename 
                                 FROM vtiger_rolestage rs 
                                 JOIN vtiger_role r ON rs.roleid = r.roleid 
                                 WHERE rs.stageid = ?";
            $permissionsResult = $db->pquery($permissionsQuery, array($stageId));
            $permissions = array();
    
            // Loop through each permission record and build the permissions array
            while ($permRow = $db->fetchByAssoc($permissionsResult)) {
                $permissions[] = array(
                    'role_id' => $permRow['roleid'],
                    'role_name' => $permRow['rolename']
                );
            }
    
            // Decode 'actions' and 'conditions' fields from HTML entities to their original values
            $decodedActions = html_entity_decode($stageRow['actions']);
            $actions = json_decode($decodedActions, true);
            $decodedConditions = html_entity_decode($stageRow['conditions']);
            $conditions = json_decode($decodedConditions, false);
    
            // Prepare the stage data in the required structure
            $stageData = array(
                'id' => $stageId,
                'sequence' => $stageRow['sequence'],
                'name' => $stageRow['name'],
                'success_rate' => (int)$stageRow['success_rate'],
                'vnLabel' => $stageRow['name'], 
                'enLabel' => $stageRow['name'], 
                'value' => $stageRow['value'],
                'execution_time' => array(
                    'value' => (int)$stageRow['time'],
                    'unit' => $stageRow['time_unit']
                ),
                'is_mandatory' => $stageRow['is_mandatory'] == 1,
                'next_stages' => $nextStages,
                'permissions' => $permissions,
                'color' => $stageRow['color_code'],
                'actions' => $actions,
                'conditions' => $conditions,
                'status' => 'active',
                'created_user_id' => '1',
                'modified_user_id' => '1'
            );
    
            $stagesList[] = $stageData;
        }
    
        return $stagesList;
    }
}