from chunking import metni_parcala
from embedding import embedding_olustur


def dokuman_kaydet(cursor, dosya_adi, metin):

    parcalar = metni_parcala(metin)

    cursor.execute(
        """
        SELECT id
        FROM documents
        WHERE filename = %s
        """,
        (dosya_adi,)
    )

    mevcut_document = cursor.fetchone()

    if mevcut_document:

        document_id = mevcut_document[0]

        cursor.execute(
            """
            DELETE FROM chunks
            WHERE document_id = %s
            """,
            (document_id,)
        )

    else:

        cursor.execute(
            """
            INSERT INTO documents
            (filename, created_at, updated_at)
            VALUES (%s, NOW(), NOW())
            RETURNING id
            """,
            (dosya_adi,)
        )

        document_id = cursor.fetchone()[0]

    for i, parca in enumerate(parcalar, start=1):

        embedding = embedding_olustur(parca)

        print(f"Chunk {i} embedding oluşturuldu.")
        print("Embedding boyutu:", len(embedding))

        embedding_text = "[" + ",".join(map(str, embedding)) + "]"

        cursor.execute(
            """
            INSERT INTO chunks
            (document_id, content, embedding, chunk_index, created_at, updated_at)
            VALUES (%s, %s, %s::vector, %s, NOW(), NOW())
            """,
            (
                document_id,
                parca,
                embedding_text,
                i
            )
        )

        print(f"Chunk {i} PostgreSQL'e kaydedildi.")

    return len(parcalar)