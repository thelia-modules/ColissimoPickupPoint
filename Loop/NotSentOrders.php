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

use Propel\Runtime\ActiveQuery\Criteria;
use ColissimoPickupPoint\ColissimoPickupPoint;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\OrderQuery;
use Thelia\Core\Template\Loop\Order;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusQuery;

/**
 * Class NotSentOrders
 * @package ColissimoPickupPoint\Loop
 * @author Thelia <info@thelia.net>
 */
class NotSentOrders extends Order
{
    public function getArgDefinitions()
    {
        return new ArgumentCollection(Argument::createBooleanTypeArgument('with_prev_next_info', false));
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $status = OrderStatusQuery::create()
            ->filterByCode(
                array(
                    OrderStatus::CODE_PAID,
                    OrderStatus::CODE_PROCESSING,
                ),
                Criteria::IN
            )
            ->find()
            ->toArray('code');
        $query = OrderQuery::create()
            ->filterByDeliveryModuleId(ColissimoPickupPoint::getModCode())
            ->filterByStatusId(
                array(
                    $status[OrderStatus::CODE_PAID]['Id'],
                    $status[OrderStatus::CODE_PROCESSING]['Id']),
                Criteria::IN
            );

        return $query;
    }
}
