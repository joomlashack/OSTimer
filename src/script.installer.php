<?php
/**
 * @package   OSTimer
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

require_once 'library/Installer/include.php';

use Alledia\Installer\AbstractScript;

/**
 * Custom installer script
 */
class Mod_OSTimerInstallerScript extends AbstractScript
{
    /**
     * @param string                     $type
     * @param JInstallerAdapterComponent $parent
     *
     * @return void
     */
    public function postFlight($type, $parent)
    {
        parent::postFlight($type, $parent);

        // Convert legacy date fields to the new one
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select(
                array(
                    'id',
                    'params'
                )
            )
            ->from('#__modules')
            ->where('module = ' . $db->quote('mod_ostimer'));
        $modules = $db->setQuery($query)->loadObjectList();

        if (!empty($modules)) {
            foreach ($modules as $module) {
                $params = new JRegistry($module->params);

                $evDay   = $params->get('ev_d', null);
                $evMonth = $params->get('ev_m', null);
                $evYear  = $params->get('ev_y', null);

                if (!is_null($evDay)) {
                    $newDate = sprintf('%s-%s-%s', $evDay, $evMonth, $evYear);
                    $params->set('ev_date', $newDate);

                    $query = $db->getQuery(true)
                        ->update('#__modules')
                        ->set('params = '  . $db->quote($params->toString()))
                        ->where('id = ' . $module->id);
                    $db->setQuery($query)->execute();
                }
            }
        }
    }
}
