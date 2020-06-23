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
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Template\ParserInterface;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Model\ConfigQuery;

/**
 * Class SearchCityController
 * @package IciRelais\Controller
 * @author Thelia <info@thelia.net>
 */
class GetSpecificLocation extends BaseFrontController
{
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

    public function search()
    {
        $countryid = $this->getRequest()->query->get('countryid');
        $zipcode = $this->getRequest()->query->get('zipcode');
        $city = $this->getRequest()->query->get('city');
        $addressId = $this->getRequest()->query->get('address');

        return $this->get($countryid, $zipcode, $city, $addressId);
    }

    /**
     * @param null $template
     * @return ParserInterface instance parser
     * @throws Exception
     */
    protected function getParser($template = null)
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
