<?php

declare(strict_types=1);

namespace EBlick\ContaoTrigger\EventListener\DataContainer;

use Contao\Backend;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\Date;
use Contao\Image;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use EBlick\ContaoTrigger\Component\ComponentManager;
use EBlick\ContaoTrigger\DataContainer\DataContainerComponentInterface;
use EBlick\ContaoTrigger\DataContainer\Definition;
use EBlick\ContaoTrigger\EventListener\TriggerListener;

class Trigger
{
    public function __construct(private ComponentManager $componentManager, private Connection $connection, private TriggerListener $triggerSystem, private ContaoFramework $framework)
    {
    }

    /**
     * Import data container definitions from components.
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

    public function onGetError(DataContainer $dc): string
    {
        if (!$dc->activeRecord->error) {
            return '';
        }

        return sprintf(
            '<div class="widget clr trigger-error"><h3>%s</h3><span><i>%s</i><br><br>%s</span></div>',
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['error'][0],
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['error'][1],
            nl2br($dc->activeRecord->error)
        );
    }

    public function onResetError(DataContainer $dc): void
    {
        $this->connection->executeQuery(
            'UPDATE tl_eblick_trigger SET error = NULL WHERE id =?',
            [$dc->id]
        );
    }

    public function onGenerateLabel(array $row): string
    {
        if ($row['error']) {
            $state = 'error';
        } elseif ($row['enabled']) {
            $state = $row['lastRun'] ? 'running' : 'waiting';
        } else {
            $state = 'paused';
        }

        $this->framework->initialize();
        $datimFormat = $this->framework->getAdapter(Config::class)->get('datimFormat');

        $lastRun = $row['lastRun'] ?
            Date::parse($datimFormat, $row['lastRun']).' ('.($row['lastDuration'] / 1000).'s)' : '[…]';

        $numRuns = (int) $this->connection
            ->executeQuery('SELECT COUNT(*) FROM tl_eblick_trigger_log WHERE pid =?', [$row['id']])
            ->fetchOne()
        ;

        return sprintf(
            '<div class="trigger-list trigger-state-%s"><span class="title">%s</span>'.
            '<div class="icon"></div><div class="type">%s → %s (%s)<br><span>%s</span></div></div>',
            $state,
            $row['title'],
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['condition'][$row['condition_type']][0],
            $GLOBALS['TL_LANG']['tl_eblick_trigger']['action'][$row['action_type']][0],
            $numRuns,
            $lastRun
        );
    }

    public function onShowSimulateButton(array $row, string $href, string|null $label, string $title, string $icon, string $attributes): string
    {
        // only show for disabled triggers
        if ($row['enabled']) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href.'&amp;id='.$row['id']),
            StringUtil::specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    public function onExecute(): void
    {
        $this->triggerSystem->onExecute();
        $this->redirectBack();
    }

    public function onSimulate(DataContainer $dc): void
    {
        $this->triggerSystem->onSimulate((int) $dc->id);
        $this->redirectBack();
    }

    public function onReset(DataContainer $dc): void
    {
        $this->connection->executeQuery(
            'DELETE FROM tl_eblick_trigger_log WHERE pid =?',
            [$dc->id]
        );
        $this->connection->executeQuery(
            'UPDATE tl_eblick_trigger SET lastDuration = 0, lastRun = 0 WHERE id =?',
            [$dc->id]
        );

        $this->redirectBack();
    }

    /**
     * Merge datacontainer properties as a sub component.
     */
    private function importComponent(string $componentType, string $componentName, Definition $definition): void
    {
        $node = &$GLOBALS['TL_DCA']['tl_eblick_trigger'];

        // add component to respective component selector
        $node['fields'][$componentType.'_type']['options'][] = $componentName;

        // add palettes (as a sub palette), fields, selectors and sub palettes
        $node['subpalettes'][$componentType.'_type_'.$componentName] = $definition->palette;

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
     * Redirect to current listing after action.
     */
    private function redirectBack(): void
    {
        $this->framework->initialize();

        /** @var Controller $controller */
        $controller = $this->framework->getAdapter(Controller::class);
        /** @noinspection StaticInvocationViaThisInspection */
        $controller->redirect($controller->addToUrl(null, true, ['key']));
    }
}
