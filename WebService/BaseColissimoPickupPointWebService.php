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

namespace ColissimoPickupPoint\WebService;

use ColissimoPickupPoint\ColissimoPickupPoint;

/**
 * Class BaseColissimoPickupPointWebService
 * @package ColissimoPickupPoint\WebService
 * @author Thelia <info@thelia.net>
 *
 * @method BaseColissimoPickupPointWebService getAccountNumber()
 * @method BaseColissimoPickupPointWebService setAccountNumber($value)
 * @method BaseColissimoPickupPointWebService getPassword()
 * @method BaseColissimoPickupPointWebService setPassword($value)
 * @method BaseColissimoPickupPointWebService getWeight()
 * @method BaseColissimoPickupPointWebService setWeight($value)
 */
abstract class BaseColissimoPickupPointWebService extends BaseWebService
{

    protected $account_number=null;
    protected $password=null;
    protected $filter_relay=null;
    /** @var string Weight in grammes !*/
    protected $weight=null;

    public function __construct($function)
    {
        $url = ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_ENDPOINT);

        parent::__construct($url, $function);
    }
}
