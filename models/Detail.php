<?php
/*
    Detail_Model
    Author: The Vi
    Date: 22/1/2025
    Purpose: Provides functions for retrieving detailed pipeline information, including roles, stages, and permissions.
*/
class Settings_PipelineConfig_Detail_Model extends Vtiger_Base_Model {

    public static function getDetailPipeline($pipelineId) { 
        $db = PearDatabase::getInstance();
        
        $pipelineQuery = "SELECT * 
                          FROM vtiger_pipeline 
                          WHERE pipelineid = ?";

        $pipelineResult = $db->pquery($pipelineQuery, array($pipelineId));

        $pipelineData = $db->fetchByAssoc($pipelineResult);
      
        $rolesQuery = "SELECT rp.roleid, r.rolename 
                       FROM vtiger_rolepipeline rp 
                       JOIN vtiger_role r ON rp.roleid = r.roleid 
                       WHERE rp.pipelineid = ?";

        $rolesResult = $db->pquery($rolesQuery, array($pipelineId));

        $rolesSelected = array();

        while ($roleRow = $db->fetchByAssoc($rolesResult)) {
            $rolesSelected[] = array(
                'role_id' => $roleRow['roleid'],
                'role_name' => $roleRow['rolename']
            );
        }
    
        $stagesQuery = "SELECT * FROM vtiger_stage WHERE pipelineid = ? ORDER BY sequence ASC";
        $stagesResult = $db->pquery($stagesQuery, array($pipelineId));
        $stagesList = array();

        while ($stageRow = $db->fetchByAssoc($stagesResult)) {
            $stageId = $stageRow['stageid'];
            
            $nextStagesQuery = "SELECT allowedstageid FROM vtiger_allowedmoveto WHERE stageid = ?";
            $nextStagesResult = $db->pquery($nextStagesQuery, array($stageId));
            $nextStages = array();

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
            
            $permissionsQuery = "SELECT r.roleid, r.rolename 
                                 FROM vtiger_rolestage rs 
                                 JOIN vtiger_role r ON rs.roleid = r.roleid 
                                 WHERE rs.stageid = ?";

            $permissionsResult = $db->pquery($permissionsQuery, array($stageId));
            $permissions = array();

            while ($permRow = $db->fetchByAssoc($permissionsResult)) {
                $permissions[] = array(
                    'role_id' => $permRow['roleid'],
                    'role_name' => $permRow['rolename']
                );
            }
        
            $decodedActions = html_entity_decode($stageRow['actions']);
			$actions = json_decode($decodedActions, true);
            $decodedConditions = html_entity_decode($stageRow['conditions']);
			// $conditions = json_decode($decodedConditions, true);
            $conditions = json_decode($decodedConditions, false);
        
            $stageData = array(
                'id' => $stageId,
                'sequence' => $stageRow['sequence'],
                'name' => $stageRow['name'],
                'success_rate' => (int)$stageRow['success_rate'],
                'vnLabel' => $stageRow['name'], // Giả định nhãn tiếng Việt
                'enLabel' => $stageRow['name'], // Giả định nhãn tiếng Anh
                'value' => $stageRow['value'],
                'execution_time' => array(
                    'value' => (int)$stageRow['time'],
                    'unit' => $stageRow['time_unit']
                ),
                'is_mandatory' => $stageRow['is_mandatory'] == 1, // Kiểm tra nếu is_mandatory là 1
                'next_stages' => $nextStages,
                'permissions' => $permissions,
                'color' => $stageRow['color_code'],
                // 'actions' => $stageRow['actions'],
                'actions' => $actions,
                // 'conditions' => $stageRow['conditions'],
                'conditions' => $conditions,
                'status' => 'active', // Giả định mặc định
                'created_time' => '2025-02-10T06:56:24.075Z', // Giả định
                'modified_time' => '2025-02-10T06:56:24.075Z',
                'created_user_id' => '1',
                'modified_user_id' => '1'
            );
            $stagesList[] = $stageData;
        }
        
        $result = array(
            'id' => $pipelineData['pipelineid'],
            'name' => $pipelineData['name'],
            'time' => (int)$pipelineData['duration'],
            'time_unit' => $pipelineData['time_unit'],
            'module' => $pipelineData['module'],
            'autoTransition' => $pipelineData['auto_move'] == 1, // Explicitly check if auto_move is 1
            'rolesSelected' => $rolesSelected,
            'description' => $pipelineData['description'],
            'status' => $pipelineData['status'],
            'stagesList' => $stagesList
        );
        
        return $result;
    }
}