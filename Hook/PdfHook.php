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
    public function onInvoiceAfterDeliveryModule(HookRenderEvent $event)
    {
        // No So Colissimo information if the delivery module is not ColissimoPickupPoint
        if (ColissimoPickupPoint::getModuleId() == $event->getArgument('module_id')) {
            return;
        }

        $order = OrderQuery::create()->findOneById($event->getArgument('order'));

        if (!is_null($order)) {
            $event->add($this->render(
                'delivery_mode_infos.html',
                ['delivery_address_id' => $order->getDeliveryOrderAddressId()]
            ));
        }
    }
}
