# Employee Management REST API

A RESTful API built with **PHP 8.2** and **Laravel 10.x** for managing employee records without a database. The
application focuses on clean architecture, separation of concerns, strict typing, and predictable HTTP/JSON API
behavior.

---

## Requirements

- **PHP:** `>= 8.2` (with standard Laravel extensions: `mbstring`, `xml`, `ctype`, `json`, `tokenizer`, `openssl`)
- **Composer:** `>= 2.x`
    - Run `php ./get-composer.php` to download version 2.11.2.

*(No external database, Redis instance, or Docker setup is required; however, a docker compose file is provided
for convenience.)*

---

## Setup & Installation

1. **Clone the repository and navigate into the project directory:**

```bash
git clone <repository-url>
cd <project-directory>
```

2. **Install PHP dependencies:**
   Note: There are several vulnerabilities reported against Laravel 10.x. Since this is not a production application,
   their IDs have been explicitly ignored in the `composer.json` file.

```bash
php composer.phar install
```

3. **Configure the environment:**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Start the local development server:**

```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000/api/employees`.

### Docker

```bash
docker-compose up -d
```

The API will be available at `http://localhost:8000/api/employees`.

---

## Running Automated Tests

The test suite covers listing employees, retrieving by ID, creating records, input validation, duplicate email
prevention, deletion, and 404/409 error responses.

Run the suite via PHPUnit:

```bash
php ./vendor/bin/phpunit
```

---

## API Reference & Example Usage

All endpoints expect and return JSON. Include `Accept: application/json` in your request headers.

| Method   | Endpoint              | Description                      | Success Status   |
|----------|-----------------------|----------------------------------|------------------|
| `GET`    | `/api/employees`      | Retrieve all employees           | `200 OK`         |
| `GET`    | `/api/employees/{id}` | Retrieve a single employee by ID | `200 OK`         |
| `POST`   | `/api/employees`      | Create a new employee            | `201 Created`    |
| `DELETE` | `/api/employees/{id}` | Delete an employee by ID         | `204 No Content` |

### `curl` Examples

**List all employees:**

```bash
curl -s -H "Accept: application/json" [http://127.0.0.1:8000/api/employees](http://127.0.0.1:8000/api/employees)
```

**Retrieve an employee by ID:**

```bash
curl -s -H "Accept: application/json" [http://127.0.0.1:8000/api/employees/1](http://127.0.0.1:8000/api/employees/1)
```

**Create a new employee:**

```bash
curl -i -X POST [http://127.0.0.1:8000/api/employees](http://127.0.0.1:8000/api/employees) \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "firstName": "Ada",
    "lastName": "Lovelace",
    "email": "ada.lovelace@example.com"
  }'
```

**Delete an employee:**

```bash
curl -i -X DELETE [http://127.0.0.1:8000/api/employees/1](http://127.0.0.1:8000/api/employees/1) \
  -H "Accept: application/json"
```

---

## Architecture & Design Decisions

### 1. Separation of Concerns

* **HTTP Layer (`EmployeeController`, `StoreEmployeeRequest`, `EmployeeResource`):** Handles routing, request
  validation, HTTP status codes, and JSON serialization. Controllers contain zero business logic or persistence details.
* **Data Transfer Object (`EmployeeData`):** Uses PHP 8.2 `readonly` properties to pass normalized, strongly typed data
  from the request layer into the service and repository layers.
* **Business Logic Layer (`EmployeeService`):** Enforces domain rules—such as case-insensitive email uniqueness and
  existence checks—independently of how records are stored.
* **Data Access Layer (`EmployeeRepositoryInterface` & `InMemoryEmployeeRepository`):** Abstracts storage behind an
  interface bound in `AppServiceProvider`, allowing the in-memory store to be swapped for an Eloquent/database
  implementation without changing the service or controller layers.
* **Centralized Error Handling (`Handler.php`):** Domain exceptions (`EmployeeNotFoundException`,
  `DuplicateEmailException`) are mapped in the exception handler to clean JSON error payloads (`404 Not Found` and
  `409 Conflict`), keeping controllers free of `try/catch` boilerplate.

### 2. No Database Persistence Across Requests

In standard PHP execution models, static class properties reset at the end of every HTTP request. For this reason,
modified records are not persisted across requests. However, I've included a couple of example employee records in the
`EmployeeController` constructor.

* `InMemoryEmployeeRepository` stores the employee collection in memory.
* The store initializes automatically with two sample records (`ID 1` and `ID 2`) on first access, and tracks a
  monotonic ID sequence so deleted IDs are never reused.

### 3. Assumptions

* **Email Uniqueness:** Duplicate email addresses are rejected with `409 Conflict`.
* **Route Constraints:** `{id}` route parameters are constrained with `->whereNumber('id')` so non-numeric identifiers
  return a clean `404 Not Found` at the routing layer.
* **Response Wrapping:** `JsonResource::withoutWrapping()` is enabled in `AppServiceProvider` so single-resource
  responses match the flat JSON object structure specified in the requirements.
