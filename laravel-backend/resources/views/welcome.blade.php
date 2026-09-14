<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RAG Doküman Soru-Cevap Sistemi</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 30px;
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

        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background: #3f51b5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .document {
            padding: 10px;
            margin-bottom: 10px;
            background: #f4f4f4;
        }

        .answer {
            margin-top: 20px;
            padding: 15px;
            background: #f4f4f4;
        }

        .source {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
        }

        .success {
            margin-top: 15px;
            padding: 10px;
            background: #e8f5e9;
        }

        .error {
            margin-top: 15px;
            padding: 10px;
            background: #ffebee;
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

            <input
                type="file"
                name="dosya"
                accept=".txt,.pdf"
                required
            >

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

                    <strong>
                        {{ $dokuman['dosya_adi'] }}
                    </strong>

                    -
                    {{ $dokuman['chunk_sayisi'] }} chunk

                </div>

            @endforeach

        @else

            <p>
                Henüz doküman yüklenmedi.
            </p>

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

                    @foreach($kaynaklar as $kaynak)

                        <div class="source">

                            <strong>
                                {{ $kaynak['kaynak'] }}
                            </strong>

                            <p>
                                Parça:
                                {{ $kaynak['chunk_index'] }}
                            </p>

                            <p>
                                Benzerlik:
                                {{ isset($kaynak['benzerlik']) ? number_format($kaynak['benzerlik'] * 100, 2) . '%' : '-' }}
                            </p>

                            @if(isset($kaynak['icerik']))

                                <p>
                                    {{ $kaynak['icerik'] }}
                                </p>

                            @endif

                        </div>

                    @endforeach

                @endif

            </div>

        @endif

    </div>

</div>

</body>

</html>