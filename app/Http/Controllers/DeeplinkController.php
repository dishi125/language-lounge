<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeeplinkController extends Controller
{
    public function index(Request $request)
    {
        $redirectLink = env('LANGUAGE_LOUNGE_PLAY_STORE_LINK');
        if (preg_match('/(iphone|ipad|ipod)/i', $_SERVER['HTTP_USER_AGENT'])) {
            $redirectLink = env('LANGUAGE_LOUNGE_APP_STORE_LINK');
        } elseif (preg_match('/(android)/i', $_SERVER['HTTP_USER_AGENT'])) {
            $redirectLink = env('LANGUAGE_LOUNGE_PLAY_STORE_LINK');
        }

        $data = [
            'redirectLink' => $redirectLink,
        ];

        return view('deeplink', compact('data'));
    }

    public function qrCode(Request $request)
    {
        $name = "Lukes bar";
        $qr_code = \App\Models\QrCode::where('name',$name)->pluck('qr_code_path_svg')->first();

        return view('qr-code', compact('qr_code'));
    }

}
