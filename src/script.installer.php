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

use Alledia\Installer\AbstractScript;
use Joomla\CMS\Installer\InstallerAdapter;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps

defined('_JEXEC') or die();

require_once 'library/Installer/include.php';

class mod_ostimerInstallerScript extends AbstractScript
{
    /**
     * @inheritDoc
     */
    protected function customPostFlight(string $type, InstallerAdapter $parent): void
    {
        if ($type == 'update') {
            $this->convertLegacyDates();
        }
    }

    /**
     * Convert legacy date fields to the new one
     *
     * @return void
     * @since v2.8.2
     */
    protected function convertLegacyDates(): void
    {
        $db      = $this->dbo;
        $query   = $db->getQuery(true)
            ->select([
                'id',
                'params'
            ])
            ->from('#__modules')
            ->where('module = ' . $db->quote('mod_ostimer'));
        $modules = $db->setQuery($query)->loadObjectList();

        if (!empty($modules)) {
            foreach ($modules as $module) {
                $params = new JRegistry($module->params);

                $evDay   = $params->get('ev_d');
                $evMonth = $params->get('ev_m');
                $evYear  = $params->get('ev_y');

                if ($evDay !== null) {
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
