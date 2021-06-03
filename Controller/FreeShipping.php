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

namespace ColissimoPickupPoint\Controller;

use ColissimoPickupPoint\Form\FreeShippingForm;
use ColissimoPickupPoint\Model\ColissimoPickupPointAreaFreeshipping;
use ColissimoPickupPoint\Model\ColissimoPickupPointAreaFreeshippingQuery;
use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshipping;
use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshippingQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Response;

use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;
use Thelia\Model\AreaQuery;

class FreeShipping extends BaseAdminController
{
    /**
     * Toggle on or off free shipping for all areas without minimum cart amount, or set the minimum cart amount to reach for all areas to get free shipping
     *
     * @return mixed|JsonResponse|Response|null
     */
    public function toggleFreeShippingActivation()
    {
        if (null !== $response = $this
                ->checkAuth(array(AdminResources::MODULE), array('ColissimoPickupPoint'), AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(FreeShippingForm::getName());
        $response=null;

        try {
            $vform = $this->validateForm($form);
            $freeshipping = $vform->get('freeshipping')->getData();
            $freeshippingFrom = $vform->get('freeshipping_from')->getData();

            if (null === $deliveryFreeshipping = ColissimoPickupPointFreeshippingQuery::create()->findOneById(1)){
                $deliveryFreeshipping = new ColissimoPickupPointFreeshipping();
            }

            $deliveryFreeshipping
                ->setActive($freeshipping)
                ->setFreeshippingFrom($freeshippingFrom)
                ->save()
            ;

            $response = $this->generateRedirectFromRoute(
                'admin.module.configure',
                array(),
                array (
                    'current_tab'=> 'prices_slices_tab',
                    'module_code'=> 'ColissimoPickupPoint',
                    '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction',
                    'price_error_id' => null,
                    'price_error' => null
                )
            );
        } catch (\Exception $e) {
            $response = JsonResponse::create(array('error' => $e->getMessage()), 500);
        }

        return $response;
    }

    /**
     * @return mixed|null|\Symfony\Component\HttpFoundation\Response
     */
    public function setAreaFreeShipping()
    {
        if (null !== $response = $this
                ->checkAuth(array(AdminResources::MODULE), array('ColissimoPickupPoint'), AccessManager::UPDATE)) {
            return $response;
        }

        $data = $this->getRequest()->request;

        try {
            $data = $this->getRequest()->request;

            $colissimo_pickup_area_id = $data->get('area-id');
            $cartAmount = $data->get('cart-amount');

            if ($cartAmount < 0 || $cartAmount === '') {
                $cartAmount = null;
            }

            $aeraQuery = AreaQuery::create()->findOneById($colissimo_pickup_area_id);
            if (null === $aeraQuery) {
                return null;
            }

            $colissimoPickupPointAreaFreeshippingQuery = ColissimoPickupPointAreaFreeshippingQuery::create()
                ->filterByAreaId($colissimo_pickup_area_id)
                ->findOne();

            if (null === $colissimoPickupPointAreaFreeshippingQuery) {
                $colissimoPickupPointFreeShipping = new ColissimoPickupPointAreaFreeshipping();

                $colissimoPickupPointFreeShipping
                    ->setAreaId($colissimo_pickup_area_id)
                    ->setCartAmount($cartAmount)
                    ->save();
            }

            $cartAmountQuery = ColissimoPickupPointAreaFreeshippingQuery::create()
                ->filterByAreaId($colissimo_pickup_area_id)
                ->findOneOrCreate()
                ->setCartAmount($cartAmount)
                ->save();

        } catch (\Exception $e) {
        }

        return $this->generateRedirectFromRoute(
            'admin.module.configure',
            array(),
            array(
                'current_tab' => 'prices_slices_tab',
                'module_code' => 'ColissimoPickupPoint',
                '_controller' => 'Thelia\\Controller\\Admin\\ModuleController::configureAction',
                'price_error_id' => null,
                'price_error' => null
            )
        );
    }
}
