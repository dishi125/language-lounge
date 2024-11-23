<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banner.index');
    }

    public function bannerForm(Request $request)
    {
        $formTitle = __('pages.user.add_banner');

        return view('admin.banner.form',compact('formTitle'));
    }
}
