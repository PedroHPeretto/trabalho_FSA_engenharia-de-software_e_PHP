-- =============================================================
-- SEED INICIAL DO BANCO DE DADOS
-- Sistema de Biblioteca FSA-PHP-Eng
-- Data de referência: 2026-05-24
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE logs;
TRUNCATE TABLE fines;
TRUNCATE TABLE reservations;
TRUNCATE TABLE loans;
TRUNCATE TABLE books;
TRUNCATE TABLE password_reset_tokens;
TRUNCATE TABLE users;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- USERS
-- Senha padrão para todos: "password"
-- Hash bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- =============================================================

INSERT INTO users (id, name, cpf, email, role, password, blocked, created_at, updated_at, deleted_at) VALUES

-- Admin
('a0000000-0000-0000-0000-000000000001',
 'Pedro Henrique Peretto',
 '11122233309',
 'pedrohperetto@gmail.com',
 'admin',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 false,
 '2026-01-01 08:00:00', '2026-01-01 08:00:00', NULL),

-- Bibliotecários
('a0000000-0000-0000-0000-000000000002',
 'Maria Oliveira',
 '22233344415',
 'maria.oliveira@biblioteca.com',
 'librarian',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 false,
 '2026-01-02 08:00:00', '2026-01-02 08:00:00', NULL),

('a0000000-0000-0000-0000-000000000003',
 'Carlos Santos',
 '33344455520',
 'carlos.santos@biblioteca.com',
 'librarian',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 false,
 '2026-01-03 08:00:00', '2026-01-03 08:00:00', NULL),

-- Clientes
('a0000000-0000-0000-0000-000000000004',
 'Ana Costa',
 '44455566628',
 'ana.costa@email.com',
 'customer',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 false,
 '2026-02-10 10:00:00', '2026-02-10 10:00:00', NULL),

('a0000000-0000-0000-0000-000000000005',
 'Pedro Alves',
 '55566677731',
 'pedro.alves@email.com',
 'customer',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 false,
 '2026-02-15 11:00:00', '2026-02-15 11:00:00', NULL),

('a0000000-0000-0000-0000-000000000006',
 'Fernanda Lima',
 '66677788836',
 'fernanda.lima@email.com',
 'customer',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 false,
 '2026-03-01 09:00:00', '2026-03-01 09:00:00', NULL),

-- Cliente bloqueado (possui multa em aberto)
('a0000000-0000-0000-0000-000000000007',
 'Rafael Souza',
 '77788899940',
 'rafael.souza@email.com',
 'customer',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 true,
 '2026-03-05 14:00:00', '2026-05-10 09:00:00', NULL),

('a0000000-0000-0000-0000-000000000008',
 'Beatriz Mendes',
 '88899900053',
 'beatriz.mendes@email.com',
 'customer',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 false,
 '2026-03-20 16:00:00', '2026-03-20 16:00:00', NULL);


-- =============================================================
-- BOOKS
-- =============================================================

INSERT INTO books (id, title, author, media, stock, digital_link, reserved, reserve_expiration, reserved_to, fine, created_at, updated_at, deleted_at) VALUES

-- Livros físicos
('b0000000-0000-0000-0000-000000000001',
 'Dom Quixote',
 'Miguel de Cervantes',
 'physical', 3, NULL, false, NULL, NULL, false,
 '2026-01-05 10:00:00', '2026-01-05 10:00:00', NULL),

('b0000000-0000-0000-0000-000000000002',
 'O Senhor dos Anéis',
 'J.R.R. Tolkien',
 'physical', 2, NULL, false, NULL, NULL, false,
 '2026-01-05 10:00:00', '2026-01-05 10:00:00', NULL),

-- Reservado para Ana Costa
('b0000000-0000-0000-0000-000000000003',
 '1984',
 'George Orwell',
 'physical', 1, NULL, true, '2026-05-31 23:59:59',
 'a0000000-0000-0000-0000-000000000004',
 false,
 '2026-01-05 10:00:00', '2026-05-17 14:00:00', NULL),

('b0000000-0000-0000-0000-000000000004',
 'O Pequeno Príncipe',
 'Antoine de Saint-Exupéry',
 'physical', 5, NULL, false, NULL, NULL, false,
 '2026-01-05 10:00:00', '2026-01-05 10:00:00', NULL),

-- Com multa ativa (empréstimo vencido e não devolvido)
('b0000000-0000-0000-0000-000000000005',
 'Crime e Castigo',
 'Fiódor Dostoiévski',
 'physical', 2, NULL, false, NULL, NULL, true,
 '2026-01-05 10:00:00', '2026-05-10 09:00:00', NULL),

-- Livros digitais
('b0000000-0000-0000-0000-000000000006',
 'Clean Code',
 'Robert C. Martin',
 'digital', NULL, 'https://biblioteca.com/digital/clean-code.pdf',
 false, NULL, NULL, false,
 '2026-01-10 10:00:00', '2026-01-10 10:00:00', NULL),

('b0000000-0000-0000-0000-000000000007',
 'Design Patterns',
 'Gang of Four',
 'digital', NULL, 'https://biblioteca.com/digital/design-patterns.pdf',
 false, NULL, NULL, false,
 '2026-01-10 10:00:00', '2026-01-10 10:00:00', NULL),

('b0000000-0000-0000-0000-000000000008',
 'The Pragmatic Programmer',
 'Andrew Hunt e David Thomas',
 'digital', NULL, 'https://biblioteca.com/digital/pragmatic-programmer.pdf',
 false, NULL, NULL, false,
 '2026-01-10 10:00:00', '2026-01-10 10:00:00', NULL);


-- =============================================================
-- LOANS
-- =============================================================

INSERT INTO loans (id, book_id, user_id, loaned_at, due_date, returned_at, has_fine, fine_paid, created_at, updated_at) VALUES

-- Devolvido no prazo (Ana Costa / 1984)
('l0000000-0000-0000-0000-000000000001',
 'b0000000-0000-0000-0000-000000000003',
 'a0000000-0000-0000-0000-000000000004',
 '2026-04-01 09:00:00', '2026-04-15 23:59:59', '2026-04-14 17:00:00',
 false, false,
 '2026-04-01 09:00:00', '2026-04-14 17:00:00'),

-- Empréstimo ativo (Pedro Alves / Dom Quixote)
('l0000000-0000-0000-0000-000000000002',
 'b0000000-0000-0000-0000-000000000001',
 'a0000000-0000-0000-0000-000000000005',
 '2026-05-10 10:00:00', '2026-05-24 23:59:59', NULL,
 false, false,
 '2026-05-10 10:00:00', '2026-05-10 10:00:00'),

-- Devolvido com atraso — gerou multa (paga) (Fernanda Lima / O Senhor dos Anéis)
('l0000000-0000-0000-0000-000000000003',
 'b0000000-0000-0000-0000-000000000002',
 'a0000000-0000-0000-0000-000000000006',
 '2026-03-01 09:00:00', '2026-03-15 23:59:59', '2026-03-20 11:00:00',
 true, true,
 '2026-03-01 09:00:00', '2026-03-25 10:00:00'),

-- Vencido e não devolvido — multa em aberto (Rafael Souza / Crime e Castigo)
('l0000000-0000-0000-0000-000000000004',
 'b0000000-0000-0000-0000-000000000005',
 'a0000000-0000-0000-0000-000000000007',
 '2026-04-20 09:00:00', '2026-05-04 23:59:59', NULL,
 true, false,
 '2026-04-20 09:00:00', '2026-05-10 09:00:00'),

-- Devolvido no prazo (Beatriz Mendes / O Pequeno Príncipe)
('l0000000-0000-0000-0000-000000000005',
 'b0000000-0000-0000-0000-000000000004',
 'a0000000-0000-0000-0000-000000000008',
 '2026-05-01 10:00:00', '2026-05-15 23:59:59', '2026-05-13 14:00:00',
 false, false,
 '2026-05-01 10:00:00', '2026-05-13 14:00:00');


-- =============================================================
-- FINES
-- =============================================================

INSERT INTO fines (id, loan_id, user_id, amount, paid, created_at, updated_at) VALUES

-- Multa paga (Fernanda Lima / atraso de 5 dias)
('f0000000-0000-0000-0000-000000000001',
 'l0000000-0000-0000-0000-000000000003',
 'a0000000-0000-0000-0000-000000000006',
 25.00, true,
 '2026-03-20 11:00:00', '2026-03-25 10:00:00'),

-- Multa em aberto (Rafael Souza / 20 dias de atraso)
('f0000000-0000-0000-0000-000000000002',
 'l0000000-0000-0000-0000-000000000004',
 'a0000000-0000-0000-0000-000000000007',
 100.00, false,
 '2026-05-05 08:00:00', '2026-05-05 08:00:00');


-- =============================================================
-- RESERVATIONS
-- =============================================================

INSERT INTO reservations (id, book_id, user_id, reserved_at, expiration_date, status, created_at, updated_at) VALUES

-- Reserva ativa / pendente (Ana Costa / 1984)
('v0000000-0000-0000-0000-000000000001',
 'b0000000-0000-0000-0000-000000000003',
 'a0000000-0000-0000-0000-000000000004',
 '2026-05-17 14:00:00', '2026-05-31 23:59:59',
 'pending',
 '2026-05-17 14:00:00', '2026-05-17 14:00:00'),

-- Reserva cumprida (Pedro Alves / Dom Quixote — virou empréstimo)
('v0000000-0000-0000-0000-000000000002',
 'b0000000-0000-0000-0000-000000000001',
 'a0000000-0000-0000-0000-000000000005',
 '2026-05-08 10:00:00', '2026-05-15 23:59:59',
 'fulfilled',
 '2026-05-08 10:00:00', '2026-05-10 10:00:00'),

-- Reserva cancelada (Fernanda Lima / Clean Code)
('v0000000-0000-0000-0000-000000000003',
 'b0000000-0000-0000-0000-000000000006',
 'a0000000-0000-0000-0000-000000000006',
 '2026-04-10 09:00:00', '2026-04-17 23:59:59',
 'cancelled',
 '2026-04-10 09:00:00', '2026-04-12 11:00:00');


-- =============================================================
-- LOGS
-- =============================================================

INSERT INTO logs (id, made_by, action, description, date, created_at, updated_at) VALUES

('e0000000-0000-0000-0000-000000000001',
 'a0000000-0000-0000-0000-000000000001',
 'USER_CREATED',
 'Usuário Rafael Souza (rafael.souza@email.com) cadastrado no sistema.',
 '2026-03-05 14:00:00', '2026-03-05 14:00:00', '2026-03-05 14:00:00'),

('e0000000-0000-0000-0000-000000000002',
 'a0000000-0000-0000-0000-000000000002',
 'LOAN_CREATED',
 'Empréstimo registrado: Crime e Castigo para Rafael Souza. Devolução prevista: 2026-05-04.',
 '2026-04-20 09:00:00', '2026-04-20 09:00:00', '2026-04-20 09:00:00'),

('e0000000-0000-0000-0000-000000000003',
 'a0000000-0000-0000-0000-000000000002',
 'FINE_GENERATED',
 'Multa gerada para Rafael Souza. Empréstimo vencido em 2026-05-04. Valor: R$ 100,00.',
 '2026-05-05 08:00:00', '2026-05-05 08:00:00', '2026-05-05 08:00:00'),

('e0000000-0000-0000-0000-000000000004',
 'a0000000-0000-0000-0000-000000000001',
 'USER_BLOCKED',
 'Usuário Rafael Souza bloqueado por possuir multa em aberto.',
 '2026-05-10 09:00:00', '2026-05-10 09:00:00', '2026-05-10 09:00:00'),

('e0000000-0000-0000-0000-000000000005',
 'a0000000-0000-0000-0000-000000000003',
 'FINE_PAID',
 'Multa de R$ 25,00 quitada por Fernanda Lima referente ao empréstimo de O Senhor dos Anéis.',
 '2026-03-25 10:00:00', '2026-03-25 10:00:00', '2026-03-25 10:00:00');
