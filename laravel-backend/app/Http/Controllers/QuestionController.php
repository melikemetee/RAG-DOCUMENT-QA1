<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuestionController extends Controller
{
    public function sor(Request $request)
    {
        $response = Http::post(
            'http://127.0.0.1:8000/sor',
            [
                'soru' => $request->soru
            ]
        );

        $sonuc = $response->json();

        $dokumanResponse = Http::get(
            'http://127.0.0.1:8000/dokumanlar'
        );

        $dokumanlar = $dokumanResponse->json();

        return view('welcome', [
            'soru' => $sonuc['soru'] ?? $request->soru,
            'cevap' => $sonuc['cevap'] ?? '',
            'kaynaklar' => $sonuc['kaynaklar'] ?? [],
            'dokumanlar' => $dokumanlar
        ]);
    }
}

