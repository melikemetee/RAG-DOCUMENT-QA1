from dotenv import load_dotenv
import psycopg2
import os


load_dotenv()


def veritabani_baglantisi():

    connection = psycopg2.connect(
        host=os.getenv("DB_HOST"),
        port=os.getenv("DB_PORT"),
        database=os.getenv("DB_NAME"),
        user=os.getenv("DB_USER"),
        password=os.getenv("DB_PASSWORD"),
        client_encoding="UTF8"
    )

    return connection