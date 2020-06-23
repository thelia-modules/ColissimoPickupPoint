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

namespace ColissimoPickupPoint\Loop;
use ColissimoPickupPoint\ColissimoPickupPoint;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * class ColissimoPickupPointId
 * @package ColissimoPickupPoint\Loop
 * @author Thelia <info@thelia.net>
 */
class ColissimoPickupPointId extends BaseLoop implements ArraySearchLoopInterface
{
    /**
     * this method returns an array
     *
     * @return array
     */
    public function buildArray()
    {
        return array(ColissimoPickupPoint::getModCode());
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $id) {
            $loopResultRow = new LoopResultRow();
            $loopResult->addRow(
                $loopResultRow->set('MODULE_ID', $id)
            );
        }

        return $loopResult;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection();
    }

}
