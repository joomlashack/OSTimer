<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2025 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSTimer.  If not, see <https://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

use Alledia\Framework\AutoLoader;
use Joomla\CMS\Factory;

try {
    $frameworkPath = JPATH_SITE . '/libraries/allediaframework/include.php';
    if (!(is_file($frameworkPath) && include $frameworkPath)) {
        $app = Factory::getApplication();

        if ($app->isClient('administrator')) {
            $app->enqueueMessage('[OSTimer] Joomlashack framework not found', 'error');
        }

        return false;
    }

    if (defined('ALLEDIA_FRAMEWORK_LOADED') && !defined('OSTIMER_LOADED')) {
        AutoLoader::register('Alledia\OSTimer', __DIR__ . '/library/Alledia/OSTimer');

        define('OSTIMER_LOADED', true);
    }

} catch (Throwable $error) {
    Factory::getApplication()->enqueueMessage('[OSTimer] Unable to initialize: ' . $error->getMessage(), 'error');

    return false;
}

return defined('ALLEDIA_FRAMEWORK_LOADED') && defined('OSTIMER_LOADED');
