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

namespace ColissimoPickupPoint;

use ColissimoPickupPoint\Model\ColissimoPickupPointAreaFreeshippingQuery;
use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshippingQuery;
use PDO;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;
use ColissimoPickupPoint\Model\ColissimoPickupPointPriceSlicesQuery;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Thelia\Model\Country;
use Thelia\Model\CountryArea;
use Thelia\Model\ModuleImageQuery;
use Thelia\Model\ModuleQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Module\AbstractDeliveryModule;
use Thelia\Module\Exception\DeliveryException;
use Thelia\Tools\Version\Version;

class ColissimoPickupPoint extends AbstractDeliveryModule
{
    protected $request;
    protected $dispatcher;

    private static $prices = null;

    const DOMAIN = 'colissimopickuppoint';

    const COLISSIMO_USERNAME = 'colissimo_pickup_point_username';

    const COLISSIMO_PASSWORD = 'colissimo_pickup_point_password';

    const COLISSIMO_GOOGLE_KEY = 'colissimo_pickup_point_google_map_key';

    const COLISSIMO_ENDPOINT = 'colissimo_pickup_point_endpoint_url';

    /**
     * These constants refer to the imported CSV file.
     * IMPORT_NB_COLS: file's number of columns (begin at 1)
     * IMPORT_DELIVERY_REF_COL: file's column where delivery reference is set (begin at 0)
     * IMPORT_ORDER_REF_COL: file's column where order reference is set (begin at 0)
     */
    const IMPORT_NB_COLS = 2;
    const IMPORT_DELIVERY_REF_COL = 0;
    const IMPORT_ORDER_REF_COL = 1;

    /**
     * This method is called by the Delivery loop, to check if the current module has to be displayed to the customer.
     * Override it to implements your delivery rules/
     *
     * If you return true, the delivery method will de displayed to the customer
     * If you return false, the delivery method will not be displayed
     *
     * @param Country $country the country to deliver to.
     *
     * @return boolean
     * @throws PropelException
     */
    public function isValidDelivery(Country $country)
    {
        if (empty($this->getAllAreasForCountry($country))) {
            return false;
        }

        $countryAreas = $country->getCountryAreas();
        $areasArray = [];

        /** @var CountryArea $countryArea */
        foreach ($countryAreas as $countryArea) {
            $areasArray[] = $countryArea->getAreaId();
        }

        $prices = ColissimoPickupPointPriceSlicesQuery::create()
            ->filterByAreaId($areasArray)
            ->findOne()
        ;

        $freeShipping = ColissimoPickupPointFreeshippingQuery::create()
            ->filterByActive(1)
            ->findOne()
        ;

        /** Check if Colissimo delivers the asked area*/
        if (null !== $prices || null !== $freeShipping) {
            return true;
        }

        return false;
    }

    /**
     * @param $areaId
     * @param $weight
     * @param $cartAmount
     *
     * @return mixed
     * @throws DeliveryException
     */
    public static function getPostageAmount($areaId, $weight, $cartAmount = 0)
    {
        /** Check if freeshipping is activated */
        try {
            $freeshipping = ColissimoPickupPointFreeshippingQuery::create()
                ->findPk(1)
                ->getActive()
            ;
        } catch (\Exception $exception) {
            $freeshipping = false;
        }

        /** Get the total cart price needed to have a free shipping for all areas, if it exists */
        try {
            $freeshippingFrom = ColissimoPickupPointFreeshippingQuery::create()
                ->findPk(1)
                ->getFreeshippingFrom()
            ;
        } catch (\Exception $exception) {
            $freeshippingFrom = false;
        }

        /** Set the initial postage price as 0 */
        $postage = 0;

        /** If free shipping is enabled, skip and return 0 */
        if (!$freeshipping) {

            /** If a min price for general freeshipping is defined and the cart reach this amount, return a postage of 0 */
            if (null !== $freeshippingFrom && $freeshippingFrom <= $cartAmount) {
                return 0;
            }

            /** Get the minimum price for free shipping in the area of the order */
            $areaFreeshipping = ColissimoPickupPointAreaFreeshippingQuery::create()
                ->filterByAreaId($areaId)
                ->findOne()
            ;

            if ($areaFreeshipping) {
                $areaFreeshipping = $areaFreeshipping->getCartAmount();
            }

            /** If the cart price is superior to the minimum price for free shipping in the area of the order,
             * return the postage as free.
             */
            if (null !== $areaFreeshipping && $areaFreeshipping <= $cartAmount) {
                return 0;
            }

            /** Search the list of prices and order it in ascending order */
            $areaPrices = ColissimoPickupPointPriceSlicesQuery::create()
                ->filterByAreaId($areaId)
                ->filterByWeightMax($weight, Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByWeightMax(null)
                ->filterByPriceMax($cartAmount, Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByPriceMax(null)
                ->orderByWeightMax()
                ->orderByPriceMax();

            /** Find the correct postage price for the cart weight and price according to the area and delivery mode in $areaPrices*/
            $firstPrice = $areaPrices
                ->find()
                ->getFirst()
            ;

            /** If no price was found, return null */
            if (null === $firstPrice) {
                return null;
                //throw new DeliveryException('Colissimo delivery unavailable for your cart weight or delivery country');
            }

            $postage = $firstPrice->getPrice();
        }

        return $postage;
    }

    /**
     * Calculate and return delivery price
     *
     * @param Country $country
     * @return mixed
     * @throws DeliveryException
     * @throws PropelException
     */
    public function getPostage(Country $country)
    {
        $request = $this->getRequest();

        $cartWeight = $request->getSession()->getSessionCart($this->getDispatcher())->getWeight();
        $cartAmount = $request->getSession()->getSessionCart($this->getDispatcher())->getTaxedAmount($country);

        $areaIdArray = $this->getAllAreasForCountry($country);
        if (empty($areaIdArray)) {
            throw new DeliveryException('Your delivery country is not covered by Colissimo.');
        }

        if (null === $postage = $this->getMinPostage($areaIdArray, $cartWeight, $cartAmount)) {
            throw new DeliveryException('Colissimo delivery unavailable for your cart weight or delivery country');
        }

        return $postage;
    }

    public function getMinPostage($areaIdArray, $cartWeight, $cartAmount)
    {
        $minPostage = null;

        foreach ($areaIdArray as $areaId) {
            try {
                $postage = self::getPostageAmount($areaId, $cartWeight, $cartAmount);
                if (null === $postage) {
                    continue ;
                }
                if ($minPostage === null || $postage < $minPostage) {
                    $minPostage = $postage;
                    if ($minPostage == 0) {
                        break;
                    }
                }
            } catch (\Exception $ex) {
                throw new DeliveryException($ex->getMessage()); //todo make a better catch
            }
        }

        if (null === $minPostage) {
            throw new DeliveryException("Colissimo delivery unavailable for your cart weight or delivery country");
        }

        return $minPostage;
    }

    /**
     * Returns ids of area containing this country and covered by this module
     * @param Country $country
     * @return array Area ids
     */
    public function getAllAreasForCountry(Country $country)
    {
        $areaArray = [];

        $sql = 'SELECT ca.area_id as area_id FROM country_area ca
               INNER JOIN area_delivery_module adm ON (ca.area_id = adm.area_id AND adm.delivery_module_id = :p0)
               WHERE ca.country_id = :p1';

        $con = Propel::getConnection();

        $stmt = $con->prepare($sql);
        $stmt->bindValue(':p0', $this->getModuleModel()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':p1', $country->getId(), PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $areaArray[] = $row['area_id'];
        }

        return $areaArray;
    }

    /** Return the module code */
    public function getCode()
    {
        return 'ColissimoPickupPoint';
    }

    /**
     * Check if the config values exist, and creates them otherwise
     */
    protected function checkModuleConfig() {
        /** Colissimo Username / Account number */
        if (null === self::getConfigValue(self::COLISSIMO_USERNAME)) {
            self::setConfigValue(self::COLISSIMO_USERNAME, '');
        }

        /** Colissimo password */
        if (null === self::getConfigValue(self::COLISSIMO_PASSWORD)) {
            self::setConfigValue(self::COLISSIMO_PASSWORD, '');
        }

        /** Colissimo Google Map key */
        if (null === self::getConfigValue(self::COLISSIMO_GOOGLE_KEY)) {
            self::setConfigValue(self::COLISSIMO_GOOGLE_KEY, '');
        }

        /** Colissimo Endpoint url for pickup point */
        if (null === self::getConfigValue(self::COLISSIMO_ENDPOINT)) {
            self::setConfigValue(self::COLISSIMO_ENDPOINT, 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0?wsdl');
        }
    }

    public function postActivation(ConnectionInterface $con = null)
    {
        try {
            // Security to not erase user config on reactivation
            ColissimoPickupPointPriceSlicesQuery::create()->findOne();
            ColissimoPickupPointAreaFreeshippingQuery::create()->findOne();
            ColissimoPickupPointFreeshippingQuery::create()->findOne();
        } catch (\Exception $e) {
            $database = new Database($con->getWrappedConnection());
            $database->insertSql(null, [__DIR__ . '/Config/thelia.sql', __DIR__ . '/Config/insert.sql']);
        }

        if (!ColissimoPickupPointFreeshippingQuery::create()->filterById(1)->findOne()) {
            ColissimoPickupPointFreeshippingQuery::create()->filterById(1)->findOneOrCreate()->setActive(0)->save();
        }

        $this->checkModuleConfig();

        /** Insert the images from image folder if first module activation */
        $module = $this->getModuleModel();
        if (ModuleImageQuery::create()->filterByModule($module)->count() === 0) {
            $this->deployImageFolder($module, sprintf('%s/images', __DIR__), $con);
        }
    }

    /** Return the module ID */
    public static function getModCode()
    {
        return ModuleQuery::create()->findOneByCode('ColissimoPickupPoint')->getId();
    }

    /**
     * @inheritDoc
     */
    public function update($currentVersion, $newVersion, ConnectionInterface $con = null)
    {
        $this->checkModuleConfig();

        $finder = (new Finder)
            ->files()
            ->name('#.*?\.sql#')
            ->sortByName()
            ->in(__DIR__ . DS . 'Config' . DS . 'update');

        $database = new Database($con);

        /** @var SplFileInfo $updateSQLFile */
        foreach ($finder as $updateSQLFile) {
            if (version_compare($currentVersion, str_replace('.sql', '', $updateSQLFile->getFilename()), '<')) {
                $database->insertSql(
                    null,
                    [
                        $updateSQLFile->getPathname()
                    ]
                );
            }
        }
    }

    public function getDeliveryMode()
    {
        return "pickup";
    }
}
