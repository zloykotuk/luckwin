# Luckwin

Невеликий застосунок «щасливе посилання»: користувач реєструється за юзернеймом і телефоном та отримує унікальне тимчасове посилання. За цим посиланням можна натиснути «I'm feeling lucky» — випадає число від 1 до 1000, парне/непарне визначає перемогу чи програш, а розмір виграшу залежить від того, наскільки велике число випало. Історія останніх ігор і саме посилання можна будь-коли перегенерувати або деактивувати.

Проєкт зроблений на Laravel 13 + Inertia.js v3 (Vue 3, без окремого API-клієнта) з Pinia для стану на фронтенді та Tailwind CSS v4 для стилів. Дані з бекенду завжди віддаються у форматі `{"data": {...}}` через `JsonResource`.

## Стек

- **Backend:** PHP 8.4, Laravel 13, PostgreSQL
- **Frontend:** Vue 3, Inertia.js, Pinia, Tailwind CSS v4, Vite, Laravel Wayfinder (типізовані виклики роутів/контролерів з TypeScript)
- **Інфраструктура:** Docker Compose (nginx + php-fpm + postgres) — весь застосунок збирається й запускається через Docker, нічого встановлювати на хості не потрібно
- **Тести:** Pest (unit + feature), Larastan (PHPStan), Laravel Pint

## Вимоги

Тільки **Docker** і **Docker Compose**. PHP, Composer чи Node.js на хості не потрібні — все виконується всередині контейнера `app`.

## Встановлення та запуск (все через Docker)

### 1. Налаштувати оточення

```bash
cp .env.example .env
```

`.env.example` вже налаштований під PostgreSQL і порти з `docker-compose.yml` (`APP_PORT=8080`, `FORWARD_DB_PORT=5432`). За потреби зміните ці значення в `.env` — їх підхоплює і застосунок, і `docker-compose.yml`.

### 2. Зібрати та підняти контейнери

```bash
docker compose up -d --build
```

Піднімається три контейнери:

- `nuxgame-app` — PHP-FPM + Node.js (Laravel і збірка фронтенду)
- `nuxgame-db` — PostgreSQL
- `nuxgame-nginx` — nginx, віддає застосунок на `http://localhost:${APP_PORT}` (типово `8080`)

### 3. Встановити залежності всередині контейнера

```bash
docker compose exec app composer install
docker compose exec app npm install
```

### 4. Згенерувати ключ застосунку і застосувати міграції

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

### 5. Зібрати фронтенд

```bash
docker compose exec app npm run build
```

Після цього застосунок доступний на **http://localhost:8080** (або на порту з `APP_PORT` у вашому `.env`).

## Корисні команди

Усе виконується всередині контейнера `app`:

```bash
# Тести (Pest, ізольована in-memory SQLite, реальна БД не чіпається)
docker compose exec app php artisan test

# Статичний аналіз
docker compose exec app vendor/bin/phpstan analyse

# Стиль коду
docker compose exec app vendor/bin/pint

# Лінт і типи фронтенду
docker compose exec app npm run lint:check
docker compose exec app npm run types:check

# Логи контейнерів
docker compose logs -f app

# Зупинити стек
docker compose down
```

## Структура бекенду

- `app/Http/Controllers` — тонкі контролери; для ендпоінтів з одним методом використовуються invokable Actions (`RegisterAction`, `ShowLinkAction`, `PlayLuckyAction`, `ShowHistoryAction`)
- `app/Http/Requests` — валідація вхідних даних
- `app/Data` — DTO/data-класи, якими контролери й сервіси обмінюються даними (моделі в аргументи сервісів/репозиторіїв не передаються)
- `app/Services` — уся бізнес-логіка
- `app/Repositories` — уся робота з базою даних, по одному репозиторію на модель
- `app/Helper` — допоміжні класи без стану (наприклад, нормалізація телефону)
