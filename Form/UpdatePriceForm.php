<?php

namespace ColissimoPickupPoint\Form;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('area', IntegerType::class, array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(
                            array($this,
                                'verifyAreaExist')
                    )
                )
            ))
            ->add('weight', NumberType::class, array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add('price', NumberType::class, array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Callback(
                            array($this,
                                'verifyValidPrice')
                    )
                )
            ))
            ->add('franco', NumberType::class, array())
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

    public static function getName()
    {
        return 'colissimopickuppoint_update_price_form';
    }
}