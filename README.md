# Public Sector Budget Management Platform

A Laravel 12 application for managing and visualizing government budgets, expenses, and analytics.
It provides a **versioned REST API** for integrations and a **web-based admin dashboard** for operational use.

---

## Overview

GovBudget is designed to support transparency and efficient budget management through:

- Centralized budget and expense tracking
- Public analytics endpoints for data visibility
- Administrative tools for managing users, units, and fiscal years
- Full audit logging of system activity

---

## Features

### API (v1)

- Token-based authentication (Laravel Sanctum)
- Budgets and budget items (CRUD)
- Expenses (CRUD + summaries)
- Government units with hierarchy
- Fiscal years (single active year enforcement)
- User management (admin)
- Audit logs (read-only, filterable)
- Public analytics endpoints

### Web Dashboard

- Budget and expense overview
- Analytics visualization
- Admin panels:
    - Users
    - Government units
    - Fiscal years
    - Audit logs

### Core Capabilities

- Account activation via signed URLs
- Role-based access control (policies + middleware)
- Centralized audit logging
- Background jobs for analytics recalculation
- Cache invalidation for analytics consistency
- API documentation via Scribe
- Configurable rate limiting, CORS, and security headers

---

## Tech Stack

### Backend

- PHP 8.2
- Laravel 12
- Laravel Sanctum (authentication)
- MySQL
- Database-backed queue and cache

### Frontend

- Blade templates
- Vite
- Vanilla JavaScript
- CSS

### Tooling

- Composer
- Node.js + npm
- Pest (testing)
- Scribe (API documentation)

---

## Getting Started

### Prerequisites

- PHP ≥ 8.2
- Composer
- Node.js (LTS)
- MySQL
- (Optional) Redis for cache/queue

---

### 1. Clone the Repository

```bash
git clone <repo-url>
cd gov-budget-platform
```

---

### 2. Environment Setup

```bash
cp .env.example .env
```

Update:

- Database credentials
- Mailer configuration
- Queue & cache drivers
- API-related configs

---

### 3. Install Dependencies & Build

```bash
composer install
php artisan key:generate
php artisan migrate

npm install
npm run build
```

---

### 4. (Optional) Seed Database

```bash
php artisan db:seed
```

---

## Running the Application

### Development (recommended)

```bash
composer run dev
```

This starts:

- Laravel server
- Queue worker
- Log viewer
- Vite dev server

---

### Manual Setup

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

---

### Access

- Web UI: http://localhost:8000
- API: http://localhost:8000/api

---

## API Overview

### Authentication

```http
POST /api/v1/auth/login
POST /api/v1/auth/logout
```

Uses Bearer token (Sanctum).

---

### Core Endpoints

- `/api/v1/budgets`
- `/api/v1/budget-items`
- `/api/v1/expenses`
- `/api/v1/government-units`
- `/api/v1/fiscal-years`
- `/api/v1/users`
- `/api/v1/audit-logs`
- `/api/v1/analytics`

---

## Account Activation Flow

- User receives signed URL with token
- GET `/activate-account/{token}` → show form
- POST `/activate-account/{token}` → activate account

---

## Events & Background Processing

### Events

- BudgetModified
- ExpenseModified
- FiscalYearModified
- GovernmentUnitModified
- UserModified

### Jobs

- RecalculateBudgetAnalytics

### Services

- AnalyticsService
- BudgetAnalyticsCalculator

These handle:

- Analytics recalculation
- Cache invalidation
- Audit logging

---

## API Documentation

Generated using **Scribe**:

```bash
php artisan scribe:generate
```

---

## Deployment Notes

Ensure:

- Proper `.env` configuration
- Queue worker is running
- Secure secrets (`APP_KEY`, DB credentials)
- Correct CORS and security headers
- Production cache/session setup (Redis recommended)

---

## License

This project is built on Laravel, licensed under the MIT License.
