import os

from dotenv import load_dotenv

from google import genai
load_dotenv()
client = genai.Client(

api_key=os.getenv("GEMINI_API_KEY")

)
def cevap_olustur(soru: str, context: str) -> str:

    prompt = f"""


Aşağıdaki doküman parçalarını kullanarak soruyu cevapla.
Doküman parçaları:

{context}
Soru:

{soru}
Cevabı sadece verilen dokümanlara dayanarak ver.

Eğer cevap dokümanlarda yoksa bunu belirt.

Cevabı düz metin olarak ver, kod bloğu kullanma.

"""
    response = client.models.generate_content(
        model="gemini-3.5-flash-lite",
        contents=prompt
    )

    cevap = response.text or ""

    cevap = cevap.replace("```python", "")
    cevap = cevap.replace("```", "")

    return cevap.strip()