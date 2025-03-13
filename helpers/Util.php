<?php

/**
 * Helper methods to work with ShortURLs
 */
class Settings_PipelineConfig_Util_Helper {
   /**
     * Returns the ID based on the module name
     * @param string $moduleName Module name
     * @return int|null Module ID or null if not found
     */
    public static function getModuleIdByName($moduleName) {

        $moduleIds = [
            'Leads' => 50,
            'Potentials' => 118,
            'HelpDesk' => 161,
            'Project' => 652
        ];

        return $moduleIds[$moduleName] ?? null;
    }

    //Implement by The Vi to calculate overdue days
    public static function calculateOverdueDays($recordId, $moduleName) {
        global $adb;
        
        // Get entity table from module name
        $moduleInstance = CRMEntity::getInstance($moduleName);
        $tableMap = $moduleInstance->table_name;
        
        // Get record data
        $result = $adb->pquery(
            "SELECT stage_changing_time, stageid 
            FROM $tableMap 
            WHERE {$moduleInstance->table_index} = ?",
            array($recordId)
        );
        
        if ($adb->num_rows($result) == 0) {
            return 0;
        }

        $recordData = $adb->fetch_array($result);
        $stageChangingTime = $recordData['stage_changing_time'];
        $stageId = $recordData['stageid'];
        
        if (empty($stageChangingTime) || empty($stageId)) {
            return 0;
        }

        // Get stage information
        $stageResult = $adb->pquery(
            "SELECT time, time_unit 
            FROM vtiger_stage 
            WHERE stageid = ?",
            array($stageId)
        );
        
        if ($adb->num_rows($stageResult) == 0) {
            return 0;
        }

        $stageData = $adb->fetch_array($stageResult);
        $stageTime = $stageData['time'];
        $timeUnit = $stageData['time_unit'];

        // Convert stage time to days
        $stageDays = 0;
        switch (strtolower($timeUnit)) {
            case 'day':
                $stageDays = $stageTime;
                break;
            case 'month':
                $stageDays = $stageTime * 30;
                break;
            case 'year':
                $stageDays = $stageTime * 365;
                break;
            default:
                return 0;
        }

        // Convert stage_changing_time to timestamp if it's not already
        $stageChangingTimestamp = $stageChangingTime;
        
        // Calculate deadline timestamp
        $deadlineTimestamp = $stageChangingTimestamp + ($stageDays * 86400);
        
      // Calculate current timestamp
        $currentTimestamp = time();

        // Calculate overdue days as an integer (can be negative)
        $overdueDays = floor(($currentTimestamp - $deadlineTimestamp) / 86400);

        
        return  $overdueDays;
    }

}