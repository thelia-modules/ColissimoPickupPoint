<?php


namespace ColissimoPickupPoint\Listener;


use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\WebService\FindByAddress;
use OpenApi\Events\DeliveryModuleOptionEvent;
use OpenApi\Events\OpenApiEvents;
use OpenApi\Model\Api\DeliveryModuleOption;
use OpenApi\Model\Api\ModelFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Thelia\Core\Event\Delivery\PickupLocationEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Model\CountryArea;
use Thelia\Model\PickupLocation;
use Thelia\Model\PickupLocationAddress;
use Thelia\Module\Exception\DeliveryException;

class APIListener implements EventSubscriberInterface
{
    /** @var ContainerInterface  */
    protected $container;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * APIListener constructor.
     * @param ContainerInterface $container We need the container because we use a service from another module
     * which is not mandatory, and using its service without it being installed will crash
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
    }

    /**
     * Calls the Colissimo API and returns a response containing the informations of the relay points found
     *
     * @param PickupLocationEvent $pickupLocationEvent
     * @return mixed
     */
    protected function callWebService(PickupLocationEvent $pickupLocationEvent)
    {
        $countryCode = '';

        if ($country = $pickupLocationEvent->getCountry()) {
            $countryCode = $country->getIsoalpha2();
        }

        // Then ask the Web Service
        $request = new FindByAddress();
        $request
            ->setAddress($pickupLocationEvent->getAddress())
            ->setZipCode($pickupLocationEvent->getZipCode())
            ->setCity($pickupLocationEvent->getCity())
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
    public function getPickupLocations(PickupLocationEvent $pickupLocationEvent)
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

    public function getDeliveryModuleOptions(DeliveryModuleOptionEvent $deliveryModuleOptionEvent)
    {
        if ($deliveryModuleOptionEvent->getModule()->getId() !== ColissimoPickupPoint::getModuleId()) {
            return ;
        }

        $isValid = true;
        $orderPostage = null;
        
        try {
            $module = new ColissimoPickupPoint();
            $country = $deliveryModuleOptionEvent->getCountry();

            $orderPostage = $module->getMinPostage(
                $country,
                $deliveryModuleOptionEvent->getCart()->getWeight(),
                $deliveryModuleOptionEvent->getCart()->getTaxedAmount($country),
                $this->requestStack->getCurrentRequest()->getSession()->getLang()->getLocale()
            );

        } catch (\Exception $exception) {
            $isValid = false;
        }

        $minimumDeliveryDate = ''; // TODO (calculate delivery date from day of order)
        $maximumDeliveryDate = ''; // TODO (calculate delivery date from day of order

        /** @var DeliveryModuleOption $deliveryModuleOption */
        $deliveryModuleOption = ($this->container->get('open_api.model.factory'))->buildModel('DeliveryModuleOption');
        $deliveryModuleOption
            ->setCode('ColissimoPickupPoint')
            ->setValid($isValid)
            ->setTitle('Colissimo Pickup Point')
            ->setImage('')
            ->setMinimumDeliveryDate($minimumDeliveryDate)
            ->setMaximumDeliveryDate($maximumDeliveryDate)
            ->setPostage(($orderPostage) ? $orderPostage->getAmount() : 0)
            ->setPostageTax(($orderPostage) ? $orderPostage->getAmountTax() : 0)
            ->setPostageUntaxed(($orderPostage) ? $orderPostage->getAmount() - $orderPostage->getAmountTax() : 0)
        ;

        $deliveryModuleOptionEvent->appendDeliveryModuleOptions($deliveryModuleOption);
    }

    public static function getSubscribedEvents()
    {
        $listenedEvents = [];

        /** Check for old versions of Thelia where the events used by the API didn't exists */
        if (class_exists(PickupLocation::class)) {
            $listenedEvents[TheliaEvents::MODULE_DELIVERY_GET_PICKUP_LOCATIONS] = array("getPickupLocations", 128);
        }

        if (class_exists(DeliveryModuleOptionEvent::class)) {
            $listenedEvents[OpenApiEvents::MODULE_DELIVERY_GET_OPTIONS] = array("getDeliveryModuleOptions", 128);
        }

        return $listenedEvents;
    }
}
