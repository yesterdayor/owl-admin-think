<?php

namespace app\admin\controller;

use OwlAdmin\OwlAdmin;
use OwlAdmin\Libs\Captcha;
use Illuminate\Support\Facades\Hash;
use OwlAdmin\Renderers\Page;
use OwlAdmin\Models\AdminUser;
use Symfony\Component\HttpFoundation\Response;
use OwlAdmin\Services\AdminUserService;
use think\facade\Validate;
use think\Request;

class AuthController extends AdminController
{
    protected string $serviceName = AdminUserService::class;

    public function login(Request $request)
    {
        if (config('admin.auth.login_captcha')) {
            if (!$request->has('captcha')) {
                return $this->response()->fail(__('admin.required', ['attribute' => __('admin.captcha')]));
            }

            if (strtolower(admin_decode($request->sys_captcha)) != strtolower($request->captcha)) {
                return $this->response()->fail(__('admin.captcha_error'));
            }
        }

        try {
            $validator = Validate::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ], [
                'username' . '.required' => __('admin.required', ['attribute' => __('admin.username')]),
                'password.required'      => __('admin.required', ['attribute' => __('admin.password')]),
            ]);

            if ($validator->fails()) {
                abort(Response::HTTP_BAD_REQUEST, $validator->errors()->first());
            }
            $adminModel = config("admin.auth.model", AdminUser::class);
            $user       = $adminModel::query()->where('username', $request->username)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                $token = $user->createToken('admin')->plainTextToken;

                return $this->response()->success(compact('token'), __('admin.login_successful'));
            }

            abort(Response::HTTP_BAD_REQUEST, __('admin.login_failed'));
        } catch (\Exception $e) {
            return $this->response()->fail($e->getMessage());
        }
    }

    public function loginPage()
    {
        $captcha       = null;
        $enableCaptcha = config('admin.auth.login_captcha');

        // 验证码
        if ($enableCaptcha) {
            $captcha = amisMake()->InputGroupControl()->body([
                amisMake()->TextControl()->name('captcha')->placeholder(__('admin.captcha'))->required(),
                amisMake()->HiddenControl()->name('sys_captcha'),
                amisMake()->Service()->id('captcha-service')->api('get:' . admin_url('/captcha'))->body(
                    amisMake()
                        ->Image()
                        ->src('${captcha_img}')
                        ->height('1.917rem')
                        ->className('p-0 border captcha-box')
                        ->set(
                            'clickAction',
                            ['actionType' => 'reload', 'target' => 'captcha-service']
                        )
                ),
            ]);
        }

        $form = amisMake()->Form()->id('login-form')->title()->api(admin_url('/login'))->body([
            amisMake()->TextControl()->name('username')->placeholder(__('admin.username'))->required(),
            amisMake()
                ->TextControl()
                ->type('input-password')
                ->name('password')
                ->placeholder(__('admin.password'))
                ->required(),
            $captcha,
            amisMake()->CheckboxControl()->name('remember_me')->option(__('admin.remember_me'))->value(true),

            // 登录按钮
            amisMake()
                ->VanillaAction()
                ->actionType('submit')
                ->label(__('admin.login'))
                ->level('primary')
                ->className('w-full'),
        ])->actions([]); // 清空默认的提交按钮

        $failAction = [];
        if ($enableCaptcha) {
            // 登录失败后刷新验证码
            $failAction = [
                // 登录失败事件
                'submitFail' => [
                    'actions' => [
                        // 刷新验证码外层Service
                        ['actionType' => 'reload', 'componentId' => 'captcha-service'],
                    ],
                ],
            ];
        }
        $form->onEvent(array_merge([
            // 页面初始化事件
            'inited'     => [
                'actions' => [
                    // 读取本地存储的登录参数
                    [
                        'actionType' => 'custom',
                        'script'     => <<<JS
let loginParams = localStorage.getItem('loginParams')
if(loginParams){
    loginParams = JSON.parse(loginParams)
    doAction({
        actionType: 'setValue',
        componentId: 'login-form',
        args: { value: loginParams }
    })
}
JS
    ,

                    ],
                ],
            ],
            // 登录成功事件
            'submitSucc' => [
                'actions' => [
                    // 保存登录参数到本地, 并跳转到首页
                    [
                        'actionType' => 'custom',
                        'script'     => <<<JS
let _data = {}
if(event.data.remember_me){
    _data = { username: event.data.username, password: event.data.password }
}
window.\$owl.afterLoginSuccess(_data, event.data.result.data.token)
JS,

                    ],
                ],
            ],
        ], $failAction));

        $card = amisMake()->Card()->className('w-96 m:w-full')->body([
            amisMake()->Flex()->justify('space-between')->className('px-2.5 pb-2.5')->items([
                amisMake()->Image()->src(url(config('admin.logo')))->width(40)->height(40),
                amisMake()
                    ->Tpl()
                    ->className('font-medium')
                    ->tpl('<div style="font-size: 24px">' . config('admin.name') . '</div>'),
            ]),
            $form,
        ]);

        return amisMake()->Page()->css([
            '.captcha-box .cxd-Image--thumb' => [
                'padding' => '0',
                'cursor'  => 'pointer',
                'border'  => 'var(--Form-input-borderWidth) solid var(--Form-input-borderColor)',

                'border-top-right-radius'    => '4px',
                'border-bottom-right-radius' => '4px',
            ],
            '.cxd-Image-thumb'               => ['width' => 'auto'],
        ])->body(
            amisMake()->Wrapper()->className("h-screen w-full flex items-center justify-center")->body($card)
        );
    }


    public function logout()
    {
        $this->guard()->user()->currentAccessToken()->delete();

        return $this->response()->successMessage();
    }

    public function currentUser()
    {
        $userInfo = OwlAdmin::user()->only(['name', 'avatar']);

        $menus = amisMake()
            ->DropdownButton()
            ->hideCaret()
            ->trigger('hover')
            ->label($userInfo['name'])
            ->align('right')
            ->btnClassName('navbar-user')
            ->menuClassName('min-w-0 px-2')
            ->set('icon', $userInfo['avatar'])
            ->buttons([
                amisMake()
                    ->VanillaAction()
                    ->iconClassName('pr-2')
                    ->icon('fa fa-user-gear')
                    ->label(__('admin.user_setting'))
                    ->onClick('window.location.hash = "#/user_setting"'),
                amisMake()
                    ->VanillaAction()
                    ->iconClassName('pr-2')
                    ->label(__('admin.logout'))
                    ->icon('fa-solid fa-right-from-bracket')
                    ->onClick('window.$owl.logout()'),
            ]);

        return $this->response()->success(array_merge($userInfo, compact('menus')));
    }
}
