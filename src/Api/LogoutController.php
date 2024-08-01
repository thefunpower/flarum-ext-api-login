<?php

namespace FeApi\Login\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\User\User;
use Laminas\Diactoros\Response\JsonResponse;

class LogoutController extends BaseController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $actor = $request->getAttribute('actor'); 
		if (!$actor || !$actor->exists) {
		 	return new JsonResponse(['success' => true, 'message' =>  $this->lang('api_login.login.NotLogin')]);
		} 
		$session = $request->getAttribute('session');
		$this->authenticator->logout($session);
		return new JsonResponse(['success' => true, 'message' => $this->lang($this->lang('api_login.login.Logout Success'))]);
    }
}