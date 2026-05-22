# Processos Circulares — Sistema Integrado de Gestão Documental e Fluxo Interno

[![Status: Concluído](https://img.shields.io/badge/STATUS-CONCLUÍDO-green?style=for-the-badge)](https://github.com/Guilherme-Bisof/processos_circulares)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

> Solução web estruturada para a centralização, auditoria e gerenciamento de processos administrativos, emissão de ofícios, controle de acervo digital e sincronização de agendas institucionais. Projetado sob a ótica de máxima eficiência operacional e segurança de dados local.

---

## 📋 Sumário

- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades Principais](#-funcionalidades-principais)
- [Arquitetura e Tecnologias](#-arquitetura-e-tecnologias)
- [Políticas de Segurança Implementadas](#-políticas-de-segurança-implementadas)
- [Estrutura de Pastas](#-estrutura-de-pastas)
- [O Que Este Projeto Demonstra Tecnicamente](#-o-que-este-projeto-demonstra-tecnicamente)
- [Instalação e Execução Local](#-instalação-e-execução-local)
- [Modelagem do Banco de Dados (Schema)](#-modelagem-do-banco-de-dados-schema)

---

##  Sobre o Projeto

O **Processos Circulares** é uma aplicação corporativa desenvolvida para mitigar o gargalo de comunicações descentralizadas e perda de prazos em fluxos de trabalho administrativos. O sistema atua como um hub central onde a triagem de documentos, o arquivamento seguro de mídias e a agenda de compromissos operam de forma integrada.

O projeto foca no uso avançado do ecossistema nativo do PHP, provando que aplicações estruturadas sem a sobrecarga (*overhead*) de grandes frameworks podem entregar alta performance, manutenibilidade limpa e total controle sobre as regras de negócio e sanitização de dados.

---

##  Funcionalidades Principais

* **Controle de Acesso Baseado em Papéis (RBAC):** Sistema de permissões multinível integrado à sessão (`admin`, `recepcao`, `facilitador`), garantindo que rotas e dados sensíveis sejam acessados estritamente por pessoal autorizado.
* **Módulo de Gestão Documental (CRUD Avançado):** Rastreabilidade total e gerenciamento de processos administrativos e ofícios institucionais.
* **Mecanismo de Storage Local:** Upload seguro de arquivos anexos a processos, com isolamento de diretórios e validação de integridade.
* **Agenda Corporativa Integrada:** Módulo de agendamentos internos para otimização de reuniões e sincronização de prazos processuais.
* **Alertas Dinâmicos (Flash Messages):** Sistema nativo de gerenciamento de estados de sessão para feedbacks contextuais ao usuário (sucesso, alertas e erros de operação).

---

##  Arquitetura e Tecnologias

* **Back-end:** PHP 7.4+ (Estruturação modular nativa)
* **Banco de Dados:** MySQL / MariaDB (Persistência relacional)
* **Front-end:** HTML5 Semântico, JavaScript (Manipulação assíncrona/Eventos) e Bootstrap (Layout responsivo)
* **Estilização e Iconografia:** Font Awesome

---

##  Políticas de Segurança Implementadas

A aplicação foi submetida a uma esteira de refatoração focada em blindagem contra vulnerabilidades web comuns:

* **Prevenção contra SQL Injection:** Implementação rigorosa de *Prepared Statements* com `prepare()` e `bind_param()` em todos os motores de busca e escrita do sistema (`modulos/processos/*`).
* **Segurança de Escopo de Sessão:** Isolamento de rotas administrativas via middleware nativo de validação (`permitir(['admin'])`).
* **Proteção de Credenciais:** Desacoplamento completo de variáveis de ambiente locais e chaves de banco de dados do código-fonte através de políticas restritas no `.gitignore`.

---

##  Estrutura de Pastas

A engenharia do projeto adota o isolamento de escopos e responsabilidades por módulos:

```
processos_circulares/
├── core/            # Configurações globais, conexão PDO/MySQLi e regras de autenticação
├── modulos/         # Funcionalidades isoladas encapsuladas por contexto de negócio
│   ├── agenda/      # Regras e visualização do cronograma interno
│   ├── arquivos/    # Gestão de uploads e referências de arquivos
│   ├── oficios/     # Lógica de emissão e controle de ofícios
│   ├── processos/   # Motor principal de manipulação de processos administrativos
│   └── usuarios/    # Gestão cadastral e controle de níveis de privilégios
├── assets/          # Ativos estáticos compilados (CSS, JS, Imagens)
├── uploads/         # Repositório físico local de documentos anexados
└── *.php            # Pontos de entrada e rotas globais da aplicação (Login, Painel)
``` 

---

##  O Que Este Projeto Demonstra Tecnicamente

A construção deste ecossistema evidencia maturidade de engenharia de software nos seguintes aspectos avaliados pelo mercado:

1. **Domínio de PHP Nativo e Estruturado:** Capacidade de construir arquiteturas limpas, modulares e reutilizáveis sem dependência cega de componentes externos.
2. **Modelagem de Bancos de Dados Relacionais:** Manipulação eficiente de queries complexas, relacionamentos entre tabelas e normalização de dados.
3. **Padrões de Segurança Web:** Conhecimento prático sobre como mitigar falhas críticas de persistência e garantir sessões de usuário seguras.
4. **Resolução de Complexidade Operacional:** Habilidade de traduzir regras complexas de triagem governamental ou corporativa em um fluxo de software intuitivo.

---

##  Instalação e Execução Local

**Pré-requisitos:** XAMPP, WampServer ou ambiente Docker configurado com PHP 7.4+ e MySQL.

1. Efetue o clone do repositório no diretório raiz do seu servidor local (ex: `htdocs`):
```bash
   git clone [https://github.com/Guilherme-Bisof/processos_circulares.git](https://github.com/Guilherme-Bisof/processos_circulares.git)
   cd processos_circulares
```
2. Certifique-se de que o servidor web possui permissões de escrita na pasta de armazenamento:
```bash
  chmod -R 755 uploads/
```
3. Crie o arquivo de ambiente e ajuste as credenciais de conexão em core/conexao.php:
```PHP
  $host = "localhost";
  $user = "root";
  $pass = "sua_senha_aqui";
  $dbname = "processos_circulares";
```

---

##  Modelagem do Banco de Dados (Schema)
Execute o script abaixo em seu gerenciador SGBD (ex: phpMyAdmin) para instanciar a estrutura base de usuários. As demais tabelas de processos e logs são mapeadas de forma automatizada via leitura dos escopos em modulos/.

```SQL
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
) ENGINE=InnoDB;
```
Tabelas de Negócio Mapeadas e Verificadas: processos_circulares_agenda, processos_circulares_arquivamentos, processos_circulares_oficios, processos_circulares_total.

---

# Autor
Desenvolvido por Guilherme Bisof.

Sinta-se à vontade para expandir o ecossistema ou conectar-se através do meu [Linkedin](https://www.linkedin.com/in/guilhermebisof/)
