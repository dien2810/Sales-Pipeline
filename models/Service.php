<?php
require_once('include/SMSer.php');
require_once('include/Mailer.php');
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
class PipelineConfig_Service_Model {

    // Implemented by The Vi to send repeat notifications
    public static function sendNotications() {
          self::sendRepeatNotifications();
    }

    /**
     * Send repeat notifications based on notification_repeat table
     * Duplicates existing notifications at specified times
     */
    public static function sendRepeatNotifications() {
        $db = PearDatabase::getInstance();
        $log = LoggerManager::getLogger('PLATFORM');
        $log->info('[CRON] Started sendRepeatNotifications');
        
        $currentTime = date('Y-m-d H:i');
        
        // Join query to reduce number of database calls
        $query = "SELECT n.* 
                FROM vtiger_notification_repeat r
                INNER JOIN vtiger_notifications n 
                    ON r.notification_id = n.id
                WHERE 
                    DATE_FORMAT(r.notification_send_time, '%Y-%m-%d %H:%i') = ?
                    AND n.read = 0";
        
        $result = $db->pquery($query, array($currentTime));
        
        if ($result && $db->num_rows($result) > 0) {
            while ($record = $db->fetch_array($result)) {
                try {
                    // Execute insert and get new ID in single transaction
                    $db->startTransaction();
                    
                    $insertParams = array(
                        $record['receiver_id'],
                        $record['category'],
                        $record['image'],
                        $record['related_record_id'],
                        $record['related_record_name'],
                        $record['related_module_name'], 
                        date('Y-m-d H:i:s'),
                        0, // Default unread
                        $record['extra_data']
                    );
                    
                    $db->pquery(
                        "INSERT INTO vtiger_notifications 
                        (receiver_id, category, image, related_record_id, related_record_name, 
                         related_module_name, created_time, `read`, extra_data) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                        $insertParams
                    );
                    
                    $newId = $db->getLastInsertID();
                    $db->completeTransaction();
                    
                    $log->info("[CRON] Duplicated notification {$record['id']} as new id $newId");
                } catch (Exception $e) {
                    $db->rollbackTransaction();
                    $log->error("Error duplicating notification: " . $e->getMessage());
                }
            }
        } else {
            $log->info("[CRON] No notifications to repeat at $currentTime");
        }
        
        $log->info('[CRON] Finished sendRepeatNotifications');
    }

    /**
     * Send notifications for overdue pipeline stage transitions
     * Notifies relevant users when a stage transition is past due
     */
    public static function sendOverdueStageNotifications() {
        // Empty function for overdue stage notifications
        // TODO: Implement notification logic for overdue pipeline stages
    }

    /**
     * Send notifications for upcoming pipeline stage deadlines
     * Alerts users about stages that are approaching their deadline
     */
    public static function sendUpcomingStageNotifications() {
        // Empty function for upcoming stage deadline notifications
        // TODO: Implement notification logic for upcoming stage deadlines
    }

    /**
     * Send notifications when conditions are met for stage transition
     * Notifies users when all requirements are fulfilled to move to next stage
     */
    public static function sendStageReadyNotifications() {
        // Empty function for stage transition readiness notifications
        // TODO: Implement notification logic for stage transition readiness
    }

    // Add by Dien Nguyen on 2025-03-11 to create cron for pipeline action
    public static function processPipelineActions(){
        $adb = PearDatabase::getInstance();
        require_once 'modules/Settings/PipelineConfig/models/PipelineScheduler.php';
        require_once 'modules/Settings/PipelineConfig/models/ActionQueue.php';
        $pipelineScheduler = new PipelineScheduler();
        $pipelineScheduler->queuePipelineActions();

        $util = new VTWorkflowUtils();
        $adminUser = $util->adminUser();
        $actionQueue = new ActionQueue();
        // get all actions that do_after is less than current time
        $readyActions = $actionQueue->getReadyActions();
        // echo "c".count($readyActions);
        foreach($readyActions as $actionDetails){
            list($entity_id, $do_after, $action_contents) = $actionDetails;
            $entity = VTEntityCache::getCachedEntity($entity_id);
            if(!$entity) {
                $entity = new VTWorkflowEntity($adminUser, $entity_id);
                if (!$entity){
                    vtws_retrieve($entity_id, $adminUser);
                }
            }
            // Get the record id from entity_id
            $entityId = $entity->getId();
            $parts = explode('x', $entityId);
            $recordId = end($parts);
            
            try {
                PipelineAction::processActions($action_contents, $recordId, $entity->getModuleName());
            }
            catch (Throwable $ex) {
                echo '[vtRunTaskJob::doTask] Error: ' . $ex->getMessage();
            }
        }
    }
}
?>
