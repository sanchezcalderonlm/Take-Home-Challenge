# Backend Take-Home challenge By Luis Miguel Sánchez Calderón

Requirements:
1. Data aggregation and storage: Implement a backend system that fetches articles
   from selected data sources (choose at least 3 from the provided list) and stores
   them locally in a database. Ensure that the data is regularly updated from the live
   data sources.
2. API endpoints: Create API endpoints for the frontend application to interact with the
   backend. These endpoints should allow the frontend to retrieve articles based on
   search queries, filtering criteria (date, category, source), and user preferences
   (selected sources, categories, authors).

## For this example we use 

https://newsapi.org/docs/client-libraries/php
https://developer.nytimes.com/docs/articlesearch-product/1/routes/articlesearch.json/get
https://source.opennews.org/rss/

We use these APIs since the others are not available to run the application.

## Requirements

* docker >= 20
* docker-compose >= 1.29
* php >= 8.3
* laravel >= 11
* mysql >= 8.0.15
* composer >= 2.0.7

## Installation

1. `sudo nano /etc/hosts`
2. `127.0.0.1 news-api.com`
3. `git clone `
4. `cd news`
5. `cp .env.example .env`
6. `docker build`
7. `docker up`

## In other bash console execute
8. `docker exec -it news_api_php bash`
9. `composer install`
10. `php artisan migrate --seed`
11. `set the correct env NEWS_API_ORG_KEY`
12. `set the correct env NY_TIME_KEY`
13. `php artisan queue:work`


The app will fetch all the data daily in the morning
You can see more information in the routes/console.php

If you want to execute the app for get the news manually 
You can call this endpoints
`POST http://news-api.com:8091/api/test/newsapi`
`POST http://news-api.com:8091/api/test/nytimes`
`POST http://news-api.com:8091/api/test/opennews`


## The app provide this endpoints:
`GET http://news-api.com:8091/api/apis`
This provide the catalog of the api that you want to use.

`GET http://news-api.com:8091/api/source`
This provides the catalog of the source. You can filter the catalog by the api_id provided in the endpoint above.
You can use page = integer for get the number of the page.

`GET http://news-api.com:8091/api/news`
This endpoint displays news and can be filtered using the following fields:
page = integer
api_id = integer
source_id = integer
author = string
title = string
description = string
content = string
published_at = date in format Y-m-d

Example
`GET http://news-api.com:8091/api/news?page=1&api_id=1&source_id=9`
