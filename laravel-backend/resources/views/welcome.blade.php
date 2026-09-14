<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RAG Doküman Soru-Cevap Sistemi</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px 20px;
            background: #f4f6f8;
            color: #222;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin-bottom: 10px;
            font-size: 32px;
        }

        .header p {
            color: #666;
            margin: 0;
        }

        .section {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 14px;
            border: 1px solid #e1e4e8;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .section h2 {
            margin-top: 0;
            margin-bottom: 20px;
        }

        .dosya-alani {
            margin-bottom: 20px;
        }

        .dosya-sec {
            display: inline-block;
            padding: 11px 18px;
            background: #eef2ff;
            color: #3f51b5;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .dosya-sec:hover {
            background: #e0e7ff;
        }

        #dosya {
            display: none;
        }

        #dosya-adi {
            margin-left: 10px;
            color: #666;
        }

        input[type="text"] {
            width: 100%;
            padding: 13px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #3f51b5;
        }

        button {
            padding: 11px 20px;
            border: none;
            border-radius: 8px;
            background: #3f51b5;
            color: white;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #303f9f;
        }

        .success {
            margin-top: 15px;
            padding: 13px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 8px;
        }

        .error {
            margin-top: 15px;
            padding: 13px;
            background: #ffebee;
            color: #c62828;
            border-radius: 8px;
        }

        .document {
            margin-bottom: 12px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .document-name {
            font-weight: bold;
            color: #333;
            word-break: break-word;
        }

        .chunk-count {
            color: #3f51b5;
            font-weight: bold;
            white-space: nowrap;
        }

        .answer {
            margin-top: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #3f51b5;
        }

        .answer h2 {
            margin-bottom: 12px;
        }

        .answer p {
            line-height: 1.6;
        }

        .source {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border: 1px solid #e1e4e8;
            border-radius: 8px;
        }

        .source strong {
            color: #3f51b5;
        }

        .source-content {
            margin-top: 10px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            line-height: 1.6;
            color: #444;
        }

        @media (max-width: 600px) {

            body {
                padding: 20px 12px;
            }

            .section {
                padding: 18px;
            }

            .header h1 {
                font-size: 25px;
            }

            #dosya-adi {
                display: block;
                margin: 10px 0 0 0;
            }

            .document {
                flex-direction: column;
                align-items: flex-start;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <h1>RAG Doküman Soru-Cevap Sistemi</h1>

        <p>
            Dokümanlarınızı yükleyin ve içerikleri hakkında soru sorun.
        </p>

    </div>

    <div class="section">

        <h2>Doküman Yükle</h2>

        <form action="/yukle" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="dosya-alani">

                <label for="dosya" class="dosya-sec">
                    Dosya Seç
                </label>

                <input
                    type="file"
                    id="dosya"
                    name="dosya"
                    accept=".txt,.pdf"
                    required
                    onchange="dosyaAdiniGoster()"
                >

                <span id="dosya-adi">
                    Henüz dosya seçilmedi
                </span>

            </div>

            <button type="submit">
                Dokümanı Yükle
            </button>

        </form>

        @if(session('yukleme_mesaji'))

            <div class="success">
                {{ session('yukleme_mesaji') }}
            </div>

        @endif

        @if($errors->has('dosya'))

            <div class="error">
                {{ $errors->first('dosya') }}
            </div>

        @endif

    </div>

    <div class="section">

        <h2>Yüklü Dokümanlar</h2>

        @if(isset($dokumanlar) && count($dokumanlar) > 0)

            @foreach($dokumanlar as $dokuman)

                <div class="document">

                    <div class="document-name">
                        {{ $dokuman['dosya_adi'] }}
                    </div>

                    <div class="chunk-count">
                        {{ $dokuman['chunk_sayisi'] }} chunk
                    </div>

                </div>

            @endforeach

        @else

            <p>Henüz doküman yüklenmedi.</p>

        @endif

    </div>

    <div class="section">

        <h2>Soru Sor</h2>

        <form action="/sor" method="POST">

            @csrf

            <label for="soru">
                Sorunuzu yazın:
            </label>

            <input
                type="text"
                id="soru"
                name="soru"
                placeholder="Örneğin: Python nedir?"
                value="{{ $soru ?? '' }}"
                required
            >

            <button type="submit">
                Sor
            </button>

        </form>

        @if(isset($cevap) && $cevap)

            <div class="answer">

                <h2>Cevap</h2>

                <p>
                    {{ str_replace('```', '', $cevap) }}
                </p>

                @if(isset($kaynaklar) && count($kaynaklar) > 0)

                    <h3>Kaynaklar</h3>

                    <ul>

                        @foreach($kaynaklar as $kaynak)

                            <li class="source">

                                <strong>
                                    {{ $kaynak['kaynak'] }}
                                </strong>

                                <br>

                                Parça:
                                {{ $kaynak['chunk_index'] }}

                                <br>

                                Benzerlik:
                                {{ isset($kaynak['benzerlik']) ? number_format($kaynak['benzerlik'] * 100, 2) . '%' : '-' }}

                                @if(isset($kaynak['icerik']))

                                    <div class="source-content">
                                        {{ $kaynak['icerik'] }}
                                    </div>

                                @endif

                            </li>

                        @endforeach

                    </ul>

                @endif

            </div>

        @endif

    </div>

</div>

<script>

    function dosyaAdiniGoster() {

        const dosya = document.getElementById("dosya");

        const dosyaAdi = document.getElementById("dosya-adi");

        if (dosya.files.length > 0) {

            dosyaAdi.textContent = dosya.files[0].name;

        } else {

            dosyaAdi.textContent = "Henüz dosya seçilmedi";

        }

    }

</script>

</body>

</html>