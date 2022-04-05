cd /var/www/html
git clone https://github.com/laravel/laravel.git
cd /var/www/html/laravel
composer install
cp -a .env.example .env
php artisan key:generate
php artisan config:cache
