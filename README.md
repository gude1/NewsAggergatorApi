# NewsAggregator Api
 A news aggregator laravel application that pulls articles from three news sources(NewsApiOrg, Guardian News Api, New York Times) and displays them in a clean, easy-to-read format

## Table of Contents

* [Features](#Features)
* [Configuration](#Configuration and setup)
* [Tech Stack](#Tech%Stack)
* [Documentation](#Documentation)

## Features
<li> User Authentication(Sign up & Sign in) with laravel sanctum</li>
<li>Search for news articles by author, category </li>



## Configuration and setup
<h2>Ensure that you have properly setup docker on your local machine</h2>
<li>git clone the repo</li>
<li>cd into the root directory</li>
<li>Run the command below:</li>

```bash
docker compose up --build 
```
<p>Once the Docker container is running, open another terminal or command prompt, navigate to the root directory of the Laravel project, and run the following commands :</p>

```bash
docker-compose exec app touch database/database.sqlite
docker-compose exec app php artisan migrate
docker-compose exec app php artisan serve --host=0.0.0.0 --port=8000
```

```
<p>Laravel app should be available on http://127.0.0.1:8000/</p>

## Tech Stack
Php Laravel

## Documentation

<li>Visit swagger doc on http://127.0.0.1:8000/api/documentation</li>
