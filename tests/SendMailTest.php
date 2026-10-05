<?php

namespace ColissimoPickupPoint\Tests;

use ColissimoPickupPoint\Listener\SendMail;
use PHPUnit\Framework\TestCase;
use Thelia\Core\Template\ParserInterface;

/**
 * Run from a Thelia install:
 * vendor/bin/phpunit --bootstrap vendor/autoload.php vendor/thelia/modules/ColissimoPickupPoint/tests
 */
class SendMailTest extends TestCase
{
    private const MAIL_TEMPLATES_DIRECTORY = __DIR__ . '/../templates/email/default/';

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
    }

    /**
     * Thelia 3 binds ParserInterface to a fallback that throws on assign(): the variables of the mail go through
     * the mailer, as parameters of the message.
     */
    public function testListenerDoesNotDependOnTheParser(): void
    {
        $types = array_map(
            static fn (\ReflectionParameter $parameter): string => (string) $parameter->getType(),
            (new \ReflectionClass(SendMail::class))->getConstructor()->getParameters()
        );

        $this->assertNotContains(ParserInterface::class, $types);
    }

    public function testShippingMailIsWrittenInTwig(): void
    {
        foreach (['order_shipped_pp.html.twig', 'order_shipped_pp.txt.twig'] as $template) {
            $this->assertFileExists(self::MAIL_TEMPLATES_DIRECTORY . $template);
            $this->assertDoesNotMatchRegularExpression('/\{(intl|loop|\$|assign|extends file)/', file_get_contents(self::MAIL_TEMPLATES_DIRECTORY . $template));
        }

        $this->assertFileDoesNotExist(self::MAIL_TEMPLATES_DIRECTORY . 'order_shipped_pp.html');
        $this->assertFileDoesNotExist(self::MAIL_TEMPLATES_DIRECTORY . 'order_shipped_pp.txt');
    }
}
