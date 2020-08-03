<?php


namespace ColissimoPickupPoint\Smarty\Plugins;


use Thelia\Model\ModuleConfigQuery;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\Request;

class ColissimoPickupPointGoogleApiKey extends AbstractSmartyPlugin
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
            new SmartyPluginDescriptor('function', 'colissimoPickupPointGoogleApiKey', $this, 'colissimoPickupPointGoogleApiKey')
        );
    }

    public function colissimoPickupPointGoogleApiKey($params, $smarty)
    {
        $key = ModuleConfigQuery::create()
            ->filterByName('colissimo_pickup_point_google_map_key')
            ->findOne()
            ->getValue()
        ;

        $smarty->assign('colissimoPickupPointGoogleMapKey', $key);

        return $key;
    }
}