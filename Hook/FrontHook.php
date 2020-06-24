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
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;


/**
 * Class FrontHook
 * @package ColissimoPickupPoint\Hook
 * @author Michaël Espeche <mespeche@openstudio.fr>
 */
class FrontHook extends BaseHook {
    public function onOrderDeliveryExtra(HookRenderEvent $event)
    {
        $content = $this->render('colissimo-pickup-point.html', $event->getArguments());
        $event->add($content);
    }

    public function onOrderInvoiceDeliveryAddress(HookRenderEvent $event)
    {
        $content = $this->render('delivery-address.html', $event->getArguments());
        $event->add($content);
    }

    public function onMainHeadBottom(HookRenderEvent $event)
    {
        $content = $this->addCSS('assets/css/styles.css');
        $event->add($content);
    }
} 