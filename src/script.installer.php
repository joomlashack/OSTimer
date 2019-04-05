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

require_once 'library/Installer/include.php';

use Alledia\Installer\AbstractScript;

/**
 * Custom installer script
 */
class Mod_OSTimerInstallerScript extends AbstractScript
{
    /**
     * @param string            $type
     * @param JInstallerAdapter $parent
     *
     * @return void
     * @throws Exception
     */
    public function postFlight($type, $parent)
    {
        parent::postFlight($type, $parent);

        switch ($type) {
            case 'update':
                $this->convertLegacyDates();
                break;
        }
    }

    /**
     * Convert legacy date fields to the new one
     *
     * @return void
     */
    protected function convertLegacyDates()
    {
        $db      = JFactory::getDbo();
        $query   = $db->getQuery(true)
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
                        ->set('params = ' . $db->quote($params->toString()))
                        ->where('id = ' . $module->id);
                    $db->setQuery($query)->execute();
                }
            }
        }
    }
}
