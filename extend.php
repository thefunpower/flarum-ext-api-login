<?php
 
namespace FeApi\Login;

use Flarum;
use Flarum\Extend;
use FeApi\Login\Api\LoginTokenController;
use Flarum\Event\ConfigureWebApp;
use Flarum\Frontend\Document;

use Flarum\User\Event\Saving;
use Flarum\User\User;
use Flarum\User\Exception\InvalidArgumentException;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\View())
        ->namespace('api_login.templates', __DIR__.'/resources/templates'),

    /**
    * /api/v2/login-token?username=admin&token=
    */
    (new Extend\Routes('api'))->get('/v2/login-token', 'v2.login-token', LoginTokenController::class),
  

];


