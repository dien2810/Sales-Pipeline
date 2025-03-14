<?php
// Add by Dien Nguyen on 2025-03-11 to queue pipeline action
require_once('include/events/SqlResultIterator.inc');
class ActionQueue{
    public function queueAction($action, $entityId, $when=0){
        global $adb;
        
        $actionContents = json_encode($action);
        $query = "SELECT COUNT(*) as count FROM vtiger_pipelineaction_queue WHERE entity_id = ? AND do_after = ? AND action_contents = ?";
        $params = array($entityId, $when, $actionContents);
        $result = $adb->pquery($query, $params);
        $row = $adb->fetchByAssoc($result);

        if ($row['count'] == 0 && $entityId) {
            $adb->pquery('INSERT INTO vtiger_pipelineaction_queue(entity_id, do_after, action_contents) VALUES(?, ?, ?)', array($entityId, $when, $actionContents));
        }
        return true;
    }
    
    /**
     * Get a list of action/entityId pairs ready for execution.
     *
     * The method fetches action/entity id where the when timestamp
     * is less than the current time when the method was called.
     */
    public function getReadyActions(){
        global $adb;
        $time = time();
        $result = $adb->pquery('SELECT entity_id, do_after, action_contents FROM vtiger_pipelineaction_queue WHERE do_after<?', array($time));
        $it =  new SqlResultIterator($adb, $result);
        $arr = array();
        foreach($it as $row){
            if($this->checkEntityExists($row->entity_id)) {
                $arr[]=array($row->entity_id, $row->do_after, $row->action_contents);
            }
        }
        $adb->pquery("delete from vtiger_pipelineaction_queue where do_after<?", array($time));
        return $arr;
    }

    public function checkEntityExists($id) {
		$idParts = explode('x', $id);
		$recordId = $idParts[1];
		$status = Vtiger_Util_Helper::checkRecordExistance($recordId);
		if ($status == 0) {
			$db = PearDatabase::getInstance();
			$webServiceObject = VtigerWebserviceObject::fromId($db, $idParts[0]);
			if ($webServiceObject->getEntityName() == 'Leads' && isLeadConverted($recordId)) {
				return false;
			}
			return true;
		}
		return false;
	}
}