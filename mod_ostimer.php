<?php
/**
 * @package   mod_ostimer
 * @contact   www.ostraining.com, support@ostraining.com
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

// Include the syndicate functions only once
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');

$list = modOSTimerHelper::getList($params);
require(JModuleHelper::getLayoutPath('mod_ostimer'));