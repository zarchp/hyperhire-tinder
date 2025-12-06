# Tinder App Backend API

This is a simplified Tinder-style backend API built with Laravel.  
It provides endpoints to fetch recommended people, list liked people, like/dislike them, undo last swipes  
and a scheduled job that notifies an admin when someone becomes "popular" (more than 50 likes).

---

## Assumptions

- There is no authentication system for this test.
- A **virtual user** is simulated using the `X-User-Id` HTTP header.
- If the header is not provided, the backend defaults to `user_id = 1`.
- The focus of this implementation is on **backend logic**, not on authentication or authorization.

---

## Tech Stack

- PHP (Laravel 12)
- MySQL
- L5-Swagger (OpenAPI documentation)

## API Overview

The backend exposes the following main endpoints:

| Method   | Endpoint          | Description                                                       |
| -------- | ----------------- | ----------------------------------------------------------------- |
| `GET`    | `/api/people`       | Get list of recommended people to swipe on |
| `GET`    | `/api/people/liked` | Get list of people that the current user has liked |
| `POST`   | `/api/people/swipes`      | Record a 'like' or 'dislike' action and check for a mutual match |
| `DELETE` | `/api/people/undo-swipes` | Undo a most recent swipe action performed by the current user |

For full details:  
**➡️ [API Documentation – /api/documentation](https://hyperhire-tinder.anzar.dev/api/documentation)**

---

## Features

### People & Matching

1. Header Authentication: Uses the `X-User-Id` header for user identification, default ID `1`.
1. User Recommendations: Returns a list of people the authenticated user hasn’t swiped on yet.
1. Swipe & Match: Saves each like/dislike and creates a match when both users like each other.
1. Undo Last Swipe: Lets the user remove their most recent swipe action.
1. Popularity Check: Runs a scheduled job to find popular users and notify the admin once they cross a like threshold.
1. Testing: Includes feature tests using Pest.
1. API Documentation: Fully documented with Swagger / OpenAPI 3.0.

### Cronjob / Scheduler

- Finds users who have received **more than 50 likes**.
- Sends an email notification to the admin.
- Marks them as notified to avoid duplicate alerts.

---

## Database Schema (Simplified)

![ERD](http://hyperhire-tinder.anzar.dev/erd.png)  

### users

- `id` (bigint, PK)
- `name` (string)
- `age` (unsigned int)
- `location` (string)
- `is_popular_notified` (boolean, default: false)
- `created_at`, `updated_at` (timestamps)

### user_pictures

- `id` (bigint, PK)
- `user_id` (FK → users.id, on delete cascade)
- `image_url` (string)
- `order` (unsigned int, default: 0)
- `created_at`, `updated_at` (timestamps)

### swipes

- `id` (bigint, PK)
- `actor_user_id` (FK → users.id, on delete cascade)
- `target_user_id` (FK → users.id, on delete cascade)
- `type` (enum: `like`, `dislike`)
- `description` (string, nullable)
- `created_at`, `updated_at` (timestamps)
- unique(`actor_user_id`, `target_user_id`)

---

## How to Run

1. **Clone the repository:**

   ```bash
   git clone https://github.com/zarchp/hyperhire-tinder.git
   cd hyperhire-tinder
   ```

1. Copy `.env.example` file:

   ```bash
   cp .env.example .env
   ```

1. Install dependencies:

   ```bash
   composer install
   ```

1. Generate the application key:

   ```bash
   php artisan key:generate
   ```

1. Run migration & seed:

   ```bash
   php artisan migrate --seed
   ```

1. Run the test suite (Pest):

   ```bash
   ./vendor/bin/pest
   ```

1. Start the local development server:

   ```bash
   php artisan serve
   ```

1. Generate swagger documentation:

   ```bash
   php artisan l5-swagger:generate
   ```

1. View the API documentation at:

   ```bash
   http://localhost:8000/api/documentation
   ```
