<?php
require_once('include/SMSer.php');
require_once('include/Mailer.php');
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
class PipelineConfig_Service_Model {

    // Implemented by The Vi to send repeat notifications
    public static function sendNotications() {
        $db = PearDatabase::getInstance();
        $log = LoggerManager::getLogger('PLATFORM');
        $log->info('[CRON] Started sendNotications');
        $currentTime = date('Y-m-d H:i');
        $queryRepeat = "SELECT * FROM vtiger_notification_repeat 
                        WHERE DATE_FORMAT(notification_send_time, '%Y-%m-%d %H:%i') = ?";
        $resultRepeat = $db->pquery($queryRepeat, array($currentTime));
        if($resultRepeat && $db->num_rows($resultRepeat) > 0) {
            while($repeatRecord = $db->fetch_array($resultRepeat)) {
                $notificationId = $repeatRecord['notification_id'];
                $queryNotification = "SELECT * FROM vtiger_notifications 
                                      WHERE id = ? AND `read` = 0";
                $resultNotification = $db->pquery($queryNotification, array($notificationId));
                if($resultNotification && $db->num_rows($resultNotification) > 0) {
                    while($notificationRecord = $db->fetch_array($resultNotification)) {
                
                        $receiver_id         = $notificationRecord['receiver_id'];
                        $category            = $notificationRecord['category'];
                        $image               = $notificationRecord['image'];
                        $related_record_id   = $notificationRecord['related_record_id'];
                        $related_record_name = $notificationRecord['related_record_name'];
                        $related_module_name = $notificationRecord['related_module_name'];
                        $created_time        = date('Y-m-d H:i:s'); 
                        $read                = $notificationRecord['read']; 
                        $extra_data          = $notificationRecord['extra_data'];
                        $queryInsert = "INSERT INTO vtiger_notifications 
                            (receiver_id, category, image, related_record_id, related_record_name, related_module_name, created_time, `read`, extra_data) 
                            VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $db->pquery($queryInsert, array(
                            $receiver_id,
                            $category,
                            $image,
                            $related_record_id,
                            $related_record_name,
                            $related_module_name,
                            $created_time,
                            $read,
                            $extra_data
                        ));

                        $log->info("[CRON] Duplicated notification record (notification_id: $notificationId) as new id $newId");
                    }
                }
            }
        } else {
            $log->info("[CRON] No notification repeat record found for current time: $currentTime");
        }
        $log->info('[CRON] Finished sendNotications');
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
