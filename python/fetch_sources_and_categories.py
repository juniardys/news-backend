from dotenv import load_dotenv
import mysql.connector
import os
import requests

load_dotenv()

# create db connection
db = mysql.connector.connect(
    host=os.getenv("DB_HOST"),
    user=os.getenv("DB_USERNAME"),
    password=os.getenv("DB_PASSWORD"),
    database=os.getenv("DB_DATABASE"),
)

cursor = db.cursor(dictionary=True)

# New York Times Initialization
new_york_times_key = os.getenv("NEW_YORK_TIMES_KEY")
new_york_times_url = "https://api.nytimes.com/svc/news/v3/content/section-list.json"

# The Guardian Initialization
the_guardian_key = os.getenv("THE_GUARDIAN_KEY")
the_guardian_url = "https://content.guardianapis.com/sections"

# News API Initialization
news_api_key = os.getenv("NEWS_API_KEY")
news_api_url = "https://newsapi.org/v2/top-headlines/sources"

# NEW YORK TIMES
# Fetch API
print("New York Times: Fetch API...")
response = requests.get(new_york_times_url, {
    'api-key': new_york_times_key,
})
print("New York Times: Successfully retrieved API.")
data = response.json()
if data:
    print("New York Times: Insert to database...")
    for result in data["results"]:
        key = result["section"]
        name = result["display_name"]

        # check if category exists in database
        sql = "SELECT * FROM `categories` WHERE `key` = %s"
        params = (key,)
        cursor.execute(sql, params)
        result = cursor.fetchone()
        if not result:
            # insert category to database
            sql = "INSERT INTO `categories` (`key`, `name`, `created_at`, `updated_at`) VALUES (%s, %s, NOW(), NOW())"
            params = (key, name,)
            cursor.execute(sql, params)
            db.commit()
    print('New York Times: Successfully insert to database')

# THE GUARDIAN
# Fetch API
print("The Guardian: Fetch API...")
response = requests.get(the_guardian_url, {
    'api-key': the_guardian_key
})
print("The Guardian: Successfully retrieved API.")
data = response.json()
if data.get("response", {}).get("status", None) == "ok":
    print("The Guardian: Insert to database...")
    for result in data["response"]["results"]:
        key = result["id"]
        name = result["webTitle"]

        # check if category exists in database
        sql = "SELECT * FROM `categories` WHERE `key` = %s"
        params = (key,)
        cursor.execute(sql, params)
        result = cursor.fetchone()
        if not result:
            # insert category to database
            sql = "INSERT INTO `categories` (`key`, `name`, `created_at`, `updated_at`) VALUES (%s, %s, NOW(), NOW())"
            params = (key, name,)
            cursor.execute(sql, params)
            db.commit()
    print('The Guardian: Successfully insert to database.')

# NEWS API
# Fetch API
print("News API: Fetch API...")
response = requests.get(news_api_url, {
    'apiKey': news_api_key
})
print("News API: Successfully retrieved API.")
data = response.json()
type = 'newsapi'
if data.get("status", None) == "ok":
    print("News API: Insert to database...")
    for result in data["sources"]:
        key = result["id"]
        name = result["name"]

        # check if source exists in database
        sql = "SELECT * FROM `sources` WHERE `key` = %s"
        params = (key,)
        cursor.execute(sql, params)
        result = cursor.fetchone()
        if not result:
            # insert source to database
            sql = "INSERT INTO `sources` (`key`, `name`, `type`, `created_at`, `updated_at`) VALUES (%s, %s, %s, NOW(), NOW())"
            params = (key, name, type,)
            cursor.execute(sql, params)
            db.commit()
    print('News API: Successfully insert to database')
