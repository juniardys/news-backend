from dotenv import load_dotenv
from datetime import datetime
from slugify import slugify
import mysql.connector
import os
import requests
import string

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
new_york_times_url = "https://api.nytimes.com/svc/news/v3/content/{source}/all.json"
new_york_times_type = "newyorktimes"

# The Guardian Initialization
the_guardian_key = os.getenv("THE_GUARDIAN_KEY")
the_guardian_url = "https://content.guardianapis.com/search"
the_guardian_type = "theguardian"

# News API Initialization
news_api_key = os.getenv("NEWS_API_KEY")
news_api_url = "https://newsapi.org/v2/top-headlines"
news_api_type = "newsapi"

# Load categories
print("Load categories...")
categoriesbyname = {}
categoriesbykey = {}
sql = "SELECT * FROM `categories`"
cursor.execute(sql)
result = cursor.fetchall()
if result:
    for category in result:
        categoriesbyname[category["name"]] = category["id"]
        categoriesbykey[category["key"]] = category["id"]
print("Successfully loaded categories")

# Load authors
print("Load authors...")
authors = {}
sql = "SELECT * FROM `authors`"
cursor.execute(sql)
result = cursor.fetchall()
if result:
    for author in result:
        authors[author["name"]] = author["id"]
print("Successfully loaded authors")

# New York Times
print("New York Times: Retrieved sources from db...")
sql = "SELECT * FROM `sources` WHERE `type` = %s"
params = (new_york_times_type,)
cursor.execute(sql, params)
sources = cursor.fetchall()
print("New York Times: Successfully retrieved sources from db.")
if sources:
    for source in sources:
        print("New York Times: Fetch API (" + source["name"] + ")...")
        response = requests.get(new_york_times_url.format(source = source["key"]), {
            'api-key': new_york_times_key,
            'limit': 25,
        })
        data = response.json()
        if data.get("status", None) == "OK":
            print("New York Times: Successfully fetch API (" + source["name"] + ").")
            print("New York Times: Save into database...")
            for news in data["results"]:
                # check if the news valid content
                if not news["title"] or not news["url"]:
                    continue

                if not news["byline"]:
                    news["byline"] = "BY ANONYMOUS"

                # check if news exists
                sql = "SELECT * FROM `news` WHERE `title` = %s AND source_id = %s"
                params = (news["title"], source["id"],)
                cursor.execute(sql, params)
                result = cursor.fetchone()
                if not result:
                    # define source
                    sourceid = source["id"]

                    # define category
                    categoryid = categoriesbyname.get(news["section"], None)
                    if not categoryid:
                        # insert category to database
                        categoryname = news["section"]
                        categorykey = slugify(categoryname)
                        sql = "INSERT INTO `categories` (`key`, `name`, `created_at`, `updated_at`) VALUES (%s, %s, NOW(), NOW())"
                        params = (categorykey, categoryname,)
                        cursor.execute(sql, params)
                        db.commit()
                        categoryid = cursor.lastrowid
                        categoriesbyname[categoryname] = categoryid
                        categoriesbykey[categorykey] = categoryid

                    # define author
                    authorname = string.capwords(news["byline"][3:])
                    authorkey = slugify(authorname)
                    authorid = authors.get(authorname, None)

                    if not authorid:
                        # insert author to database
                        sql = "INSERT INTO `authors` (`key`, `name`, `created_at`, `updated_at`) VALUES (%s, %s, NOW(), NOW())"
                        params = (authorkey, authorname,)
                        cursor.execute(sql, params)
                        db.commit()
                        authorid = cursor.lastrowid
                        authors[authorname] = authorid

                    # get image
                    image = ""
                    if news["multimedia"]:
                        findimage = next((item for item in news["multimedia"] if item['format'] == "Normal"), None)
                        if findimage:
                            image = findimage["url"]

                    # convert news date
                    try:
                        createdat = datetime.strptime(news['created_date'], '%Y-%m-%dT%H:%M:%S%z') if news['created_date'] else datetime.now()
                    except:
                        createdat = datetime.now()
                    try:
                        updatedat = datetime.strptime(news['updated_date'], '%Y-%m-%dT%H:%M:%S%z') if news['updated_date'] else datetime.now()
                    except:
                        updatedat = datetime.now()
                    try:
                        publishedat = datetime.strptime(news['published_date'], '%Y-%m-%dT%H:%M:%S%z') if news['published_date'] else datetime.now()
                    except:
                        publishedat = datetime.now()

                    # insert news
                    sql = "INSERT INTO `news` (title, content, image, original_url, source_id, category_id, author_id, published_at, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
                    params = (news["title"], news.get("abstract", None), image, news["url"], sourceid, categoryid, authorid, publishedat, createdat, updatedat,)
                    cursor.execute(sql, params)
                    db.commit()
            print("New York Times: Successfully save into database.")
        else:
            print("New York Times: Failed fetch API (" + source["name"] + ").")

# The Guardian
print("The Guardian: Retrieved sources from db...")
sql = "SELECT * FROM `sources` WHERE `type` = %s"
params = (the_guardian_type,)
cursor.execute(sql, params)
source = cursor.fetchone()
print("The Guardian: Successfully retrieved sources from db.")
if source:
    print("The Guardian: Fetch API (" + source["name"] + ")...")
    response = requests.get(the_guardian_url, {
        'api-key': the_guardian_key,
        'page-size': 50,
    })
    data = response.json()
    if data.get("response", {}).get("status", None) == "ok":
        print("The Guardian: Successfully fetch API.")
        print("The Guardian: Save into database...")
        for news in data["response"]["results"]:
            # check if the news valid content
            if not news["webTitle"] or not news["webUrl"]:
                continue

            # check if news exists
            sql = "SELECT * FROM `news` WHERE `title` = %s AND source_id = %s"
            params = (news["webTitle"], source["id"],)
            cursor.execute(sql, params)
            result = cursor.fetchone()
            if not result:
                # define source
                sourceid = source["id"]

                # define category
                categoryid = categoriesbykey.get(news["sectionId"], None)
                if not categoryid:
                    # insert category to database
                    categoryname = news["sectionName"]
                    categorykey = news["sectionId"]
                    sql = "INSERT INTO `categories` (`key`, `name`, `created_at`, `updated_at`) VALUES (%s, %s, NOW(), NOW())"
                    params = (categorykey, categoryname,)
                    cursor.execute(sql, params)
                    db.commit()
                    categoryid = cursor.lastrowid
                    categoriesbyname[categoryname] = categoryid
                    categoriesbykey[categorykey] = categoryid

                # define author
                authorname = "The Guardian"
                authorkey = slugify(authorname)
                authorid = authors.get(authorname, None)

                if not authorid:
                    # insert author to database
                    sql = "INSERT INTO `authors` (`key`, `name`, `created_at`, `updated_at`) VALUES (%s, %s, NOW(), NOW())"
                    params = (authorkey, authorname,)
                    cursor.execute(sql, params)
                    db.commit()
                    authorid = cursor.lastrowid
                    authors[authorname] = authorid

                # convert news date
                try:
                    publishedat = datetime.strptime(news['webPublicationDate'], '%Y-%m-%dT%H:%M:%SZ') if news['webPublicationDate'] else datetime.now()
                except:
                    publishedat = datetime.now()

                # insert news
                sql = "INSERT INTO `news` (title, original_url, source_id, category_id, author_id, published_at, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
                params = (news["webTitle"], news["webUrl"], sourceid, categoryid, authorid, publishedat, publishedat, publishedat,)
                cursor.execute(sql, params)
                db.commit()
        print("The Guardian: Successfully save into database.")
    else:
        print("The Guardian: Failed fetch API.")

# News API
print("News API: Retrieved sources from db...")
sources = {}
sql = "SELECT * FROM `sources` WHERE `type` = %s"
params = (news_api_type,)
cursor.execute(sql, params)
result = cursor.fetchall()
if result:
    for source in result:
        sources[source["key"]] = source["id"]
print("News API: Successfully retrieved sources from db.")
listcategories = ['business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology']
for categorykey in listcategories:
    # define category
    categoryid = categoriesbykey.get(categorykey)

    print("News API: Fetch API (" + categorykey + ")...")
    response = requests.get(news_api_url, {
        'apiKey': news_api_key,
        'category': categorykey,
        'pageSize': 20,
    })
    data = response.json()
    if data.get("status", None) == "ok":
        print("News API: Successfully fetch API (" + categorykey + ").")
        print("News API: Save into database...")
        for news in data["articles"]:
            # check if the news valid content
            if not news["title"] or not news["url"]:
                continue

            if not news["author"]:
                news["author"] = "Anonymous"

            if not news["source"]["id"]:
                news["source"]["id"] = slugify(news["source"]["name"])

            # define source
            sourcename = news["source"]["name"]
            sourcekey = news["source"]["id"]
            sourceid = sources.get(sourcekey, None)
            # check source exists
            if not sourceid:
                sql = "INSERT INTO `sources` (`key`, `name`, `type`, `created_at`, `updated_at`) VALUES (%s, %s, %s, NOW(), NOW())"
                params = (sourcekey, sourcename, news_api_type,)
                cursor.execute(sql, params)
                db.commit
                sourceid = cursor.lastrowid

            # check if news exists
            sql = "SELECT * FROM `news` WHERE `title` = %s AND source_id = %s"
            params = (news["title"], sourceid,)
            cursor.execute(sql, params)
            result = cursor.fetchone()
            if not result:
                # define author
                authorname = news["author"]
                authorkey = slugify(authorname)
                authorid = authors.get(authorname, None)

                if not authorid:
                    # insert author to database
                    sql = "INSERT INTO `authors` (`key`, `name`, `created_at`, `updated_at`) VALUES (%s, %s, NOW(), NOW())"
                    params = (authorkey, authorname,)
                    cursor.execute(sql, params)
                    db.commit()
                    authorid = cursor.lastrowid
                    authors[authorname] = authorid

                # get image
                image = news["urlToImage"]

                # convert news date
                try:
                    publishedat = datetime.strptime(news['publishedAt'], '%Y-%m-%dT%H:%M:%SZ') if news['publishedAt'] else datetime.now()
                except:
                    publishedat = datetime.now()

                # insert news
                sql = "INSERT INTO `news` (title, content, image, original_url, source_id, category_id, author_id, published_at, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
                params = (news["title"], news["description"], image, news["url"], sourceid, categoryid, authorid, publishedat, publishedat, publishedat,)
                cursor.execute(sql, params)
                db.commit()
        print("News API: Successfully save into database.")
    else:
        print("News API: Failed fetch API (" + categorykey + ").")
