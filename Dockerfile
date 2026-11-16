FROM php:8.2-zts-alpine AS build

WORKDIR /app

COPY composer.json composer.lock get-composer.php ./

RUN php get-composer.php \
    && php composer.phar install --no-dev --no-interaction --no-progress --no-scripts --no-suggest --optimize-autoloader

FROM php:8.2-zts-alpine AS runtime

WORKDIR /app

COPY --from=build /app/vendor /app/vendor
COPY . .

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host", "0.0.0.0"]
