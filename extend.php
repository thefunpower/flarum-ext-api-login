<?php

namespace FeApi\Login;

use Flarum;
use Flarum\Extend;
use Flarum\Event\ConfigureWebApp;
use Flarum\Frontend\Document;
use Flarum\User\Event\Saving;
use Flarum\User\User;
use Flarum\User\Exception\InvalidArgumentException;
use FeApi\Login\Api\LoginTokenController;
use FeApi\Login\Api\CreateUserByTokenController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))->get('/v2/login-token', 'v2.login-token', LoginTokenController::class),
    (new Extend\Routes('api'))->get('/v2/create-user-by-token', 'v2.create-user-by-token', CreateUserByTokenController::class),


];