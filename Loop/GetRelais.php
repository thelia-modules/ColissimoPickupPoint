<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
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
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace ColissimoPickupPoint\Loop;

use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\WebService\FindByAddress;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Log\Tlog;
use Thelia\Model\AddressQuery;
use Thelia\Model\ConfigQuery;
use Thelia\Model\CountryQuery;

/**
 * Class GetRelais
 * @package ColissimoPickupPoint\Loop
 * @author Thelia <info@thelia.net>
 */
class GetRelais extends BaseLoop implements ArraySearchLoopInterface
{
    /**
     * @inheritdoc
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('countryid', ''),
            Argument::createAnyTypeArgument('zipcode', ''),
            Argument::createAnyTypeArgument('city', ''),
            Argument::createIntTypeArgument('address')
        );
    }

    /**
     * @return array|mixed
     * @throws \ErrorException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function buildArray()
    {
        // Find the address ... To find ! \m/
        $zipcode = $this->getZipcode();
        $city = $this->getCity();
        $countryId = $this->getCountryid();

        $addressId = $this->getAddress();

        if (!empty($addressId) && (!empty($zipcode) || !empty($city))) {
            throw new \InvalidArgumentException(
                "Cannot have argument 'address' and 'zipcode' or 'city' at the same time."
            );
        }

        if (null !== $addressModel = AddressQuery::create()->findPk($addressId)) {
            $address = array(
                'zipcode' => $addressModel->getZipcode(),
                'city' => $addressModel->getCity(),
                'address' => $addressModel->getAddress1(),
                'countrycode' => $addressModel->getCountry()->getIsoalpha2()
            );
        } elseif (empty($zipcode) || empty($city)) {
            $search = AddressQuery::create();

            $customer = $this->securityContext->getCustomerUser();
            if ($customer !== null) {
                $search->filterByCustomerId($customer->getId());
                $search->filterByIsDefault('1');
            } else {
                throw new \ErrorException('Customer not connected.');
            }

            $search = $search->findOne();
            $address['zipcode'] = $search->getZipcode();
            $address['city'] = $search->getCity();
            $address['address'] = $search->getAddress1();
            $address['countrycode'] = $search->getCountry()->getIsoalpha2();
        } else {
            $address = array(
                'zipcode' => $zipcode,
                'city' => $city,
                'address' => '',
                'countrycode' => CountryQuery::create()
                    ->findOneById($countryId)
                    ->getIsoalpha2()
            );
        }

        // Then ask the Web Service
        $request = new FindByAddress();
        $request
            ->setAddress($address['address'])
            ->setZipCode($address['zipcode'])
            ->setCity($address['city'])
            ->setCountryCode($address['countrycode'])
            ->setFilterRelay('1')
            ->setRequestId(md5(microtime()))
            ->setLang('FR')
            ->setOptionInter('1')
            ->setShippingDate(date('d/m/Y'))
            ->setAccountNumber(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_USERNAME))
            ->setPassword(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_PASSWORD))
        ;

        try {
            $response = $request->exec();
        } catch (InvalidArgumentException $e) {
            $response = array();
        } catch (\SoapFault $e) {
            $response = array();
        }

        if (!is_array($response) && $response !== null) {
            $newResponse[] = $response;
            $response = $newResponse;
        }

        return $response;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $item) {
            $loopResultRow = new LoopResultRow();

            //Tlog::getInstance()->addDebug(print_r($item, true));
            foreach ($item as $key => $value) {
                $loopResultRow->set($key, $value);
            }

            // format distance
            $distance = (string) $loopResultRow->get('distanceEnMetre');
            if (strlen($distance) < 4) {
                $distance .= ' m';
            } else {
                $distance = (string)(float)$distance / 1000;
                while (substr($distance, strlen($distance) - 1, 1) == "0") {
                    $distance = substr($distance, 0, strlen($distance) - 1);
                }
                $distance = str_replace('.', ',', $distance) . ' km';
            }
            $loopResultRow->set('distance', $distance);

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
