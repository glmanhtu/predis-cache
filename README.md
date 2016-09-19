#Predis cache
=========================================
Simple Wordpress plugin to manage cache in Redis

This project are under development, if you are interested with this project, please contributing

##Features

*	Support clean cache of home page when we have new post
* 	Support clean cache of home page & cache of specific post which we want to delete or update
*  Support clean entire cache

##How to install
*	Download the zip version of this repository 
* 	Unzip & replace redis_host in predis-cache.php file
*  	Compress again
* 	Install to your WP via file upload
*	Active Predis-Cache plugin

##Usage
*	When you create or update new post, the cache of home page and cache of the post (if update) will be clean automatic
* 	When you want to clean entire cache in Redis, hover mouse in top menu bar at Predis Cache menu & select `Empty all cache on this site`