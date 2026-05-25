# Projeto Biblioteca

---

## Context

Com base nos documentos existentes dentro da pasta `docs/`, crie o sistema proposto.

- Use o subagent `software-architect` para criar um plano completo de implementação
- Use o subagent `backend-engineer` para implementar o plano
- Use o subagent `qa-engineer` para criar testes unitários e de integração, e testar a qualidade e funcionamento do sistema

---

## Implementation plan

---

### Step 0 — Docker & Environment Setup

**Files:** `app/Dockerfile`, `docker-compose.yml`, `.env`, `Makefile`

- Write `app/Dockerfile` with PHP 8.2-fpm, Composer, `pdo_mysql` extension, `php artisan serve`
- Update `docker-compose.yml`: point to `app/Dockerfile`; remove local MySQL (use external AWS RDS from `.env`)
- Extend `.env` with: `APP_KEY`, `APP_ENV`, `APP_DEBUG`, `MAIL_*`, `QUEUE_CONNECTION=sync`
- Populate `Makefile` with: `make install`, `make up`, `make migrate`, `make seed`, `make test`

**Acceptance:** `docker compose up` starts; `php artisan db:show` connects to external MySQL.

---

### Step 1 — Laravel Installation & Custom Directory Configuration

**Files:** `app/composer.json`, `app/bootstrap/app.php`, `app/config/`

- Install Laravel inside `app/` via `composer create-project laravel/laravel`
- Remap PSR-4 autoload in `composer.json` to use `src/controllers/`, `src/models/`, `src/services/`, `src/dtos/`, `src/mail/`, `src/middleware/`
- Set `database.migrations` config to `src/database/migrations`
- Run `php artisan key:generate`

**Acceptance:** `php artisan about` reports correct paths; `php artisan migrate:status` finds migrations dir.

---

### Step 2 — Database Migrations

**Files:** `app/src/database/migrations/`

Migration order (foreign key dependency):

1. `create_users_table` — id (uuid PK), name, cpf (unique), email (unique), role (enum: customer|librarian|admin), password, blocked (bool), SoftDeletes
2. `create_books_table` — id (uuid PK), title, author, media (enum: physical|digital), stock (int, nullable), digital_link (varchar, nullable), reserved (bool), reserve_expiration (datetime, nullable), reserved_to (uuid FK→users), fine (bool), SoftDeletes
3. `create_loans_table` — id (uuid PK), book_id (FK), user_id (FK), loaned_at, due_date, returned_at (nullable), has_fine (bool), fine_paid (bool)
4. `create_fines_table` — id (uuid PK), loan_id (FK), user_id (FK), amount (decimal 8,2 default 100.00), paid (bool)
5. `create_reservations_table` — id (uuid PK), book_id (FK), user_id (FK), reserved_at, expiration_date, status (enum: pending|fulfilled|cancelled)
6. `create_logs_table` — id (uuid PK), made_by (FK→users), action (string), description (text), date (datetime)

**Acceptance:** `php artisan migrate` runs cleanly; all six tables present with correct columns.

---

### Step 3 — Eloquent Models

**Files:** `app/src/models/`

- `User` — HasUuids, SoftDeletes, Notifiable; hasMany Loan, Fine, Reservation, Log; scopes: scopeBlocked, scopeByRole
- `Book` — HasUuids, SoftDeletes; hasMany Loan, Reservation; belongsTo User (reserved_to); scopes: scopePhysical, scopeDigital, scopeAvailable
- `Loan` — HasUuids; belongsTo Book, User; hasOne Fine; scopes: scopeActive (returned_at null), scopeOverdue
- `Fine` — HasUuids; belongsTo Loan, User
- `Reservation` — HasUuids; belongsTo Book, User; scopes: scopePending, scopeExpired
- `Log` — HasUuids; belongsTo User (made_by)

**Acceptance:** All relationships resolve in `php artisan tinker`.

---

### Step 4 — DTOs (Data Transfer Objects)

**Files:** `app/src/dtos/`

Immutable `readonly` PHP 8.1+ classes, each with `static fromRequest(Request $request): static` that calls `$request->validate(...)`:

- `RegisterUserDTO`, `UpdateUserDTO`, `LoginDTO`, `PasswordRecoveryDTO`, `ResetPasswordDTO`
- `CreateBookDTO`, `UpdateBookDTO`
- `CreateLoanDTO`, `RenewLoanDTO`, `ReturnLoanDTO`
- `CreateReservationDTO`, `CancelReservationDTO`
- `PayFineDTO`

**Acceptance:** Invalid input triggers `ValidationException` before reaching services.

---

### Step 5 — Services (Business Logic)

**Files:** `app/src/services/`

Implement in this order:

1. **`LogService`** — `log(User $actor, string $action, string $description): void`
2. **`AuthService`** — login, logout, sendPasswordRecoveryEmail, resetPassword
3. **`UserService`** — createUser, updateUser, deleteUser, blockUser, unblockUser, listUsers, findUser, assertUserNotBlocked, assertNoPendingFines
4. **`BookService`** — createBook, updateBook, deleteBook, listBooks, findBook, decrementStock (throws if stock=0), incrementStock, assertBookAvailable
5. **`LoanService`** — createLoan (checks: not blocked, no pending fines, book available → decrement stock → log), renewLoan (checks: not blocked, no reservations), returnLoan (set returned_at, increment stock, create Fine if late, block user, fulfill next reservation)
6. **`FineService`** — createFine (amount fixed R$100.00), payFine (unblock user if no more unpaid fines), listUnpaidFines
7. **`ReservationService`** — createReservation (checks: not blocked, no fines, book unavailable), cancelReservation, fulfillNext (oldest pending reservation fulfilled on book return)
8. **`NotificationService`** — sendDueDateReminders (query loans due tomorrow, queue DueDateReminderMail)

**Business Rules enforced:**
- Blocked/delinquent users denied loans and reservations
- Physical books: no two simultaneous active loans (stock constraint)
- Fine: R$100.00 fixed on late return
- Renewal blocked if book has pending reservations
- Reservations in chronological order

**Acceptance:** Unit tests pass for all service methods with mocked dependencies.

---

### Step 6 — Controllers

**Files:** `app/src/controllers/`

Thin controllers: build DTO → call service → return view or redirect with flash.

- `AuthController` — showLogin, login, logout, showForgotPassword, sendRecoveryEmail, showResetPassword, resetPassword
- `UserController` — index, show, create, store, edit, update, destroy, block, unblock
- `BookController` — index, show, create, store, edit, update, destroy
- `LoanController` — index, show, store, renew, return
- `FineController` — index, pay
- `ReservationController` — index, store, cancel

**Acceptance:** Each action reachable; invalid input returns 422; unauthorized returns 403.

---

### Step 7 — Routes & Middleware

**Files:** `routes/web.php`, `app/src/middleware/`

**Middleware:**
- `Authenticate` — redirect guests to login
- `CheckRole(string ...$roles)` — abort 403 if user role not in allowed list
- `LogRequest` — post-response: calls LogService for every mutating request
- `HandleErrors` — maps domain exceptions (UserBlockedException, OutOfStockException, etc.) to flash messages

**Route groups:**
- Guest: `/login`, `/forgot-password`, `/reset-password`
- Authenticated (auth, LogRequest): `/logout`, `/books` (read), `/reservations`, `/fines` (read)
- Librarian+Admin (auth, CheckRole:librarian|admin, LogRequest): `/books` (write), `/loans`, `/fines` (pay)
- Admin-only (auth, CheckRole:admin, LogRequest): `/users`

**Acceptance:** `php artisan route:list` shows all routes with correct middleware stacks.

---

### Step 8 — Email Notifications & Scheduler

**Files:** `app/src/mail/`, `app/Console/Commands/`

- `DueDateReminderMail` — Mailable with Blade template (`emails/due-date-reminder.blade.php`); book title, due date, user name
- `PasswordRecoveryMail` — token link to reset password
- `SendDueDateReminders` Artisan command (`loans:notify-due`) — calls `NotificationService::sendDueDateReminders()`; scheduled daily at 08:00

**Acceptance:** `php artisan loans:notify-due` queues one mail per qualifying loan.

---

### Step 9 — Frontend (Blade + Tailwind CSS)

**Files:** `resources/views/`, `vite.config.js`, `package.json`

- Install Tailwind CSS via npm + Vite pipeline
- **Layout:** `layouts/app.blade.php` — role-aware nav, flash messages banner
- **Views to build:**
  - `auth/login`, `auth/forgot-password`, `auth/reset-password`
  - `books/index` (catalog with search/filter), `books/show`, `books/create`, `books/edit`
  - `loans/index`, `loans/show` (with renew/return actions)
  - `fines/index`
  - `reservations/index`
  - `users/index`, `users/create`, `users/edit`
  - `emails/due-date-reminder`
- All forms: `@csrf`, method spoofing, inline `@error` validation messages
- Responsive: mobile (375px) through desktop (1280px)

**Acceptance:** `npm run build` compiles; all pages responsive; role-aware nav hides unauthorized links.

---

### Step 10 — Unit Tests

**Files:** `app/src/tests/Unit/`

- `UserServiceTest` — block/unblock, assertNotBlocked throws, assertNoPendingFines throws
- `BookServiceTest` — decrementStock throws at 0, assertBookAvailable variants
- `LoanServiceTest` — createLoan (happy path, blocked user, no stock, pending fine), renewLoan (blocked by reservation), returnLoan (fine creation triggered)
- `FineServiceTest` — amount always 100.00, payFine unblocks only when no remaining unpaid fines
- `ReservationServiceTest` — reserve available book throws, fulfillNext picks oldest
- `LogServiceTest` — record created with correct fields
- `AuthServiceTest` — login failure throws, reset with invalid token throws

**Acceptance:** `php artisan test --testsuite=Unit` passes 100%; no test touches the real database.

---

### Step 11 — Integration Tests

**Files:** `app/src/tests/Integration/`

Uses `RefreshDatabase` (SQLite in-memory):

- `AuthIntegrationTest` — login/logout/password recovery
- `BookIntegrationTest` — CRUD by librarian; customer denied write access
- `LoanIntegrationTest` — full lifecycle (loan→renew→return with/without fine); blocked user denied
- `FineIntegrationTest` — fine created on late return; user unblocked after payment
- `ReservationIntegrationTest` — reserve unavailable book; fulfilled on return
- `UserIntegrationTest` — admin CRUD; customer denied
- `LogIntegrationTest` — every mutating action produces a log record
- `RBACIntegrationTest` — all role boundaries tested against protected routes

**Acceptance:** `php artisan test --testsuite=Integration` passes; ≥80% coverage on services.

---

### Config Extract

- Fine amount: `config/library.php → fine_amount = 100.00`
- Loan renewal days: `config/library.php → renewal_days = 14`
- Reservation expiration days: `config/library.php → reservation_expiry_days = 3`
