def benzer_chunklari_getir(
    cursor,
    soru_embedding,
    limit=3,
    esik_degeri=0.40
):

    embedding_text = "[" + ",".join(
        map(str, soru_embedding)
    ) + "]"

    sorgu = """
        SELECT
            chunks.id,
            chunks.chunk_index,
            chunks.content,
            chunks.embedding <=> %s::vector,
            documents.filename
        FROM chunks
        JOIN documents
            ON chunks.document_id = documents.id
        WHERE chunks.embedding <=> %s::vector <= %s
        ORDER BY chunks.embedding <=> %s::vector
        LIMIT %s
    """

    cursor.execute(
        sorgu,
        (
            embedding_text,
            embedding_text,
            esik_degeri,
            embedding_text,
            limit
        )
    )

    sonuclar = cursor.fetchall()

    return sonuclar
