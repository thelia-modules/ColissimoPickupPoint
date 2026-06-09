<?php

namespace ColissimoPickupPoint\Hook;

use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\Form\ConfigureColissimoPickupPoint;
use ColissimoPickupPoint\Form\ExportOrder;
use ColissimoPickupPoint\Form\FreeShippingForm;
use ColissimoPickupPoint\Form\ImportForm;
use ColissimoPickupPoint\Form\TaxRuleForm;
use ColissimoPickupPoint\Model\ColissimoPickupPointAreaFreeshippingQuery;
use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshipping;
use ColissimoPickupPoint\Model\ColissimoPickupPointFreeshippingQuery;
use ColissimoPickupPoint\Model\ColissimoPickupPointPriceSlicesQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Model\AreaQuery;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusQuery;

class BackHook extends BaseHook
{
    public function __construct(
        private readonly TheliaFormFactory $formFactory,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $request = $this->getRequest();
        $tab = $request?->query->get('current_tab', 'export') ?? 'export';

        $moduleId = (int) ColissimoPickupPoint::getModuleId();

        $event->add($this->render('ColissimoPickupPoint/module_configuration.html.twig', [
            'tab' => $tab,
            'check_rights_errors' => $this->getCheckRightsErrors(),
            'currency_symbol' => $this->getDefaultCurrencySymbol(),
            'export_form' => $this->formFactory->createForm(ExportOrder::getName())->createView()->getView(),
            'config_form' => $this->formFactory->createForm(ConfigureColissimoPickupPoint::getName())->createView()->getView(),
            'import_form' => $this->formFactory->createForm(ImportForm::getName())->createView()->getView(),
            'tax_rule_form' => $this->formFactory->createForm(TaxRuleForm::getName())->createView()->getView(),
            'freeshipping_form' => $this->formFactory->createForm(FreeShippingForm::getName())->createView()->getView(),
            'freeshipping' => $this->getFreeshipping(),
            'not_sent_orders' => $this->getNotSentOrders(),
            'module_id' => $moduleId,
            'areas' => $this->getAreas($moduleId),
        ]));
    }

    public function onModuleConfigJs(HookRenderEvent $event): void
    {
        $event->add($this->render('ColissimoPickupPoint/module-config-js.html.twig'));
    }

    public function renderColishipExport(HookRenderEvent $event): void
    {
        $orderId = (int) $event->getArgument('id', 0);

        if ($orderId === 0) {
            return;
        }

        $order = OrderQuery::create()
            ->filterById($orderId)
            ->filterByStatusId([2, 3], Criteria::IN)
            ->findOne();

        if (null === $order) {
            return;
        }

        if ((int) $order->getDeliveryModuleId() !== (int) ColissimoPickupPoint::getModuleId()) {
            return;
        }

        $event->add($this->render('ColissimoPickupPoint/order-edit-coliship-export.html.twig', [
            'order_id' => $order->getId(),
            'export_form' => $this->formFactory->createForm(ExportOrder::getName())->createView()->getView(),
        ]));
    }

    /**
     * @return array<int, array{ERRMES: string, ERRFILE: string}>
     */
    private function getCheckRightsErrors(): array
    {
        $errors = [];
        $dir = __DIR__ . '/../Config/';

        if (!is_readable($dir)) {
            $errors[] = ['ERRMES' => $this->trans("Can't read Config directory"), 'ERRFILE' => ''];

            return $errors;
        }

        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if (\strlen($file) > 5 && str_ends_with($file, '.json') && !is_readable($dir . $file)) {
                    $errors[] = ['ERRMES' => $this->trans("Can't read file"), 'ERRFILE' => 'ColissimoPickupPoint/Config/' . $file];
                }
            }
            closedir($handle);
        }

        return $errors;
    }

    private function getDefaultCurrencySymbol(): string
    {
        $currency = CurrencyQuery::create()->filterByByDefault(1)->findOne()
            ?? CurrencyQuery::create()->findOne();

        return $currency?->getSymbol() ?? '';
    }

    /**
     * @return array{active: bool, from: float|null}
     */
    private function getFreeshipping(): array
    {
        $freeshipping = ColissimoPickupPointFreeshippingQuery::create()->findOneById(1);

        if (null === $freeshipping) {
            $freeshipping = new ColissimoPickupPointFreeshipping();
            $freeshipping->setId(1);
            $freeshipping->setActive(0);
            $freeshipping->save();
        }

        return [
            'active' => (bool) $freeshipping->getActive(),
            'from' => $freeshipping->getFreeshippingFrom(),
        ];
    }

    /**
     * @return array<int, array{id: int, ref: string, customer_id: int|null, customer_name: string, create_date: \DateTime|null, total: float, currency_symbol: string}>
     */
    private function getNotSentOrders(): array
    {
        $status = OrderStatusQuery::create()
            ->filterByCode([OrderStatus::CODE_PAID, OrderStatus::CODE_PROCESSING], Criteria::IN)
            ->find()
            ->toArray('code');

        $orders = OrderQuery::create()
            ->filterByDeliveryModuleId((int) ColissimoPickupPoint::getModuleId())
            ->filterByStatusId([$status[OrderStatus::CODE_PAID]['Id'], $status[OrderStatus::CODE_PROCESSING]['Id']], Criteria::IN)
            ->find();

        $rows = [];

        foreach ($orders as $order) {
            $customer = $order->getCustomer();
            $tax = 0.0;
            $rows[] = [
                'id' => $order->getId(),
                'ref' => $order->getRef(),
                'customer_id' => $customer?->getId(),
                'customer_name' => null !== $customer
                    ? trim($customer->getFirstname() . ' ' . $customer->getLastname())
                    : $this->trans('Unknown customer'),
                'create_date' => $order->getCreatedAt(),
                'total' => $order->getTotalAmount($tax, true, true),
                'currency_symbol' => $order->getCurrency()?->getSymbol() ?? '',
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{id: int, name: string, freeshipping_amount: float|string|null, slices: array<int, array{slice_id: int, max_weight: float|null, max_price: float|null, price: float|null}>}>
     */
    private function getAreas(int $moduleId): array
    {
        $areas = AreaQuery::create()
            ->useAreaDeliveryModuleQuery()
                ->filterByDeliveryModuleId([$moduleId], Criteria::IN)
            ->endUse()
            ->find();

        $result = [];

        foreach ($areas as $area) {
            $areaFreeshipping = ColissimoPickupPointAreaFreeshippingQuery::create()
                ->filterByAreaId($area->getId())
                ->findOne();

            $slices = [];
            $prices = ColissimoPickupPointPriceSlicesQuery::create()
                ->filterByAreaId($area->getId())
                ->orderByWeightMax()
                ->find();

            foreach ($prices as $price) {
                $slices[] = [
                    'slice_id' => $price->getId(),
                    'max_weight' => $price->getWeightMax(),
                    'max_price' => $price->getPriceMax(),
                    'price' => $price->getPrice(),
                ];
            }

            $result[] = [
                'id' => $area->getId(),
                'name' => $area->getName(),
                'freeshipping_amount' => $areaFreeshipping?->getCartAmount(),
                'slices' => $slices,
            ];
        }

        return $result;
    }

    private function trans(string $id): string
    {
        return $this->translator->trans($id, [], ColissimoPickupPoint::DOMAIN);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                [
                    'type' => 'back',
                    'method' => 'onModuleConfiguration',
                ],
            ],
            'module.config-js' => [
                [
                    'type' => 'back',
                    'method' => 'onModuleConfigJs',
                ],
            ],
            'order.tab-content' => [
                [
                    'type' => 'back',
                    'method' => 'renderColishipExport',
                ],
            ],
        ];
    }
}
