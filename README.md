# RAG-DOCUMENT-QA1

A Retrieval-Augmented Generation (RAG) based document question answering system.

The system allows users to upload TXT and PDF documents, process their content into smaller chunks, create embeddings, store them in PostgreSQL with pgvector, and ask questions about the uploaded documents.

## Features

* Upload TXT and PDF documents
* Extract text from PDF files
* Split documents into smaller chunks
* Create embeddings using Gemini
* Store document chunks and embeddings in PostgreSQL
* Use pgvector for vector similarity search
* Retrieve the most relevant document chunks
* Generate answers using Gemini
* Show source documents and retrieved chunks
* Display document and chunk information
* Laravel-based web interface
* FastAPI backend for AI and RAG operations

## Technologies

* Python
* FastAPI
* Google Gemini API
* PostgreSQL
* pgvector
* PHP
* Laravel
* HTML
* CSS
* pypdf
* psycopg2

## Project Structure

```
RAG-DOCUMENT-QA1/
│
├── documents/
│   ├── ornek.txt
│   ├── python.txt
│   ├── yapayzeka.txt
│   └── ...
│
├── api.py
├── rag.py
├── chunking.py
├── embedding.py
├── retrieval.py
├── generation.py
├── database.py
├── document_manager.py
├── database_test.py
├── save_embeddings.py
├── modeller.py
│
├── laravel-backend/
│   ├── app/
│   ├── resources/
│   ├── routes/
│   └── ...
│
├── .gitignore
└── README.md
```

## How It Works

The system follows these main steps:

### 1. Document Upload

The user uploads a TXT or PDF document through the Laravel web interface.

### 2. Text Extraction

TXT files are read directly.

PDF files are processed using `pypdf` and their text content is extracted.

### 3. Chunking

The document text is divided into smaller chunks.

The current chunking process uses:

* 100 words per chunk
* 20 words overlap

The overlap helps preserve context between neighboring chunks.

### 4. Embedding

Each chunk is converted into a numerical vector using the Gemini embedding model:

`gemini-embedding-001`

The embeddings are stored in PostgreSQL using the pgvector extension.

### 5. Question Embedding

When the user asks a question, the question is also converted into an embedding.

### 6. Similarity Search

The question embedding is compared with the stored document embeddings.

The most relevant chunks are retrieved using cosine distance.

### 7. Answer Generation

The retrieved chunks are sent to Gemini together with the user's question.

The model is instructed to answer using the provided document context.

If the required information is not found in the retrieved documents, the system returns:

> Bu bilgi yüklenen dokümanlarda bulunamadı.

### 8. Sources

The application displays the documents and chunks used to generate the answer.

This makes it easier to see where the answer came from.

## Database

The project uses PostgreSQL and pgvector.

Main tables:

### documents

Stores uploaded document information.

Main fields include:

* `id`
* `filename`
* `created_at`
* `updated_at`

### chunks

Stores document chunks and their embeddings.

Main fields include:

* `id`
* `document_id`
* `content`
* `embedding`
* `chunk_index`
* `created_at`
* `updated_at`

The `embedding` column uses a 3072-dimensional pgvector field.

## API Endpoints

The FastAPI backend provides the following endpoints:

### GET `/`

Checks whether the API is running.

### POST `/sor`

Receives a question and returns:

* question
* generated answer
* source documents
* retrieved chunks
* similarity information

Example request:

```
{
    "soru": "Python nedir?"
}
```

### POST `/dokuman-yukle`

Processes an uploaded document and stores its chunks and embeddings.

### GET `/dokumanlar`

Returns the uploaded documents and their chunk counts.

## Running the Project

### 1. Start PostgreSQL

Make sure PostgreSQL is running and the `rag_document_qa` database is available.

The pgvector extension should also be enabled.

### 2. Configure Environment Variables

Create a `.env` file in the project root.

Example:

```
GEMINI_API_KEY=your_api_key

DB_HOST=localhost
DB_PORT=5432
DB_NAME=rag_document_qa
DB_USER=postgres
DB_PASSWORD=your_password
```

Do not upload your `.env` file to GitHub.

### 3. Install Python Dependencies

Install the required Python packages:

```
pip install google-genai python-dotenv psycopg2-binary fastapi uvicorn pypdf
```

### 4. Start FastAPI

From the project root:

```
uvicorn api:app
```

The API will run at:

```
http://127.0.0.1:8000
```

FastAPI documentation is available at:

```
http://127.0.0.1:8000/docs
```

### 5. Start Laravel

Open another terminal and go to the Laravel project:

```
cd laravel-backend
```

Then run:

```
php artisan serve --port=8001
```

The web application will be available at:

```
http://127.0.0.1:8001
```

## Example

A user can upload a document containing information about Python and then ask:

```
Python nedir?
```

The system:

1. Converts the question into an embedding.
2. Searches the document embeddings.
3. Retrieves the most relevant chunks.
4. Sends the chunks and question to Gemini.
5. Generates an answer.
6. Displays the answer and its sources.

## Project Goal

The main goal of this project is to understand and implement the basic workflow of a RAG system by combining document processing, vector embeddings, similarity search, database technologies, and generative AI.

The project also provides practical experience with integrating Python-based AI services with a Laravel web application.

## Current Status

The core RAG workflow is working.

Implemented features:

* [x] TXT document upload
* [x] PDF document upload
* [x] PDF text extraction
* [x] Document chunking
* [x] Gemini embeddings
* [x] PostgreSQL database
* [x] pgvector
* [x] Vector similarity search
* [x] Gemini answer generation
* [x] Source display
* [x] FastAPI backend
* [x] Laravel web interface
* [x] GitHub repository

## Future Improvements

Possible future improvements include:

* Better chunking strategies
* More advanced document processing
* Improved source highlighting
* Document deletion
* User authentication
* Better error handling
* Improved UI/UX
* Support for additional document formats
* More advanced vector search
* Deployment of the application


GitHub: [melikemetee](https://github.com/melikemetee)
