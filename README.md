# FinnTerest

FinnTerest is an application to track expenses, generate spending insights, and suggest savings strategies using data patterns. Often in our lives, we happen to come across scenarios when we have more expenses done than income generated, leading us to a problematic situation. This happens more in case of university students or new job holders. By entering salary, budget and allotment based expenses, the program aims at making it easy for the users to keep a track of their spendings. Additionally the program implements stock market and financial website news and keeps a split calculator for little calculations that one might need on the go.




ℹ️ Quick summary: a Laravel backend + TypeScript + Vite frontend for personal finance tracking and savings suggestions.

## Table of contents

- [Features](#features)
- [Tech stack & Architecture](#tech-stack--architecture)
- [Getting started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Backend (Laravel) quick start](#backend-laravel-quick-start)
  - [Frontend (Vite + Vue) quick start](#frontend-vite--vue-quick-start)
- [Environment variables](#environment-variables)
- [Testing](#testing)
- [Contributing](#contributing)
- [Roadmap](#roadmap)
- [Acknowledgements](#acknowledgements)
- [Contact](#contact)

## Features

- 💸 Expense tracking: add, edit, and categorize transactions.
- 📊 Spending insights: charts and summarized analytics to show trends and category breakdowns.
- 🔒 Authentication & Tokens: personal access tokens and role checking for APIs (Laravel Sanctum compatible).
- 📅 Set Monthly Budget: define and monitor personal budget limits each month.
- 📊 Category-Wise Spending Chart & Tracker: visualize spending by category and track against remaining budget.
- 🎯 Goal Creation & Viewing: set and monitor savings or spending goals.
- 📈 Stock Market Insights: display real-time/updated market trends with a ribbon on the homepage and top 5 stocks in the dashboard.
- 🧮 Simple Calculator: quick calculations (e.g., splitting bills among friends).
- 🏦 Savings Gateway: save leftover monthly budget directly via linked bank routes.
- 📑 Export Monthly Report as PDF: generate a detailed monthly progress report in PDF format.
- ⚡ Fast frontend: Vite + TypeScript + Vue for a responsive UI.


## Tech stack & Architecture

- Backend: PHP 8.x, Laravel (routes in `routes/`, controllers in `app/Http/Controllers`, models in `app/Models`).
- Frontend: TypeScript, Vue (single-file components in `frontend/src/components`), Vite for bundling.
- Database: SQLite included for local development (`database/database.sqlite`) with migrations in `database/migrations`.
- Tests: PHPUnit for backend tests (`tests/`).

The repository is split into `backend/` and `frontend/` folders for clear separation of concerns.

## Getting started

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16 and npm 
- SQLite 
- Git

On Windows (PowerShell), example package manager commands:

```powershell

 git clone https://github.com/Raiat37/FinnTerest.git

```

### Backend (Laravel) quick start

Open a terminal in `backend/` and run:

```powershell
cd backend;
composer install;
cp .env.example .env; # review env values
php artisan key:generate;
# ensure database path exists and is writable. using sqlite by default
php artisan migrate --seed;
php artisan serve --host=127.0.0.1 --port=8000
```

Notes:

- If you use a different DB, update `backend/.env` and `config/database.php`.
- There is a seeded user factory (`database/factories/UserFactory.php`) for convenience.

### Frontend (Vite + Vue) quick start

Open a terminal in `frontend/` and run:

```powershell
cd frontend;
npm install;
npm run dev
```

The frontend is configured to talk to the backend API; check `frontend/src/lib` or `frontend/vite.config.ts` to adjust the dev proxy or API base URL.

## Environment variables

- Backend: copy `backend/.env.example` to `backend/.env` and set DB and mail values as needed.
- Frontend: environment files are in `frontend/` (Vite uses `import.meta.env`).

## Testing

Run backend tests (from `backend/`):

```powershell
cd backend;
./vendor/bin/phpunit --testdox
```

Add frontend unit tests / e2e tests as the project evolves.

## Contributing

We welcome contributions. A minimal contributing workflow:

1. Fork the repository.
2. Create a feature branch (example: `feature/my-feature`).
3. Write tests for new features / fix bugs.
4. Open a PR and describe the change.

Guidelines:

- Keep changes focused and small.
- Run existing tests and linting before creating a PR.

## Roadmap

- Improve UI/UX for onboarding and goal setup.
- Mobile responsive layout and PWA support.
- Multi-currency support and exchange-rate history.


## Acknowledgements

- Built with Laravel and Vue/Vite.
- Thanks to open-source libraries used across the project (see `composer.json` and `frontend/package.json`).
- BD stock data: a portion of Bangladesh stock market data used in this project was scraped from the repository https://github.com/faysal515/bd-stock-api — thank you to the original project and its author for making the data available.

## Contact


If you need help or want to collaborate, open an issue or reach out to the repository owner.

