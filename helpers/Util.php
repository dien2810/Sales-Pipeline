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
}