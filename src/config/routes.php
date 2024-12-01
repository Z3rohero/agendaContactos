<?php
require_once 'Router/Router.php';
require_once 'controllers/ContactController.php';

$router = new Router\Router('/agendaContactos/src');
$controller = new ContactController();

// Rutas GET
$router->add('GET','/', [$controller, 'index']);
$router->add('GET','/contacts/list', [$controller, 'list']);
$router->add('GET','/contacts/get_contact/{id}', [$controller, 'getContact']);

// Rutas POST
$router->add('POST','/contacts/create', [$controller, 'create']);

// Rutas PUT
$router->add('PUT','/contacts/update/{id}', [$controller, 'update']);

// Rutas DELETE
$router->add('DELETE','/contacts/delete/{id}', [$controller, 'delete']);

return $router;