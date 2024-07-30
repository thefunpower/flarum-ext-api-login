<?php

namespace FeApi\Login\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginTokenController extends BaseController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->parseRequest($request);
        return $this->doLogin();
    }
}