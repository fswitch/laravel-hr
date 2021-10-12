<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## Install and run

```
mkdir -p /var/www/laravel
cd /var/www/laravel
git init
git remote add origin -f https://github.com/fswitch/laravel-hr.git
git config sparseCheckout true
echo "etc/*" > .git/info/sparse-checkout
git checkout
```

Check etc/docker-compose.yml for the correct user name and uid.
Check MySQL parameters in htdocs/.env
Copy htdocs/.env to etc/.env

```
mkdir /var/www/laravel/htdocs
chown $user /var/www/laravel/htdocs
cd /var/www/laravel/etc
```

run docker: `docker-compose up`

get into the docker environment and install required packages

```
docker exec -ti laravel_php /bin/bash
:/var/www$ cd /var/www/
:/var/www$ composer create-project laravel/laravel /var/www 8.6.2
:/var/www$ curl https://www.adminer.org/latest.php -L -o /var/www/public/adminer.php
:/var/www$ php artisan migrate
:/var/www$ composer require laravel/ui
:/var/www$ php artisan ui bootstrap
:/var/www$ npm install
:/var/www$ npm install bootstrap-maxlength --save
:/var/www$ composer require "almasaeed2010/adminlte=~3.1"
:/var/www$ npm --prefix=/var/www install admin-lte@3.1.0 --save
:/var/www$ composer require yajra/laravel-datatables-oracle:9.18.1
:/var/www$ composer require intervention/image
:/var/www$ composer require giggsey/libphonenumber-for-php
:/var/www$ npm install datatables.net-bs4 --save
:/var/www$ npm install bootstrap-select@1.13.18 --save
:/var/www$ npm install ajax-bootstrap-select@1.4.5 --save
:/var/www$ npm install bootstrap-autocomplete@2.3.7 --save
:/var/www$ npm install bootstrap-datepicker@1.9.0 --save
:/var/www$ npm install fontawesome@5.6.3 --save
:/var/www$ npm run dev
:/var/www$ composer require jamesmills/laravel-timezone
:/var/www$ cp vendor/jamesmills/laravel-timezone/src/database/migrations/add_timezone_column_to_users_table.php.stub database/migrations/`date +%Y_%m_%d_%H%M%S`_add_timezone_column_to_users_table.php
:/var/www$ cp vendor/jamesmills/laravel-timezone/src/config/timezone.php config/timezone.php
```

### After that you can pull the code and migrate DB.

```
cd /var/www/laravel
rm -f .git/info/sparse-checkout
git pull
docker exec -ti laravel_php /bin/bash
:/var/www$ cd /var/www
:/var/www$ php artisan migrate
:/var/www$ npm run dev
```

To post fake data to DB you should look at database/factories and database/seeders

Add positions first: `:/var/www$ php artisan db:seed --class=PositionSeeder`
Then you can populate DB with employees: `:/var/www$ php artisan db:seed --class=EmployeeSeeder`

http://127.0.0.1:8000
