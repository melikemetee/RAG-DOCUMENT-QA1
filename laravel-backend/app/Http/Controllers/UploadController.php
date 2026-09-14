<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UploadController extends Controller
{
    public function yukle(Request $request)
    {
        $request->validate([
            'dosya' => 'required|file|mimes:txt,pdf|max:51200'
        ]);

        if (!$request->hasFile('dosya')) {
            return redirect('/')->with(
                'yukleme_mesaji',
                'Dosya seçilmedi.'
            );
        }

        $dosya = $request->file('dosya');

        if (!$dosya->isValid()) {
            return redirect('/')->with(
                'yukleme_mesaji',
                'Dosya geçersiz: ' . $dosya->getErrorMessage()
            );
        }

        $dosyaAdi = $dosya->getClientOriginalName();

        $dosya->move(
            base_path('../documents'),
            $dosyaAdi
        );

        $response = Http::post(
            'http://127.0.0.1:8000/dokuman-yukle',
            [
                'dosya_adi' => $dosyaAdi
            ]
        );

        if ($response->failed()) {
            return redirect('/')->with(
                'yukleme_mesaji',
                'Dosya kaydedildi fakat FastAPI işlemi başarısız oldu.'
            );
        }

        $sonuc = $response->json();

        if (isset($sonuc['hata'])) {
            return redirect('/')->with(
                'yukleme_mesaji',
                'FastAPI hatası: ' . $sonuc['hata']
            );
        }

        return redirect('/')->with(
            'yukleme_mesaji',
            'Doküman başarıyla işlendi. Chunk sayısı: ' . $sonuc['chunk_sayisi']
        );
    }

    public function apiYukle(Request $request)
    {
        $request->validate([
            'dosya' => 'required|file|mimes:txt,pdf|max:51200'
        ]);

        if (!$request->hasFile('dosya')) {
            return response()->json([
                'hata' => 'Dosya seçilmedi.'
            ], 400);
        }

        $dosya = $request->file('dosya');

        if (!$dosya->isValid()) {
            return response()->json([
                'hata' => $dosya->getErrorMessage()
            ], 400);
        }

        $dosyaAdi = $dosya->getClientOriginalName();

        $dosya->move(
            base_path('../documents'),
            $dosyaAdi
        );

        $response = Http::post(
            'http://127.0.0.1:8000/dokuman-yukle',
            [
                'dosya_adi' => $dosyaAdi
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'hata' => 'Dosya kaydedildi fakat FastAPI işlemi başarısız oldu.'
            ], 500);
        }

        $sonuc = $response->json();

        if (isset($sonuc['hata'])) {
            return response()->json([
                'hata' => $sonuc['hata']
            ], 500);
        }

        return response()->json([
            'mesaj' => 'Doküman başarıyla işlendi.',
            'dosya' => $dosyaAdi,
            'chunk_sayisi' => $sonuc['chunk_sayisi']
        ]);
    }
}