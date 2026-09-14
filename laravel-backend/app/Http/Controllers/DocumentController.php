<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DocumentController extends Controller
{
    public function index()
    {
        $response = Http::get(
            'http://127.0.0.1:8000/dokumanlar'
        );

        $dokumanlar = $response->json();

        return view('welcome', [
            'dokumanlar' => $dokumanlar
        ]);
    }
}