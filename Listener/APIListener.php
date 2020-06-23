<?php


namespace ColissimoPickupPoint\Listener;


use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\WebService\FindByAddress;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Thelia\Core\Event\Delivery\PickupLocationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\PickupLocation;
use Thelia\Model\PickupLocationAddress;

class APIListener implements EventSubscriberInterface
{
    /**
     * Calls the Colissimo API and returns a response containing the informations of the relay points found
     *
     * @param PickupLocationEvent $pickupLocationEvent
     * @return mixed
     */
    protected function callWebService(PickupLocationEvent $pickupLocationEvent)
    {
        $zipcode = $pickupLocationEvent->getZipCode();
        $city = $pickupLocationEvent->getCity();
        $address = $pickupLocationEvent->getAddress();
        $countryCode = '';

        if ($country = $pickupLocationEvent->getCountry()) {
            $countryCode = $country->getIsoalpha2();
        }


        // Then ask the Web Service
        $request = new FindByAddress();
        $request
            ->setAddress($address)
            ->setZipCode($zipcode)
            ->setCity($city)
            ->setCountryCode($countryCode)
            ->setFilterRelay('1')
            ->setRequestId(md5(microtime()))
            ->setLang('FR')
            ->setOptionInter('1')
            ->setShippingDate(date('d/m/Y'))
            ->setAccountNumber(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_USERNAME))
            ->setPassword(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_PASSWORD))
        ;

        try {
            $responses = $request->exec();
        } catch (InvalidArgumentException $e) {
            $responses = array();
        } catch (\SoapFault $e) {
            $responses = array();
        }

        if (!is_array($responses) && $responses !== null) {
            $newResponse[] = $responses;
            $responses = $newResponse;
        }

        return $responses;
    }

    /**
     * Creates and returns a new location address
     *
     * @param $response
     * @return PickupLocationAddress
     */
    protected function createPickupLocationAddressFromResponse($response)
    {
        /** We create the new location address */
        $pickupLocationAddress = new PickupLocationAddress();

        /** We set the differents properties of the location address */
        $pickupLocationAddress
            ->setId($response->identifiant)
            ->setTitle($response->nom)
            ->setAddress1($response->adresse1)
            ->setAddress2($response->adresse2)
            ->setAddress3($response->adresse3)
            ->setCity($response->localite)
            ->setZipCode($response->codePostal)
            ->setPhoneNumber('')
            ->setCellphoneNumber('')
            ->setCompany('')
            ->setCountryCode($response->codePays)
            ->setFirstName('')
            ->setLastName('')
            ->setIsDefault(0)
            ->setLabel('')
            ->setAdditionalData([])
        ;

        return $pickupLocationAddress;
    }

    /**
     * Creates then returns a location from a response of the WebService
     *
     * @param $response
     * @return PickupLocation
     * @throws \Exception
     */
    protected function createPickupLocationFromResponse($response)
    {
        /** We create the new location */
        $pickupLocation = new PickupLocation();

        /** We set the differents properties of the location */
        $pickupLocation
            ->setId($response->identifiant)
            ->setTitle($response->nom)
            ->setAddress($this->createPickupLocationAddressFromResponse($response))
            ->setLatitude($response->coordGeolocalisationLatitude)
            ->setLongitude($response->coordGeolocalisationLongitude)
            ->setOpeningHours(PickupLocation::MONDAY_OPENING_HOURS_KEY, $response->horairesOuvertureLundi)
            ->setOpeningHours(PickupLocation::TUESDAY_OPENING_HOURS_KEY, $response->horairesOuvertureMardi)
            ->setOpeningHours(PickupLocation::WEDNESDAY_OPENING_HOURS_KEY, $response->horairesOuvertureMercredi)
            ->setOpeningHours(PickupLocation::THURSDAY_OPENING_HOURS_KEY, $response->horairesOuvertureJeudi)
            ->setOpeningHours(PickupLocation::FRIDAY_OPENING_HOURS_KEY, $response->horairesOuvertureVendredi)
            ->setOpeningHours(PickupLocation::SATURDAY_OPENING_HOURS_KEY, $response->horairesOuvertureSamedi)
            ->setOpeningHours(PickupLocation::SUNDAY_OPENING_HOURS_KEY, $response->horairesOuvertureDimanche)
            ->setModuleId(ColissimoPickupPoint::getModuleId())
        ;

        return $pickupLocation;
    }

    /**
     * Get the list of locations (relay points)
     *
     * @param PickupLocationEvent $pickupLocationEvent
     * @throws \Exception
     */
    public function get(PickupLocationEvent $pickupLocationEvent)
    {
        if (null !== $moduleIds = $pickupLocationEvent->getModuleIds()) {
            if (!in_array(ColissimoPickupPoint::getModuleId(), $moduleIds)) {
                return ;
            }
        }

        $responses = $this->callWebService($pickupLocationEvent);

        foreach ($responses as $response) {
            $pickupLocationEvent->appendLocation($this->createPickupLocationFromResponse($response));
        }
    }

    public static function getSubscribedEvents()
    {
        $listenedEvents = [];

        /** Check for old versions of Thelia where the events used by the API didn't exists */
        if (class_exists(PickupLocation::class)) {
            $listenedEvents[TheliaEvents::MODULE_DELIVERY_GET_PICKUP_LOCATIONS] = array("get", 128);
        }

        return $listenedEvents;
    }
}