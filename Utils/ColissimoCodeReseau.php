<?php

namespace ColissimoPickupPoint\Utils;

use ColissimoPickupPoint\ColissimoPickupPoint;

/**
 * Class ColissimoCodeReseau
 * @package ColissimoPickupPoint\Utils
 */
class ColissimoCodeReseau
{
    const CODE_RESEAU_ARRAY =
        [
            'DE' =>
                [
                    'CMT' => 'R03',
                    'BDP' => 'X00',
                    'PCS' => 'X00'
                ],
            'ES' =>
                [
                    'CMT' => 'R03',
                    'BDP' => 'X00',
                ],
            'GB' =>
                [
                    'CMT' => 'R03'
                ],
            'LU' =>
                [
                    'CMT' => 'R03'
                ],
            'NL' =>
                [
                    'BDP' => 'X00',
                    'CMT' => 'R03',
                ],
            'BE' =>
                [
                    'BDP' => 'R12',
                    'CMT' => 'R12',
                ]
        ];

    public static function getCodeReseau($countryCode, $relayTypeCode)
    {
        if (array_key_exists($countryCode, self::CODE_RESEAU_ARRAY)) {
            $innerArray = self::CODE_RESEAU_ARRAY[$countryCode];
            if (array_key_exists($relayTypeCode, $innerArray)) {
                return $innerArray[$relayTypeCode];
            }
        }
        return null;
    }
}
