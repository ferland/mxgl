<?php

require_once __DIR__ . '/silex/bootstrap.php';

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig', array());
});

$app->run();