<?php

/**
 * Core include all mandaotry classes and other php dependencies
 *
 */

// Core Classes
require_once './logs/logs-manager.sclass.php';
require_once './logs/logger.class.php';
require_once './settings/settings-manager.sclass.php';

// Database Management
require_once './database/database-manager.sclass.php';

// Externals classes
require_once './external/CSVImporter.class.php';

require_once './application.class.php';

?>
