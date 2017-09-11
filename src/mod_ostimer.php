<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Alledia\OSTimer\AbstractModule;
use Joomla\Registry\Registry;

require_once 'include.php';

/**
 * @var object           $module
 * @var string[]         $attribs
 * @var string           $chrome
 * @var JApplicationSite $app
 * @var string           $scope
 * @var Registry         $params
 * @var string           $template
 * @var string           $path
 * @var JLanguage        $lang
 * @var string           $coreLanguageDirectory
 * @var string           $extensionLanguageDirectory
 * @var string[]         $langPaths
 * @var string           $content
 */

if ($modOSTimer = AbstractModule::getInstance($module)) {
    $modOSTimer->init();
} else {
    JFactory::getApplication()->enqueueMessage('MOD_OSTIMER_ERROR_INSTANTIATION', 'error');
}
