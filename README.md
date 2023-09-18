## Interview goals
**Q1:** If you have doubts about the requirements make assumptions and document them in a README.md file in the root of the project repo, together with any instructions on how to build/run
the utility

**A1:** I have noted my doubts in the "DOUBTS" section of the readme file.

**Q2:** Which infrastructure resources you would need to run the utility daily in an automated fashion?

**A2:** On a Linux server machine I would have installed SupervisorCTL (Documentation at the bottom of the page for installation) and have a daily cronjob that calls the execution logic daily at 00:00:00 with the current date to generate the crosswords

**Q3:** How would you deploy your code to the infrastructure?

**A3:** In the case of this small project, taking into account changes aren't done on a daily basis, I would either:
- connect to the server via SSH tunneling, navigate to the project directory and manually run a preset shell script(if on linux), that will pull my code from my git repository(**NOTE!** check included "deploy.sh" file)
- automatically noted in response "A4"

**Q4:** How would you automate your deployments so that each push to your default branch will trigger a deployment to your production environment

**A4:** Based on the GIT repository provider I would either:
- create a script that watches a specific branch (ex: production), connects to the server via SSH tunneling (that has a specific user attached to it, with reduced server directory access only for the project for security reasons). This would either be done using GitHub Actions or Atlasian Bitbucket pipeline system after reading their specific documentation.
- if I would need to take into account a rollback option to a previous application version, then I would link the script to either the Git branch tag system or watch if new "release" branches are created, deploy in a separate directory that has the release name on the server, and modify a directory symlink (targeted by the nGinx config) to point to the new directory branch. 

**Q5:** Is it possible to get the data about past crosswords? If yes, how would you build a solution to get all the historical crosswords data?

**A5:** I have implemented a solution where the crossword data is kept in a SQL database (in my case MySQL). On first data retrieval check if data exists in a cache key "crosswords_{url_query_date}" and use the data from there, else fallback to query the database based on the specified date and cache is result list is not empty. Would probably add a method to flush/re-cache a specific cache key in case of manual text update.

## DOUBTS and MENTIONS
- Taking into account the lack of specificity if the data should be dynamically generated, I have opted to insert the provided test example in the database via first of create logic. Implementation of automate word generation with description for the word and directional offset would probably require more time to implement. Additionally I would add an administration panel for word correction as needed.
- I do not have a lot of hands on automation deployment experience (in production) since in past jobs we either: manually deployed the application(as mentioned in answer 3) or had the infrastructure set up for us by a devOps team, leaving me to concentrate on development. All my knowlage is based on informal talks with DevOps teams, online quides

## Tech stack / Infrastructure
- OS:
    - Production - Linux Ubuntu
    - Development - Windows / Linux
- [Nginx](https://docs.nginx.com/)
    - [new config](https://www.linode.com/docs/guides/how-to-enable-disable-website/)
- [Laravel V10.x](https://laravel.com/docs/10.x)

- [MySql] - MariaDB installed
    - [DB user setup](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql)

### Tech requirements
- PHP8.1+
- Redis (OPTIONAL if you don't want to use file caching)
- MySql / MariaDB

### PHP extensions
- Core
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- PHP Redis

Check "/package-lock.js" and "/composer.lock" for dependencies.

### APPLICATION STARTUP
After installing all the tech stack requirements:
- create 2 MySQL databases locally: fly_go_crosswords, fly_go_crosswords_testing
- configure application project_url and database connections in the .env file
- run in project root CLI:
  - "php artisan migrate" and "php artisan --env=testing migrate" and - to create database tables
  - OPTIONAL! if you don't want to configure nGinx server you can run "php artisan serve" to start up portable server
- "php artisan test" for PHPUnit route assertion url

### TESTING
The Laravel framework comes with build in testing features. For my tests I used a second MySql database "fly_go_crosswords_testing" configured in

- Run in root CLI the fallowing command "php artisan test" for PHPUnit route assertion url
- Manual tests can be done visiting the {base_url}/api/crosswords?date={YYYY-MM-DD valid date}


## Nginx Server config
**NOTE!** Laravel ships with mobile server that is started via "php artisan serve" CLI command (app will run on "http://127.0.0.1:8000/"), read more in documentation.
**NOTE!** If you are using Apache or Nginx, you will have to add the Application URL in your machine hosts file (Linux path="/etc/hosts", Windows path "c:\Windows\System32\Drivers\etc\hosts")

### Windows
```
server {
    listen 80;
    listen [::]:80;
    # local dev domain
     server_name {project_url} www.{project_url}
    # path to project
    root C:/stash/{project_name}/public;

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

    server_name {project_url} www.{project_url}

    access_log /var/log/nginx/{project_name}_access.log;
    error_log /var/log/nginx/{project_name}_error.log;
        
    root /var/www/{project_name}/public;
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
## Linux SupervisorCTL
### Ubuntu/Debian
Configs found in "/etc/supervisor/conf.d/".

Fallow [docs](https://www.digitalocean.com/community/tutorials/how-to-install-and-manage-supervisor-on-ubuntu-and-debian-vps) to set up new workers.
