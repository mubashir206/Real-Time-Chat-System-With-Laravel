<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Services\SettingServies;


class SettingController extends Controller
{
    protected $settingServies;

    public function __construct(SettingServies $settingServies)
    {
        $this->settingServies = $settingServies;
    }
    public function index(){
        return view('setting.index');
    }

    public function update(UpdateUserRequest $request)
    {
        return $this->settingServies->update($request);
    }
}
