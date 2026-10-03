# Guess the Car

Guess the Car is a browser-based car trivia and guessing game built with PHP and MySQL. Each day reveals a new vehicle challenge, with layered hints and a five-guess limit. The project also includes a one-shot guessing mini-game, a dream car finder, car database browsing, and car news.

## Overview

The site is designed as a light, static-friendly PHP app with dynamic game logic and JSON-backed content. It is built around a daily vehicle challenge where users progressively reveal hints until they identify the featured car. The game keeps progress in the browser via `localStorage`, while server-side validation handles guess checking.

Core experience:

- Daily car guessing challenge
- Five-image progression with clue reveals
- Daily counters and solved streak tracking
- One-shot challenge mode
- Dream car discovery tools
- Car database and news sections

## Features

### Daily Guess the Car

- New car released every day in a configured timezone
- Up to five guesses before the answer is revealed
- Clues expand after each wrong answer
- Browser-side progress persistence
- Daily statistics such as solves and streaks

### One Shot Prototype

- One image, one guess, no second chances
- Lightweight challenge mode built for quick play sessions

### Dream Car Finder

- Interactive car discovery flows
- Multiple discovery styles including category-based and wheel-based selection
- Separate mini-app under the `Dreamcar/` directory

### Car Database and News

- Browse car information and relationships
- Read recent automotive news from a configured API source
- Content stored in JSON and server-rendered PHP pages

## Tech Stack

- PHP 8+
- MySQL / MariaDB
- JavaScript for browser-side game logic
- JSON data files for curated content and news cache
- HTML/CSS for the UI

## Project Structure

```text
.
├── Dreamcar/              # Dream car finder app
├── Res/                   # Site assets and game images
├── assets/                # CSS and JS frontend code
├── data/                  # JSON datasets
├── inc/                   # PHP bootstrap, DB, game logic, and shared helpers
├── news/                  # Cached news data
├── .htaccess              # Apache URL rules
├── database.php           # Database browser page
├── daily.php              # Daily challenge entry page
├── game_display.php       # Main gameplay page
├── index.php              # Homepage
├── oneshot.php            # One-shot prototype page
├── news.php               # News page
├── LICENSE                # MIT license
├── README.md              # Project documentation
└── .gitignore             # Ignore local/dev artifacts
```

## Prerequisites

Before running locally, make sure you have:

- PHP installed and available in your PATH
- A MySQL-compatible database server running locally or remotely
- Optional: a NewsAPI key if you want live news data

## Configuration

The app reads database and app settings either from environment variables or a `config.php` file placed next to the site root (one directory above the project folder, depending on your hosting setup).

Example `config.php`:

```php
<?php

define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'guess_the_car');
define('NEWSAPI_KEY', 'your-newsapi-key');
define('GTC_MAX_DAY', 28);
define('GTC_START_DATE', '2026-09-15');
define('GTC_TIMEZONE', 'Europe/Istanbul');
```

You can also set the same values as environment variables instead of defining them in PHP.

## Local Development

From the project root, start a local PHP server:

```bash
php -S localhost:8000
```

Then open:

```text
http://localhost:8000/
```

If the project is deployed in a subdirectory instead of the web root, the app automatically detects the base path via `base_url()`.

## Database Notes

The backend expects a MySQL database connection and uses the `games` table for daily counters such as `enters` and `corrects`. Ensure your database is configured before launching the site. If the database is unavailable, the app logs the problem instead of crashing the page.

## News Integration

The news section fetches content using the configured NewsAPI key and caches it in the `news/` directory. The cache is intentionally stored as JSON and refreshed as needed by the news logic.

## Credits

- [confetti-js](https://github.com/Agezao/confetti-js) by Agezao (MIT), vendored in `assets/vendor/`.
- [Bootstrap](https://getbootstrap.com/) and [Bootstrap Icons](https://icons.getbootstrap.com/) (MIT), loaded from jsDelivr with SRI hashes.
- Concept car pictures and summaries are loaded live from [Wikipedia](https://www.wikipedia.org/) and stay under their own licences.
- News headlines from [NewsAPI.org](https://newsapi.org/).

## License

The **code** in this repository is released under the [MIT License](LICENSE).

**Images are not covered by the MIT License.** Car photos, test-mule photos and other pictures in `Res/`, `Dreamcar/` and the favicons/logo belong to their respective owners and are included only to run this non-commercial fan site. They may not be reused under the MIT License. If you own an image here and want it credited or removed, please open an issue.
Guess the Car

Guess the Car is a browser-based car trivia and guessing game built with PHP and MySQL. Each day reveals a new vehicle challenge, with layered hints and a five-guess limit. The project also includes a one-shot guessing mini-game, a dream car finder, car database browsing, and car news.

## Overview

The site is designed as a light, static-friendly PHP app with dynamic game logic and JSON-backed content. It is built around a daily vehicle challenge where users progressively reveal hints until they identify the featured car. The game keeps progress in the browser via `localStorage`, while server-side validation handles guess checking.

Core experience:
- Daily car guessing challenge
- Five-image progression with clue reveals
- Daily counters and solved streak tracking
- One-shot challenge mode
- Dream car discovery tools
- Car database and news sections

## Features

### Daily Guess the Car
- New car released every day in a configured timezone
- Up to five guesses before the answer is revealed
- Clues expand after each wrong answer
- Browser-side progress persistence
- Daily statistics such as solves and streaks

### One Shot Prototype
- One image, one guess, no second chances
- Lightweight challenge mode built for quick play sessions

### Dream Car Finder
- Interactive car discovery flows
- Multiple discovery styles including category-based and wheel-based selection
- Separate mini-app under the `Dreamcar/` directory

### Car Database and News
- Browse car information and relationships
- Read recent automotive news from a configured API source
- Content stored in JSON and server-rendered PHP pages

## Tech Stack

- PHP 8+
- MySQL / MariaDB
- JavaScript for browser-side game logic
- JSON data files for curated content and news cache
- HTML/CSS for the UI

## Project Structure

```text
.
├── Dreamcar/              # Dream car finder app
├── Res/                   # Site assets and game images
├── assets/                # CSS and JS frontend code
├── data/                  # JSON datasets
├── inc/                   # PHP bootstrap, DB, game logic, and shared helpers
├── news/                  # Cached news data
├── .htaccess              # Apache URL rules
├── database.php           # Database browser page
├── daily.php              # Daily challenge entry page
├── game_display.php       # Main gameplay page
├── index.php              # Homepage
├── oneshot.php            # One-shot prototype page
├── news.php               # News page
├── LICENSE                # MIT license
├── README.md              # Project documentation
└── .gitignore             # Ignore local/dev artifacts
```

## Prerequisites

Before running locally, make sure you have:
- PHP installed and available in your PATH
- A MySQL-compatible database server running locally or remotely
- Optional: a NewsAPI key if you want live news data

## Configuration

The app reads database and app settings either from environment variables or a `config.php` file placed next to the site root (one directory above the project folder, depending on your hosting setup).

Example `config.php`:

```php
<?php

define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'guess_the_car');
define('NEWSAPI_KEY', 'your-newsapi-key');
define('GTC_MAX_DAY', 28);
define('GTC_START_DATE', '2026-09-15');
define('GTC_TIMEZONE', 'Europe/Istanbul');
```

You can also set the same values as environment variables instead of defining them in PHP.

## Local Development

From the project root, start a local PHP server:

```bash
php -S localhost:8000
```

Then open:

```text
http://localhost:8000/
```

If the project is deployed in a subdirectory instead of the web root, the app automatically detects the base path via `base_url()`.

## Database Notes

The backend expects a MySQL database connection and uses the `games` table for daily counters such as `enters` and `corrects`. Ensure your database is configured before launching the site. If the database is unavailable, the app logs the problem instead of crashing the page.

## News Integration

The news section fetches content using the configured NewsAPI key and caches it in the `news/` directory. The cache is intentionally stored as JSON and refreshed as needed by the news logic.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.

## Notes

This repository appears to be a public, community-facing version of the site. The code is structured for straightforward deployment on a typical PHP hosting environment with Apache and MySQL.

