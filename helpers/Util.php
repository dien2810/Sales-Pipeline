<?php

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
        // List of modules and corresponding IDs
        $moduleIds = [
            'Leads' => 50,
            'Potentials' => 118,
            'HelpDesk' => 161,
            'Project' => 652
        ];

        // Check if moduleName is in the list and return ID
        return $moduleIds[$moduleName] ?? null;
    }
}