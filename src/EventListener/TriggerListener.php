<?php

declare(strict_types=1);

/*
 * @copyright eBlick Medienberatung
 * @license   LGPL-3.0+
 * @link      https://github.com/eBlick/contao-trigger
 */

namespace EBlick\ContaoTrigger\EventListener;

use Contao\CoreBundle\Monolog\ContaoContext;
use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\Execution\ExecutionContextFactory;
use EBlick\ContaoTrigger\Execution\ExecutionException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Stopwatch\Stopwatch;

class TriggerListener
{
    private Stopwatch $executionTimer;

    public function __construct(private ComponentManager $componentManager, private Connection $connection, private LoggerInterface $logger, private ExecutionContextFactory $executionContextFactory, private RequestStack $requestStack)
    {
        $this->executionTimer = new Stopwatch();
    }

    /**
     * Run all triggers - this should be called periodically to make time constraints work.
     */
    public function onExecute(): void
    {
        // temporary fix to circumvent easy themes bug
        if ('BE' === TL_MODE && null === $this->requestStack->getCurrentRequest()) {
            return;
        }

        $triggers = $this->connection
            ->executeQuery(
                'SELECT * FROM tl_eblick_trigger '.
                'WHERE enabled = 1 && error IS NULL'
            )
            ->fetchAllAssociative()
        ;

        if (!$triggers) {
            return;
        }

        $factory = new LockFactory(new FlockStore());
        $lock = $factory->createLock('trigger-execution', 60);

        $lock->acquire();

        // execute triggers
        try {
            foreach ($triggers as $trigger) {
                $this->execute((object) $trigger);
                $lock->refresh();
            }
        } finally {
            $lock->release();
        }
    }

    /**
     * Simulate a trigger and write logs without executing actions.
     */
    public function onSimulate(int $triggerId): void
    {
        $trigger = $this->connection
            ->executeQuery(
                'SELECT * FROM tl_eblick_trigger WHERE id = ?',
                [$triggerId]
            )
            ->fetchAssociative()
        ;

        $this->execute((object) $trigger, true);
    }

    /**
     * Execute a single trigger.
     */
    private function execute(\stdClass $trigger, bool $simulated = false): void
    {
        $condition = $this->componentManager->getCondition($trigger->condition_type);
        $action = $this->componentManager->getAction($trigger->action_type);

        if (!$condition || !$action) {
            return;
        }

        // setup execution data environment
        $this->executionTimer->reset();
        $this->executionTimer->start('trigger-'.$trigger->id);
        $startTime = time();

        $executionContext = $this->executionContextFactory->createExecutionContext($trigger, $startTime);
        $dataPrototype = $condition->getDataPrototype((int) $trigger->id);

        $fireCallback =
            function (array $data = [], int $originId = 0, string $origin = 'tl_eblick_trigger') use ($simulated, $action, $executionContext, $dataPrototype): void {
                // fire action or skip if simulating
                if ($simulated || $action->fire($executionContext, $this->filterData($dataPrototype, $data))) {
                    $executionContext->addLog($originId, $origin, $simulated);
                }
            };

        // condition evaluation
        try {
            $condition->evaluate($executionContext, $fireCallback);
        } catch (\Exception $e) {
            $this->connection->executeQuery(
                'UPDATE tl_eblick_trigger SET error = ? WHERE id = ?',
                [$e->getMessage(), $trigger->id]
            );

            if ($e instanceof ExecutionException) {
                $this->logger->warning(
                    sprintf('An error occurred during execution of trigger %s.', $trigger->id),
                    ['exception' => $e, 'contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
                );
            } else {
                $this->logger->critical(
                    sprintf('An unexpected exception occurred during execution of trigger %s.', $trigger->id),
                    ['exception' => $e, 'contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)]
                );
            }
        }

        // update trigger meta data
        $stopwatchEvent = $this->executionTimer->stop('trigger-'.$trigger->id);
        $this->connection->executeQuery(
            'UPDATE tl_eblick_trigger SET lastRun = ?, lastDuration = ? WHERE id = ?',
            [$executionContext->getStartTime(), (int) $stopwatchEvent->getDuration(), $trigger->id]
        );
    }

    /**
     * Constrain processing data to what has been set in the respective prototype.
     */
    private function filterData(array $dataPrototype, array $data): array
    {
        // filter by keys (currently does not support nesting)
        return array_filter(
            $data,
            static fn ($v) => \array_key_exists($v, $dataPrototype),
            ARRAY_FILTER_USE_KEY
        );
    }
}
