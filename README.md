QUEUE_CONNECTION=sync,
php artisan migrate:fresh
php artisan shield:generate --all
php artisan db:seed
