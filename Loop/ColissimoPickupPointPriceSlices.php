<?php

namespace ColissimoPickupPoint\Loop;

use ColissimoPickupPoint\Model\ColissimoPickupPointPriceSlicesQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class ColissimoPickupPointPriceSlices extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('area_id', null, true)
        );
    }

    public function buildModelCriteria()
    {
        $areaId = $this->getAreaId();

        $areaPrices = ColissimoPickupPointPriceSlicesQuery::create()
            ->filterByAreaId($areaId)
            ->orderByWeightMax();

        return $areaPrices;
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var \ColissimoPickupPoint\Model\ColissimoPickupPointPriceSlices $price */
        foreach ($loopResult->getResultDataCollection() as $price) {
            $loopResultRow = new LoopResultRow($price);
            $loopResultRow
                ->set('SLICE_ID', $price->getId())
                ->set('MAX_WEIGHT', $price->getWeightMax())
                ->set('MAX_PRICE', $price->getPriceMax())
                ->set('PRICE', $price->getPrice())
                ->set('FRANCO', $price->getFrancoMinPrice())
            ;
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
