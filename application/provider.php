<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用容器绑定定义
use OwlAdmin\Extend\Manager;
use OwlAdmin\Libs\Asset;
use OwlAdmin\Libs\Context;

return [
    'admin.extend' => Manager::class,
    'admin.context' => Context::class,
    'admin.setting' =>  fn() => settings(),
    'admin.asset'   =>  Asset::class,
];
