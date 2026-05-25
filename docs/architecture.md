# System Architecture

---

## Sobre

Este documento descreve a arquitetura do sistema, incluindo sua infraestrutura e tecnologias usadas no desenvolvimento

---

## Geral

- A aplicação deverá ter modo de desenvolvimento e produção
- O Frontend deverá ser separado do Backend

---

## Frontend

- O frontend deverá ser construído dentro da API, com *Tailwind* para criar toda a estilização do site.

- O site deverá ser responsivo para variados tipos de tela, como celulares, tablets, desktops e Televisões.

- O site deverá dar feedback visual para as ações do usuário

- O site deverá tem uma *UI* (User Interface) atrativa visualmente e que siga bons padrões de system design, e uma *UX* (User Experience) que priorize a facilidade de uso para diferentes tipos de usuário, além de padronizações para fácil reconhecimento e consistência.

- Componentes como botões, layouts, textos, etc deverão ser todos criados em uma pasta de componentes e ser reutilizados, afim de manter consistência visual.

---

## Backend

- O backend deverá ser feito utilizando *PHP* com *Laravel*, e o banco de dados utilizado *MySQL*.

- A arquitetura utilizada será *MSC* (Model-Service-Controller), tendo as separações de responsabilidade aplicadas corretamente.

- A camada *Model* será responsável por gerenciar as consultas ao banco de dados

- A camada *Service* será responsável por aplicar a lógica da API e garantir que as regras de negócio sejam aplicadas

- A camada *Controller* será responsável por gerenciar chamadas *HTTP*

- Deverão ser usados *DTOs* (Data-transfer-objects) para garantir o controle dos dados recebidos do *Frontend*

- Todas as rotas deverão registrar logs descritivos das ações feitas na API, contendo horário, usuário, ação feita, e descrição detalhada

- A API deverá ter um *middleware* para tratamento de erros

- A API deverá ser construída utilizando *POO* (Programação Orientada á Objetos)

- A API deverá ter testes unitários e de integração, que cubram todos os cenários propostos de acordo com os requisitos da aplicação

- A API deverá ter um *RBAC* (Role Based Access).

- A API deverá utilizar variáveis de ambiente para não expor dados sensíveis

---
