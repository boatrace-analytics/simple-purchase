<?php

namespace Boatrace\Analytics\Tests;

use Boatrace\Analytics\MainSimplePurchaser;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * @author shimomo
 */
class MainSimplePurchaserTest extends PHPUnitTestCase
{
    /**
     * @var \Boatrace\Analytics\MainSimplePurchaser
     */
    protected $simplePurchaser;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->simplePurchaser = new MainSimplePurchaser;
    }

    /**
     * @doesNotPerformAssertions
     * @return void
     */
    public function testPurchaser(): void
    {
        $this->simplePurchaser
            ->setDepositAmount(1000)
            ->setSubscriberNumber(getenv('SUBSCRIBER_NUMBER'))
            ->setPersonalIdentificationNumber(getenv('PERSONAL_IDENTIFICATION_NUMBER'))
            ->setAuthenticationPassword(getenv('AUTHENTICATION_PASSWORD'))
            ->setPurchasePassword(getenv('PURCHASE_PASSWORD'))
            ->purchase(24, 12, [213, 214, 215, 216, 231, 241, 251, 261]);
    }
}
