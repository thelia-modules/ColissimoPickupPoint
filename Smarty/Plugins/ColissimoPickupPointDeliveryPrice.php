<?php

namespace ColissimoPickupPoint\Smarty\Plugins;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Model\CountryArea;
use Thelia\Model\CountryQuery;
use Thelia\Model\Country;
use Thelia\Module\Exception\DeliveryException;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class ColissimoPickupPointDeliveryPrice extends AbstractSmartyPlugin
{
    protected $request;
    protected $dispatcher;

    public function __construct(
        RequestStack $requestStack,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->dispatcher = $dispatcher;
    }

    public function getPluginDescriptors()
    {
        return [
            new SmartyPluginDescriptor('function', 'colissimoPickupPointDeliveryPrice', $this, 'colissimoPickupPointDeliveryPrice')
        ];
    }

    public function colissimoPickupPointDeliveryPrice($params, $smarty)
    {
        $country = Country::getShopLocation();
        if (isset($params['country'])) {
            $country = CountryQuery::create()->findOneById($params['country']);
        }

        $cartWeight = $this->request->getSession()->getSessionCart($this->dispatcher)->getWeight();
        $cartAmount = $this->request->getSession()->getSessionCart($this->dispatcher)->getTaxedAmount($country);

        $countryAreas = $country->getCountryAreas();
        $areasArray = [];

        /** @var CountryArea $countryArea */
        foreach ($countryAreas as $countryArea) {
            $areasArray[] = $countryArea->getAreaId();
        }

        try {
            $price = (new ColissimoPickupPoint)->getMinPostage(
                $areasArray,
                $cartWeight,
                $cartAmount
            );
        } catch (DeliveryException $ex) {
            $smarty->assign('isValidMode', false);
        }

        $smarty->assign('deliveryPrice', $price);
    }
}