<?php
declare(strict_types=1);

/** @var \Laravel\Lumen\Routing\Router $router */

// MailChimp group
$router->group(['prefix' => 'mailchimp', 'namespace' => 'MailChimp'], function () use ($router) {
    // Lists group
    $router->group(['prefix' => 'lists'], function () use ($router) {
        $router->post('/', 'ListsController@create');
        $router->get('/', 'ListsController@showAll');
        $router->get('/{listId}', 'ListsController@show');
        $router->put('/{listId}', 'ListsController@update');
        $router->delete('/{listId}', 'ListsController@remove');
    });
    $router->group( [],function () use ($router) {
        $router->post('lists/{listId}/members/', 'MembersController@create');
        $router->get('lists/{listId}/members/', 'MembersController@showAll');
        $router->get('lists/{listId}/members/{memberId}', 'MembersController@show');
        $router->put('lists/{listId}/members/{memberId}', 'MembersController@update');
        $router->delete('lists/{listId}/members/{memberId}', 'MembersController@remove');
    });
});
