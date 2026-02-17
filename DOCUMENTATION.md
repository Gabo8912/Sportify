<div align="center">

# ⚽ Sportify (ScoutMarket v1.0)

[![Read Documentation](https://img.shields.io/badge/📘-Read%20Full%20Documentation-007bff?style=for-the-badge)](./DOCUMENTATION.md)
[![Project Status](https://img.shields.io/badge/Status-Production%20Ready-success?style=for-the-badge)](./README.md)

</div>

---
# Sportify: Comprehensive Technical Documentation (v1.0)

**Version:** 1.0.0 (MVP)  
**Status:** Production Ready  


## Table of Contents

1. Executive Summary & Tech Stack  
2. System Architecture  
3. Database Schema & Data Integrity  
4. Core Feature Implementation (Deep Dive)  
5. Security & Access Control  
6. Performance Optimization Strategies  
7. Admin Panel & Analytics  
8. Deployment & DevOps  
9. Future Roadmap & Scalability  

---

## 1. Executive Summary & Tech Stack

Sportify is a high-performance vertical social network tailored for the football industry. Unlike generic platforms, it enforces structured data collection (metrics, positions) relevant to sports recruitment while maintaining the engagement mechanics of modern social media such as infinite feeds and short-form video.

### Technology Stack

| Layer | Technology | Version | Rationale |
|------|-----------|---------|-----------|
| Backend | Laravel | 12.x | Robust ecosystem, strict typing with PHP 8.4, and efficient handling of complex relationships |
| Frontend | Vue.js | 3.4 | Reactive UI with Composition API for clean logic reuse |
| Bridge | Inertia.js | 1.0 | SPA behavior without maintaining a separate API repository |
| Styling | Tailwind CSS | 3.4 | Utility-first CSS, paired with Shadcn/UI for accessible components |
| Database | MySQL | 8.0+ | ACID compliance for transactional data |
| Infrastructure | Railway | Nixpacks | Containerized deployment with auto-scaling |

---

## 2. System Architecture

The application follows a Modern Monolithic Architecture (Majestic Monolith pattern).

- Unified codebase for backend and frontend
- Server-side routing via Laravel returning Inertia-rendered responses
- Asset bundling and HMR handled by Vite

### Directory Structure Overview

```text
/app
  ├── Http/Controllers   Business logic (VideoController, ProfileController)
  ├── Models             Eloquent ORM models (User, Profile, Video)
  ├── Policies           Authorization rules
  └── Services           Optional complex logic abstraction
/database
  ├── migrations         Schema version control
  └── seeders            Demo data population
/resources
  ├── js/Pages           Vue views (Home, Profile, Admin)
  ├── js/Components      Reusable UI components
````

---

## 3. Database Schema & Data Integrity

The database is designed following Third Normal Form (3NF), with selective denormalization for performance.

### Key Entity Relationships

#### Users (`users`)

* Central authentication entity
* Role enum: player, scout, admin
* Relationships: hasOne Profile, hasMany Videos

#### Profiles (`profiles`)

* Stores role-specific data
* Polymorphic validation logic based on role
* Foreign key `user_id` with ON DELETE CASCADE

#### Videos (`videos`)

* Stores storage path and metadata
* Denormalized counters: `views_count`, `likes_count` for performance

#### Interaction Tables

* `likes`: Composite unique index on user_id + video_id
* `followers`: Self-referencing many-to-many (follower_id, following_id)

---

## 4. Core Feature Implementation (Deep Dive)

### A. Player Flow & Video Engine

* Input: MP4/MOV upload

* Processing:

  1. MIME and size validation (max 100MB)
  2. Hashed file storage in `storage/app/public/videos`
  3. Database record creation

* Feed Implementation:

  * Backend: `Video::with('user.profile')->cursorPaginate(10)`
  * Frontend: Intersection Observer triggers pagination via Inertia

---

### B. Scouting Algorithm

Filtering example: left-footed striker under 20 in Madrid.

* Implemented via Laravel Local Scopes on User model

```php
public function scopeFilter($query, array $filters) {
    $query->when($filters['position'] ?? null, function ($q, $pos) {
        $q->whereHas('profile', fn($p) => $p->where('position', $pos));
    });
}
```

* Frontend debounce of 300ms reduces database load

---

### C. Messaging System

* Database-driven 1:1 messaging
* Messages grouped by sender/receiver pair
* Optimistic UI: messages render instantly while backend persists data

---

## 5. Security & Access Control

* Role-Based Access Control via middleware (e.g. EnsureUserIsAdmin)
* CSRF protection on all forms
* SQL injection prevention through Eloquent ORM and parameter binding
* Mass assignment protection using `$fillable`

---

## 6. Performance Optimization Strategies

### A. Eager Loading

* Prevents N+1 queries using `with(['user', 'profile'])`
* Reduces query count from O(n) to O(1)

### B. Atomic Counters

* Likes and views stored as integer counters
* Updated via atomic increment operations
* Ensures constant-time reads

### C. Asset Optimization

* Vite for minification and bundling
* Lazy loading of admin and modal components

---

## 7. Admin Panel & Analytics

* Aggregated metrics using Carbon date ranges
* Optional caching with `Cache::remember`
* Soft deletes for users and content to allow recovery

---

## 8. Deployment & DevOps

### Infrastructure

* Hosted on Railway with Nixpacks
* Containerized runtime

### Commands

Build:

```bash
npm install && npm run build && composer install --optimize-autoloader
```

Start:

```bash
php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
```

* `--force` required for non-interactive production migrations

### SSL / HTTPS

* HTTPS enforced in `AppServiceProvider`
* Prevents mixed content issues in modern browsers

---

## 9. Future Roadmap & Scalability

### Phase 2: Engagement

* Push notifications (Firebase / OneSignal)
* Activity and notification center

### Phase 3: Scale & Performance

* Video transcoding via FFmpeg and queues
* Search upgrade using Laravel Scout with Meilisearch

---

This documentation certifies that the project meets professional engineering standards for maintainability, security, and scalability.

```
```
