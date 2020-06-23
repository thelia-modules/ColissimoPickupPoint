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

use ColissimoPickupPoint\Utils\ColissimoCodeReseau;
use ColissimoPickupPoint\WebService\FindById;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Delivery\DeliveryPostageEvent;
use Thelia\Core\Event\TheliaEvents;

use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Address;
use Thelia\Model\CountryQuery;
use Thelia\Model\OrderAddressQuery;
use ColissimoPickupPoint\ColissimoPickupPoint;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Model\AddressQuery;
use ColissimoPickupPoint\Model\AddressColissimoPickupPointQuery;
use ColissimoPickupPoint\Model\AddressColissimoPickupPoint;
use ColissimoPickupPoint\Model\OrderAddressColissimoPickupPoint;

/**
 * Class SetDeliveryModule
 * @package ColissimoPickupPoint\Listener
 * @author Thelia <info@thelia.net>
 */
class SetDeliveryModule implements EventSubscriberInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    protected function check_module($id)
    {
        return $id == ColissimoPickupPoint::getModCode();
    }

    private function callWebServiceFindRelayPointByIdFromRequest(Request $request)
    {
        $relay_infos = explode(':', $request->get('colissimo_pickup_point_code'));

        $pr_code = $relay_infos[0];
        $relayType = count($relay_infos) > 1 ? $relay_infos[1] : null ;
        $relayCountryCode = count($relay_infos) > 2 ? $relay_infos[2] : null ;

        if (!empty($pr_code)) {
            $req = new FindById();

            $req->setId($pr_code)
                ->setLangue('FR')
                ->setDate(date('d/m/Y'))
                ->setAccountNumber(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_USERNAME))
                ->setPassword(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_PASSWORD));

            // An argument "Code réseau" is now required in addition to the Relay Point Code to identify a relay point outside France.
            // This argument is optional for relay points inside France.
            if ($relayType != null && $relayCountryCode != null) {
                $codeReseau = ColissimoCodeReseau::getCodeReseau($relayCountryCode, $relayType);
                if ($codeReseau !== null) {
                    $req->setReseau($codeReseau);
                }
            }

            return $req->exec();
        }

        return null;
    }

    public function isModuleColissimoPickupPoint(OrderEvent $event)
    {
        if ($this->check_module($event->getDeliveryModule())) {
            $request = $this->getRequest();

            $address = AddressColissimoPickupPointQuery::create()
                ->findPk($event->getDeliveryAddress());

            $request->getSession()->set('ColissimoPickupPointDeliveryId', $event->getDeliveryAddress());
            if ($address === null) {
                $address = new AddressColissimoPickupPoint();
                $address->setId($event->getDeliveryAddress());
            }

            $response = $this->callWebServiceFindRelayPointByIdFromRequest($request);

            if ($response !== null) {
                $customerName = AddressQuery::create()
                    ->findPk($event->getDeliveryAddress());

                $address = AddressColissimoPickupPointQuery::create()
                    ->findPk($event->getDeliveryAddress());

                $request->getSession()->set('ColissimoPickupPointDeliveryId', $event->getDeliveryAddress());

                if ($address === null) {
                    $address = new AddressColissimoPickupPoint();
                    $address->setId($event->getDeliveryAddress());
                }

                $relayCountry = CountryQuery::create()->findOneByIsoalpha2($response->codePays);

                if ($relayCountry == null) {
                    $relayCountry = $customerName->getCountry();
                }

                $address
                    ->setCode($response->identifiant)
                    ->setType($response->typeDePoint)
                    ->setCompany($response->nom)
                    ->setAddress1($response->adresse1)
                    ->setAddress2($response->adresse2)
                    ->setAddress3($response->adresse3)
                    ->setZipcode($response->codePostal)
                    ->setCity($response->localite)
                    ->setFirstname($customerName->getFirstname())
                    ->setLastname($customerName->getLastname())
                    ->setTitleId($customerName->getTitleId())
                    ->setCountryId($relayCountry->getId())
                    ->save()
                ;
            } else {
                $message = Translator::getInstance()->trans('No pickup points were selected', [], ColissimoPickupPoint::DOMAIN);
                throw new \Exception($message);
            }
        }
    }

    public function updateDeliveryAddress(OrderEvent $event)
    {
        if ($this->check_module($event->getOrder()->getDeliveryModuleId())) {
            $request = $this->getRequest();

            $tmp_address = AddressColissimoPickupPointQuery::create()
                ->findPk($request->getSession()->get('ColissimoPickupPointDeliveryId'));

            if ($tmp_address === null) {
                throw new \ErrorException('Got an error with ColissimoPickupPoint module. Please try again to checkout.');
            }

            $savecode = new OrderAddressColissimoPickupPoint();

            $savecode
                ->setId($event->getOrder()->getDeliveryOrderAddressId())
                ->setCode($tmp_address->getCode())
                ->setType($tmp_address->getType())
                ->save()
            ;

            $update = OrderAddressQuery::create()
                ->findPK($event->getOrder()->getDeliveryOrderAddressId())
                ->setCompany($tmp_address->getCompany())
                ->setAddress1($tmp_address->getAddress1())
                ->setAddress2($tmp_address->getAddress2())
                ->setAddress3($tmp_address->getAddress3())
                ->setZipcode($tmp_address->getZipcode())
                ->setCity($tmp_address->getCity())
                ->save()
            ;
        }
    }

    public function getPostageRelayPoint(DeliveryPostageEvent $event)
    {
        if ($this->check_module($event->getModule()->getModuleModel()->getId())) {
            $request = $this->getRequest();

            // If the relay point service was chosen, we store the address of the chosen relay point in
            //    the DeliveryPostageEvent in order for Thelia to recalculate the postage cost from this address.

            $response = $this->callWebServiceFindRelayPointByIdFromRequest($request);

            if ($response !== null) {
                $address = new Address();
                $relayCountry = CountryQuery::create()->findOneByIsoalpha2($response->codePays);

                $address
                    ->setCompany($response->nom)
                    ->setAddress1($response->adresse1)
                    ->setAddress2($response->adresse2)
                    ->setAddress3($response->adresse3)
                    ->setZipcode($response->codePostal)
                    ->setCity($response->localite)
                    ->setCountryId($relayCountry->getId())
                ;

                $event->setAddress($address);
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
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => array('isModuleColissimoPickupPoint', 64),
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('updateDeliveryAddress', 256),
            TheliaEvents::MODULE_DELIVERY_GET_POSTAGE => array('getPostageRelayPoint', 257)
        );
    }
}
