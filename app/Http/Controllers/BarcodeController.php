<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DNS1D;

class BarcodeController extends Controller
{
    public function show($code)
    {
        if (!preg_match('/^\d{9}$/', $code)) {
            abort(400, 'A kódnak 9 számjegyből kell állnia.');
        }

        $svg = DNS1D::getBarcodeSVG($code, 'C128');

        $pngBase64 = DNS1D::getBarcodePNG($code, 'C128', 2, 60);

        return view('barcode.show', compact('svg', 'pngBase64', 'code'));
    }
}
