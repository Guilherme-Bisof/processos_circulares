# Processos Circulares 🚀

**Descrição curta**

Processos Circulares é um sistema web em PHP procedural para gerenciamento de processos, ofícios, arquivos e agenda interna. Projeto ideal para demonstrar habilidades em PHP, CRUD, autenticação simples e upload de arquivos.

---

## 📋 Índice

- Tecnologias
- Pré-requisitos
- Instalação e execução
- Configuração do banco de dados
- Estrutura do projeto
- Funcionalidades principais
- Boas práticas de segurança
- Sugestões para portfólio
- Como contribuir
- Licença

---

## 🔧 Tecnologias

- PHP (procedural)
- MySQL / MariaDB
- HTML/CSS (Bootstrap)
- JavaScript
- Font Awesome

---

## ⚙️ Pré-requisitos

- XAMPP (ou Apache + PHP + MySQL)
- PHP 7.4+ recomendado
- Acesso ao phpMyAdmin ou MySQL CLI

---

## 🚀 Instalação e execução local

1. Copie a pasta do projeto para a raiz do servidor (ex.: `C:\xampp\htdocs\processos_circulares`).
2. Inicie Apache e MySQL no XAMPP.
3. Crie o banco de dados e importe o dump (se disponível) — ver seção abaixo.
4. Atualize as credenciais de conexão em `core/conexao.php` (host, user, pass, dbname).
5. Garanta que a pasta `uploads/` seja gravável pelo servidor.
6. Acesse no navegador: `http://localhost/processos_circulares/`

---

## 🗄️ Configuração do banco de dados

Arquivo de conexão: `core/conexao.php`

Exemplo (substitua valores pelo seu ambiente):

```php
$host = "localhost";
$user = "root";
$pass = "sua_senha_aqui";
$dbname = "processos_circulares";
```

Se não houver dump SQL no repositório, crie o banco e adicione as tabelas mínimas. Exemplo para **usuarios_circulares**:

```sql
CREATE DATABASE processos_circulares CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE processos_circulares;

CREATE TABLE usuarios_circulares (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  usuario VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  tipo ENUM('admin','recepcao','facilitador') NOT NULL,
  facilitador_nome VARCHAR(255),
  ativo TINYINT(1) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

Observação: o projeto referencia outras tabelas como `processos_circulares`, `processos_circulares_agenda`, `processos_circulares_arquivamentos`, `oficios` etc. Verifique consultas em `modulos/` para descobrir colunas e relações necessárias.

---

## 🗂️ Estrutura do projeto

- `core/` — configurações e autenticação (`conexao.php`, `auth.php`, `header.php`)
- `modulos/` — funcionalidades por módulo (`agenda/`, `arquivos/`, `oficios/`, `processos/`, `usuarios/`)
- `assets/` — CSS, JS, imagens
- `uploads/` — arquivos enviados
- `login.php`, `logout.php`, `painel.php` — páginas principais

---

## ✨ Funcionalidades principais

- Autenticação e controle de permissões (ex.: `permitir(['admin'])`).
- CRUD de processos, ofícios e arquivos.
- Uploads e armazenamento em `uploads/`.
- Agenda interna para agendamentos.
- Gestão de usuários com tipos (admin, recepcao, facilitador).

---

## 🛠️ Ajustes aplicados (março 2026)

- Tabelas `processos_circulares` trocadas para `processos_circulares_total` em todas as operações de CRUD de processos.
- Validação `prepare()` e `bind_param()` reforçada em scripts críticos (`modulos/processos/*`).
- Inclusão de funções de sessão `setFlash`/`getFlash` em `core/auth.php` para mensagens padronizadas.
- Criação da rota de exclusão de usuários: `modulos/usuarios/excluir.php`.
- `.gitignore` atualizado para evitar commit de credenciais locais (`core/conexao.php`) e arquivos de dados.

## 🗃️ Esquema de tabelas confirmadas

- `processos_circulares_agenda`
- `processos_circulares_arquivamentos`
- `processos_circulares_oficios`
- `processos_circulares_total`
- `usuarios_circulares`

> Confirme que as tabelas acima existem antes de rodar o sistema. Faça backup antes de migrar.

## 🤝 Como contribuir

1. Fork → branch com feature/fix → pull request.
2. Explique as mudanças no PR e adicione screenshots quando aplicável.

---

