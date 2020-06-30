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

namespace EBlick\ContaoTrigger\EventListener;

use Contao\CoreBundle\Monolog\ContaoContext;
use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\Execution\ExecutionContextFactory;
use EBlick\ContaoTrigger\Execution\ExecutionException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Stopwatch\Stopwatch;

class TriggerListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var ComponentManager */
    private $componentManager;

    /** @var Connection */
    private $database;

    /** @var LoggerInterface */
    private $logger;

    /** @var ExecutionContextFactory */
    private $executionContextFactory;

    /** @var Stopwatch */
    private $executionTimer;

    /** @var RequestStack */
    private $requestStack;

    /**
     * TriggerListener constructor.
     *
     * @param ComponentManager        $componentManager
     * @param Connection              $database
     * @param LoggerInterface         $logger
     * @param ExecutionContextFactory $executionContextFactory
     * @param RequestStack            $requestStack
     */
    public function __construct(
        ComponentManager $componentManager,
        Connection $database,
        LoggerInterface $logger,
        ExecutionContextFactory $executionContextFactory,
        RequestStack $requestStack
    ) {
        $this->componentManager        = $componentManager;
        $this->database                = $database;
        $this->logger                  = $logger;
        $this->executionContextFactory = $executionContextFactory;
        $this->requestStack           = $requestStack;

        $this->executionTimer = new Stopwatch();
    }

    /**
     * Run all triggers - this should be called periodically to make time constraints work.
     *
     * @throws \Exception
     */
    public function onExecute(): void
    {
        // temporary fix to circumvent easy themes bug
        if ('BE' === TL_MODE && null === $this->requestStack->getCurrentRequest()) {
            return;
        }

        $triggers = $this->database
            ->executeQuery(
                'SELECT * FROM tl_eblick_trigger ' .
                'WHERE enabled = 1 && error IS NULL'
            )
            ->fetchAll(\PDO::FETCH_OBJ);

        if (!$triggers) {
            return;
        }

        $factory = new Factory(new FlockStore($this->getTempDir()));
        $lock    = $factory->createLock('trigger-execution', 60);

        $lock->acquire();

        // execute triggers
        try {
            foreach ($triggers as $trigger) {
                $this->execute($trigger);
                /** @noinspection DisconnectedForeachInstructionInspection */
                $lock->refresh();
            }
        } finally {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $lock->release();
        }
    }


    /**
     * Simulate a trigger and write logs without executing actions.
     *
     * @param $triggerId
     *
     * @throws \Exception
     */
    public function onSimulate($triggerId) : void
    {
        $trigger = $this->database
            ->executeQuery(
                'SELECT * FROM tl_eblick_trigger WHERE id = ?',
                [$triggerId]
            )
            ->fetch(\PDO::FETCH_OBJ);

        $this->execute($trigger, true);
    }

    /**
     * Execute a single trigger.
     *
     * @param \stdClass $trigger
     *
     * @param bool      $simulated
     *
     * @throws \EBlick\ContaoTrigger\Execution\ExecutionException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     */
    private function execute(\stdClass $trigger, $simulated = false): void
    {
        $condition = $this->componentManager->getCondition($trigger->condition_type);
        $action    = $this->componentManager->getAction($trigger->action_type);

        if (!$condition || !$action) {
            return;
        }

        // setup execution data environment
        $this->executionTimer->reset();
        $this->executionTimer->start('trigger-' . $trigger->id);
        $startTime = time();

        $executionContext = $this->executionContextFactory->createExecutionContext($trigger, $startTime);
        $dataPrototype    = $condition->getDataPrototype((int) $trigger->id);

        $fireCallback =
            function (array $data = [], int $originId = 0, string $origin = 'tl_eblick_trigger')
            use ($simulated, $action, $executionContext, $dataPrototype) {
                // fire action or skip if simulating
                if ($simulated || $action->fire($executionContext, $this->filterData($dataPrototype, $data))) {
                    $executionContext->addLog($originId, $origin, $simulated);
                }
            };

        // condition evaluation
        try {
            $condition->evaluate($executionContext, $fireCallback);

        } catch (\Exception $e) {
            $this->database->executeQuery(
                'UPDATE tl_eblick_trigger SET error = ? WHERE id = ?',
                [sprintf($e->getMessage()), $trigger->id]
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
        $stopwatchEvent = $this->executionTimer->stop('trigger-' . $trigger->id);
        $this->database->executeQuery(
            'UPDATE tl_eblick_trigger SET lastRun = ?, lastDuration = ? WHERE id = ?',
            [$executionContext->getStartTime(), (int) $stopwatchEvent->getDuration(), $trigger->id]
        );
    }

    /**
     * Creates an installation specific folder in the temporary directory and returns its path.
     *
     * @return string
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    private function getTempDir(): string
    {
        $tmpDir = sys_get_temp_dir() . '/' . md5($this->container->getParameter('kernel.project_dir'));

        if (!is_dir($tmpDir)) {
            $this->container->get('filesystem')->mkdir($tmpDir);
        }

        return $tmpDir;
    }

    /**
     * Constrain processing data to what has been set in the respective prototype.
     *
     * @param array $dataPrototype
     * @param array $data
     *
     * @return array
     */
    private function filterData(array $dataPrototype, array $data): array
    {
        if (null === $data) {
            $data = [];
        }

        // filter by keys (currently does not support nesting)
        $data = array_filter(
            $data,
            function ($v) use ($dataPrototype) {
                return array_key_exists($v, $dataPrototype);
            },
            ARRAY_FILTER_USE_KEY
        );

        return $data;
    }
}