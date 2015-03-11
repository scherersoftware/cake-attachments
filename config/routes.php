<?php
use Cake\Routing\Router;

Router::plugin('Attachments', function ($routes) {
    $routes->fallbacks('DashedRoute');
});
