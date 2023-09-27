<?php

namespace ColissimoPickupPoint\Controller;

use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\Form\TaxRuleForm;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

#[Route('/admin/module/ColissimoPickupPoint/tax_rule', name: 'colissimo_pickup_point_tax_rule_')]
class TaxRuleController extends BaseAdminController
{
    #[Route('/save', name: 'save')]
    public function saveTaxRule()
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, ColissimoPickupPoint::DOMAIN, AccessManager::UPDATE)) {
            return $response;
        }

        $taxRuleForm = $this->createForm(TaxRuleForm::getName());

        $message = false;

        $url = '/admin/module/ColissimoPickupPoint';

        try {
            $form = $this->validateForm($taxRuleForm);

            // Get the form field values
            $data = $form->getData();

            ColissimoPickupPoint::setConfigValue(ColissimoPickupPoint::COLISSIMO_TAX_RULE_ID, $data["tax_rule_id"]);

        } catch (FormValidationException $ex) {
            $message = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
        }

        if ($message !== false) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans('ColissimoPickupPoint configuration', [], ColissimoPickupPoint::DOMAIN),
                $message,
                $taxRuleForm,
                $ex
            );
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl($url, [ 'tab' => 'tax_rule']));
    }
}