from embedding import embedding_olustur
from generation import cevap_olustur
from retrieval import benzer_chunklari_getir
from database import veritabani_baglantisi
from document_manager import dokuman_kaydet
from pypdf import PdfReader
import os


def pdf_metni_oku(dosya_yolu):

    reader = PdfReader(dosya_yolu)

    metin = ""

    for sayfa in reader.pages:

        sayfa_metni = sayfa.extract_text()

        if sayfa_metni:
            metin += sayfa_metni + "\n"

    return metin


def dokumanlari_yukle():

    connection = veritabani_baglantisi()
    cursor = connection.cursor()

    dosyalar = os.listdir("documents")

    for dosya_adi in dosyalar:

        if not dosya_adi.lower().endswith(".txt"):
            continue

        dosya_yolu = os.path.join(
            "documents",
            dosya_adi
        )

        with open(
            dosya_yolu,
            "r",
            encoding="utf-8"
        ) as dosya:

            metin = dosya.read()

        dokuman_kaydet(
            cursor,
            dosya_adi,
            metin
        )

    connection.commit()

    cursor.close()
    connection.close()


def tek_dokuman_yukle(dosya_adi):

    dosya_yolu = os.path.join(
        "documents",
        dosya_adi
    )

    if dosya_adi.lower().endswith(".pdf"):

        metin = pdf_metni_oku(dosya_yolu)

    else:

        with open(
            dosya_yolu,
            "r",
            encoding="utf-8"
        ) as dosya:

            metin = dosya.read()

    if not metin.strip():

        raise ValueError(
            "Dokümandan metin çıkarılamadı."
        )

    connection = veritabani_baglantisi()
    cursor = connection.cursor()

    chunk_sayisi = dokuman_kaydet(
        cursor,
        dosya_adi,
        metin
    )

    connection.commit()

    cursor.close()
    connection.close()

    return chunk_sayisi


def rag_soru_cevapla(soru):

    connection = veritabani_baglantisi()
    cursor = connection.cursor()

    soru_embedding = embedding_olustur(soru)

    sonuclar = benzer_chunklari_getir(
        cursor,
        soru_embedding
    )

    if not sonuclar:

        cursor.close()
        connection.close()

        return {
            "cevap": "Bu bilgi yüklenen dokümanlarda bulunamadı.",
            "kaynaklar": []
        }

    context = ""

    for sonuc in sonuclar:

        context += (
            f"Kaynak: {sonuc[4]}\n"
            f"Parça: {sonuc[1]}\n"
            f"{sonuc[2]}\n\n"
        )

    cevap = cevap_olustur(
        soru,
        context
    )

    kaynaklar = []

    for sonuc in sonuclar:

        mesafe = sonuc[3]

        kaynaklar.append({
            "chunk_id": sonuc[0],
            "chunk_index": sonuc[1],
            "kaynak": sonuc[4],
            "icerik": sonuc[2],
            "mesafe": mesafe,
            "benzerlik": 1 - mesafe
        })

    cursor.close()
    connection.close()

    return {
        "cevap": cevap,
        "kaynaklar": kaynaklar
    }


def main():

    dokumanlari_yukle()

    soru = input("Sorunuzu yazın: ")

    sonuc = rag_soru_cevapla(soru)

    print("\nCevap:")
    print(sonuc["cevap"])

    print("\nKaynaklar:")

    for kaynak in sonuc["kaynaklar"]:

        print(
            kaynak["kaynak"],
            "- Parça:",
            kaynak["chunk_index"],
            "- Benzerlik:",
            kaynak["benzerlik"]
        )


if __name__ == "__main__":
    main()