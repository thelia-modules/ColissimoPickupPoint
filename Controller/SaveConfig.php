<?php

namespace ColissimoPickupPoint\Controller;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Thelia\Controller\Admin\BaseAdminController;
use ColissimoPickupPoint\Form\ConfigureColissimoPickupPoint;
use Thelia\Core\Translation\Translator;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

class SaveConfig extends BaseAdminController
{
    public function save()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array('ColissimoPickupPoint'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = new ConfigureColissimoPickupPoint($this->getRequest());
        try {
            $vform = $this->validateForm($form);

            ColissimoPickupPoint::setConfigValue(ColissimoPickupPoint::COLISSIMO_USERNAME, $vform->get(ColissimoPickupPoint::COLISSIMO_USERNAME)->getData());
            ColissimoPickupPoint::setConfigValue(ColissimoPickupPoint::COLISSIMO_PASSWORD, $vform->get(ColissimoPickupPoint::COLISSIMO_PASSWORD)->getData());
            ColissimoPickupPoint::setConfigValue(ColissimoPickupPoint::COLISSIMO_GOOGLE_KEY, $vform->get(ColissimoPickupPoint::COLISSIMO_GOOGLE_KEY)->getData());
            ColissimoPickupPoint::setConfigValue(ColissimoPickupPoint::COLISSIMO_ENDPOINT, $vform->get(ColissimoPickupPoint::COLISSIMO_ENDPOINT)->getData());

            return $this->generateRedirect(
                URL::getInstance()->absoluteUrl('/admin/module/ColissimoPickupPoint', ['current_tab' => 'configure'])
            );
        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans('Colissimo Pickup Point update config'),
                $e->getMessage(),
                $form,
                $e
            );

            return $this->render(
                'module-configure',
                [
                    'module_code' => 'ColissimoPickupPoint',
                    'current_tab' => 'configure',
                ]
            );
        }
    }
}
