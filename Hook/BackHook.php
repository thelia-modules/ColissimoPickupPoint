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
}
