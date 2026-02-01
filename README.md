
# Sportify (ScoutMarket v1.0)

Sportify is a high-performance vertical social network designed to bridge the gap between football talent (Players) and professional recruiters (Scouts).

It combines the engagement of a TikTok-style video feed with LinkedIn-style professional networking tools.


## Key Features

### Multi-Role System
Distinct workflows for:
- Players: Talent showcase
- Scouts: Discovery and recruitment
- Admins: Moderation and analytics

### Vertical Video Feed
- Infinite scroll
- Auto-play
- Likes and comments
- Optimized for mobile engagement

### Advanced Scouting Engine
Filter talent by:
- Position
- Age
- Location
- Preferred foot
- Club status

### Real-Time Messaging
- Direct 1:1 communication between Scouts and Players

### Admin Dashboard
- User management
- Content moderation
- Platform analytics

### Performance Metrics
- Physical attributes: Height, Weight
- Digital engagement: Views, Likes

---

## Local Installation Guide

Follow these steps to set up the project locally.

---

## Prerequisites

- PHP 8.4+ (Strict Requirement)
- Composer
- Node.js and NPM
- MySQL 8.0+

---

## Installation Steps

### Clone the Repository
```bash
git clone https://github.com/Gabo8912/Sportify.git
cd sportify
````

### Install Backend Dependencies

```bash
composer install
```

### Install Frontend Dependencies

```bash
npm install
```

### Environment Setup

```bash
cp .env.example .env
```

Edit `.env` and set the following:

* DB_DATABASE
* DB_USERNAME
* DB_PASSWORD

### Generate App Key and Link Storage

```bash
php artisan key:generate
php artisan storage:link
```

### Migrate and Seed Database

```bash
php artisan migrate:fresh --seed
```

This will create the database schema and inject demo data (Players, Scouts, Admin).

---

## Run the Application

Terminal 1 (Backend):

```bash
php artisan serve
```

Terminal 2 (Frontend):

```bash
npm run dev
```

Access the application at:
[http://localhost:8000](http://localhost:8000)

---

## Default Credentials (Seeder)

| Role   | Name          | Email                                             | Password |
| ------ | ------------- | ------------------------------------------------- | -------- |
| Admin  | Admin Gavo    | [gavo00321@gmail.com](mailto:gavo00321@gmail.com) | password |
| Player | Lionel Messi  | [leo@miami.com](mailto:leo@miami.com)             | password |
| Scout  | Pep Guardiola | [pep@city.com](mailto:pep@city.com)               | password |

---

## Tech Stack

### Backend

* Laravel 12
* PHP 8.4
* MySQL

### Frontend

* Vue.js 3
* Inertia.js (SPA)
* Tailwind CSS
* Shadcn/UI

### Infrastructure

* Railway (Nixpacks)
* Docker-ready

```
```
