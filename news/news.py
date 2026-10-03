"""Optional: refresh news_data.json from the command line (e.g. a cron job).

The website refreshes the cache by itself (see inc/news.php), so this script is only
needed if you prefer cron. Usage:  NEWSAPI_KEY=... python news/news.py
"""
import datetime
import json
import os
import sys
import time

import requests

API_KEY = os.environ.get("NEWSAPI_KEY", "")
DOMAINS = ",".join([
    "motor1.com", "carscoops.com", "autoblog.com", "caranddriver.com", "motortrend.com",
    "thedrive.com", "jalopnik.com", "autocar.co.uk", "topgear.com", "carbuzz.com",
    "insideevs.com", "roadandtrack.com", "autoevolution.com", "autoexpress.co.uk",
    "hagerty.com", "autoweek.com", "evo.co.uk", "pistonheads.com", "electrek.co",
    "speedhunters.com", "drive.com.au", "carexpert.com.au",
])
# The free NewsAPI plan only allows articles from roughly the last 30 days.
FROM_DATE = (datetime.date.today() - datetime.timedelta(days=28)).isoformat()
FILE_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), "news_data.json")

if not API_KEY:
    sys.exit("Set the NEWSAPI_KEY environment variable first.")

try:
    response = requests.get(
        "https://newsapi.org/v2/everything",
        params={"domains": DOMAINS, "language": "en", "sortBy": "publishedAt",
                "pageSize": 100, "from": FROM_DATE},
        headers={"X-Api-Key": API_KEY, "User-Agent": "GuessTheCar/2.0"},
        timeout=15,
    )
    data = response.json()
    if response.status_code == 200 and data.get("status") == "ok" and data.get("articles"):
        data["fetchedAt"] = int(time.time())
        with open(FILE_PATH, "w", encoding="utf-8") as json_file:
            json.dump(data, json_file, indent=4, ensure_ascii=False)
        print(f"Saved {len(data['articles'])} articles to {FILE_PATH}")
    else:
        print(f"Error {response.status_code}: {data.get('code')} {data.get('message')}")
except Exception as e:
    print(f"An error occurred: {e}")
