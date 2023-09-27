<?php

namespace ColissimoPickupPoint\Form;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\TaxRuleI18nQuery;

class TaxRuleForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add("tax_rule_id",
                ChoiceType::class,
                [
                    'data' => (int)ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_TAX_RULE_ID),
                    'choices' => $this->getTaxRules(),
                    'label' => Translator::getInstance()->trans('Tax Rule', [], ColissimoPickupPoint::DOMAIN),
                ]
            );
    }

    private function getTaxRules(): array
    {
        $res = [];

        /** @var Request $request */
        $request = $this->request;

        $lang = $request->getSession()?->getAdminEditionLang();

        $taxRules = TaxRuleI18nQuery::create()
            ->filterByLocale($lang->getLocale())
            ->find();

        $res[Translator::getInstance()->trans('Default Tax rule', [], ColissimoPickupPoint::DOMAIN)] = null;

        foreach ($taxRules as $taxRule)
        {
            $res[$taxRule->getTitle()] = $taxRule->getId();
        }

        return $res;
    }
}