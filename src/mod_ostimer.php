<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Alledia\OSTimer\Free\Joomla\Module;

require_once 'include.php';

$modOSTimer = Module::getInstance(null, $module);
$modOSTimer->init();
