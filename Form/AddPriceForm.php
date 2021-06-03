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

class AddPriceForm extends BaseForm
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
                    new Constraints\Callback(
                            array($this,
                                'verifyValidWeight')
                    )
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

    public function verifyValidWeight($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans("The weight value is not valid.", [], ColissimoPickupPoint::DOMAIN));
        }

        if ($value < 0) {
            $context->addViolation(Translator::getInstance()->trans("The weight value must be superior to 0.", [], ColissimoPickupPoint::DOMAIN));
        }
    }

    public function verifyValidPrice($value, ExecutionContextInterface $context)
    {
        if (!preg_match("#^\d+\.?\d*$#", $value)) {
            $context->addViolation(Translator::getInstance()->trans("The price value is not valid.", [], ColissimoPickupPoint::DOMAIN));
        }
    }

    public static function getName()
    {
        return 'colissimo_pickup_point_price_slices_create';
    }
}