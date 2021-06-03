<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace ColissimoPickupPoint\Form;
use Propel\Runtime\ActiveQuery\Criteria;
use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Thelia\Form\BaseForm;
use Thelia\Model\Base\OrderQuery;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Order;
use Thelia\Model\OrderStatusQuery;
use Thelia\Model\OrderStatus;

/**
 * Class ExportOrder
 * @package ColissimoPickupPoint\Form
 * @author Thelia <info@thelia.net>
 */
class ExportOrder extends BaseForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $status = OrderStatusQuery::create()
            ->filterByCode(
                array(
                    OrderStatus::CODE_PAID,
                    OrderStatus::CODE_PROCESSING,
                    OrderStatus::CODE_SENT
                ),
                Criteria::IN
            )
            ->find()
            ->toArray('code')
        ;
        $query = OrderQuery::create()
            ->filterByDeliveryModuleId(ColissimoPickupPoint::getModCode())
            ->filterByStatusId(array($status['paid']['Id'], $status['processing']['Id']), Criteria::IN)
            ->find();

        $this->formBuilder
            ->add('new_status_id', ChoiceType::class,array(
                    'label' => Translator::getInstance()->trans('server'),
                    'choices' => array(
                        Translator::getInstance()->trans('Do not change') => 'nochange',
                        Translator::getInstance()->trans('Set orders status as processing') => 'processing',
                        Translator::getInstance()->trans('Set orders status as sent') => 'sent'
                    ),
                    'required' => 'true',
                    'expanded' => true,
                    'multiple' => false,
                    'data' => 'nochange'
                )
            );
        /** @var Order $order */
        foreach ($query as $order) {
            $this->formBuilder
                ->add(
                    'order_' . $order->getId(),
                    CheckboxType::class,
                    [
                        'label' => $order->getRef(),
                        'label_attr' => ['for' => 'export_' . $order->getId()]
                    ]
                )
                ->add(
                    'order_weight_' . $order->getId(),
                    'number'
                )
            ;
        }
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public static function getName()
    {
        return 'exportcolissimopickuppointorder';
    }

}
