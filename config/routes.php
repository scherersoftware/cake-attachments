<?php

use Cake\Routing\Router;

Router::plugin('Attachments', function ($routes): void {
    $routes->fallbacks('DashedRoute');
});
