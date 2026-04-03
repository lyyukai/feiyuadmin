<?php

namespace app\controller;

use app\common\service\JsonService;
use think\facade\Request;

class IndexController extends controller
{


    public function index($name = '')
    {
        $template = app()->getRootPath() . 'public/pc/index.html';
        if (Request::isMobile()) {
            $template = app()->getRootPath() . 'public/mobile/index.html';
        }
        if (file_exists($template)) {
            return view($template);
        }
        return JsonService::success($name);
    }


}
