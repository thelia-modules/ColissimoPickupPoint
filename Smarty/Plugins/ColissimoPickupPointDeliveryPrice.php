<?php

namespace ColissimoPickupPoint\Smarty\Plugins;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\Request;
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
        Request $request,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->request = $request;
        $this->dispatcher = $dispatcher;
    }

    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor('function', 'colissimoPickupPointDeliveryPrice', $this, 'colissimoPickupPointDeliveryPrice')
        );
    }

    public function colissimoPickupPointDeliveryPrice($params, $smarty)
    {
        $country = Country::getShopLocation();
        if (isset($params['country'])) {
            $country = CountryQuery::create()->findOneById($params['country']);
        }

        $cartWeight = $this->request->getSession()->getSessionCart($this->dispatcher)->getWeight();
        $cartAmount = $this->request->getSession()->getSessionCart($this->dispatcher)->getTaxedAmount($country);

        try {
            $price = ColissimoPickupPoint::getPostageAmount(
                $country->getAreaId(),
                $cartWeight,
                $cartAmount
            );
        } catch (DeliveryException $ex) {
            $smarty->assign('isValidMode', false);
        }

        $smarty->assign('deliveryPrice', $price);
    }
}