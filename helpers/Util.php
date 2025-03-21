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

    // public static function calculateOverdueDays($recordId, $moduleName) {
    //     global $adb;
        
    //     // Get entity table from module name
    //     $moduleInstance = CRMEntity::getInstance($moduleName);
    //     $tableMap = $moduleInstance->table_name;
        
    //     // Get record data
    //     $result = $adb->pquery(
    //         "SELECT stage_changing_time, stageid 
    //         FROM $tableMap 
    //         WHERE {$moduleInstance->table_index} = ?",
    //         array($recordId)
    //     );
        
    //     if ($adb->num_rows($result) == 0) {
    //         return 0;
    //     }
    
    //     $recordData = $adb->fetch_array($result);
    //     $stageChangingTime = $recordData['stage_changing_time'];
    //     $stageId = $recordData['stageid'];
        
    //     if (empty($stageChangingTime) || empty($stageId)) {
    //         return 0;
    //     }
    
    //     // Get stage information
    //     $stageResult = $adb->pquery(
    //         "SELECT time, time_unit 
    //         FROM vtiger_stage 
    //         WHERE stageid = ?",
    //         array($stageId)
    //     );
        
    //     if ($adb->num_rows($stageResult) == 0) {
    //         return 0;
    //     }
    
    //     $stageData = $adb->fetch_array($stageResult);
    //     $stageTime = $stageData['time'];
    //     $timeUnit = $stageData['time_unit'];
    
    //     // Calculate start time (end of day)
    //     $startDate = date('Y-m-d', $stageChangingTime);
    //     $startTimestamp = strtotime($startDate . ' 23:59:59');
    
    //     // Calculate due time based on stage time and unit
    //     $daysToAdd = 0;
    //     switch ($timeUnit) {
    //         case 'Day':
    //             $daysToAdd = $stageTime;
    //             break;
    //         case 'Month':
    //             $daysToAdd = $stageTime * 30;
    //             break;
    //         case 'Year':
    //             $daysToAdd = $stageTime * 365;
    //             break;
    //         default:
    //             $daysToAdd = 0;
    //     }
    
    //     // Calculate due timestamp (end of due day)
    //     $dueDate = date('Y-m-d', strtotime("+$daysToAdd days", $startTimestamp));
    //     $dueTimestamp = strtotime($dueDate . ' 23:59:59');
        
    //     // Calculate current timestamp
    //     $currentTimestamp = time();
    
    //     // Calculate overdue days
    //     $overdueDays = floor(($currentTimestamp - $dueTimestamp) / 86400);
        
    //     return $overdueDays;
    // }
    public static function calculateOverdueDays($recordId, $moduleName) {
        global $adb;

        $moduleInstance = CRMEntity::getInstance($moduleName);
        $tableMap = $moduleInstance->table_name;
    
      
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
    
        $serverTimezone = new DateTimeZone(date_default_timezone_get());
    
    
        $stageChangingDateTime = new DateTime('@' . $stageChangingTime);
        $stageChangingDateTime->setTimezone($serverTimezone);
        $stageChangingDateTime->setTime(23, 59, 59);
    
     
        $dueDateTime = clone $stageChangingDateTime;
        $dueDateTime->modify("+{$stageDays} days");
    
      
        $currentDateTime = new DateTime('now', $serverTimezone);
        $currentDateTime->setTime(23, 59, 59);
    
        $dueTimestamp = $dueDateTime->getTimestamp();
        $currentTimestamp = $currentDateTime->getTimestamp();
    
        $overdueDays = (int) floor(($currentTimestamp - $dueTimestamp) / 86400);
    
        return $overdueDays;
    }
}