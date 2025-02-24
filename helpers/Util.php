<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Helper methods to work with ShortURLs
 */
class Settings_PipelineConfig_Util_Helper {
    /**
     * Trả về ID dựa vào tên module
     * @param string $moduleName Tên module
     * @return int|null ID của module hoặc null nếu không tìm thấy
     */
    public static function getModuleIdByName($moduleName) {
        // Danh sách các module và ID tương ứng
        $moduleIds = [
            'Leads' => 50,
            'Potentials' => 118,
            'HelpDesk' => 161,
            'Project' => 652
        ];

        // Kiểm tra moduleName có trong danh sách không và trả về ID
        return $moduleIds[$moduleName] ?? null;
    }
}