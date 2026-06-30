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

namespace ColissimoPickupPoint\Controller;

use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\WebService\FindById;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Controller\Front\BaseFrontController;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Core\Template\ParserInterface;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Model\ConfigQuery;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class SearchCityController
 * @package IciRelais\Controller
 * @author Thelia <info@thelia.net>
 */
class GetSpecificLocation extends BaseFrontController
{
    /**
     * @Route("{countryid}/{zipcode}/{city}/{address}", name="get_location", methods="GET")
     */
    #[Route('/module/ColissimoPickupPoint/', name: 'specific_location_')]
    public function get($countryid, $zipcode, $city, $address="")
    {
        $content = $this->renderRaw(
            'getSpecificLocationColissimoPickupPoint',
            array(
                '_countryid_' => $countryid,
                '_zipcode_' => $zipcode,
                '_city_' => $city,
                '_address_' => $address
            )
        );
        $response = new Response($content, 200, $headers = array('Content-Type' => 'application/json'));

        return $response;
    }

    /**
     */
    #[Route('point/{point_id}', name: 'get_point_info')]
    public function getPointInfo($point_id)
    {
        $req = new FindById();

        $req->setId($point_id)
            ->setLangue('FR')
            ->setDate(date('d/m/Y'))
            ->setAccountNumber(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_USERNAME))
            ->setPassword(ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_PASSWORD))
        ;

        $response = $req->exec();

        $response = new JsonResponse($response);

        return $response;
    }

    /**
     */
    #[Route('points', name: 'search')]
    public function search(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        $countryid = $request->attributes->get('countryid', $request->query->get('countryid', $request->request->get('countryid')));
        $zipcode = $request->attributes->get('zipcode', $request->query->get('zipcode', $request->request->get('zipcode')));
        $city = $request->attributes->get('city', $request->query->get('city', $request->request->get('city')));
        $addressId = $request->attributes->get('address', $request->query->get('address', $request->request->get('address')));

        return $this->get($countryid, $zipcode, $city, $addressId);
    }

    /**
     * @param null $template
     * @return object|null $parser
     */
    protected function getParser($template = null): ParserInterface
    {
        $parser = $this->container->get('thelia.parser');

        // Define the template that should be used
        $parser->setTemplateDefinition(
            new TemplateDefinition(
                'default',
                TemplateDefinition::FRONT_OFFICE
            )
        );

        return $parser;
    }
}
