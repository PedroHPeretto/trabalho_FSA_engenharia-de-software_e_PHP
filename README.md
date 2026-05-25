# Biblioteca Digital

---

## Links

- [Sobre](#sobre)
- [Tecnologias utilizadas](#tecnologias-utilizadas)
- [Como utilizar](#como-utilizar)
- [Documentações](#documentações)

---

## Sobre

Este projeto é um trabalho dado nas matérias de *Engenharia de Software* e *Linguagem PHP*. O intuíto é desenvolver as habilidades dos alunos de criação de sistemas estrurados, utilizando as boas práticas de engenharia de software, e a linguagem de programação PHP.

---

## Tecnologias utilizadas

- [PHP](https://www.php.net/)
- [Laravel](https://laravel.com/)
- [Tailwind](https://tailwindcss.com/)
- [MySQL](https://www.mysql.com/)

---

## Como utilizar

### Pré-requisitos

Antes de começar, certifique-se de ter instalado em sua máquina:

- [Docker](https://www.docker.com/products/docker-desktop/)

---

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio>
cd trabalho_FSA_PHP_Eng
```

### 2. Configurar as variáveis de ambiente

Crie o arquivo `.env` na raiz do projeto com as credenciais do banco de dados:

```bash
DB_HOST=<host-do-banco>
DB_NAME=<nome-do-banco>
DB_USER=<usuario>
DB_PASSWORD=<senha>
```

### 3. Instalar as dependências

Instala as dependências PHP (Composer) e JavaScript (npm):

```bash
make install
```

---

### Rodando a aplicação

### 1. Abra o Docker Desktop

### 2. Suba o container

```bash
make up
```

A aplicação estará disponível em [http://localhost:8000](http://localhost:8000).

Para parar os containers:

```bash
make down
```

---

### Comandos disponíveis

| Comando | Descrição |
|---|---|
| `make install` | Instala as dependências PHP e JavaScript |
| `make up` | Sobe os containers Docker em segundo plano |
| `make down` | Para e remove os containers Docker |
| `make test` | Executa a suíte de testes |

---

## Documentações

- [Caso](docs/case.md)
- [Arquitetura](docs/architecture.md)
- [Diagramas](docs/diagrams.md)
- [Fluxos](docs/flows.md)
- [Modelos](docs/models.md)
- [Processos](docs/process.md)
- [Requisitos](docs/requirements.md)

---
