<?php

namespace ColissimoPickupPoint\Loop;

use ColissimoPickupPoint\Model\ColissimoPickupPointAreaFreeshipping;
use ColissimoPickupPoint\Model\ColissimoPickupPointAreaFreeshippingQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class AreaFreeshipping extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('area_id')
        );
    }

    public function buildModelCriteria()
    {
        $areaId = $this->getAreaId();

        $modes = ColissimoPickupPointAreaFreeshippingQuery::create();

        if (null !== $areaId) {
            $modes->filterByAreaId($areaId);
        }

        return $modes;
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var ColissimoPickupPointAreaFreeshipping $mode */
        foreach ($loopResult->getResultDataCollection() as $mode) {
            $loopResultRow = new LoopResultRow($mode);
            $loopResultRow
                ->set('ID', $mode->getId())
                ->set('AREA_ID', $mode->getAreaId())
                ->set('CART_AMOUNT', $mode->getCartAmount());

            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }

}