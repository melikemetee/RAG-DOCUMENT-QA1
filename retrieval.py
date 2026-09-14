def benzer_chunklari_getir(cursor, soru_embedding, limit=3, esik_degeri=0.40):

    soru_embedding_text = "[" + ",".join(map(str, soru_embedding)) + "]"

    cursor.execute(
        """
        SELECT
            chunks.id,
            chunks.chunk_index,
            chunks.content,
            chunks.embedding <=> %s::vector AS distance,
            documents.filename
        FROM chunks
        JOIN documents
            ON chunks.document_id = documents.id
        WHERE chunks.embedding <=> %s::vector <= %s
        ORDER BY chunks.embedding <=> %s::vector
        LIMIT %s
        """,
        (
            soru_embedding_text,
            soru_embedding_text,
            esik_degeri,
            soru_embedding_text,
            limit
        )
    )

    return cursor.fetchall()