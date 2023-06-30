# NewsAggregator Api
 A news aggregator laravel application that pulls articles from three news sources(NewsApiOrg, Guardian News Api, New York Times) and displays them in a clean, easy-to-read format

## Table of Contents

* [Features](#Features)
* [Configuration](#Configuration)
* [Tech Stack](#Tech%Stack)
* [Documentation](#Documentation)

## Features
<li> User Authentication(Sign up & Sign in) with laravel sanctum</li>
<li>Search for news artciles by author, category </li>



## Configuration

Project is built with Laravel version 10

<li>Ensure that php version >= 8.1 is installed</li>
<li>Install and setup composer</li>
<li>Get api key for newsapi from -[NewsApiOrg:](https://newsapi.org/)</li>
<li>Get api key for Guardians news from -[Guardian News:](https://open-platform.theguardian.com/documentation/)</li>
<li>Get api key for new york times from -[NewYorkTimes:](https://developer.nytimes.com/apis/)</li>

<li>Create .env file in the root directory of the project, copy the content of .env.example into your .env file</li>

<li>Update this section in your .env with your api keys</li>

```bash
GUARDIAN_NEWS_API_kEY=
NEWYORKTIMES_API_KEY=
NEWSAPIORG_API_KEY=
```

<li>Run the following commands</li>

```bash
git clone this repo
cd into the project
composer install
php artisan migrate
php artisan l5-swagger:generate
php artisan serve
```

## Tech Stack
Php Laravel

## Documentation

<li>Visit swagger doc on http://{LOCAL_HOST_ADDRESS}/api/documentation#/Users</li>
