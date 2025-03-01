<?php
require_once('include/SMSer.php');
require_once('include/Mailer.php');
class PipelineConfig_Service_Model {
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
                        //     $newId,
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
}
?>
