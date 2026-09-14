from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel

from rag import rag_soru_cevapla, tek_dokuman_yukle
from database import veritabani_baglantisi


app = FastAPI()


app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:5173"],
    allow_methods=["*"],
    allow_headers=["*"]
)


class Soru(BaseModel):
    soru: str


class Dokuman(BaseModel):
    dosya_adi: str


@app.get("/")
def ana_sayfa():

    return {"message": "RAG API çalışıyor"}


@app.post("/sor")
def soru_sor(soru: Soru):

    sonuc = rag_soru_cevapla(soru.soru)

    return {
        "soru": soru.soru,
        "cevap": sonuc["cevap"],
        "kaynaklar": sonuc["kaynaklar"]
    }


@app.post("/dokuman-yukle")
def dokuman_yukle(dokuman: Dokuman):

    try:

        chunk_sayisi = tek_dokuman_yukle(
            dokuman.dosya_adi
        )

        return {
            "mesaj": "Doküman başarıyla işlendi.",
            "dosya": dokuman.dosya_adi,
            "chunk_sayisi": chunk_sayisi
        }

    except Exception as hata:

        return {
            "hata": str(hata)
        }


@app.get("/dokumanlar")
def dokumanlari_getir():

    connection = veritabani_baglantisi()
    cursor = connection.cursor()

    cursor.execute(
        """
        SELECT documents.filename, COUNT(chunks.id)
        FROM documents
        LEFT JOIN chunks
        ON documents.id = chunks.document_id
        GROUP BY documents.id, documents.filename
        ORDER BY documents.id
        """
    )

    sonuclar = cursor.fetchall()

    cursor.close()
    connection.close()

    dokumanlar = []

    for sonuc in sonuclar:

        dokumanlar.append({
            "dosya_adi": sonuc[0],
            "chunk_sayisi": sonuc[1]
        })

    return dokumanlar


if __name__ == "__main__":
    import uvicorn

    uvicorn.run(
        app,
        host="127.0.0.1",
        port=8000
    )