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


use Contao\Backend;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\EventListener\TriggerListener;

class Trigger implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /** @var ComponentManager */
    private $componentManager;

    /** @var Connection */
    private $database;

    /** @var TriggerListener */
    private $triggerSystem;

    /**
     * Trigger constructor.
     *
     * @param ComponentManager $componentManager
     * @param Connection       $database
     * @param TriggerListener  $triggerSystem
     */
    public function __construct(
        ComponentManager $componentManager,
        Connection $database,
        TriggerListener $triggerSystem
    ) {
        $this->componentManager = $componentManager;
        $this->database         = $database;
        $this->triggerSystem    = $triggerSystem;
    }

    /**
     * Import data container definitions from components.
     *
     * @param string $table
     */
    public function onImportDefinitions(string $table): void
    {
        if ('tl_eblick_trigger' !== $table) {
            return;
        }

        foreach ($this->componentManager->getConditionNames() as $conditionName) {
            $condition = $this->componentManager->getCondition($conditionName);

            if ($condition instanceof DataContainerComponentInterface) {
                $this->importComponent(
                    'condition',
                    $conditionName,
                    $condition->getDataContainerDefinition()
                );
            }
        }

        foreach ($this->componentManager->getActionNames() as $actionName) {
            $action = $this->componentManager->getAction($actionName);

            if ($action instanceof DataContainerComponentInterface) {
                $this->importComponent(
                    'action',
                    $actionName,
                    $action->getDataContainerDefinition()
                );
            }
        }
    }

    /**
     * Merge datacontainer properties as a sub component.
     *
     * @param string     $componentType
     * @param string     $componentName
     * @param Definition $definition
     */
    private function importComponent(string $componentType, string $componentName, Definition $definition): void
    {
        $node = &$GLOBALS['TL_DCA']['tl_eblick_trigger'];

        // add component to respective component selector
        $node['fields'][$componentType . '_type']['options'][] = $componentName;

        // add palettes (as a sub palette), fields, selectors and sub palettes
        $node['subpalettes'][$componentType . '_type_' . $componentName] = $definition->palette;
        foreach ($definition->fields as $fieldName => $field) {
            $node['fields'][$fieldName] = $field;
        }
        foreach ($definition->selectors as $selector) {
            $node['palettes']['__selector__'][] = $selector;
        }
        foreach ($definition->subPalettes as $subPaletteName => $subPalette) {
            $node['subpalettes'][$subPaletteName] = $subPalette;
        }
    }

    /**
     * @param DataContainer $dc
     *
     * @return string
     */
    public function onGetError(DataContainer $dc): string
    {
        if (!$dc->activeRecord->error) {
            return '';
        }
        return sprintf(
            '<div class="widget clr trigger-error"><h3>%s</h3><span><i>%s</i><br><br>%s</span></div>',
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['error'][0],
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['error'][1],
            nl2br_html5($dc->activeRecord->error)
        );
    }

    /**
     * @param DataContainer $dc
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onResetError(DataContainer $dc): void
    {
        $this->database->executeQuery(
            'UPDATE tl_eblick_trigger SET error = NULL WHERE id =?',
            [$dc->id]
        );
    }

    /**
     * @param array $row
     *
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onGenerateLabel(array $row): string
    {
        if ($row['error']) {
            $state = 'error';
        } else {
            if ($row['enabled']) {
                $state = $row['lastRun'] ? 'running' : 'waiting';
            } else {
                $state = 'paused';
            }
        }

        $this->framework->initialize();
        /** @noinspection PhpUndefinedMethodInspection */
        $datimFormat = $this->framework->getAdapter(Config::class)->get('datimFormat');

        $lastRun = $row['lastRun'] ?
            \Date::parse($datimFormat, $row['lastRun']) . ' (' . ($row['lastDuration'] / 1000) . 's)' : '[…]';

        $numRuns = $this->database
            ->executeQuery('SELECT COUNT(*) FROM tl_eblick_trigger_log WHERE pid =?', [$row['id']])
            ->fetch(\PDO::FETCH_COLUMN);

        return sprintf(
            '<div class="trigger-list trigger-state-%s"><span class="title">%s</span>' .
            '<div class="icon"></div><div class="type">%s → %s (%s)<br><span>%s</span></div></div>',
            $state,
            $row['title'],
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['condition'][$row['condition_type']][0],
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['action'][$row['action_type']][0],
            $numRuns,
            $lastRun
        );
    }

    /**
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function onShowSimulateButton(
        array $row,
        string $href,
        string $label,
        string $title,
        string $icon,
        string $attributes
    ): string {
        // only show for disabled triggers
        if ($row['enabled']) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href . '&amp;id=' . $row['id']),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }


    /**
     * @throws \Exception
     */
    public function onExecute(): void
    {
        $this->triggerSystem->onExecute();
        $this->redirectBack();
    }

    /**
     * @param DataContainer $dc
     *
     * @throws \Exception
     */
    public function onSimulate(DataContainer $dc): void
    {
        $this->triggerSystem->onSimulate($dc->id);
        $this->redirectBack();
    }

    /**
     * @param DataContainer $dc
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onReset(DataContainer $dc): void
    {
        $this->database->executeQuery(
            'DELETE FROM tl_eblick_trigger_log WHERE pid =?',
            [$dc->id]
        );
        $this->database->executeQuery(
            'UPDATE tl_eblick_trigger SET lastDuration = NULL, lastRun = NULL WHERE id =?',
            [$dc->id]
        );

        $this->redirectBack();
    }

    /**
     * Redirect to current listing after action.
     */
    private function redirectBack(): void
    {
        $this->framework->initialize();

        /** @var \Contao\Controller $controller */
        $controller = $this->framework->getAdapter(Controller::class);
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection StaticInvocationViaThisInspection */
        $controller->redirect($controller->addToUrl(null, true, ['key']));
    }
}