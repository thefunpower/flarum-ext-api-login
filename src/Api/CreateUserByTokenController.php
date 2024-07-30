<?php

namespace FeApi\Login\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Flarum\User\User;

class CreateUserByTokenController extends BaseController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $res = $this->parseRequest($request); 
        if($res){
            return $res;
        }
        $data = $this->decode_data;
        if(!isset($data['nid']) || !isset($data['name'])) {
            return new JsonResponse(['success' => false, 'message' => 'Lost Params']);
        }
        $data = $this->register();
        return $this->doLogin($data);
    }

    protected function register()
    {
        $data = $this->decode_data;
        $name = $data['name'] ?? '';
        $email = $data['email'] ?? '';
        $nid   = $data['nid'] ?? '';
        $nickname = $data['nickname'] ?? '';
        $user = User::where('third_nid', $nid)->first();
        if(!$user) {
            $password = 'third#'.md5($email.mt_rand(10000, 99999));
            try {
                $user = User::register($name, $email, $password);
                $user->third_nid = $nid;
                $user->is_email_confirmed = 1;
                $user->save();
            } catch (\Throwable $th) {
                $err = $th->getMessage();
                throw new \Exception($err);
            }
        } else {
            if($email && $user->email != $email) {
                $user->email = $email;
                $flag = true;
            }
            if($nickname && $nickname != $user->nickname) {
                $flag = true;
                $user->nickname = $nickname;
            }
            if($flag) {
                $user->save();
            }
        }
        return ['name' => $name];
    }
}