<?php

declare(strict_types=1);

/*
 * @copyright LUMAS Consulting
 * @license   LGPL-3.0+
 * @link      https://github.com/lumas-consulting/contao-trigger
 */

namespace EBlick\ContaoTrigger\NotificationCenter;

use Terminal42\NotificationCenterBundle\NotificationType\NotificationTypeInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\AnythingTokenDefinition;
use Terminal42\NotificationCenterBundle\Token\Definition\Factory\TokenDefinitionFactoryInterface;
use Terminal42\NotificationCenterBundle\Token\Definition\TextTokenDefinition;

class TriggerNotificationType implements NotificationTypeInterface
{
    public const string NAME = 'eblick_trigger';

    public function __construct(private readonly TokenDefinitionFactoryInterface $factory)
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTokenDefinitions(): array
    {
        return [
            $this->factory->create(AnythingTokenDefinition::class, 'data_*', 'trigger_data_*'),
            $this->factory->create(TextTokenDefinition::class, 'trigger_id', 'trigger_id'),
            $this->factory->create(TextTokenDefinition::class, 'trigger_title', 'trigger_title'),
            $this->factory->create(TextTokenDefinition::class, 'trigger_startTime', 'trigger_startTime'),
        ];
    }
}
