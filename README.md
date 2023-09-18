## Interview goals
1. If you have doubts about the requirements make assumptions and document them in a README.md file in the root of the project repo, together with any instructions on how to build/run
the utility

2. Which infrastructure resources you would need to run the utility daily in an automated fashion?

3. How would you deploy your code to the infrastructure?

4. How would you automate your deployments so that each push to your default branch will trigger a deployment to your production environment

5. Is it possible to get the data about past crosswords? If yes, how would you build a solution to get all the historical crosswords data?
   -


## Tech requirements
- PHP8.1+
- Redis (OPTIONAL if you don't want to use file caching)
- MySql / MariaDB

## PHP extensions
- Core
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- PHP Redis

## Tech stack
- OS:
  - Production - Linux Ubuntu
  - Windows/Linux/Mac
- [Nginx](https://docs.nginx.com/)
    - [new config](https://www.linode.com/docs/guides/how-to-enable-disable-website/)
- [Laravel V10.x](https://laravel.com/docs/10.x)

- [MySql] - MariaDB installed
    - [DB user setup](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql)

Check "/package-lock.js" and "/composer.lock" for dependencies.


## Nginx Server config
NOTE! Laravel ships with mobile server that is started via "php artisan serve" CLI command (app will run on "http://127.0.0.1:8000/"), read more in documentation.

### Windows
```
server {
    listen 80;
    listen [::]:80;
    # local dev domain
    server_name dev-up-comunitate.ro;
    # path to project
    root C:/stash/up_comunitate/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        # windows port manually started
        fastcgi_pass 127.0.0.1:9999; 
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Linux
```
Found in "/etc/nginx/sites-available/"
after new config use "ln -s /etc/nginx/sites-available/new_config.conf /etc/nginx/sites-enabled/new_config.conf" 
```
```
server {
    listen 80 default_server;

    server_name up-comunitate.ro www.up-comunitate.ro

    access_log /var/log/nginx/up_comunity_access.log;
    error_log /var/log/nginx/up_comunity_error.log;
        
    root /var/www/up_comunity/public;
	index index.php index.html;

	# serve static files directly
	location ~* \.(jpg|jpeg|gif|css|png|js|ico|html)$ {
		access_log off;
		expires max;
		log_not_found off;
	}

	# removes trailing slashes (prevents SEO duplicate content issues)
	if (!-d $request_filename)
	{
		rewrite ^/(.+)/$ /$1 permanent;
	}

	# enforce NO www
	if ($host ~* ^www\.(.*))
	{
		set $host_without_www $1;
		rewrite ^/(.*)$ $scheme://$host_without_www/$1 permanent;
	}

	# unless the request is for a valid file (image, js, css, etc.), send to bootstrap
	if (!-e $request_filename)
	{
		rewrite ^/(.*)$ /index.php?/$1 last;
		break;
	}

	location / {
		try_files $uri $uri/ /index.php?$query_string;
	}

	location ~* \.php$ {
		try_files $uri = 404;
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass unix:/run/php/php8.1-fpm.sock; # may also be: 127.0.0.1:9000;
		fastcgi_index index.php;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		include fastcgi_params;
	}

	location ~ /\.ht {
		deny all;
	}
}
```
