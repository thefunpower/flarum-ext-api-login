<?php

namespace FeApi\Login\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginTokenController extends BaseController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $res = $this->parseRequest($request); 
        if($res){
            return $res;
        }
        return $this->doLogin();
    }
}