# Models

---

## Sobre

Este documento detalha a modelagem das entidades existentes na aplicação que deverão ser criadas no banco de dados

---

```mermaid
erDiagram
    %% Entidades Principais
    USER {
        uuid id PK
        varchar name
        varchar cpf
        varchar email
        role_enum role
        varchar password
        bool blocked
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    BOOK {
        uuid id PK
        varchar title
        varchar author
        book_media_enum media
        int stock
        varchar digital_link
        bool reserved
        timestamp reserve_expiration
        uuid reserved_to FK
        bool fine
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    LOAN {
        uuid id PK
        uuid book_id FK
        uuid user_id FK
        timestamp loaned_at
        timestamp due_date
        timestamp returned_at
        bool has_fine
        bool fine_paid
        timestamp created_at
        timestamp updated_at
    }

    FINE {
        uuid id PK
        uuid loan_id FK
        uuid user_id FK
        decimal amount
        bool paid
        timestamp created_at
        timestamp updated_at
    }

    RESERVATION {
        uuid id PK
        uuid book_id FK
        uuid user_id FK
        timestamp reserved_at
        timestamp expiration_date
        reservation_status_enum status
        timestamp created_at
        timestamp updated_at
    }

    LOGS {
        uuid id PK
        uuid made_by FK
        varchar action
        text description
        timestamp date
        timestamp created_at
        timestamp updated_at
    }

    PASSWORD_RESET_TOKEN {
        varchar email PK
        varchar token
        timestamp created_at
    }

    %% Enums representados como tabelas de domínio
    ROLE_ENUM {
        customer customer
        librarian librarian
        admin admin
    }

    BOOK_MEDIA_ENUM {
        physical physical
        digital digital
    }

    RESERVATION_STATUS_ENUM {
        pending pending
        fulfilled fulfilled
        cancelled cancelled
    }

    %% Relacionamentos e Cardinalidades
    USER ||--oN BOOK : "reserva (reserved_to)"
    USER ||--oN LOGS : "executa (made_by)"
    USER ||--oN LOAN : "realiza"
    USER ||--oN FINE : "possui"
    USER ||--oN RESERVATION : "faz"
    
    BOOK ||--oN LOAN : "emprestado"
    BOOK ||--oN RESERVATION : "reservado"
    
    LOAN ||--o| FINE : "gera"
    
    ROLE_ENUM ||--oN USER : "define_papel"
    BOOK_MEDIA_ENUM ||--oN BOOK : "define_midia"
    RESERVATION_STATUS_ENUM ||--oN RESERVATION : "define_status"
```

---
