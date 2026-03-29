<?php

namespace ColissimoPickupPoint\Listener;

use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshippingQuery;
use ColissimoPickupPoint\Model\ColissimoPickupPointPriceSlicesQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Thelia\Model\AreaDeliveryModuleQuery;
use Thelia\Model\ModuleConfigQuery;

class ConfigListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'module.config' => [
                'onModuleConfig', 128
                ],
        ];
    }

    public function onModuleConfig(GenericEvent $event): void
    {
        $subject = $event->getSubject();

        if ($subject !== "HealthStatus") {
            throw new \RuntimeException('Event subject does not match expected value');
        }

        $moduleConfig = [];
        $moduleConfig['module'] = ColissimoPickupPoint::getModuleCode();
        $configsCompleted = true;

        $shippingZoneConfig = AreaDeliveryModuleQuery::create()
            ->filterByDeliveryModuleId(ColissimoPickupPoint::getModuleId())
            ->find();

        $configModuleFree = ColissimoPickupPointFreeshippingQuery::create()
            ->findOne();

        $configModuleSlices = ColissimoPickupPointPriceSlicesQuery::create()
            ->find();

        $configModule = ModuleConfigQuery::create()
            ->filterByModuleId(ColissimoPickupPoint::getModuleId())
            ->filterByName(['colissimo_pickup_point_username', 'colissimo_pickup_point_password', 'colissimo_pickup_point_google_map_key', 'colissimo_pickup_point_endpoint_url'])
            ->find();

        foreach ($configModule as $config) {
            $moduleConfig[$config->getName()] = $config->getValue();
            if ($config->getName() === 'colissimo_pickup_point_username' && $config->getValue() === "") {
                $configsCompleted = false;
            }
            if ($config->getName() === 'colissimo_pickup_point_password' && $config->getValue() === "") {
                $configsCompleted = false;
            }
        }

        if ($configModuleFree) {
            $moduleConfig['freeshipping_active'] = $configModuleFree->getActive();
            $moduleConfig['freeshipping_from'] = $configModuleFree->getFreeshippingFrom();
            if ($configModuleFree->getActive()) {
                if ($moduleConfig['colissimo_pickup_point_username'] === ""
                    || $moduleConfig['colissimo_pickup_point_password'] === ""
                    || $moduleConfig['colissimo_pickup_point_google_map_key'] === ""
                    || $moduleConfig['colissimo_pickup_point_endpoint_url'] === "") {
                    $configsCompleted = false;
                }
            } else {
                if ($configModuleSlices->count() === 0) {
                    $configsCompleted = false;
                }
            }
        }

        if ($shippingZoneConfig->count() === 0) {
            $configsCompleted = false;
        }

        $moduleConfig['completed'] = $configsCompleted;

        $event->setArgument('colissimo_pickup_point.module.config', $moduleConfig);
    }


}