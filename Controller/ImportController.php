<?php

namespace ColissimoPickupPoint\Controller;

use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;
use ColissimoPickupPoint\ColissimoPickupPoint;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Map\OrderTableMap;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatusQuery;
use Thelia\Tools\URL;

/**
 * Class ImportController
 * @package ColissimoPickupPoint\Controller
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class ImportController extends BaseAdminController
{
    public function importAction()
    {
        $i = 0;

        $con = Propel::getWriteConnection(OrderTableMap::DATABASE_NAME);
        $con->beginTransaction();

        $form = $this->createForm('colissimo.pickup.point.import');

        try {
            $vForm = $this->validateForm($form);

            // Get file
            $importedFile = $vForm->getData()['import_file'];

            // Check extension
            if (strtolower($importedFile->getClientOriginalExtension()) !='csv') {
                throw new FormValidationException(
                    Translator::getInstance()->trans('Bad file format. CSV expected.',
                        [],
                        ColissimoPickupPoint::DOMAIN)
                );
            }

            $csvData = file_get_contents($importedFile);
            $lines = explode(PHP_EOL, $csvData);

            // For each line, parse columns
            foreach ($lines as $line) {
                $parsedLine = str_getcsv($line, ";");

                // Get delivery and order ref
                $deliveryRef = $parsedLine[ColissimoPickupPoint::IMPORT_DELIVERY_REF_COL];
                $orderRef = $parsedLine[ColissimoPickupPoint::IMPORT_ORDER_REF_COL];

                // Save delivery ref if there is one
                if (!empty($deliveryRef)) {
                    $this->importDeliveryRef($deliveryRef, $orderRef, $i);
                }
            }

            $con->commit();

            // Get number of affected rows to display
            $this->getSession()->getFlashBag()->add(
                'import-result',
                Translator::getInstance()->trans(
                    'Operation successful. %i orders affected.',
                    ['%i' => $i],
                    ColissimoPickupPoint::DOMAIN
                )
            );

            // Redirect
            return $this->generateRedirect(URL::getInstance()->absoluteUrl($form->getSuccessUrl(), ['current_tab' => 'import']));
        } catch (FormValidationException $e) {
            $con->rollback();

            $this->setupFormErrorContext(null, $e->getMessage(), $form);

            return $this->render(
                'module-configure',
                [
                    'module_code' => ColissimoPickupPoint::getModuleCode(),
                    'current_tab' => 'import'
                ]
            );
        }
    }

    /**
     * Update order's delivery ref
     *
     * @param string $deliveryRef
     * @param string $orderRef
     * @param int $i
     * @throws PropelException
     */
    public function importDeliveryRef($deliveryRef, $orderRef, &$i)
    {
        // Check if the order exists
        if (null !== $order = OrderQuery::create()->findOneByRef($orderRef)) {
            $event = new OrderEvent($order);

            // Check if delivery refs are different
            if ($order->getDeliveryRef() != $deliveryRef) {
                $event->setDeliveryRef($deliveryRef);
                $this->getDispatcher()->dispatch(TheliaEvents::ORDER_UPDATE_DELIVERY_REF, $event);

                $sentStatusId = OrderStatusQuery::create()
                    ->filterByCode('sent')
                    ->select('ID')
                    ->findOne();

                // Set 'sent' order status if not already sent
                if ($sentStatusId != null && $order->getStatusId() != $sentStatusId) {
                    $event->setStatus($sentStatusId);
                    $this->getDispatcher()->dispatch(TheliaEvents::ORDER_UPDATE_STATUS, $event);
                }

                $i++;
            }
        }
    }
}
