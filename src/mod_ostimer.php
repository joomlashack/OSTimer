<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2019 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSTimer.
 *
 * OSTimer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSTimer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSTimer.  If not, see <http://www.gnu.org/licenses/>.
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
