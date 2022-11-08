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

namespace ColissimoPickupPoint\Listener;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\ParserInterface;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use Thelia\Model\LangQuery;
use Thelia\Model\MessageQuery;
use Thelia\Model\OrderAddressQuery;

/**
 * Class SendMail
 * @package Colissimo\Listener
 * @author Manuel Raynaud <mraynaud@openstudio.fr>
 */
class SendMail implements EventSubscriberInterface
{

    protected $parser;
    protected $request;
    protected $mailer;

    public function __construct(ParserInterface $parser, MailerFactory $mailer, RequestStack $requestStack)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function updateStatus(OrderEvent $event, $mailer)
    {
        $order = $event->getOrder();
        $colissimoPickupPoint = new ColissimoPickupPoint();

        if ($order->isSent() && $order->getDeliveryModuleId() == $colissimoPickupPoint->getModuleModel()->getId()) {
            $contact_email = ConfigQuery::read('store_email');

            if ($contact_email) {

                $message = MessageQuery::create()
                    ->filterByName(ColissimoPickupPoint::CONFIRMATION_MESSAGE_NAME)
                    ->findOne();

                if (false === $message || null === $message) {
                    throw new \Exception("Failed to load message ".ColissimoPickupPoint::CONFIRMATION_MESSAGE_NAME.".");
                }

                $order = $event->getOrder();
                $customer = $order->getCustomer();

                // Configured site URL
                $urlSite =  ConfigQuery::read('url_site');

                // for one domain by lang
                if ((int) ConfigQuery::read('one_domain_foreach_lang', 0) === 1) {
                    // We always query the DB here, as the Lang configuration (then the related URL) may change during the
                    // user session lifetime, and improper URLs could be generated. This is quite odd, okay, but may happen.
                    $urlSite = LangQuery::create()->findPk($this->request->getSession()->getLang()->getId())->getUrl();
                }

                $orderDeliveryAddress = OrderAddressQuery::create()
                    ->filterById($order->getDeliveryOrderAddressId())
                    ->findOne();

                $pickupName = $orderDeliveryAddress->getCompany();
                $pickupAddress1 = $orderDeliveryAddress->getAddress1();
                $pickupAddress2 = $orderDeliveryAddress->getAddress2();
                $pickupAddress3 = $orderDeliveryAddress->getAddress3();
                $pickupZipCode = $orderDeliveryAddress->getZipcode();
                $pickupCity = $orderDeliveryAddress->getCity();
                $pickupCellphone = $orderDeliveryAddress->getCellphone();

                $this->parser->assign('customer_id', $customer->getId());
                $this->parser->assign('order_ref', $order->getRef());
                $this->parser->assign('order_date', $order->getCreatedAt());
                $this->parser->assign('update_date', $order->getUpdatedAt());
                $this->parser->assign('package', $order->getDeliveryRef());
                $this->parser->assign('store_name', ConfigQuery::read('store_name'));
                $this->parser->assign('store_url', $urlSite);
                $this->parser->assign('pickup_name', $pickupName);
                $this->parser->assign('pickup_address1', $pickupAddress1);
                $this->parser->assign('pickup_address2', $pickupAddress2);
                $this->parser->assign('pickup_address3', $pickupAddress3);
                $this->parser->assign('pickup_zipcode', $pickupZipCode);
                $this->parser->assign('pickup_city', $pickupCity);
                $this->parser->assign('pickup_cellphone', $pickupCellphone);

                $message
                    ->setLocale($order->getLang()->getLocale());

                $email = $this->mailer->createEmailMessage(
                    'order_notification',
                    [ConfigQuery::getStoreEmail() => ConfigQuery::getStoreName()],
                    [$customer->getEmail() => $customer->getFirstname() . " " . $customer->getLastname()],
                    [
                        'order_id' => $event->getOrder()->getId(),
                        'order_ref' => $event->getOrder()->getRef(),
                    ]
                );

                $this->mailer->send($email);
            }
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array('updateStatus', 128)
        );
    }
}
