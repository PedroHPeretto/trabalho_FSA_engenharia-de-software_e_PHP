# Process Documentation

---

## Sobre

Este documento apresenta uma descriçõa detalhada dos processos existentes no sistema

---

## Visão Geral do Sistema

O sistema de biblioteca digital tem como objetivo automatizar e centralizar os processos de gestão de acervo físico e digital, empréstimos, reservas, penalidades e gerenciamento de usuários.
A solução busca eliminar problemas encontrados no modelo atual baseado em planilhas, como:

- Falta de rastreabilidade de empréstimos; 
- Erros no controle de estoque físico; 
- Dificuldade no gerenciamento de conteúdos digitais; 
- Baixa confiabilidade das informações; 
- Ausência de controle automatizado de multas e bloqueios.

---

## Objetivos do sistema

Implementar uma plataforma digital integrada para controle de acervo, empréstimos, reservas e acesso a conteúdos digitais.

### Objetivos Específicos

- Automatizar o controle de estoque físico; 
- Centralizar o gerenciamento de livros físicos e digitais; 
- Garantir rastreabilidade das operações; 
- Automatizar cálculo de multas; 
- Melhorar a experiência do usuário; 
- Garantir segurança e conformidade com a LGPD.

---

## Perfis de Usuários 

**Leitor:**

- Consultar acervo; 
- Solicitar empréstimos; 
- Renovar empréstimos; 
- Consultar histórico; 
- Acessar livros digitais. 

**Bibliotecário:**

- Gerenciar acervo; 
- Controlar estoque; 
- Aprovar empréstimos; 
- Registrar devoluções; 
- Gerenciar multas; 
- Cadastrar usuários. 

**Administrador do Sistema:**

- Administração geral; 
- Auditoria; 
- Controle de permissões; 
- Gestão de disponibilidade e segurança.

---

## Processos

### Processo de cadastro de livros

Permitir o registro de livros físicos e digitais no sistema.

**Requisitos Relacionados:**

- RF01 — Cadastro de Títulos 
-	RF03 — Catálogo Digital 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Acessar Módulo de Cadastro]) --> B[Informar Título e Autor]
    B --> C{Tipo do Livro?}
    
    C -->|Físico| D[Informar Quantidade em Estoque]
    C -->|Digital| E[Informar Link de Acesso]
    
    D --> F[Sistema Valida os Dados]
    E --> F
    
    F --> G[Sistema Salva o Cadastro]
    G --> H([Saída: Livro disponível no catálogo])
```

**Regras de Negócio:**

- Todo livro deve possuir título e autor. 
- Livros digitais devem possuir link válido. 
- Estoque físico não pode ser negativo.

### Empréstimo de livro 

Registrar empréstimos de exemplares físicos.

**Requisitos Relacionados:**

- RF02 — Controle de Estoque 
- RF04 — Realização de Empréstimo 
- RF07 — Bloqueio por Inadimplência 
- RNF05 — Integridade de Dados 


**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Usuário solicita empréstimo]) --> B[Sistema verifica Autenticação]
    
    B --> C{Possui multas pendentes?}
    C -->|Sim| X([Empréstimo Negado])
    C -->|Não| D{Exemplar disponível?}
    
    D -->|Não| Y([Empréstimo Negado])
    D -->|Sim| E[Bibliotecário confirma o empréstimo]
    
    E --> F[Sistema vincula exemplar ao usuário]
    F --> G[Sistema define data de devolução]
    G --> H[Sistema atualiza estoque automaticamente]
    
    H --> I[Registro é salvo no histórico]
    I --> J([Saída: Empréstimo realizado com sucesso])
```

**Regras de Negócio:**

- Usuários inadimplentes não podem emprestar livros. 
- O mesmo exemplar físico não pode possuir dois empréstimos simultâneos. 
- Apenas usuários autenticados podem realizar empréstimos.

### Renovação de empréstimo

Permitir extensão do prazo de devolução.

**Requisitos Relacionados:**

-	RF05 — Renovação de Empréstimo 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Usuário solicita renovação]) --> B[Sistema verifica reservas pendentes]
    B --> C[Sistema verifica situação do usuário]
    
    C --> D{Há impedimentos?}
    
    D -->|Sim| X([Renovação Negada])
    
    D -->|Não| E[Gerar nova data de devolução]
    E --> F[Atualizar histórico do empréstimo]
    
    F --> G([Saída: Prazo renovado])
```

**Regras de Negócio:**

- Não é permitido renovar livros reservados. 
- Usuários bloqueados não podem renovar empréstimos.

### Reserva de livro

Permitir reserva de títulos indisponíveis.

**Requisitos Relacionados:**

-	RF04 — Empréstimos 
-	RF07 — Bloqueio por Inadimplência 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Usuário solicita reserva]) --> B[Sistema verifica disponibilidade do livro]
    B --> C[Sistema verifica pendências financeiras]
    
    C --> D{Possui pendências?}
    
    D -->|Sim| X([Reserva Negada])
    
    D -->|Não| E[Registrar reserva no sistema]
    E --> F[Inserir usuário na fila de espera]
    
    F --> G([Saída: Reserva concluída])
```

**Regras de Negócio:**

-	Usuários inadimplentes não podem reservar livros. 
-	Reservas seguem ordem cronológica.

### Devolução de livros

Registrar devolução e calcular multas.

**Requisitos Relacionados:**

-	RF02 — Controle de Estoque 
-	RF06 — Cálculo de Multas 
-	RF08 — Consulta de Histórico 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Usuário devolve livro]) --> B[Bibliotecário registra devolução]
    B --> C[Sistema verifica atraso]
    
    C --> D{Há atraso?}
    
    D -->|Sim| E[Multa de R$ 100,00 é gerada automaticamente]
    D -->|Não| F[Sistema atualiza o estoque]
    
    E --> F
    F --> G[Sistema atualiza o histórico]
    G --> H([Saída: Livro devolvido e multa registrada, se aplicável])
```

**Regras de Negócio:**

- Multa fixa de R$ 100,00 por atraso. 
- Estoque deve ser incrementado automaticamente.

### Pagamento da multa

Regularizar situação do usuário.

**Requisitos Relacionados:**

- RF06 — Cálculo de Multas 
- RF07 — Bloqueio por Inadimplência 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Usuário realiza pagamento]) --> B[Bibliotecário registra pagamento]
    
    B --> C[Sistema atualiza status da multa]
    C --> D[Sistema remove bloqueio do usuário]
    
    D --> E[Histórico é atualizado]
    E --> F([Saída: Usuário liberado para novos empréstimos])
```

### Consulta de Histórico

Permitir rastreabilidade de operações.

**Requisitos Relacionados:**

- RF08 — Consulta de Histórico 
- RNF06 — Auditoria 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Usuário ou Bibliotecário solicita consulta]) --> B[Sistema inicia recuperação de dados]
    
    B --> C1[Recuperar Empréstimos]
    B --> C2[Recuperar Devoluções]
    B --> C3[Recuperar Multas]
    B --> C4[Recuperar Pagamentos]
    
    C1 --> D[Dados são compilados e exibidos]
    C2 --> D
    C3 --> D
    C4 --> D
    
    D --> E([Saída: Histórico completo disponível])
```

### Gestão de Usuários

Gerenciar leitores e funcionários.

**Requisitos Relacionados:**

- RF09 — Gestão de Usuários 
- RNF01 — Segurança 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Administrador acessa módulo]) --> B{Escolha a Ação}
    
    B -->|Cadastrar| C1[Informar dados do novo usuário]
    B -->|Editar| C2[Alterar dados do usuário existente]
    B -->|Excluir| C3[Selecionar usuário para remoção]
    
    C1 --> D[Sistema valida permissões de Admin]
    C2 --> D
    C3 --> D
    
    D --> E{Permissão Válida?}
    E -->|Não| X([Acesso Negado / Operação Cancelada])
    E -->|Sim| F[Sistema salva as alterações]
    
    F --> G([Saída: Usuário atualizado no sistema])
```

**Regras de Negócio:**

- Login deve ser único. 
- Senhas devem ser criptografadas.

### Recuperação de Senha

Permitir recuperação segura de acesso.

**Requisitos Relacionados:**

-	RF11 — Recuperação de Senha 
-	RNF01 — Segurança 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Usuário solicita recuperação]) --> B[Sistema gera token e envia email com link temporário]
    B --> C[Usuário acessa o link e redefine a senha]
    
    C --> D[Sistema valida os requisitos da nova senha]
    D --> E{Senha Válida?}
    
    E -->|Não| C
    E -->|Sim| F[Sistema atualiza as credenciais no banco de dados]
    
    F --> G([Saída: Acesso recuperado])
```

### Notificações Automáticas

Avisar usuários sobre vencimentos.

**Requisitos Relacionados:**

-	RF10 — Notificações Automáticas 

**Fluxo do Processo:**

```mermaid
graph TD
    A([Início: Execução do Job de Verificação]) --> B[Sistema consulta empréstimos no banco de dados]
    B --> C{Faltam 24h para o vencimento?}
    
    C -->|Não| X[Ignorar registro]
    C -->|Sim| D[Sistema dispara e-mail automático de alerta]
    
    D --> E[Armazenar registro de envio no histórico de notificações]
    E --> F([Saída: Usuário notificado])
```

---
