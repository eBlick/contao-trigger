<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\Component\ComponentManager;
use NotificationCenter\Model\Notification;

class NotificationAction
{
    public function __construct(private ComponentManager $componentManager, private Connection $connection)
    {
    }

    public function onGetTokenList(DataContainer $dc): string
    {
        // format data list as simple tokens
        $tokens = '##trigger_id##, ##trigger_title##, ##trigger_startTime##';

        // display individual condition data
        $conditionType = $this->connection
            ->executeQuery('SELECT condition_type FROM tl_eblick_trigger WHERE id = ?', [$dc->id])
            ->fetchOne()
        ;

        if ($conditionType && $condition = $this->componentManager->getCondition($conditionType)) {
            $tokens .= ', '.implode(
                ', ',
                array_map(
                    static fn ($v) => '##data_'.$v.'##',
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

    public function getNotificationChoices(): array
    {
        if (!class_exists(Notification::class)) {
            return [];
        }

        return $this->connection
            ->executeQuery(
                "SELECT id, title FROM tl_nc_notification WHERE type='eblick_notification_action' ORDER BY title"
            )
            ->fetchAllKeyValue()
        ;
    }
}
