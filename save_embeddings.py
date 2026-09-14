from google import genai
from dotenv import load_dotenv
import psycopg2
import os


load_dotenv()

client = genai.Client(
    api_key=os.getenv("GEMINI_API_KEY")
)


connection = psycopg2.connect(
    host=os.getenv("DB_HOST"),
    port=os.getenv("DB_PORT"),
    database=os.getenv("DB_NAME"),
    user=os.getenv("DB_USER"),
    password=os.getenv("DB_PASSWORD"),
    client_encoding="UTF8"
)

cursor = connection.cursor()

print("PostgreSQL bağlantısı başarılı!")


with open("documents/ornek.txt", "r", encoding="utf-8") as dosya:
    metin = dosya.read()


def metni_parcala(metin, parca_boyutu=50, kesisim=20):

    parcalar = []

    baslangic = 0
    toplam_uzunluk = len(metin)

    while baslangic < toplam_uzunluk:

        bitis = baslangic + parca_boyutu

        parca = metin[baslangic:bitis]

        parcalar.append(parca)

        baslangic = bitis - kesisim

    return parcalar


parcalar = metni_parcala(metin)

document_id = 1

for i, parca in enumerate(parcalar, start=1):

    response = client.models.embed_content(
        model="gemini-embedding-001",
        contents=parca
    )

    embedding = response.embeddings[0].values

    embedding_text = "[" + ",".join(map(str, embedding)) + "]"


    cursor.execute(
        """
        INSERT INTO chunks
        (document_id, content, embedding, created_at, updated_at)
        VALUES (%s, %s, %s::vector, NOW(), NOW())
        """,
        (document_id, parca, embedding_text)
    )

    print(f"Chunk {i} PostgreSQL'e kaydedildi.")


connection.commit()

print("\nTüm chunk'lar başarıyla kaydedildi!")


cursor.close()
connection.close()