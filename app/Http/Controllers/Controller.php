<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $code = 0;

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function responseError($type, $options = [])
    {
        $return = [
            'code' => $this->getCode(),
            'msg' => app('error.info')->getErrorMsg($this->getCode(), request()->headers->get('lang'))
        ];

        if ($type) {
            $return['notify'] = app('error.info')->getErrorNotify($type, $options, request()->headers->get('lang'));
        }

        return response()->json($return);
    }

    public function responseData($input = [])
    {
        return response()->json([
            'code' => $this->getCode(),
            'msg' => app('error.info')->getErrorMsg($this->getCode(), request()->headers->get('lang')),
            'data' => $input
        ]);
    }
}
