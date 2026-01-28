# LoyaltyHub
![render1769540494921](https://github.com/user-attachments/assets/36498d34-d9bc-4812-aa50-3ac2f7921075)
![render1769540826188](https://github.com/user-attachments/assets/9d97c07e-c015-4a36-a114-9367373eb83c)


[![CI Pipeline](https://github.com/web-inwall/subify/actions/workflows/ci.yml/badge.svg)](https://github.com/web-inwall/loyalty-hub/actions)
[![PHP Version](https://img.shields.io/badge/PHP-8.4-4169E1.svg?style=flat&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-4169E1.svg?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1.svg?style=flat&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Redis](https://img.shields.io/badge/Redis-7.x-4169E1.svg?style=flat&logo=redis&logoColor=white)](https://redis.io/)
[![Code Style](https://img.shields.io/badge/code%20style-PSR--12-4169E1)](https://www.php-fig.org/psr/psr-12/)
[![Larastan](https://img.shields.io/badge/Larastan-Level%205-4169E1)](https://github.com/larastan/larastan)

**High-performance Loyalty & Bonus Processing API.**

LoyaltyHub is an enterprise-grade modular monolith designed to handle high-concurrency wallet operations, bonus accruals, and transaction consistency with zero race conditions. Built with robust Domain-Driven Design (DDD) principles.

---

## 🚀 Key Features

*   🛡️ **Race Condition Protection**
    Utilizes pessimistic DB locking (`lockForUpdate`) within critical transaction boundaries to ensure that concurrent balance updates are processed sequentially.

*   ⚡ **Optimistic Locking**
    Implements version-based concurrency control (`HasOptimisticLocking` trait). Any attempt to save a stale model version will be rejected, preventing "lost update" anomalies.

*   🔄 **Idempotency**
    Guarantees reliable transaction processing. Duplicate requests with the same `transaction_reference` are detected and handled gracefully (returning the original response or a conflict status) without duplicating side effects.

*   🏗️ **Modular Monolith**
    Architected using **Domain-Driven Design (DDD)**. The codebase is organized into distinct domains (`app/Domains/Loyalty`, `app/Domains/Wallet`) to ensure separation of concerns and maintainability.

*   📊 **Event Sourcing Lite**
    maintains a complete, immutable audit log of all balance changes via the `transactions` table, linked to specific operation types and metadata.

---

## 🛠 Tech Stack

| Component | Technology | Description |
| :--- | :--- | :--- |
| **Framework** | ![Laravel](https://img.shields.io/badge/-Laravel%2012-FF2D20?style=flat-square&logo=laravel&logoColor=white) | Core application framework. |
| **Language** | ![PHP](https://img.shields.io/badge/-PHP%208.4-777BB4?style=flat-square&logo=php&logoColor=white) | Typed, modern PHP with Enums and Readonly classes. |
| **Database** | ![Postgres](https://img.shields.io/badge/-PostgreSQL-4169E1?style=flat-square&logo=postgresql&logoColor=white) | Primary data store for wallets and transactions. |
| **Cache** | ![Redis](https://img.shields.io/badge/-Redis-DC382D?style=flat-square&logo=redis&logoColor=white) | High-speed caching for idempotency keys. |
| **Testing** | ![Pest](https://img.shields.io/badge/-Pest-4f5b93?style=flat-square&logo=pest&logoColor=white) | Elegant testing framework for Unit and Feature tests. |

---

## 📖 Architecture Flow

The following diagram illustrates the lifecycle of a bonus accrual request, highlighting the safety mechanisms in place.

```mermaid
sequenceDiagram
    participant Client
    participant API as WalletController
    participant Action as AccruePointsAction
    participant Cache as Redis (Idempotency)
    participant DB as PostgreSQL

    Client->>API: POST /api/wallet/accrue
    API->>Cache: Check transaction_reference
    alt Key Exists
        Cache-->>API: 409 Conflict / 200 OK
        API-->>Client: Transaction already processed
    else Key New
        API->>Action: Execute(AccruePointsData)
        Action->>DB: Begin Transaction
        Action->>DB: SELECT ... FOR UPDATE (Lock Wallet)
        DB-->>Action: Locked Record
        Action->>DB: Update Balance & Version
        Action->>DB: Insert Transaction Record
        Action->>DB: Commit Transaction
        DB-->>Action: Success
        Action-->>API: Void
        API->>Cache: Store transaction_reference
        API-->>Client: 200 OK (New Balance)
    end
```

---

## 🔌 API Reference

### Accrue Points
Adds bonus points or funds to a user's wallet.

- **Endpoint:** `POST /api/wallet/accrue`
- **Content-Type:** `application/json`

#### ✅ Request Example

```json
{
  "wallet_id": 1,
  "amount": 500,
  "reason": "Loyalty Bonus - January",
  "transaction_reference": "tx-unique-uuid-v4"
}
```

#### 🟢 Success Response (200 OK)

```json
{
  "balance": "1500.0000"
}
```

#### 🔴 Error Response (409 Conflict)

Occurs when `transaction_reference` has already been processed.

```json
{
  "message": "Transaction already processed."
}
```

---

## ⚡ Quick Start

You can have the project up and running on your local machine using [Laravel Sail](https://laravel.com/docs/sail).
1.  **Clone the repository**
    ```bash
    git clone https://github.com/web-inwall/loyalty-hub.git
    cd loyalty-hub
    ```

2.  **Start the environment**
    ```bash
    cp .env.example .env
    ./vendor/bin/sail up -d
    ```

3.  **Run migrations**
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

---

## 🧪 Testing Concurrency

I take data integrity seriously. This project includes specific integration tests to prove that my locking mechanisms (Optimistic & Pessimistic) work reliably under pressure.

To run the **concurrency and locking tests**:

```bash
# Run the optimistic locking integrity test
./vendor/bin/sail test tests/Integration/OptimisticLockingTest.php

# Run the concurrency simulation test
./vendor/bin/sail test tests/Integration/WalletConcurrencyTest.php
```

To run the **full test suite**:

```bash
./vendor/bin/sail test
```
