# Requirements

---

## Sobre

Este documento apresenta o levantamento de requisitos para a modernização do sistema de controle de uma biblioteca.
**Problema:** Atualmente, a gestão é feita parcialmente por planilhas, o que resulta em falta de rastreabilidade nos empréstimos, erros no controle de estoque físico e dificuldade em gerenciar o acesso a conteúdos digitais.
**Solução:** A implementação de um software dedicado que automatiza o ciclo de empréstimos, reservas e penalidades. O sistema garantirá a integridade dos dados e o controle centralizado de títulos físicos e digitais em uma única plataforma.

---

## Requisitos funcionais

| ID | Título | Descrição |
|:---|:-------|:----------|
| RF01 | Cadastro de Títulos | O sistema deve permitir o cadastro de livros, permitindo a distinção entre exemplares físicos e digitais. Cada livro deverá ter título e autor. |
| RF02 | Controle de Estoque (Físico) | O sistema deve atualizar automaticamente a quantidade disponível de livros físicos após cada empréstimo ou devolução. |
| RF03 | Catálogo Digital | O sistema deve fornecer um link de acesso controlado para os livros classificados como digitais. |
| RF04 | Realização de Empréstimo | O sistema deve registrar o empréstimo de livros, vinculando o exemplar ao usuário e definindo uma data de devolução. |
| RF05 | Renovação de Empréstimo | O sistema deve permitir a renovação do prazo de entrega, desde que não haja reservas pendentes para o título. |
| RF06 | Cálculo de Multas | O sistema deve calcular automaticamente o valor da multa por atraso no momento da devolução do livro. Valor fixo de R$ 100,00. |
| RF07 | Bloqueio por Inadimplência | O sistema deve impedir que usuários com multas em aberto realizem novos empréstimos ou reservas. |
| RF08 | Consulta de Histórico | O sistema deve disponibilizar ao usuário e ao bibliotecário o histórico completo de empréstimos, devoluções e multas pagas/pendentes. |
| RF09 | Gestão de Usuários | O sistema deve permitir o cadastro, edição e exclusão de leitores e funcionários. |
| RF10 | Notificações Automáticas | O sistema deve enviar um aviso por email ao usuário 24h antes do vencimento do prazo de entrega. |
| RF11 | Recuperação de Senha | O sistema deve permitir que o usuário recupere seu acesso via e-mail. |

---

## Requisitos não funcionais

| ID | Título | Descrição |
|:---|:-------|:----------|
| RNF01 | Segurança (Autenticação) | O acesso aos livros digitais e às funcionalidades de empréstimo deve ser restrito a usuários autenticados via login e senha |
| RNF02 | Usabilidade (Responsividade) | A interface do sistema deve ser responsiva, permitindo que os usuários consultem o acervo e leiam livros digitais em dispositivos móveis (tablets e smartphones). |
| RNF03 | Disponibilidade | O sistema deve possuir uma disponibilidade de 99,5% (uptime), garantindo que os usuários possam acessar o acervo digital a qualquer momento. |
| RNF04 | Desempenho | As buscas por títulos no acervo não devem demorar mais de 2 segundos para retornar os resultados ao usuário. |
| RNF05 | Integridade de Dados | O sistema deve garantir que não haja duplicidade de registros de empréstimos para o mesmo exemplar físico simultaneamente. |
| RNF06 | Rastreabilidade (Auditoria) | O sistema deve manter logs de todas as operações críticas, como abonos de multas ou alteração manual de estoque, identificando o responsável pela ação. |
| RNF07 | Conformidade com a LGPD | O sistema deve garantir a privacidade dos dados dos usuários e permitir a exclusão de dados pessoais conforme a Lei Geral de Proteção de Dados. |

---
