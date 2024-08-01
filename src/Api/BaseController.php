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

class BaseController implements RequestHandlerInterface
{
    protected $authenticator;
    protected $rememberer;
    protected $bus;
    protected $settings;
    protected $translator;
    public $serializer = UserSerializer::class;
    public $key = '';
    public $iv  = '';
    protected $par = [];
    protected $token = '';
    protected $decode_data;
    protected $rand;
    protected $session;
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
    }

    protected function parseRequest($request)
    {
        $this->par = $request->getQueryParams();
        $this->token = $this->par['token'] ?? '';
        $aes_key = $this->settings->get("api_login.aes_key");
        $aes_iv  = $this->settings->get("api_login.aes_iv");
        $this->rand    = $this->settings->get("api_login.rand");
        if(!$aes_key || !$aes_iv) {
            return new JsonResponse(['success' => false, 'message' => $this->lang('api_login.login.Please setting aes key')]);
        }
        if(!$this->token) {
            return new JsonResponse(['success' => false, 'message' => $this->lang('api_login.login.Lost token params')]);
        }
        $data = base64_decode(urldecode($this->token));
        $this->decode_data = $this->decode($data);
        $this->session = $request->getAttribute('session');
        return $this->parseData($this->decode_data);
    }

    protected function doLogin($data = [])
    {
        $data = $data ?: $this->decode_data;
        $third_nid = $data['nid'] ?? '';
        if($third_nid) {
            $user = User::where('third_nid', $third_nid)->first();
        } else {
            $user = User::where('username', $data['name'])->first();
        }

        if(!$user) {
            return new JsonResponse(['success' => false, 'message' => $this->lang('api_login.login.Login Failed')]);
        }
        $this->authenticator->logIn($this->session, SessionAccessToken::generate($user->id));
        return new JsonResponse(['success' => true, 'message' => $this->lang('api_login.login.Login Successful')]);
    }

    protected function parseData($data)
    {
        $name = $data['name'] ?? '';
        $tag = $data['tag'] ?? '';
        $created_at = $data['created_at'] ?? '';
        if(!$name || !$tag || !$created_at) {
            return new JsonResponse(['success' => false, 'message' => $this->lang('api_login.login.Params Error')]);
        }
        if($created_at < time() - 10) {
            return new JsonResponse(['success' => false, 'message' => $this->lang('api_login.login.Expire Data')]);
        }
        if(!$this->rand || $this->rand != $tag) {
            return new JsonResponse(['success' => false, 'message' => $this->lang('api_login.login.Validate Failed')]);
        }
    }

    protected function decode($data)
    {
        return @json_decode(openssl_decrypt(base64_decode($data), 'AES-128-CBC', $this->key, 1, $this->iv), true);
    }

    protected function encode($data = [])
    {
        $data = json_encode($data);
        return @base64_encode(openssl_encrypt($data, 'AES-128-CBC', $this->key, 1, $this->iv));
    }

    protected function lang($key){
        // zh-Hans
        $locale = $this->translator->getLocale(); 
        return $this->translator->trans($key);
    }
}
