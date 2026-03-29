# 🥋 Sistema de Gestão de Academias

> Sistema web completo para gestão de alunos, mensalidades e financeiro de academias de judô e esportes marciais. Desenvolvido para substituir planilhas Google Sheets por uma solução centralizada, segura e acessível de qualquer dispositivo — inclusive como app no celular.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Instalável-5A0FC8?style=flat-square&logo=pwa&logoColor=white)
![License](https://img.shields.io/badge/Licença-Privada-red?style=flat-square)

---

## 📋 Índice

- [Visão Geral](#-visão-geral)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#-tecnologias)
- [Instalação](#-instalação)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Perfis de Acesso](#-perfis-de-acesso)
- [Módulos do Sistema](#-módulos-do-sistema)
- [Importação CSV](#-importação-csv)
- [Mensagem de Cobrança](#-mensagem-de-cobrança)
- [PWA — App no Celular](#-pwa--app-no-celular)
- [Variáveis de Ambiente](#-variáveis-de-ambiente)
- [Segurança](#-segurança)
- [Fluxo de Desenvolvimento](#-fluxo-de-desenvolvimento)

---

## 🎯 Visão Geral

O sistema nasceu da necessidade de substituir uma planilha Google Sheets que controlava alunos, mensalidades e frequência de uma academia de judô. A planilha tinha colunas como `manha/tarde`, `contrato`, `faixa`, `responsavel` e uma coluna por mês com o status do pagamento (`Pix 02/03`, `ATRASADO`, `integral`, etc.).

O sistema reproduz e expande essa lógica com:

- Interface web responsiva acessível de qualquer dispositivo
- Separação por unidade (academia/escola)
- Controle financeiro com alerta de inadimplência
- Lista de cobrança com WhatsApp integrado
- Instalável como app no Android e iPhone (PWA)

---

## ✨ Funcionalidades

### 👥 Alunos
- Cadastro completo: nome, nascimento, faixa, turno (M/T/N), série, tamanho de kimono
- Status ativo/inativo e data de entrada
- Vínculo com academia e responsável
- Indicador de contrato assinado
- Ficha individual com histórico de mensalidades e exames
- Busca por nome com filtros de faixa, série, status e academia

### 👨‍👩‍👧 Responsáveis
- Cadastro com nome, telefone e e-mail
- Link direto para WhatsApp (`wa.me/55...`)
- Listagem expandível mostrando todos os alunos vinculados
- Vínculo/desvinculação de alunos diretamente na ficha do responsável com busca AJAX

### 🏫 Academias
- Múltiplas unidades no mesmo sistema
- Cada usuário pode ser restrito a uma academia específica
- Separação de dados por unidade em todos os módulos

### 💰 Financeiro (Mensalidades)
- Lançamento individual ou em lote para toda a academia
- Status: ✅ Pago | ⏳ Pendente | 🔴 Atrasado | 🟢 Integral (bolsa)
- Registro de forma de pagamento (Pix, Dinheiro, Cartão, etc.)
- Atualização automática de "pendente" para "atrasado" quando o vencimento passa
- Busca AJAX de aluno por primeiras letras ao lançar mensalidade
- Filtros por mês, status, academia e nome do aluno

### 📊 Dashboard
- Total de alunos ativos
- Card de inadimplentes **clicável** → abre painel de cobrança completo
- Total recebido no mês
- Mensalidades vencendo nos próximos 7 dias
- Próximos exames de faixa
- Próximos campeonatos
- Ações rápidas para os módulos principais

### 📞 Painel de Cobrança
- Gerado ao clicar no card de inadimplentes no Dashboard
- Lista todos os responsáveis com alunos em aberto
- Ordenado por maior valor em aberto
- Botão WhatsApp direto para cada responsável
- Geração de mensagem personalizada com 1 clique (usa o template configurado)
- Suporte a múltiplos alunos por responsável (lista cada um com valor e meses)
- Botão "Copiar todos os contatos" para ação em massa

### 🎽 Faixas
- Módulo configurável: crie, edite e exclua faixas
- Reordenação por drag-and-drop para definir a progressão
- Picker de cor com preview em tempo real
- Proteção: não permite excluir faixa em uso por algum aluno
- Faixas padrão pré-carregadas: branca, branca/cinza, cinza, cinza/azul, azul... preta

### 🎓 Exames de Faixa
- Agendamento de exames com data
- Status: pendente / aprovado / reprovado
- Aprovação com 1 clique → atualiza automaticamente a faixa do aluno
- Exibição dos próximos exames no Dashboard

### 🏆 Campeonatos
- Cadastro de eventos com nome, data e local
- Inscrição de alunos com resultado
- Próximos campeonatos visíveis no Dashboard

### 👤 Usuários
- Três perfis com permissões distintas (ver tabela abaixo)
- Criação e gestão de usuários pelo Admin
- Cada usuário pode alterar sua própria senha
- Usuários inativos não conseguem logar

### ⚙️ Configurações
- Chave PIX do professor/academia
- Nome do professor (assinatura das mensagens)
- Template da mensagem de cobrança com variáveis dinâmicas
- Preview em tempo real da mensagem

### 📥 Importação CSV
- Importa alunos e responsáveis direto do Google Sheets
- Importa mensalidades detectando automaticamente colunas de meses
- Download de CSV de exemplo com dados reais
- Mapeamento automático de colunas da planilha original

---

## 🛠️ Tecnologias

| Camada | Tecnologia |
|---|---|
| Backend | PHP 7.4+ (compatível com PHP 8.x) |
| Banco de dados | MySQL 5.7+ |
| Frontend | HTML5 + CSS3 + JavaScript puro |
| Arquitetura | MVC sem framework |
| Conexão BD | PDO com prepared statements |
| Autenticação | Sessões PHP + bcrypt |
| App mobile | PWA (Progressive Web App) |
| Ícones | SVG inline |
| Fontes | Inter (Google Fonts) |

---

## 🚀 Instalação

### Pré-requisitos

- PHP 7.4 ou superior com extensões: `pdo`, `pdo_mysql`, `session`
- MySQL 5.7 ou superior
- Servidor web com suporte a `.htaccess` (Apache) ou configuração equivalente (Nginx)

### Passo a passo

**1. Clone o repositório**

```bash
git clone git@github.com:oflaviomattos/sistema_academias.git
cd sistema_academias
```

**2. Configure o ambiente**

```bash
cp .env.example .env
```

Edite o `.env` com suas credenciais:

```env
DB_HOST=localhost
DB_NAME=nome_do_banco
DB_USER=usuario_do_banco
DB_PASS=senha_do_banco
BASE_URL=/sistema
APP_ENV=production
```

**3. Instale o banco de dados**

Acesse no navegador:
```
https://seudominio.com/sistema/install.php
```

O instalador cria todas as tabelas e o usuário admin padrão.

**4. Primeiro acesso**

```
https://seudominio.com/sistema/index.php?page=login
```

| Campo | Valor padrão |
|---|---|
| E-mail | `admin@academia.com` |
| Senha | `Admin@2025` |

> ⚠️ Troque a senha imediatamente após o primeiro acesso em **Perfil → Alterar senha**.

**5. Delete o instalador**

```bash
rm install.php
```

**6. Configure o sistema**

Acesse **Ferramentas → Configurações** e preencha:
- Chave PIX
- Nome do professor
- Template da mensagem de cobrança

---

## 📁 Estrutura do Projeto

```
sistema_academias/
│
├── api/
│   └── alunos.php              # Endpoint AJAX: busca alunos por nome
│
├── config/
│   ├── app.php                 # Configurações gerais, helpers globais
│   ├── database.php            # Conexão PDO (lê credenciais do .env)
│   └── schema.sql              # DDL completo do banco de dados
│
├── controllers/                # Lógica de negócio de cada módulo
│   ├── AuthController.php      # Login e logout
│   ├── DashboardController.php # Dashboard e painel de cobrança
│   ├── AlunosController.php    # CRUD de alunos
│   ├── ResponsaveisController.php
│   ├── AcademiasController.php
│   ├── FinanceiroController.php
│   ├── ExamesController.php
│   ├── CampeonatosController.php
│   ├── FaixasController.php
│   ├── UsuariosController.php
│   ├── PerfilController.php    # Alterar própria senha
│   ├── ConfiguracoesController.php
│   ├── ImportacaoController.php
│   └── ExemplosController.php  # Download de CSVs de exemplo
│
├── models/                     # Acesso ao banco de dados
│   ├── AlunoModel.php
│   ├── MensalidadeModel.php
│   ├── OutrosModels.php        # Academia, Responsável, Exame, Campeonato, Faixa, Configuração
│   └── UsuarioModel.php
│
├── views/                      # Templates HTML
│   ├── layouts/
│   │   ├── header.php          # Sidebar, topbar, meta tags PWA
│   │   └── footer.php          # Scripts, botão instalar PWA
│   ├── auth/login.php
│   ├── dashboard/index.php     # Dashboard + painel de cobrança
│   ├── alunos/
│   ├── financeiro/
│   ├── academias/
│   ├── exames/
│   ├── campeonatos/
│   ├── faixas/
│   ├── usuarios/
│   ├── configuracoes/
│   └── importacao/
│
├── public/
│   ├── css/app.css             # Estilos globais + responsivo + PWA
│   ├── manifest.json           # Manifest do PWA
│   ├── sw.js                   # Service Worker (cache + offline)
│   ├── favicon.ico
│   └── icons/                  # Ícones PWA (72px a 512px)
│
├── uploads/
│   └── csv/                    # CSVs importados (ignorado pelo git)
│
├── .env                        # Credenciais reais — NUNCA commitar!
├── .env.example                # Modelo de configuração
├── .gitignore
├── .htaccess                   # Rewrite rules do Apache
├── index.php                   # Front controller (roteador central)
├── install.php                 # Instalador — deletar após usar
└── README.md
```

---

## 👤 Perfis de Acesso

| Módulo | 🔴 Admin | 💰 Financeiro | 👤 Usuário |
|---|:---:|:---:|:---:|
| Dashboard | ✅ | ✅ | ✅ |
| Alunos (ver/editar) | ✅ | ✅ | ✅ |
| Responsáveis | ✅ | ✅ | ✅ |
| Financeiro / Mensalidades | ✅ | ✅ | ❌ |
| Exames de Faixa | ✅ | ✅ | ✅ |
| Campeonatos | ✅ | ✅ | ✅ |
| Academias | ✅ | ❌ | ❌ |
| Usuários | ✅ | ❌ | ❌ |
| Faixas | ✅ | ❌ | ❌ |
| Configurações | ✅ | ❌ | ❌ |
| Importar CSV | ✅ | ❌ | ❌ |
| Gerar mensalidades em lote | ✅ | ❌ | ❌ |

> Usuários com perfil **Usuário** podem ser restritos a uma academia específica.

---

## 📦 Módulos do Sistema

### Roteamento

O sistema usa um front controller (`index.php`) com roteamento via `?page=`:

```
index.php?page=dashboard
index.php?page=alunos
index.php?page=alunos.criar
index.php?page=financeiro&mes=2026-01
```

### Helpers globais (`config/app.php`)

| Função | Descrição |
|---|---|
| `redirect($rota)` | Redireciona para uma rota interna |
| `isLoggedIn()` | Verifica se há sessão ativa |
| `isAdmin()` | Verifica se o perfil é admin |
| `isFinanceiro()` | Verifica se é admin ou financeiro |
| `requireLogin()` | Redireciona para login se não autenticado |
| `requireAdmin()` | Redireciona se não for admin |
| `h($string)` | `htmlspecialchars` — proteção XSS |
| `flashSet($tipo, $msg)` | Define mensagem flash na sessão |
| `flashGet()` | Lê e limpa mensagem flash |
| `formatMoeda($valor)` | Formata como `R$ 1.234,56` |
| `formatData($date)` | Formata `Y-m-d` como `dd/mm/yyyy` |
| `mesReferencia($mes)` | `2026-01` → `Janeiro/2026` |
| `getAcademiaFiltro()` | Retorna ID da academia do usuário (null se admin) |
| `isColorDark($hex)` | Detecta se cor hex é escura |

---

## 📥 Importação CSV

O sistema aceita CSVs exportados diretamente do Google Sheets.

### Importar Alunos

| Coluna no CSV | Campo no sistema | Obrigatório |
|---|---|:---:|
| `aluno` | Nome completo | ✅ |
| `manha / t` | Turno (M/T/N/MT) | — |
| `contrato` | Contrato assinado (`ok` = sim) | — |
| `#` | Série / nível | — |
| `faixa` | Faixa | — |
| `tamanho` | Tamanho do kimono | — |
| `responsavel` | Nome do responsável | — |
| `contato` | Telefone do responsável | — |

### Importar Mensalidades

O sistema detecta automaticamente colunas de meses pelos nomes `Jan`, `Fev`, `Mar`... `Dez`.

| Conteúdo da célula | Interpretado como |
|---|---|
| `Pix 02/03` | ✅ Pago via Pix em 02/03 |
| `Pix 27/01` | ✅ Pago via Pix em 27/01 |
| `Quitado` | ✅ Pago em dinheiro |
| `Pendente` | ⏳ Pendente |
| `ATRASADO` | 🔴 Atrasado |
| `integral` | 🟢 Bolsa integral |
| *(vazia)* | Ignorada |

### Como exportar do Google Sheets

1. Abra a planilha e selecione a aba desejada (ex: `2026`)
2. **Arquivo → Fazer download → Valores separados por vírgula (.csv)**
3. Importe primeiro os **Alunos**, depois as **Mensalidades** (mesmo arquivo)

---

## 📝 Mensagem de Cobrança

Configure o template em **Ferramentas → Configurações**.

### Variáveis disponíveis

| Variável | Substituída por |
|---|---|
| `{responsavel}` | Nome do responsável |
| `{aluno}` | Nome do(s) aluno(s) com detalhes |
| `{academia}` | Nome da academia |
| `{mes}` | Mês(es) de referência |
| `{vencimento}` | Data de vencimento |
| `{valor}` | Valor da mensalidade |
| `{valor_total}` | Total em aberto (todos os alunos) |
| `{pix}` | Chave PIX configurada |
| `{professor}` | Nome do professor configurado |

### Exemplo de template

```
Bom dia familia!
Estou encaminhando este lembrete referente a parcela do Judo ({academia}) 
que venceu {mes} no valor de {valor_total}.

Aluno(s):
{aluno}

PIX: {pix} (Marcado Pago). Solicito que envie o comprovante para o controle.
Qualquer duvida estou a disposicao.

Grato!
{professor}
```

> Com 2 alunos, `{aluno}` lista cada um com seus meses e valor individual. O `{valor_total}` soma tudo automaticamente.

---

## 📱 PWA — App no Celular

O sistema é um **Progressive Web App** — pode ser instalado diretamente do navegador, sem App Store ou Play Store.

### Android (Chrome)

1. Acesse o sistema no Chrome
2. Toque no botão **📲 Instalar App** (aparece automaticamente)
3. Ou: menu `⋮` → **"Adicionar à tela inicial"**

### iPhone (Safari)

1. Acesse o sistema no **Safari** (não Chrome)
2. Toque no botão **Compartilhar** `□↑`
3. Role e toque em **"Adicionar à Tela de Início"**
4. Confirme — o app aparece na tela inicial

### Funcionalidades PWA

- **Ícone na tela inicial** com visual de app nativo
- **Tela cheia** sem barra do navegador (modo standalone)
- **Tela offline** amigável quando sem internet
- **Cache de assets** para carregamento mais rápido
- **Safe area** para notch e home indicator do iPhone
- **Atalhos** de acesso rápido (Dashboard, Alunos, Mensalidades)

---

## 🔧 Variáveis de Ambiente

| Variável | Descrição | Exemplo |
|---|---|---|
| `DB_HOST` | Host do MySQL | `localhost` |
| `DB_NAME` | Nome do banco | `gestao_academias` |
| `DB_USER` | Usuário do MySQL | `root` |
| `DB_PASS` | Senha do MySQL | `senha123` |
| `BASE_URL` | Caminho base da URL (sem barra final) | `/projetos/sistema_academias` |
| `APP_ENV` | Ambiente (`development` ou `production`) | `production` |

> Em `development`, erros de conexão mostram detalhes técnicos. Em `production`, exibem mensagem genérica.

---

## 🔒 Segurança

- **Senhas** armazenadas com `password_hash()` — algoritmo bcrypt
- **Queries** protegidas com PDO + prepared statements (sem SQL injection)
- **XSS** prevenido com `htmlspecialchars()` em todos os outputs (`h()`)
- **Credenciais** isoladas no `.env` — fora do repositório Git
- **Sessões** regeneradas no login (`session_regenerate_id()`)
- **Perfis** verificados em cada controller antes de executar ações
- **Uploads** restritos a arquivos `.csv` com validação de extensão
- **Headers de segurança** configurados no `.htaccess`

---

## 🔄 Fluxo de Desenvolvimento

### Atualizar o servidor após mudanças

```bash
# 1. Faz as alterações nos arquivos
# 2. Commita e sobe para o GitHub
git add .
git commit -m "fix: descricao do que foi alterado"
git push

# 3. No servidor, puxa as atualizações
git pull origin main
```

### Padrão de mensagens de commit

```bash
git commit -m "feat: nova funcionalidade"     # nova feature
git commit -m "fix: corrige bug no módulo X"  # correção de bug
git commit -m "style: ajuste visual na tela"  # mudança visual
git commit -m "refactor: reorganiza o model"  # refatoração
git commit -m "docs: atualiza README"         # documentação
```

### Adicionar novo módulo

1. Crie o controller em `controllers/NovoController.php`
2. Crie o model em `models/` (ou adicione classe em `OutrosModels.php`)
3. Crie as views em `views/novo/`
4. Registre as rotas em `index.php`
5. Adicione o link no sidebar em `views/layouts/header.php`

---

## 📄 Licença

Uso privado. Todos os direitos reservados.

---

*Desenvolvido para gestão de academias de judô e esportes marciais.*
