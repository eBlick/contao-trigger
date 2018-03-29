<?php

declare(strict_types=1);

/*
 * Trigger Framework Bundle for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2018, eBlick Medienberatung
 * @license    LGPL-3.0+
 * @link       https://github.com/eBlick/contao-trigger
 *
 * @author     Moritz Vondano
 */

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\Component\ComponentManager;
use NotificationCenter\Model\Notification;

class NotificationAction
{
    /** @var ComponentManager */
    private $componentManager;

    /** @var Connection */
    private $database;

    /**
     * TriggerListener constructor.
     *
     * @param ComponentManager $componentManager
     * @param Connection       $database
     */
    public function __construct(ComponentManager $componentManager, Connection $database)
    {
        $this->componentManager = $componentManager;
        $this->database         = $database;
    }

    /**
     * @param DataContainer $dc
     *
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onGetTokenList(DataContainer $dc): string
    {
        // format data list as as simple tokens
        $tokens = '##trigger_id##, ##trigger_title##, ##trigger_startTime##';

        // display individual condition data
        $conditionType = $this->database
            ->executeQuery('SELECT condition_type FROM tl_eblick_trigger WHERE id = ?', [$dc->id])
            ->fetch(\PDO::FETCH_COLUMN);

        if ($conditionType && $condition = $this->componentManager->getCondition($conditionType)) {
            $tokens .= ', '. implode(
                    ', ',
                    array_map(
                        function ($v) {
                            return '##data_' . $v . '##';
                        },
                        array_keys($condition->getDataPrototype((int) $dc->id))
                    )
                );
        }

        return sprintf(
            '<div class="widget clr"><h3>%s</h3><span style="display:inline-block; margin-top: 5px; color: #999;">%s</span></div>',
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['action_notification_tokens'],
            $tokens
        );
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getNotificationChoices(): array
    {
        if(!class_exists(Notification::class)) {
            return [];
        }

        return $this->database
            ->executeQuery(
                "SELECT id, title FROM tl_nc_notification WHERE type='eblick_notification_action' ORDER BY title"
            )
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

}