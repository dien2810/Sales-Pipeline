<?php
/*
	Edit_Model
	Author: The Vi
	Date: 22/1/2025
	Purpose: Provide utility functions for managing pipeline edit
*/
class Settings_PipelineConfig_Edit_Model extends Vtiger_Base_Model {

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
    public static function deleteStagePipeline($idStageDelete, $idStageReplace, $module) {
        $adb = PearDatabase::getInstance();

        // Bắt đầu transaction nếu cần đảm bảo tính toàn vẹn
        $adb->startTransaction();

        try {
            // 1. Xóa tất cả record trong vtiger_rolestage với stageid = $idStageDelete
            $query1 = "DELETE FROM vtiger_rolestage WHERE stageid = ?";
            $adb->pquery($query1, array($idStageDelete));

            // 2. Xóa tất cả record trong vtiger_allowedmoveto với stageid hoặc allowedstageid bằng $idStageDelete
            $query2 = "DELETE FROM vtiger_allowedmoveto WHERE stageid = ? OR allowedstageid = ?";
            $adb->pquery($query2, array($idStageDelete, $idStageDelete));

            // 3. Thực hiện thay thế các record trong vtiger_potential nếu module là 'Potentials'
            if (strtolower($module) === 'potentials') {
                // Lấy thông tin của bước thay thế: tên (name) và giá trị (value) từ bảng vtiger_stage
                $queryReplace = "SELECT name, value FROM vtiger_stage WHERE stageid = ?";
                $resultReplace = $adb->pquery($queryReplace, array($idStageReplace));
                if ($adb->num_rows($resultReplace) > 0) {
                    $repStageName = $adb->query_result($resultReplace, 0, 'name');
                    $repSalesStage = $adb->query_result($resultReplace, 0, 'value');
                    
                    // Cập nhật bảng vtiger_potential thay thế các record có stageid = $idStageDelete
                    $updatePotential = "UPDATE vtiger_potential 
                        SET stageid = ?, stagename = ?, sales_stage = ? 
                        WHERE stageid = ?";
                    $adb->pquery($updatePotential, array($idStageReplace, $repStageName, $repSalesStage, $idStageDelete));
                } else {
                    // Nếu không tìm thấy thông tin stage thay thế, rollback transaction
                    $adb->rollbackTransaction();
                    return false;
                }
            }

            // 4. Xóa bước khỏi bảng vtiger_stage
            $query4 = "DELETE FROM vtiger_stage WHERE stageid = ?";
            $adb->pquery($query4, array($idStageDelete));

            // Commit transaction sau khi thực hiện xong
            $adb->completeTransaction();

            return true;
        } catch (Exception $ex) {
            // Rollback trong trường hợp có lỗi
            $adb->rollbackTransaction();
            return false;
        }
    }

    public static function savePipeline($pipelineData, $currentUser) {
        global $adb;
        $userDisplayName = $currentUser->getDisplayName();

        try {
            $stageCount = count($pipelineData['stagesList']);
            $status = ($pipelineData['status'] == 'inActive') ? 0 : 1;
            $autoTransition = ($pipelineData['autoTransition'] === 'false' || $pipelineData['autoTransition'] === false) ? 0 : 1;

            // Lấy pipelineid mới
            $sql = "SELECT MAX(pipelineid) as max_id FROM vtiger_pipeline";
            $result = $adb->pquery($sql, array());
            $newPipelineId = $adb->query_result($result, 0, 'max_id') + 1;

            // Lưu pipeline vào bảng vtiger_pipeline
            $sqlPipeline = "INSERT INTO vtiger_pipeline (
                pipelineid, module, name, stage, status, auto_move,
                duration, time_unit, description, create_by, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $paramsPipeline = array(
                $newPipelineId,
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
            // Định nghĩa roleMapping
            $roleMapping = [
                "all" => "Tất cả",
                "H1" => "Organization",
                "H10" => "Sales Admin",
                "H11" => "CS Manager",
                "H12" => "Support",
                "H2" => "CEO",
                "H3" => "Vice President",
                "H4" => "Sales Manager",
                "H5" => "Sales Person",
                "H6" => "Marketing Manager",
                "H7" => "Marketer",
                "H8" => "Chief Accountant",
                "H9" => "Accountant",
            ];

            // Kiểm tra nếu rolesSelected chứa "all"
            $rolesToInsert = in_array("all", $pipelineData['rolesSelected']) ? array_keys($roleMapping) : $pipelineData['rolesSelected'];

            // Lưu vai trò vào vtiger_rolepipeline
            foreach ($rolesToInsert as $roleId) {
                if ($roleId !== "all") { // Bỏ qua key "all"
                    $sqlRole = "INSERT INTO vtiger_rolepipeline (roleid, pipelineid) VALUES (?, ?)";
                    $adb->pquery($sqlRole, array($roleId, $newPipelineId));
                }
            }

            // Lưu danh sách stages
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

                // Lưu quyền stage vào vtiger_rolestage
                    if (isset($stage['permissions']) && is_array($stage['permissions'])) {
                        foreach ($stage['permissions'] as $permission) {
                            $roleId = $permission['role_id'];

                            // Nếu quyền là "all", thêm tất cả role từ roleMapping
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

            // Lưu danh sách bước tiếp theo vào vtiger_allowedmoveto
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
    public static function updatePipeline($pipelineData, $currentUser) {
        global $adb;
        $userDisplayName = $currentUser->getDisplayName();
        $pipelineId = $pipelineData['id'];
    
        try {
            $adb->startTransaction();
            
            $stageCount = count($pipelineData['stagesList']);
            $status = ($pipelineData['status'] == 'inActive') ? 0 : 1;
            $autoTransition = ($pipelineData['autoTransition'] === 'false' || $pipelineData['autoTransition'] === false) ? 0 : 1;
    
            // Update pipeline information
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
    
            // Update role assignments for pipeline
            $sqlDeleteRoles = "DELETE FROM vtiger_rolepipeline WHERE pipelineid = ?";
            $adb->pquery($sqlDeleteRoles, array($pipelineId));
    
            foreach ($pipelineData['rolesSelected'] as $roleId) {
                $sqlRole = "INSERT INTO vtiger_rolepipeline (roleid, pipelineid) VALUES (?, ?)";
                $adb->pquery($sqlRole, array($roleId, $pipelineId));
            }
    
            // Get existing stages
            $existingStages = array();
            $sqlExistingStages = "SELECT stageid FROM vtiger_stage WHERE pipelineid = ?";
            $resultStages = $adb->pquery($sqlExistingStages, array($pipelineId));
            while ($row = $adb->fetch_array($resultStages)) {
                $existingStages[] = $row['stageid'];
            }
    
            // Clear all existing relationships for this pipeline's stages
            if (!empty($existingStages)) {
                // Delete existing allowed move to relationships
                $sqlDeleteAllowed = "DELETE FROM vtiger_allowedmoveto WHERE stageid IN (" . implode(',', $existingStages) . ")";
                $adb->pquery($sqlDeleteAllowed, array());
    
                // Delete existing role stage relationships
                $sqlDeleteRoleStage = "DELETE FROM vtiger_rolestage WHERE stageid IN (" . implode(',', $existingStages) . ")";
                $adb->pquery($sqlDeleteRoleStage, array());
            }
    
            // Process stages
            $newStageIds = array();
            foreach ($pipelineData['stagesList'] as $stage) {
                $stageId = intval($stage['id']);
                $newStageIds[] = $stageId;
                $isMandatory = ($stage['is_mandatory'] === true || $stage['is_mandatory'] === 'true') ? 1 : 0;
    
                // Check if stage exists
                $sqlCheckStage = "SELECT COUNT(*) as count FROM vtiger_stage WHERE stageid = ? AND pipelineid = ?";
                $resultCheck = $adb->pquery($sqlCheckStage, array($stageId, $pipelineId));
                $stageExists = $adb->query_result($resultCheck, 0, 'count') > 0;
    
                if ($stageExists) {
                    // Update existing stage
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
                    // Insert new stage
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
    
                // Insert role-stage permissions
                if (isset($stage['permissions']) && is_array($stage['permissions'])) {
                    foreach ($stage['permissions'] as $permission) {
                        $sqlRoleStage = "INSERT INTO vtiger_rolestage (stageid, roleid) VALUES (?, ?)";
                        $adb->pquery($sqlRoleStage, array($stageId, $permission['role_id']));
                    }
                }
    
                // Insert next stages relationships
                if (isset($stage['next_stages']) && is_array($stage['next_stages'])) {
                    foreach ($stage['next_stages'] as $nextStage) {
                        $nextStageId = intval($nextStage['id']);
                        $sqlAllowed = "INSERT INTO vtiger_allowedmoveto (stageid, allowedstageid) VALUES (?, ?)";
                        $adb->pquery($sqlAllowed, array($stageId, $nextStageId));
                    }
                }
            }
    
            // Remove stages that no longer exist
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