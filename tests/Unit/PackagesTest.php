<?php

namespace Tests\Unit;

use BitsHost\UpmvcSaas\SaasServiceProvider;
use PHPUnit\Framework\TestCase;

class PackagesTest extends TestCase
{
    public function test_packages_php_registers_saas_provider(): void
    {
        $path = dirname(__DIR__, 2) . '/src/Etc/packages.php';
        $this->assertFileExists($path);

        /** @var list<class-string> $providers */
        $providers = require $path;
        $this->assertIsArray($providers);
        $this->assertContains(SaasServiceProvider::class, $providers);
    }

    public function test_billing_controller_is_autoloadable(): void
    {
        $this->assertTrue(class_exists(\App\Modules\Api\Modules\Billing\Controller::class));
    }
}
