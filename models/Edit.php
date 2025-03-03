<?php
/*
	Edit_Model
	Author: The Vi
	Date: 22/1/2025
	Purpose: Provide utility functions for managing pipeline edit
*/
class Settings_PipelineConfig_Edit_Model extends Vtiger_Base_Model {

    // Implemented by The Vi - Retrieves all stages for a given pipeline ID. 
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
    //Implemented by The Vi - Deletes a stage and updates related records. 
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
    //Implemented by The Vi - Saves a new pipeline with its stages and roles. 
    public static function savePipeline($pipelineData, $currentUser) {
        global $adb;
        $userDisplayName = $currentUser->getDisplayName();

        try {
            $stageCount = count($pipelineData['stagesList']);
            $status = ($pipelineData['status'] == 'inActive') ? 0 : 1;
            $autoTransition = ($pipelineData['autoTransition'] === 'false' || $pipelineData['autoTransition'] === false) ? 0 : 1;

            $sqlPipeline = "INSERT INTO vtiger_pipeline (
                 module, name, stage, status, auto_move,
                duration, time_unit, description, create_by, created_at
            ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
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

            $roleMapping = ["all" => "Tất cả"]; 
            $allRoles = Settings_Roles_Record_Model::getAll();

            foreach ($allRoles as $role) {
                $roleId = $role->get('roleid');
                $roleName = $role->get('rolename');
                $roleMapping[$roleId] = $roleName;
            }

            $rolesToInsert = in_array("all", $pipelineData['rolesSelected']) ? array_keys($roleMapping) : $pipelineData['rolesSelected'];

            foreach ($rolesToInsert as $roleId) {
                if ($roleId !== "all") { 
                    $sqlRole = "INSERT INTO vtiger_rolepipeline (roleid, pipelineid) VALUES (?, ?)";
                    $adb->pquery($sqlRole, array($roleId, $newPipelineId));
                }
            }

            foreach ($pipelineData['stagesList'] as $stage) {
                $stageId = intval($stage['id']);
                $isMandatory = ($stage['is_mandatory'] === true || $stage['is_mandatory'] === 'true') ? 1 : 0;

                $sqlStage = "INSERT INTO vtiger_stage (
stageid, pipelineid, name, success_rate, time, time_unit, is_mandatory, color_code, sequence, value, actions, conditions
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $paramsStage = array(
                    $stageId,
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

                    if (isset($stage['permissions']) && is_array($stage['permissions'])) {
                        foreach ($stage['permissions'] as $permission) {
                            $roleId = $permission['role_id'];

                            $rolesToInsert = ($roleId === "all") ? array_keys($roleMapping) : [$roleId];

                            foreach ($rolesToInsert as $rId) {
                                if ($rId !== "all") {
                                    $sqlRoleStage = "INSERT INTO vtiger_rolestage (stageid, roleid) VALUES (?, ?)";
                                    $adb->pquery($sqlRoleStage, array($stageId, $rId));
                                }
                            }
                        }
                    }

            }

            foreach ($pipelineData['stagesList'] as $stage) {
                $stageId = intval($stage['id']);
                if (isset($stage['next_stages']) && is_array($stage['next_stages'])) {
                    foreach ($stage['next_stages'] as $nextStage) {
                        $nextStageId = intval($nextStage['id']);
                        $sqlAllowed = "INSERT INTO vtiger_allowedmoveto (stageid, allowedstageid) VALUES (?, ?)";
                        $adb->pquery($sqlAllowed, array($stageId, $nextStageId));
                    }
                }
            }

            return [
                'success' => true,
                'pipelineId' => $newPipelineId,
                'data' => $pipelineData,
                'params' => $paramsPipeline
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            ];
        }
    }
    // Implemented by The Vi - Updates an existing pipeline and its related data. 
    public static function updatePipeline($pipelineData, $currentUser) {
        global $adb;
        // $userDisplayName = $currentUser->getDisplayName();
        $pipelineId = $pipelineData['id'];
    
        try {
            $adb->startTransaction();
            
            $stageCount = count($pipelineData['stagesList']);
            $status = ($pipelineData['status'] == 'inActive') ? 0 : 1;
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
                
            $paramsUpdatePipeline = array(
                $pipelineData['module'],
                $pipelineData['name'],
                $stageCount,
                $status,
                $autoTransition,
                $pipelineData['time'],
                $pipelineData['timetype'],
                $pipelineData['description'],
                $pipelineId
            );
            $adb->pquery($sqlUpdatePipeline, $paramsUpdatePipeline);
    
            $sqlDeleteRoles = "DELETE FROM vtiger_rolepipeline WHERE pipelineid = ?";
            $adb->pquery($sqlDeleteRoles, array($pipelineId));
    
            foreach ($pipelineData['rolesSelected'] as $roleId) {
                $sqlRole = "INSERT INTO vtiger_rolepipeline (roleid, pipelineid) VALUES (?, ?)";
                $adb->pquery($sqlRole, array($roleId, $pipelineId));
            }
    
            $existingStages = array();

            $sqlExistingStages = "SELECT stageid FROM vtiger_stage WHERE pipelineid = ?";
            $resultStages = $adb->pquery($sqlExistingStages, array($pipelineId));
            while ($row = $adb->fetch_array($resultStages)) {
                $existingStages[] = $row['stageid'];
            }
    
            if (!empty($existingStages)) {
                $sqlDeleteAllowed = "DELETE FROM vtiger_allowedmoveto WHERE stageid IN (" . implode(',', $existingStages) . ")";
                $adb->pquery($sqlDeleteAllowed, array());
    
                $sqlDeleteRoleStage = "DELETE FROM vtiger_rolestage WHERE stageid IN (" . implode(',', $existingStages) . ")";
                $adb->pquery($sqlDeleteRoleStage, array());
            }
    
            $newStageIds = array();
            foreach ($pipelineData['stagesList'] as $stage) {
                $stageId = intval($stage['id']);
                $newStageIds[] = $stageId;
                $isMandatory = ($stage['is_mandatory'] === true || $stage['is_mandatory'] === 'true') ? 1 : 0;
    
                $sqlCheckStage = "SELECT COUNT(*) as count FROM vtiger_stage WHERE stageid = ? AND pipelineid = ?";
                $resultCheck = $adb->pquery($sqlCheckStage, array($stageId, $pipelineId));
                $stageExists = $adb->query_result($resultCheck, 0, 'count') > 0;
    
                if ($stageExists) {

                    $sqlUpdateStage = "UPDATE vtiger_stage SET 
                        name = ?, 
                        success_rate = ?, 
                        time = ?, 
                        time_unit = ?, 
                        is_mandatory = ?, 
                        color_code = ?, 
                        sequence = ?, 
                        value = ?, 
                        actions = ?, 
                        conditions = ?
                        WHERE stageid = ? AND pipelineid = ?";
                        
                    $paramsUpdateStage = array(
                        $stage['name'],
                        $stage['success_rate'],
                        $stage['execution_time']['value'],
                        $stage['execution_time']['unit'],
                        $isMandatory,
                        $stage['color'],
                        $stage['sequence'],
                        $stage['value'],
                        $stage['actions'],
                        $stage['conditions'],
                        $stageId,
                        $pipelineId
                    );
                    $adb->pquery($sqlUpdateStage, $paramsUpdateStage);
                } else {
                    $sqlNewStage = "INSERT INTO vtiger_stage (
                        stageid, pipelineid, name, success_rate, time, time_unit, 
                        is_mandatory, color_code, sequence, value, actions, conditions
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $paramsNewStage = array(
                        $stageId,
                        $pipelineId,
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
                    $adb->pquery($sqlNewStage, $paramsNewStage);
                }
    
                if (isset($stage['permissions']) && is_array($stage['permissions'])) {
                    foreach ($stage['permissions'] as $permission) {
                        $sqlRoleStage = "INSERT INTO vtiger_rolestage (stageid, roleid) VALUES (?, ?)";
                        $adb->pquery($sqlRoleStage, array($stageId, $permission['role_id']));
                    }
                }
    
                if (isset($stage['next_stages']) && is_array($stage['next_stages'])) {
                    foreach ($stage['next_stages'] as $nextStage) {
                        $nextStageId = intval($nextStage['id']);
                        $sqlAllowed = "INSERT INTO vtiger_allowedmoveto (stageid, allowedstageid) VALUES (?, ?)";
                        $adb->pquery($sqlAllowed, array($stageId, $nextStageId));
                    }
                }
            }
    
            $stagesToDelete = array_diff($existingStages, $newStageIds);
            if (!empty($stagesToDelete)) {
                $sqlDeleteStages = "DELETE FROM vtiger_stage WHERE stageid IN (" . implode(',', $stagesToDelete) . ")";
                $adb->pquery($sqlDeleteStages, array());
            }
            $adb->completeTransaction();
    
            return [
                'success' => true,
                'pipelineId' => $pipelineId,
                'message' => 'Pipeline updated successfully'
            ];
    
        } catch (Exception $e) {

            $adb->rollbackTransaction();
            return [
                'success' => false,
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage()
            ];
        }
    }
}