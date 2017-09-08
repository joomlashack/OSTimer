<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Alledia\Framework\AutoLoader;

define('OSTIMER_MODULE_PATH', __DIR__);

// Alledia Framework
if (!defined('ALLEDIA_FRAMEWORK_LOADED')) {
    $allediaFrameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';

    if (file_exists($allediaFrameworkPath)) {
        require_once $allediaFrameworkPath;
    }
}

if (defined('ALLEDIA_FRAMEWORK_LOADED')) {
    AutoLoader::register('Alledia\OSTimer', JPATH_SITE . '/modules/mod_ostimer/library');

} else {
    JFactory::getApplication()
        ->enqueueMessage('[OSTimer] Alledia framework not found', 'error');
}
