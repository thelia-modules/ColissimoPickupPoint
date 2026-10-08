<?php

namespace ColissimoPickupPoint\Tests;

use ColissimoPickupPoint\ColissimoPickupPoint;
use ColissimoPickupPoint\Hook\PdfHook;
use PHPUnit\Framework\TestCase;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Translation\Translator;
use Thelia\Module\BaseModule;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Run from a Thelia install:
 * vendor/bin/phpunit --bootstrap vendor/autoload.php vendor/thelia/modules/ColissimoPickupPoint/tests
 */
class PdfHookTest extends TestCase
{
    private const MODULE_ID = 7;

    private const PDF_TEMPLATES_DIRECTORY = __DIR__ . '/../templates/pdf/default/';

    public static function setUpBeforeClass(): void
    {
        spl_autoload_register(function (string $class): void {
            if (str_starts_with($class, 'ColissimoPickupPoint\\')) {
                $file = \dirname(__DIR__) . '/' . str_replace('\\', '/', substr($class, \strlen('ColissimoPickupPoint\\'))) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        });

        // The id the module has in the shop, as BaseModule::getModuleId() caches it once read.
        $moduleIds = new \ReflectionProperty(BaseModule::class, 'moduleIds');
        $moduleIds->setValue(null, [ColissimoPickupPoint::getModuleCode() => self::MODULE_ID] + $moduleIds->getValue());
        // The module instance a hook reads from the shop at construction: not needed by this hook.
        $moduleClassNames = new \ReflectionProperty(BaseHook::class, 'moduleClassNames');
        $moduleClassNames->setValue(null, [ColissimoPickupPoint::getModuleCode() => null] + $moduleClassNames->getValue());
        // A hook takes the translator of the shop at construction, unused here.
        $translator = new \ReflectionProperty(Translator::class, 'instance');
        if (!$translator->getValue() instanceof Translator) {
            $translator->setValue(null, (new \ReflectionClass(Translator::class))->newInstanceWithoutConstructor());
        }
    }

    /**
     * The invoice of an order delivered at home (or by any other module) gets no relay block, and its rendering does
     * not go through the template at all.
     */
    public function testAnOrderNotDeliveredAtARelayGetsNothing(): void
    {
        $event = new HookRenderEvent('invoice.after-delivery-module', ['order' => 12, 'module_id' => self::MODULE_ID + 1]);

        (new PdfHook())->onInvoiceAfterDeliveryModule($event);

        $this->assertSame('', $event->dump());
    }

    public function testTheTemplateIsATwigTemplate(): void
    {
        $this->assertFileExists(self::PDF_TEMPLATES_DIRECTORY . 'delivery_mode_infos.html.twig');
    }

    public function testTheRelayBlockShowsTheRelayAddress(): void
    {
        $html = $this->renderTemplate(relayRows: [['ID' => 42, 'CODE' => '012345', 'TYPE' => 'A2P']]);

        $this->assertStringContainsString('Delivered at a relay.', $html);
        $this->assertStringContainsString('Relay Shop', $html);
        $this->assertStringContainsString('75001 Paris', $html);
        $this->assertStringContainsString('France', $html);
    }

    public function testAnAddressWithoutRelayRowRendersNothing(): void
    {
        $this->assertSame('', trim($this->renderTemplate(relayRows: [])));
    }

    /**
     * @param list<array<string, mixed>> $relayRows
     */
    private function renderTemplate(array $relayRows): string
    {
        $rows = [
            'colissimo.pickup.point.order_address' => $relayRows,
            'order_address' => [['COMPANY' => 'Relay Shop', 'ADDRESS1' => '1 rue de Rivoli', 'ADDRESS2' => '', 'ADDRESS3' => '', 'ZIPCODE' => '75001', 'CITY' => 'Paris', 'COUNTRY' => 64]],
            'country' => [['TITLE' => 'France']],
        ];

        $twig = new Environment(new FilesystemLoader(self::PDF_TEMPLATES_DIRECTORY), ['strict_variables' => true]);
        $twig->addFunction(new TwigFunction('loop', static fn (string $name, string $type, array $arguments): array => $rows[$type]));
        $twig->addFilter(new TwigFilter('trans', static fn (string $message): string => $message));

        return $twig->render('delivery_mode_infos.html.twig', ['delivery_address_id' => 42, 'locale' => 'fr_FR']);
    }
}
