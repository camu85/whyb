<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Requires mios
require 'controladores/_listaControladores.php';
require 'vistas/_listaVistas.php';

//firePHP
require_once 'FirePHPCore/FirePHP.class.php';
ob_start();
//instanciar un objeto de la clase FirePHP
$firephp = FirePHP::getInstance(true);

//Portada
$app->get('/', function () use ($app) {
    global $firephp;
    $usuario = "";
    $admin = 0;
    $titulo = "Where have you been";
    $header = header::construye($usuario, $admin);
    $body=inicio::construye();
    $footer = footer::construye();
    $paginaDetalle = new plantillaPagina($titulo, $header, $body, $footer);
    $pagina = $paginaDetalle->mostrar();
    //$firephp->log($paco, 'Mensaje');
    return $pagina;
})
->bind('homepage')
;

//Formulario
$app->get('/form/', function () use ($app) {
    return users_controller::form();
})
->bind('formulario')
;

//Map
$app->get('/map/', function () use ($app) {
    return maps_controller::draw();
})
->bind('map')
;

//Places
$app->get('/places/', function () use ($app) {
    return places_controller::draw();
})
->bind('places')
;

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html',
        'errors/'.substr($code, 0, 2).'x.html',
        'errors/'.substr($code, 0, 1).'xx.html',
        'errors/default.html',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});