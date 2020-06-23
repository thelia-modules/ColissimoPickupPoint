<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace ColissimoPickupPoint\Loop;

use ColissimoPickupPoint\Model\OrderAddressColissimoPickupPoint;
use ColissimoPickupPoint\Model\OrderAddressColissimoPickupPointQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class ColissimoPickupPointOrderAddressLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Definition of loop arguments
     *
     * example :
     *
     * public function getArgDefinitions()
     * {
     *  return new ArgumentCollection(
     *
     *       Argument::createIntListTypeArgument('id'),
     *           new Argument(
     *           'ref',
     *           new TypeCollection(
     *               new Type\AlphaNumStringListType()
     *           )
     *       ),
     *       Argument::createIntListTypeArgument('category'),
     *       Argument::createBooleanTypeArgument('new'),
     *       ...
     *   );
     * }
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntTypeArgument('id', null, true)
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = OrderAddressColissimoPickupPointQuery::create();

        if (($id = $this->getId()) !== null) {
            $query->filterById((int)$id);
        }

        return $query;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var OrderAddressColissimoPickupPoint $orderAddressColissimoPickupPoint */
        foreach ($loopResult->getResultDataCollection() as $orderAddressColissimoPickupPoint) {
            $row = new LoopResultRow();
            $row->set('ID', $orderAddressColissimoPickupPoint->getId());
            $row->set('CODE', $orderAddressColissimoPickupPoint->getCode());
            $row->set('TYPE', $orderAddressColissimoPickupPoint->getType());
            $loopResult->addRow($row)
            ;
        }

        return $loopResult;
    }
}
