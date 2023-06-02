<?php
namespace app\index\controller;

use OwlAdmin\Libs\JsonResponse;

class Index
{
    public function index()
    {

        return (new JsonResponse)->success(json( ['type' => 'page', 'body' => 'hellw']));
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
