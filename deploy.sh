#turn application in maintenance ON
php artisan down;

#pull changes from git repository
git pull;

#migrate database changes
php artisan migrate;

# clear application cache, route cache, config cache
php artisan optimize;

#turn application in maintenance OFF
php artisan up;
