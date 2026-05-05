<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Docker Deployment

This repository includes a production-oriented Docker stack built around:

- `nginx` + `php-fpm` in one web container
- `sqlite` database
- file/database-backed cache, sessions, and queues
- dedicated `queue` and `scheduler` services

Files added for deployment:

- `Dockerfile`
- `compose.yaml`
- `docker/nginx/default.conf`
- `docker/php/entrypoint.sh`
- `docker/php/conf.d/app.ini`

Basic deployment flow:

1. Optionally create a dedicated Docker env file such as `.env.docker` if you want to override the `DOCKER_*` values defined in `compose.yaml`.
2. Start the stack with `docker compose up --build -d`.
3. The `web` service will run `php artisan migrate --seed --force` automatically when both `DOCKER_RUN_MIGRATIONS=true` and `DOCKER_RUN_SEEDERS=true`.
4. The public app will be available on `http://localhost` or the port set by `DOCKER_WEB_PORT`.

Notes:

- The compose stack uses `DOCKER_*` variables so it does not accidentally inherit your local Laravel `.env` values.
- The Docker deployment stores SQLite in the named volume `sqlite_data` at `/var/lib/srhr/database.sqlite`.
- The `web` image is self-contained and runs both `php-fpm` and `nginx`, so it does not depend on a separate `app` hostname at runtime.
- The default Docker env now seeds baseline CMS/app data on startup, which is useful for first-run SQLite deployments.
- The lightweight Docker defaults do not require Redis; sessions and cache use files, while the queue worker uses the database-backed queue tables created by migrations.
- The sample Docker env enables `APP_DEBUG` and sends Laravel logs to both stderr and `storage/logs/laravel.log`, so container logs show the actual exception during debugging.
- Uploaded media is persisted in the named Docker volume `storage_data`.
- Static uploads are served by nginx through `/storage/`.
- On container startup, the PHP entrypoint ensures the Laravel `public/storage` symlink exists by running `php artisan storage:link --force --no-interaction` when needed.
- If `APP_KEY` is missing at runtime, the PHP entrypoint generates one before Laravel boots so the container does not fail with the encryption key error.
- The queue worker and scheduler run as separate containers using the same application image.
- For first deployment, replace the sample `DOCKER_APP_KEY` with a real key.

To inspect runtime errors in Docker:

1. Run `docker compose logs -f web` to stream Laravel and nginx output from the web container.
2. If you need the file-based Laravel log too, run `docker compose exec web tail -f storage/logs/laravel.log`.
3. After debugging, set `DOCKER_APP_DEBUG=false` again before treating the stack as production-like.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
