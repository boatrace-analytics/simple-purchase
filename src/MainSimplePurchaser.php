<?php

namespace Boatrace\Analytics;

use DI\Container;
use DI\ContainerBuilder;
use Boatrace\Analytics\Exceptions\SimplePurchaserException;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

/**
 * @author shimomo
 */
class MainSimplePurchaser
{
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected $driver;

    /**
     * @var int
     */
    protected $depositAmount;

    /**
     * @var string
     */
    protected $subscriberNumber;

    /**
     * @var string
     */
    protected $personalIdentificationNumber;

    /**
     * @var string
     */
    protected $authenticationPassword;

    /**
     * @var string
     */
    protected $purchasePassword;

    /**
     * @return void
     */
    public function __construct()
    {
        $options = $this->getContainer()->get('ChromeOptions');
        $options->addArguments(['--headless']);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        $this->driver = RemoteWebDriver::create('http://localhost:4444/wd/hub', $capabilities);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->driver->quit();
    }

    /**
     * @param  int  $depositAmount
     * @return \Boatrace\Analytics\MainSimplePurchaser
     */
    public function setDepositAmount(int $depositAmount): MainSimplePurchaser
    {
        $this->depositAmount = $depositAmount;

        return $this;
    }

    /**
     * @param  string  $subscriberNumber
     * @return \Boatrace\Analytics\MainSimplePurchaser
     */
    public function setSubscriberNumber(string $subscriberNumber): MainSimplePurchaser
    {
        $this->subscriberNumber = $subscriberNumber;

        return $this;
    }

    /**
     * @param  string  $personalIdentificationNumber
     * @return \Boatrace\Analytics\MainSimplePurchaser
     */
    public function setPersonalIdentificationNumber(string $personalIdentificationNumber): MainSimplePurchaser
    {
        $this->personalIdentificationNumber = $personalIdentificationNumber;

        return $this;
    }

    /**
     * @param  string  $authenticationPassword
     * @return \Boatrace\Analytics\MainSimplePurchaser
     */
    public function setAuthenticationPassword(string $authenticationPassword): MainSimplePurchaser
    {
        $this->authenticationPassword = $authenticationPassword;

        return $this;
    }

    /**
     * @param  string  $purchasePassword
     * @return \Boatrace\Analytics\MainSimplePurchaser
     */
    public function setPurchasePassword(string $purchasePassword): MainSimplePurchaser
    {
        $this->purchasePassword = $purchasePassword;

        return $this;
    }

    /**
     * @param  int    $stadiumId
     * @param  int    $raceNumber
     * @param  array  $forecasts
     * @return void
     */
    public function purchase(int $stadiumId, int $raceNumber, array $forecasts): void
    {
        $this->driver->get('https://ib.mbrace.or.jp/');

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('memberNo')));
        $this->driver->findElement(WebDriverBy::name('memberNo'))->sendKeys($this->subscriberNumber);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('pin')));
        $this->driver->findElement(WebDriverBy::name('pin'))->sendKeys($this->personalIdentificationNumber);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('authPassword')));
        $this->driver->findElement(WebDriverBy::name('authPassword'))->sendKeys($this->authenticationPassword);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('loginButton')));
        $this->driver->findElement(WebDriverBy::id('loginButton'))->submit();

        $handles = $this->driver->getWindowHandles();
        $this->driver->switchTo()->window($handles[array_key_last($handles)]);

        try {
            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('newsoverviewdispCloseButton')));
            $this->driver->findElement(WebDriverBy::id('newsoverviewdispCloseButton'))->click();
        } catch (NoSuchElementException $exception) {}

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('gnavi01')));
        $this->driver->findElement(WebDriverBy::id('gnavi01'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('charge')));
        $this->driver->findElement(WebDriverBy::id('charge'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('chargeInstructAmt')));
        $this->driver->findElement(WebDriverBy::id('chargeInstructAmt'))->sendKeys($this->depositAmount / 1000);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('chargeBetPassword')));
        $this->driver->findElement(WebDriverBy::id('chargeBetPassword'))->sendKeys($this->purchasePassword);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('executeCharge')));
        $this->driver->findElement(WebDriverBy::id('executeCharge'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('ok')));
        $this->driver->findElement(WebDriverBy::linkText('OK'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('closeChargecomp')));
        $this->driver->findElement(WebDriverBy::id('closeChargecomp'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('jyo' . sprintf('%02d', $stadiumId))));
        $this->driver->findElement(WebDriverBy::id('jyo' . sprintf('%02d', $stadiumId)))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('selRaceNo' . sprintf('%02d', $raceNumber))));
        $this->driver->findElement(WebDriverBy::id('selRaceNo' . sprintf('%02d', $raceNumber)))->click();

        if (strpos($this->driver->findElement(WebDriverBy::id('selRaceNo' . sprintf('%02d', $raceNumber)))->getAttribute('class'), 'end') !== false) {
            throw new SimplePurchaserException;
        }

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('betkati6')));
        $this->driver->findElement(WebDriverBy::id('betkati6'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('betway4')));
        $this->driver->findElement(WebDriverBy::id('betway4'))->click();

        foreach ($forecasts as $forecast) {
            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.x' . substr($forecast, 0, 1) . '.y1')));
            $this->driver->findElement(WebDriverBy::cssSelector('.x' . substr($forecast, 0, 1) . '.y1'))->click();

            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.x' . substr($forecast, 1, 1) . '.y1')));
            $this->driver->findElement(WebDriverBy::cssSelector('.x' . substr($forecast, 1, 1) . '.y2'))->click();

            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.x' . substr($forecast, 2, 1) . '.y1')));
            $this->driver->findElement(WebDriverBy::cssSelector('.x' . substr($forecast, 2, 1) . '.y3'))->click();

            $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('formaAmountBtn')));
            $this->driver->findElement(WebDriverBy::id('formaAmountBtn'))->click();
        }

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.betlistbtn.combi')));
        $this->driver->findElement(WebDriverBy::cssSelector('.betlistbtn.combi'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('ok')));
        $this->driver->findElement(WebDriverBy::id('ok'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('distamoTotal')));
        $this->driver->findElement(WebDriverBy::id('distamoTotal'))->sendKeys($this->depositAmount / 100);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('execDistamo')));
        $this->driver->findElement(WebDriverBy::id('execDistamo'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('updateDistamo')));
        $this->driver->findElement(WebDriverBy::id('updateDistamo'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::cssSelector('.btnSubmit')));
        $this->driver->findElement(WebDriverBy::cssSelector('.btnSubmit'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('amount')));
        $this->driver->findElement(WebDriverBy::id('amount'))->sendKeys($this->depositAmount);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('pass')));
        $this->driver->findElement(WebDriverBy::id('pass'))->sendKeys($this->purchasePassword);

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('submitBet')));
        $this->driver->findElement(WebDriverBy::id('submitBet'))->click();

        $this->driver->wait(10, 500)->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('ok')));
        $this->driver->findElement(WebDriverBy::id('ok'))->click();

        foreach ($handles as $handle) {
            $this->driver->switchTo()->window($handle);
            $this->driver->close();
        }
    }

    /**
     * @return \DI\Container
     */
    public function getContainer(): Container
    {
        $builder = new ContainerBuilder;

        $builder->addDefinitions(__DIR__ . '/../config/definitions.php');

        return $builder->build();
    }
}
