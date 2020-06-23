<?php


namespace ColissimoPickupPoint\Loop;


use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshipping;
use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshippingQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class ColissimoPickupPointFreeshippingLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('id')
        );
    }

    public function buildModelCriteria()
    {
        if (null === $isFreeShippingActive = ColissimoPickupPointFreeshippingQuery::create()->findOneById(1)){
            $isFreeShippingActive = new ColissimoPickupPointFreeshipping();
            $isFreeShippingActive->setId(1);
            $isFreeShippingActive->setActive(0);
            $isFreeShippingActive->save();
        }

        return ColissimoPickupPointFreeshippingQuery::create()->filterById(1);
    }

    public function parseResults(LoopResult $loopResult)
    {
        /** @var ColissimoPickupPointFreeshipping $freeshipping */
        foreach ($loopResult->getResultDataCollection() as $freeshipping) {
            $loopResultRow = new LoopResultRow($freeshipping);
            $loopResultRow
                ->set('FREESHIPPING_ACTIVE', $freeshipping->getActive())
                ->set('FREESHIPPING_FROM', $freeshipping->getFreeshippingFrom());
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}