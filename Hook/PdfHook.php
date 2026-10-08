<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace ColissimoPickupPoint\Hook;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Model\OrderQuery;

class PdfHook extends BaseHook
{
    /**
     * The relay block of the invoice, for an order delivered at a relay only. Any other order gets nothing: the
     * template is rendered by the Twig parser of the PDF, which cannot render a Smarty template.
     */
    public function onInvoiceAfterDeliveryModule(HookRenderEvent $event)
    {
        if (ColissimoPickupPoint::getModuleId() !== (int) $event->getArgument('module_id')) {
            return;
        }

        $order = OrderQuery::create()->findOneById($event->getArgument('order'));

        if (null === $order) {
            return;
        }

        $event->add($this->render(
            'delivery_mode_infos.html.twig',
            [
                'delivery_address_id' => $order->getDeliveryOrderAddressId(),
                'locale' => $order->getLang()?->getLocale(),
            ]
        ));
    }


    public static function getSubscribedHooks(): array
    {
        return [
            'invoice.after-delivery-module' => [
                [
                    'type' => 'pdf',
                    'method' => 'onInvoiceAfterDeliveryModule',
                ],
            ],
        ];
    }
}
