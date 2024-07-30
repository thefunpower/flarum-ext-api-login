<?php

namespace FeApi\Login\Api;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\Foundation\ValidationException;
use Flarum\Http\Rememberer;
use Flarum\Http\RequestUtil;
use Flarum\Http\SessionAccessToken;
use Flarum\Http\SessionAuthenticator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use FoF\Impersonate\Events\Impersonated;
use Illuminate\Contracts\Session\Session;
use Illuminate\Events\Dispatcher;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginTokenController implements RequestHandlerInterface
{
    protected $authenticator;

    protected $rememberer;

    protected $bus;

    protected $settings;

    protected $translator;

    public $serializer = UserSerializer::class;

    public $key = '';
    public $iv  = '';

    public function __construct(SessionAuthenticator $authenticator, Rememberer $rememberer, Dispatcher $bus, SettingsRepositoryInterface $settings, TranslatorInterface $translator)
    {
        $this->authenticator = $authenticator;
        $this->rememberer = $rememberer;
        $this->bus = $bus;
        $this->settings = $settings;
        $this->translator = $translator;
    }


    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $par = $request->getQueryParams();
        $aes_key = $this->settings->get("api_login.aes_key");
        $aes_iv = $this->settings->get("api_login.aes_iv");
        $rand = $this->settings->get("api_login.rand");
        if(!$aes_key || !$aes_iv){
            return new JsonResponse(['success' => false, 'message' => 'Please setting aes key']);
        } 
        $data = $par['token']??'';
        if(!$data){
            return new JsonResponse(['success' => false, 'message' => 'Lost token params']);
        }
        $data = base64_decode(urldecode($data));  
        $arr = $this->decode($data); 
        $name = $arr['name']??'';
        $tag = $arr['tag']??''; 
        $created_at = $arr['created_at']??'';
        if(!$name || !$tag || !$created_at){
            return new JsonResponse(['success' => false, 'message' => 'Params error']);
        }
        if($created_at < time() - 30 ){
            return new JsonResponse(['success' => false, 'message' => 'Expire data']);
        }
        if(!$rand || $rand != $tag){
            return new JsonResponse(['success' => false, 'message' => 'Parse data failed']);
        } 
        $user = User::where('username', $name)->first();
        if(!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Login failed']);
        }
        $session = $request->getAttribute('session');
        $this->authenticator->logIn($session, SessionAccessToken::generate($user->id));
        return new JsonResponse(['success' => true, 'message' => 'Login successful']);
    }

    protected function decode($data){ 
        return @json_decode(openssl_decrypt(base64_decode($data), 'AES-128-CBC', $this->key, 1, $this->iv),true); 
    }

    protected function encode($data = []){
        $data = json_encode($data);
        return @base64_encode(openssl_encrypt($data, 'AES-128-CBC', $this->key, 1, $this->iv));
    }


}