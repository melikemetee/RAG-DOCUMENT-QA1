from google import genai
from dotenv import load_dotenv
import os


load_dotenv()


client = genai.Client(
    api_key=os.getenv("GEMINI_API_KEY")
)


def embedding_olustur(metin):

    response = client.models.embed_content(
        model="gemini-embedding-001",
        contents=metin
    )

    return response.embeddings[0].values