<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

##requirements:

- PHP 7.0.*
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

##Installation
- composer install
- you have to renamed the .env.example file to .env
- php artisan key:generate
- php artisan migrate --seed
- you only need to add the following Cron entry to your server: * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
- php artisan queue:work [Supervisor](https://laravel.com/docs/5.4/queues#supervisor-configuration)
