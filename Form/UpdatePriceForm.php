<?php

namespace ColissimoPickupPoint\Form;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\AreaQuery;

class UpdatePriceForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add('area', 'integer', array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        'methods' => array(
                            array($this,
                                'verifyAreaExist')
                        )
                    ))
                )
            ))
            ->add('weight', 'number', array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add('price', 'number', array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(array(
                        'methods' => array(
                            array($this,
                                'verifyValidPrice')
                        )
                    ))
                )
            ))
            ->add('franco', 'number', array())
        ;
    }

    public function verifyAreaExist($value, ExecutionContextInterface $context)
    {
        $area = AreaQuery::create()->findPk($value);
        if (null === $area) {
            $context->addViolation(Translator::getInstance()->trans("This area doesn't exists.", [], ColissimoPickupPoint::DOMAIN));
        }
    }

    public function verifyValidPrice($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans('The price value is not valid.', [], ColissimoPickupPoint::DOMAIN));
        }
    }

    public function getName()
    {
        return 'colissimo_pickup_point_price_slices_create';
    }
}