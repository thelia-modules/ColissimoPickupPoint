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

namespace ColissimoPickupPoint\Form;

use ColissimoPickupPoint\ColissimoPickupPoint;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

/**
 * Class ConfigureColissimoPickupPoint
 * @package ColissimoPickupPoint\Form
 * @author Thelia <info@thelia.net>
 */
class ConfigureColissimoPickupPoint extends BaseForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return void
     */
    protected function buildForm(): void
    {
        $translator = Translator::getInstance();
        $this->formBuilder
            ->add(
                ColissimoPickupPoint::COLISSIMO_USERNAME,
                TextType::class,
                [
                    'constraints' => [new NotBlank()],
                    'data'        => ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_USERNAME),
                    'label'       => $translator->trans('Account number', [], ColissimoPickupPoint::DOMAIN),
                    'label_attr'  => ['for' => ColissimoPickupPoint::COLISSIMO_USERNAME]
                ]
            )
            ->add(
                ColissimoPickupPoint::COLISSIMO_PASSWORD,
                PasswordType::class,
                [
                    'constraints' => [new NotBlank()],
                    'data'        => ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_PASSWORD),
                    'label'       => $translator->trans('Password', [], ColissimoPickupPoint::DOMAIN),
                    'label_attr'  => ['for' => ColissimoPickupPoint::COLISSIMO_PASSWORD]
                ]
            )
            ->add(
                ColissimoPickupPoint::COLISSIMO_ENDPOINT,
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Url([
                            'protocols' => ['https', 'http']
                        ])
                    ],
                    'data'        => ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_ENDPOINT),
                    'label'       => $translator->trans('Colissimo URL prod', [], ColissimoPickupPoint::DOMAIN),
                    'label_attr'  => ['for' => ColissimoPickupPoint::COLISSIMO_ENDPOINT]
                ]
            )
            ->add(
                ColissimoPickupPoint::COLISSIMO_GOOGLE_KEY,
                TextType::class,
                [
                    'constraints' => [],
                    'data'        => ColissimoPickupPoint::getConfigValue(ColissimoPickupPoint::COLISSIMO_GOOGLE_KEY),
                    'label'       => $translator->trans('Google map API key', [], ColissimoPickupPoint::DOMAIN),
                    'label_attr'  => ['for' => ColissimoPickupPoint::COLISSIMO_GOOGLE_KEY]
                ]
            )
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public static function getName()
    {
        return 'colissimopickuppoint_configure_form';
    }
}
