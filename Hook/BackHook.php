<?php

namespace ColissimoPickupPoint\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class BackHook
 * @package ColissimoPickupPoint\Hook
 */
class BackHook extends BaseHook
{
    public function onModuleConfiguration(HookRenderEvent $event)
    {
        $event->add($this->render('module_configuration.html'));
    }

    public function onModuleConfigJs(HookRenderEvent $event)
    {
        $event->add($this->render('module-config-js.html'));
    }

    public function renderColishipExport(HookRenderEvent $event)
    {
        $event->add($this->render('order-edit-coliship-export.html'));
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                [
                    'type' => 'back',
                    'method' => 'onModuleConfiguration',
                ],
            ],
            'module.config-js' => [
                [
                    'type' => 'back',
                    'method' => 'onModuleConfigJs',
                ],
            ],
            'order.tab-content' => [
                [
                    'type' => 'back',
                    'method' => 'renderColishipExport',
                ],
            ],
        ];
    }
}
