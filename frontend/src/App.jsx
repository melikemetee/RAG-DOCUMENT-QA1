import { useEffect, useState } from "react";
import "./App.css";

function App() {
    const [dokumanlar, setDokumanlar] = useState([]);
    const [soru, setSoru] = useState("");
    const [cevap, setCevap] = useState("");
    const [kaynaklar, setKaynaklar] = useState([]);
    const [dosya, setDosya] = useState(null);
    const [mesaj, setMesaj] = useState("");

    useEffect(() => {
        dokumanlariGetir();
    }, []);

    async function dokumanlariGetir() {
        const response = await fetch(
            "http://127.0.0.1:8000/dokumanlar"
        );

        const data = await response.json();

        setDokumanlar(data);
    }

    async function soruSor(event) {
        event.preventDefault();

        if (!soru.trim()) {
            return;
        }

        const response = await fetch(
            "http://127.0.0.1:8001/api/sor",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    soru: soru
                })
            }
        );

        const data = await response.json();

        if (data.hata) {
            setCevap("Hata: " + data.hata);
            setKaynaklar([]);
            return;
        }

        setCevap(data.cevap);
        setKaynaklar(data.kaynaklar || []);
    }

    async function dokumanYukle(event) {
        event.preventDefault();

        if (!dosya) {
            setMesaj("Lütfen bir dosya seçin.");
            return;
        }

        const formData = new FormData();

        formData.append("dosya", dosya);

        const response = await fetch(
            "http://127.0.0.1:8001/api/yukle",
            {
                method: "POST",
                body: formData
            }
        );

        const data = await response.json();

        if (data.hata) {
            setMesaj("Hata: " + data.hata);
        } else {
            setMesaj(
                "Doküman başarıyla işlendi. Chunk sayısı: " +
                data.chunk_sayisi
            );

            setDosya(null);

            dokumanlariGetir();
        }
    }

    return (
        <div className="app">
            <div className="header">
                <h1>RAG Doküman Soru-Cevap Sistemi</h1>

                <p>
                    Dokümanlarınızı yükleyin ve içerikleri hakkında soru sorun.
                </p>
            </div>

            <div className="card">
                <h2>Doküman Yükle</h2>

                <form onSubmit={dokumanYukle}>
                    <div className="upload-area">
                        <input
                            type="file"
                            accept=".txt,.pdf"
                            onChange={(event) =>
                                setDosya(event.target.files[0])
                            }
                        />

                        <button type="submit">
                            Dokümanı Yükle
                        </button>
                    </div>
                </form>

                {mesaj && (
                    <div className="message">
                        {mesaj}
                    </div>
                )}
            </div>

            <div className="card">
                <h2>Yüklü Dokümanlar</h2>

                {dokumanlar.length === 0 ? (
                    <p>Henüz doküman yüklenmedi.</p>
                ) : (
                    dokumanlar.map((dokuman, index) => (
                        <div className="document" key={index}>
                            <strong>
                                {dokuman.dosya_adi}
                            </strong>

                            <span>
                                {" "} - {dokuman.chunk_sayisi} chunk
                            </span>
                        </div>
                    ))
                )}
            </div>

            <div className="card">
                <h2>Soru Sor</h2>

                <form onSubmit={soruSor}>
                    <input
                        type="text"
                        value={soru}
                        onChange={(event) =>
                            setSoru(event.target.value)
                        }
                        placeholder="Örneğin: Python nedir?"
                    />

                    <button type="submit">
                        Sor
                    </button>
                </form>
            </div>

            {cevap && (
                <div className="card">
                    <h2>Cevap</h2>

                    <div className="answer">
                        <p>{cevap}</p>
                    </div>

                    {kaynaklar.length > 0 && (
                        <div>
                            <h3>Kaynaklar</h3>

                            {kaynaklar.map((kaynak, index) => (
                                <div className="source" key={index}>
                                    <strong>
                                        {kaynak.kaynak}
                                    </strong>

                                    <p>
                                        Parça: {kaynak.chunk_index}
                                    </p>

                                    <p className="similarity">
                                        Benzerlik:{" "}
                                        {(kaynak.benzerlik * 100).toFixed(2)}%
                                    </p>

                                    <p>
                                        {kaynak.icerik}
                                    </p>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}

export default App;