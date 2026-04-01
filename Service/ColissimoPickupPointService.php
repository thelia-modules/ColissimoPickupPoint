<?php

namespace ColissimoPickupPoint\Service;
use ColissimoPickupPoint\Model\AddressColissimoPickupPoint;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Security\SecurityContext;
use Thelia\Model\CountryQuery;

class ColissimoPickupPointService
{
    public function __construct(
        private readonly Session $session,
        private readonly SecurityContext $security
    ) {}

    public function saveAddress(
        ?string $company,
        ?string $address1,
        ?string $address2 = null,
        ?string $address3 = null,
        string $countryIsoAlpha2,
        string $zipCode,
        string $city,
        string $code,
        string $type,
    ): AddressColissimoPickupPoint {
        $addr = new AddressColissimoPickupPoint();
        $countryId = CountryQuery::create()->filterByIsoalpha2($countryIsoAlpha2)->findOne()->getId();
        $customer = $this->security->getCustomerUser();
        $addr
            ->setCompany($company)
            ->setTitleId($customer->getTitleId())
            ->setFirstname($customer->getFirstname())
            ->setLastname($customer->getLastname())
            ->setAddress1($address1)
            ->setAddress2($address2)
            ->setAddress3($address3)
            ->setCountryId($countryId)
            ->setZipCode($zipCode)
            ->setCity($city)
            ->setCode($code)
            ->setType($type)
            ->save()
        ;

        $this->session->set('ColissimoPickupPointDeliveryId', $addr->getId());

        return $addr;
    }
}
