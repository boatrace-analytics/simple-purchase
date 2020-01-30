<?php

namespace Boatrace\Analytics\Tests;

use Boatrace\Analytics\MainSimplePurchaser;
use Boatrace\Analytics\SimplePurchaser;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * @author shimomo
 */
class SimplePurchaserTest extends PHPUnitTestCase
{
    /**
     * @doesNotPerformAssertions
     * @return void
     */
    public function testPurchaser(): void
    {
        SimplePurchaser::setDepositAmount(1000)
            ->setSubscriberNumber(getenv('SUBSCRIBER_NUMBER'))
            ->setPersonalIdentificationNumber(getenv('PERSONAL_IDENTIFICATION_NUMBER'))
            ->setAuthenticationPassword(getenv('AUTHENTICATION_PASSWORD'))
            ->setPurchasePassword(getenv('PURCHASE_PASSWORD'))
            ->purchase(24, 12, [213, 214, 215, 216, 231, 241, 251, 261]);
    }
}
