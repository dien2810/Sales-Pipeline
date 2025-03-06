<?php
/*
	Edit_Model
	Author: The Vi
	Date: 22/1/2025
	Purpose: Provide utility functions for managing pipeline edit
*/
class Settings_PipelineConfig_Edit_Model extends Vtiger_Base_Model {

    // Implemented by The Vi to retrieves all stages for a given pipeline ID. 
    public static function getStagesByPipelineId($pipelineId) {
        $db = PearDatabase::getInstance();

        $query = 'SELECT * FROM vtiger_stage WHERE pipelineid = ? ORDER BY sequence ASC';
        $params = [$pipelineId];
    
        $result = $db->pquery($query, $params);
        
        $stages = [];
        while ($row = $db->fetch_array($result)) {
            $stages[] = [
                'stageid'       => $row['stageid'],
                'pipelineid'    => $row['pipelineid'],
                'name'          => $row['name'],
                'success_rate'  => $row['success_rate'],
                'time'          => $row['time'],
                'time_unit'     => $row['time_unit'],
                'is_mandatory'  => $row['is_mandatory'],
                'color_code'    => $row['color_code'],
                'sequence'      => $row['sequence'],
            ];
        }
    
        return $stages;
    }

    //Implemented by The Vi to deletes a stage and updates related records. 
    public static function deleteStagePipeline($idStageDelete, $idStageReplace, $module) {
        $adb = PearDatabase::getInstance();
        $adb->startTransaction();

        try {
            $query1 = "DELETE FROM vtiger_rolestage WHERE stageid = ?";
            $adb->pquery($query1, array($idStageDelete));

            $query2 = "DELETE FROM vtiger_allowedmoveto WHERE stageid = ? OR allowedstageid = ?";
            $adb->pquery($query2, array($idStageDelete, $idStageDelete));

            if (strtolower($module) === 'potentials') {

                $queryReplace = "SELECT name, value FROM vtiger_stage WHERE stageid = ?";
                $resultReplace = $adb->pquery($queryReplace, array($idStageReplace));
                if ($adb->num_rows($resultReplace) > 0) {
                    $repStageName = $adb->query_result($resultReplace, 0, 'name');
                    $repSalesStage = $adb->query_result($resultReplace, 0, 'value');
                    
                    $updatePotential = "UPDATE vtiger_potential 
                        SET stageid = ?, stagename = ?, sales_stage = ? 
                        WHERE stageid = ?";
                    $adb->pquery($updatePotential, array($idStageReplace, $repStageName, $repSalesStage, $idStageDelete));
                } else {

                    $adb->rollbackTransaction();
                    return false;
                }
            }

            $query4 = "DELETE FROM vtiger_stage WHERE stageid = ?";
            $adb->pquery($query4, array($idStageDelete));

            $adb->completeTransaction();

            return true;
        } catch (Exception $ex) {

            $adb->rollbackTransaction();
            return false;

        }
    }

    //Implemented by The Vi to saves a new pipeline with its stages and roles. 
    public static function savePipeline($pipelineData, $currentUser) {

            // Save the pipeline information 
            $resultPipeline = self::savePipelineInfo($pipelineData, $currentUser);
            $newPipelineId = $resultPipeline['pipelineId'];
            $roleMapping = $resultPipeline['roleMapping'];

            // Save the stages associated with the pipeline using the new pipeline ID and role mapping
            $stageIdMap = self::savePipelineStages($pipelineData, $newPipelineId, $roleMapping);
            return [
                'success' => true,
                'pipelineId' => $newPipelineId,
                'stageIdMap' => $stageIdMap,
                'data' => $pipelineData
            ];

    }  
    //Implemented by The Vi to save the pipeline information into the database.
    
    public static function savePipelineInfo($pipelineData, $currentUser) {
        global $adb;
        
        $userDisplayName = $currentUser->getDisplayName();
        $stageCount = count($pipelineData['stagesList']);
        $status = ($pipelineData['status'] == 'inActive') ? 0 : 1;
        $autoTransition = ($pipelineData['autoTransition'] === 'false' || $pipelineData['autoTransition'] === false) ? 0 : 1;
        
        $sqlPipeline = "INSERT INTO vtiger_pipeline (
            module, name, stage, status, auto_move,
            duration, time_unit, description, create_by, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $paramsPipeline = array(
            $pipelineData['module'],
            $pipelineData['name'],
            $stageCount,
            $status,
            $autoTransition,
            $pipelineData['time'],
            $pipelineData['timetype'],
            $pipelineData['description'],
            $userDisplayName,
            date('Y-m-d H:i:s')
        );
        
        $adb->pquery($sqlPipeline, $paramsPipeline);
        $newPipelineId = $adb->getLastInsertID();
        
        // Prepare role mapping for pipeline permissions.
        $roleMapping = array("all" => "Tất cả");
        $allRoles = Settings_Roles_Record_Model::getAll();
        foreach ($allRoles as $role) {
            $roleMapping[$role->get('roleid')] = $role->get('rolename');
        }
        
        // Determine which roles should be assigned to the pipeline.
        // If "all" is selected, then use all roles from the mapping.
        $rolesToInsert = in_array("all", $pipelineData['rolesSelected']) ? array_keys($roleMapping) : $pipelineData['rolesSelected'];
        foreach ($rolesToInsert as $roleId) {
            if ($roleId !== "all") {
                $adb->pquery("INSERT INTO vtiger_rolepipeline (roleid, pipelineid) VALUES (?, ?)", array($roleId, $newPipelineId));
            }
        }

        return array('pipelineId' => $newPipelineId, 'roleMapping' => $roleMapping);
    }

    // Implemented by The Vi to save the pipeline stages along with their permissions and allowed transitions.
    public static function savePipelineStages($pipelineData, $newPipelineId, $roleMapping) {
        global $adb;
        $stageIdMap = array(); 
        
        // Loop through each stage in the pipeline data and insert it into the database
        foreach ($pipelineData['stagesList'] as $stage) {
          
            $isMandatory = ($stage['is_mandatory'] === true || $stage['is_mandatory'] === 'true') ? 1 : 0;
        
            $sqlStage = "INSERT INTO vtiger_stage (
                pipelineid, name, success_rate, time, time_unit, is_mandatory, 
                color_code, sequence, value, actions, conditions
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $paramsStage = array(
                $newPipelineId,
                $stage['name'],
                $stage['success_rate'],
                $stage['execution_time']['value'],
                $stage['execution_time']['unit'],
                $isMandatory,
                $stage['color'],
                $stage['sequence'],
                $stage['value'],
                $stage['actions'],
                $stage['conditions']
            );
            
            $adb->pquery($sqlStage, $paramsStage);
            $newStageId = $adb->getLastInsertID();
           
            $stageIdMap[$stage['id']] = $newStageId;
            
            // Replace any references to the old stage ID with the new stage ID in the actions and conditions fields
            $modifiedActions = str_replace($stage['id'], $newStageId, $stage['actions']);
            $modifiedConditions = str_replace($stage['id'], $newStageId, $stage['conditions']);
            $adb->pquery("UPDATE vtiger_stage SET actions = ?, conditions = ? WHERE stageid = ?", 
                array($modifiedActions, $modifiedConditions, $newStageId));
            
            // If permissions are set for this stage, process them
            if (isset($stage['permissions']) && is_array($stage['permissions'])) {
                foreach ($stage['permissions'] as $permission) {
                    $roleId = $permission['role_id'];

                    $rolesToInsert = ($roleId === "all") ? array_keys($roleMapping) : array($roleId);
                    foreach ($rolesToInsert as $rId) {
                        if ($rId !== "all") {
                            $adb->pquery("INSERT INTO vtiger_rolestage (stageid, roleid) VALUES (?, ?)", array($newStageId, $rId));
                        }
                    }
                }
            }
        }
        
        // Process allowed stage transitions (next stages)
        foreach ($pipelineData['stagesList'] as $stage) {
            $currentTempId = $stage['id'];
            $currentRealId = isset($stageIdMap[$currentTempId]) ? $stageIdMap[$currentTempId] : null;
            if ($currentRealId && isset($stage['next_stages']) && is_array($stage['next_stages'])) {
                foreach ($stage['next_stages'] as $nextStage) {
                    $nextRealId = isset($stageIdMap[$nextStage['id']]) ? $stageIdMap[$nextStage['id']] : null;
                    if ($nextRealId) {
                        $adb->pquery("INSERT INTO vtiger_allowedmoveto (stageid, allowedstageid) VALUES (?, ?)", 
                            array($currentRealId, $nextRealId));
                    }
                }
            }
        }
        return $stageIdMap;
    }


    // Implemented by The Vi to updates an existing pipeline and its related data.
    public static function updatePipeline($pipelineData, $currentUser) {
        global $adb;
        $pipelineId = $pipelineData['id'];
        try {
            $adb->startTransaction();

            // Update pipeline information (including roles)
            self::updatePipelineInfo($adb, $pipelineData, $pipelineId);

            // Update pipeline stages (including basic info, actions, conditions, permissions, and allowed transitions)
            self::updatePipelineStages($adb, $pipelineData, $pipelineId);

            $adb->completeTransaction();

            return [
                'success'    => true,
                'pipelineId' => $pipelineId,
                'message'    => 'Pipeline updated successfully'
            ];

        } catch (Exception $e) {
            $adb->rollbackTransaction();
            return [
                'success'       => false,
                'error_code'    => $e->getCode(),
                'error_message' => $e->getMessage()
            ];
        }
    }
    
    //Implemented by The Vi updates the pipeline's main information and its related roles.
     
    public static function updatePipelineInfo($adb, $pipelineData, $pipelineId) {
        // Determine stage count and status values.
        $stageCount = count($pipelineData['stagesList']);
        $status = ($pipelineData['status'] === 'inActive') ? 0 : 1;
        $autoTransition = ($pipelineData['autoTransition'] === 'false' || $pipelineData['autoTransition'] === false) ? 0 : 1;

        $sqlUpdatePipeline = "UPDATE vtiger_pipeline SET 
            module = ?, 
            name = ?, 
            stage = ?, 
            status = ?, 
            auto_move = ?,
            duration = ?, 
            time_unit = ?, 
            description = ?
            WHERE pipelineid = ?";
                
        $paramsUpdatePipeline = [
            $pipelineData['module'],
            $pipelineData['name'],
            $stageCount,
            $status,
            $autoTransition,
            $pipelineData['time'],
            $pipelineData['timetype'],
            $pipelineData['description'],
            $pipelineId
        ];
        $adb->pquery($sqlUpdatePipeline, $paramsUpdatePipeline);

        // Delete all existing role associations for the pipeline.
        $sqlDeleteRoles = "DELETE FROM vtiger_rolepipeline WHERE pipelineid = ?";
        $adb->pquery($sqlDeleteRoles, [$pipelineId]);

        // Insert new role associations.
        foreach ($pipelineData['rolesSelected'] as $roleId) {
            $sqlRole = "INSERT INTO vtiger_rolepipeline (roleid, pipelineid) VALUES (?, ?)";
            $adb->pquery($sqlRole, [$roleId, $pipelineId]);
        }
    }
    
    
     //Implemented by The Vi updates or creates pipeline stages including their actions, conditions, permissions, and allowed next transitions.
  
    public static function updatePipelineStages($adb, $pipelineData, $pipelineId) {
        // Get all existing stage ids for the pipeline.
        $existingStages = [];
        $sqlExistingStages = "SELECT stageid FROM vtiger_stage WHERE pipelineid = ?";
        $resultStages = $adb->pquery($sqlExistingStages, [$pipelineId]);

        while ($row = $adb->fetch_array($resultStages)) {
            $existingStages[] = $row['stageid'];
        }

        // Delete allowed transitions and role-stage associations for existing stages.
        if (!empty($existingStages)) {
            $sqlDeleteAllowed = "DELETE FROM vtiger_allowedmoveto WHERE stageid IN (" . implode(',', $existingStages) . ")";
            $adb->pquery($sqlDeleteAllowed, []);
            
            $sqlDeleteRoleStage = "DELETE FROM vtiger_rolestage WHERE stageid IN (" . implode(',', $existingStages) . ")";
            $adb->pquery($sqlDeleteRoleStage, []);
        }

    
        $clientStageMap = [];

        // update basic stage information (update existing stage or insert new stage).
        foreach ($pipelineData['stagesList'] as $stage) {
            $clientStageId = $stage['id'];
            $value = $stage['value'];

            // Check if stage exists in the database.
            $sqlCheckStage = "SELECT stageid FROM vtiger_stage WHERE pipelineid = ? AND value = ?";
            $resultCheck = $adb->pquery($sqlCheckStage, [$pipelineId, $value]);
            $stageExists = $adb->num_rows($resultCheck) > 0;

            $isMandatory = ($stage['is_mandatory'] === true || $stage['is_mandatory'] === 'true') ? 1 : 0;

            if ($stageExists) {
                $dbStageId = $adb->query_result($resultCheck, 0, 'stageid');
                $sqlUpdateStage = "UPDATE vtiger_stage SET 
                    name = ?, 
                    success_rate = ?, 
                    time = ?, 
                    time_unit = ?, 
                    is_mandatory = ?, 
                    color_code = ?, 
                    sequence = ?
                    WHERE stageid = ?";
                $paramsUpdateStage = [
                    $stage['name'],
                    $stage['success_rate'],
                    $stage['execution_time']['value'],
                    $stage['execution_time']['unit'],
                    $isMandatory,
                    $stage['color'],
                    $stage['sequence'],
                    $dbStageId
                ];
                $adb->pquery($sqlUpdateStage, $paramsUpdateStage);
            } else {
                $sqlNewStage = "INSERT INTO vtiger_stage (
                    pipelineid, name, success_rate, time, time_unit, 
                    is_mandatory, color_code, sequence, value
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $paramsNewStage = [
                    $pipelineId,
                    $stage['name'],
                    $stage['success_rate'],
                    $stage['execution_time']['value'],
                    $stage['execution_time']['unit'],
                    $isMandatory,
                    $stage['color'],
                    $stage['sequence'],
                    $value
                ];
                $adb->pquery($sqlNewStage, $paramsNewStage);
                $dbStageId = $adb->getLastInsertID();
            }
            $clientStageMap[$clientStageId] = $dbStageId;
        }

        //  update stage actions, conditions, permissions, and allowed transitions.
        foreach ($pipelineData['stagesList'] as $stage) {
            $clientStageId = $stage['id'];
            $dbStageId = $clientStageMap[$clientStageId];

            // Process actions: update JSON actions and replace client stage IDs with database stage IDs.
            $actions = json_decode($stage['actions'], true);
            if (is_array($actions)) {
                array_walk_recursive($actions, function (&$item, $key) use ($clientStageMap) {
                    if ($key === 'stageId' && isset($clientStageMap[$item])) {
                        $item = $clientStageMap[$item];
                    }
                });
                $updatedActions = json_encode($actions);
            } else {
                $updatedActions = $stage['actions'];
            }

            // Process conditions: same as actions.
            $conditions = json_decode($stage['conditions'], true);
            if (is_array($conditions)) {
                array_walk_recursive($conditions, function (&$item, $key) use ($clientStageMap) {
                    if ($key === 'stageId' && isset($clientStageMap[$item])) {
                        $item = $clientStageMap[$item];
                    }
                });
                $updatedConditions = json_encode($conditions);
            } else {
                $updatedConditions = $stage['conditions'];
            }

            $sqlUpdateStageData = "UPDATE vtiger_stage SET actions = ?, conditions = ? WHERE stageid = ?";
            $adb->pquery($sqlUpdateStageData, [$updatedActions, $updatedConditions, $dbStageId]);

            // Insert role-stage permissions if available.
            if (isset($stage['permissions']) && is_array($stage['permissions'])) {
                foreach ($stage['permissions'] as $permission) {
                    $sqlRoleStage = "INSERT INTO vtiger_rolestage (stageid, roleid) VALUES (?, ?)";
                    $adb->pquery($sqlRoleStage, [$dbStageId, $permission['role_id']]);
                }
            }

            // Insert allowed next stages.
            if (isset($stage['next_stages']) && is_array($stage['next_stages'])) {
                foreach ($stage['next_stages'] as $nextStage) {
                    $nextClientId = $nextStage['id'];
                    if (isset($clientStageMap[$nextClientId])) {
                        $sqlAllowed = "INSERT INTO vtiger_allowedmoveto (stageid, allowedstageid) VALUES (?, ?)";
                        $adb->pquery($sqlAllowed, [$dbStageId, $clientStageMap[$nextClientId]]);
                    }
                }
            }
        }
    }
    
    // Add by Dien Nguyen on 2025-03-03 to get field expressions on create new record modal
    static function getExpressions() {
		return array('concat' => 'concat(a,b)', 'time_diffdays(a,b)' => 'time_diffdays(a,b)', 'time_diffdays(a)' => 'time_diffdays(a)', 'time_diff(a,b)' => 'time_diff(a,b)','time_diff(a)' => 'time_diff(a)',
			'add_days' => 'add_days(datefield, noofdays)', 'sub_days' => 'sub_days(datefield, noofdays)', 'add_time(timefield, minutes)' => 'add_time(timefield, minutes)', 'sub_time(timefield, minutes)' => 'sub_time(timefield, minutes)',
			'today' => "get_date('today')", 'tomorrow' => "get_date('tomorrow')",  'yesterday' => "get_date('yesterday')", 'power(base,exponential)' => "power(base,exponential)");
	}
}