<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

return static function (RouteBuilder $routes): void {
    $routes->plugin('Attachments', function ($routes): void {
        $routes->fallbacks('DashedRoute');
    });
};

