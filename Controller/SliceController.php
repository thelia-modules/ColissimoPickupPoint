<?php

namespace ColissimoPickupPoint\Controller;

use Exception;
use Propel\Runtime\Map\TableMap;
use ColissimoPickupPoint\Model\ColissimoPickupPointPriceSlices;
use ColissimoPickupPoint\Model\ColissimoPickupPointPriceSlicesQuery;
use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Core\Translation\Translator;

/**
 * @Route("/admin/module/ColissimoPickupPoint/slice/", name="colissimo_pickup_point_slice_")
 */
class SliceController extends BaseAdminController
{
    /**
     * @Route("save", name="price_save", methods="POST")
     */
    public function saveSliceAction(RequestStack $requestStack, Translator $translator)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), ['ColissimoPickupPoint'], AccessManager::UPDATE)) {
            return $response;
        }

        $this->checkXmlHttpRequest();

        $responseData = [
            'success' => false,
            'message' => '',
            'slice' => null
        ];

        $messages = [];
        $response = null;

        try {
            $requestData = $requestStack->getCurrentRequest()->request;

            if (0 !== $id = (int)$requestData->get('id', 0)) {
                $slice = ColissimoPickupPointPriceSlicesQuery::create()->findPk($id);
            } else {
                $slice = new ColissimoPickupPointPriceSlices();
            }


            if (0 !== $areaId = (int)$requestData->get('area', 0)) {
                $slice->setAreaId($areaId);
            } else {
                $messages[] = $translator->trans(
                    'The area is not valid',
                    [],
                    ColissimoPickupPoint::DOMAIN
                );
            }

            $requestPriceMax = $requestData->get('priceMax', null);
            $requestWeightMax = $requestData->get('weightMax', null);

            if (empty($requestPriceMax) && empty($requestWeightMax)) {
                $messages[] = $translator->trans(
                    'You must specify at least a price max or a weight max value.',
                    [],
                    ColissimoPickupPoint::DOMAIN
                );
            } else {
                if (!empty($requestPriceMax)) {
                    $priceMax = $this->getFloatVal($requestPriceMax);
                    if (0 < $priceMax) {
                        $slice->setPriceMax($priceMax);
                    } else {
                        $messages[] = $translator->trans(
                            'The price max value is not valid',
                            [],
                            ColissimoPickupPoint::DOMAIN
                        );
                    }
                } else {
                    $slice->setPriceMax(null);
                }

                if (!empty($requestWeightMax)) {
                    $weightMax = $this->getFloatVal($requestWeightMax);
                    if (0 < $weightMax) {
                        $slice->setWeightMax($weightMax);
                    } else {
                        $messages[] = $translator->trans(
                            'The weight max value is not valid',
                            [],
                            ColissimoPickupPoint::DOMAIN
                        );
                    }
                } else {
                    $slice->setWeightMax(null);
                }
            }



            $price = $this->getFloatVal($requestData->get('price', 0));
            if (0 <= $price) {
                $slice->setPrice($price);
            } else {
                $messages[] = $translator->trans(
                    'The price value is not valid',
                    [],
                    ColissimoPickupPoint::DOMAIN
                );
            }

            if (0 === count($messages)) {
                $slice->save();
                $messages[] = $translator->trans(
                    'Your slice has been saved',
                    [],
                    ColissimoPickupPoint::DOMAIN
                );

                $responseData['success'] = true;
                $responseData['slice'] = $slice->toArray(TableMap::TYPE_STUDLYPHPNAME);
            }
        } catch (Exception $e) {
            $message[] = $e->getMessage();
        }

        $responseData['message'] = $messages;

        return $this->jsonResponse(json_encode($responseData));
    }

    protected function getFloatVal($val, $default = -1)
    {
        if (preg_match("#^([0-9\.,]+)$#", $val, $match)) {
            $val = $match[0];
            if (strstr($val, ",")) {
                $val = str_replace(".", "", $val);
                $val = str_replace(",", ".", $val);
            }
            return (float)$val;
        }

        return $default;
    }

    /**
     * @Route("delete", name="price_delete", methods="POST")
     */
    public function deleteSliceAction(RequestStack $requestStack, Translator $translator)
    {
        $response = $this->checkAuth([], ['ColissimoPickupPoint'], AccessManager::DELETE);

        if (null !== $response) {
            return $response;
        }

        $this->checkXmlHttpRequest();

        $responseData = [
            'success' => false,
            'message' => '',
            'slice' => null
        ];

        $response = null;

        try {
            $requestData = $requestStack->getCurrentRequest()->request;

            if (0 !== $id = (int)$requestData->get('id', 0)) {
                $slice = ColissimoPickupPointPriceSlicesQuery::create()->findPk($id);
                $slice->delete();
                $responseData['success'] = true;
            } else {
                $responseData['message'] = $translator->trans(
                    'The slice has not been deleted',
                    [],
                    ColissimoPickupPoint::DOMAIN
                );
            }
        } catch (Exception $e) {
            $responseData['message'] = $e->getMessage();
        }

        return $this->jsonResponse(json_encode($responseData));
    }
}
