<?php

namespace ColissimoPickupPoint\Tests;

use ColissimoPickupPoint\Listener\APIListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Api\Resource\DeliveryPickupLocation;
use Thelia\Api\Resource\PickupLocationAddress;

/**
 * Run from a Thelia install:
 * vendor/bin/phpunit --bootstrap vendor/autoload.php vendor/thelia/modules/ColissimoPickupPoint/tests
 */
class APIListenerTest extends TestCase
{
    private const MODULE_ID = 999;

    public static function setUpBeforeClass(): void
    {
        spl_autoload_register(function (string $class): void {
            if (str_starts_with($class, 'ColissimoPickupPoint\\')) {
                $file = \dirname(__DIR__) . '/' . str_replace('\\', '/', substr($class, \strlen('ColissimoPickupPoint\\'))) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        });

        // getModuleId() queries the database unless the BaseModule static cache is already filled
        $moduleIds = new \ReflectionProperty(\Thelia\Module\BaseModule::class, 'moduleIds');
        $moduleIds->setValue(null, ['ColissimoPickupPoint' => self::MODULE_ID]);
    }

    public function testCreatePickupLocationFromWebServiceResponseUsesStrictTypes(): void
    {
        $listener = new APIListener(new Container(), new RequestStack());

        $method = new \ReflectionMethod(APIListener::class, 'createPickupLocationFromResponse');

        /** @var DeliveryPickupLocation $location */
        $location = $method->invoke($listener, $this->createWebServiceResponse());

        $this->assertSame('123456', $location->getId());
        $this->assertSame('BUREAU DE POSTE PARIS', $location->getTitle());
        $this->assertSame(48.85661, $location->getLatitude());
        $this->assertSame(2.35222, $location->getLongitude());
        $this->assertSame(self::MODULE_ID, $location->getModuleId());

        $openingHours = $location->getOpeningHours();
        $this->assertSame('09:00-18:00', $openingHours[(int) DeliveryPickupLocation::MONDAY_OPENING_HOURS_KEY]);
        $this->assertSame('', $openingHours[(int) DeliveryPickupLocation::SUNDAY_OPENING_HOURS_KEY]);

        $address = $location->getAddress();
        $this->assertInstanceOf(PickupLocationAddress::class, $address);
        $this->assertSame('123456', $address->getId());
        $this->assertSame('12 RUE DE LA PAIX', $address->getAddress1());
        $this->assertSame('75002', $address->getZipCode());
        $this->assertSame('PARIS', $address->getCity());
        $this->assertFalse($address->isDefault());
    }

    public function testEmptyCoordinatesAreMappedToNull(): void
    {
        $listener = new APIListener(new Container(), new RequestStack());

        $response = $this->createWebServiceResponse();
        $response->coordGeolocalisationLatitude = '';
        $response->coordGeolocalisationLongitude = '';

        $method = new \ReflectionMethod(APIListener::class, 'createPickupLocationFromResponse');

        /** @var DeliveryPickupLocation $location */
        $location = $method->invoke($listener, $response);

        $this->assertNull($location->getLatitude());
        $this->assertNull($location->getLongitude());
    }

    /**
     * The Colissimo SOAP web service returns every field as a string
     */
    private function createWebServiceResponse(): \stdClass
    {
        $response = new \stdClass();
        $response->identifiant = '123456';
        $response->nom = 'BUREAU DE POSTE PARIS';
        $response->adresse1 = '12 RUE DE LA PAIX';
        $response->adresse2 = '';
        $response->adresse3 = '';
        $response->localite = 'PARIS';
        $response->codePostal = '75002';
        $response->codePays = 'FR';
        $response->typeDePoint = 'BPR';
        $response->reseau = 'R03';
        $response->coordGeolocalisationLatitude = '48.85661';
        $response->coordGeolocalisationLongitude = '2.35222';
        $response->horairesOuvertureLundi = '09:00-18:00';
        $response->horairesOuvertureMardi = '09:00-18:00';
        $response->horairesOuvertureMercredi = '09:00-18:00';
        $response->horairesOuvertureJeudi = '09:00-18:00';
        $response->horairesOuvertureVendredi = '09:00-18:00';
        $response->horairesOuvertureSamedi = '09:00-12:00';
        $response->horairesOuvertureDimanche = '';

        return $response;
    }
}
