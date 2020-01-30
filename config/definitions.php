<?php

return [
    'SimplePurchaser' => \DI\create('\Boatrace\Analytics\SimplePurchaser')->constructor(
        \DI\get('MainSimplePurchaser')
    ),
    'MainSimplePurchaser' => function ($container) {
        return $container->get('\Boatrace\Analytics\MainSimplePurchaser');
    },
    'ChromeOptions' => function ($container) {
        return $container->get('\Facebook\WebDriver\Chrome\ChromeOptions');
    },
];
