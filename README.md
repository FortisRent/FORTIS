# FORTIS

Uma plataforma completa de gestão empresarial e aluguel de máquinas pesadas, desenvolvida pela **FORTIS**.

## 📋 Sobre o Projeto

FORTIS é um sistema ERP moderno que oferece soluções integradas para empresas que trabalham com aluguel de máquinas pesadas, gestão de projetos, orçamentos e operações industriais. O sistema inclui aplicações web e móveis para diferentes tipos de usuários (administradores, clientes e operadores).

## 🏗️ Arquitetura do Sistema

O projeto é composto por diferentes módulos e tecnologias:

```
FORTIS/
├── api/              # Backend PHP (API REST)
├── back/             # Backend Java Quarkus (Microserviços)
├── mobile/           # App móvel cliente (Vue.js + Quasar)
├── mobile_op/        # App móvel operador (Vue.js + Quasar)
├── chat/             # Sistema de chat em tempo real (Node.js + Socket.io)
├── mongo/            # Configuração MongoDB
├── ml/               # Módulo de Machine Learning (PHP)
└── decisao/          # Documentação e apresentações
```

## 🚀 Tecnologias Utilizadas

### Backend
- **PHP 8+** - API REST principal
- **Java 17 + Quarkus** - Microserviços
- **Node.js** - Sistema de chat
- **MySQL** - Banco de dados principal
- **MongoDB** - Banco de dados para chat

### Frontend
- **Vue.js 3** - Framework frontend
- **Quasar Framework** - UI Components
- **TypeScript** - Tipagem estática
- **Pinia** - Gerenciamento de estado
- **Leaflet** - Mapas e geolocalização

### Infraestrutura
- **Docker** - Containerização
- **Capacitor** - Apps móveis híbridos
- **Socket.io** - Comunicação em tempo real

## 📱 Aplicações

### 1. API PHP (`/api`)
API REST principal que gerencia:
- Autenticação e autorização (JWT)
- Gestão de usuários e empresas
- Orçamentos e projetos
- Máquinas e categorias
- Operadores e funcionários
- Histórico e relatórios
- Integração com OMIE (sistema financeiro)

### 2. Backend Java Quarkus (`/back`)
Microserviços para:
- Processamento de dados
- Integrações externas
- Serviços complementares

### 3. App Móvel Cliente (`/mobile`)
Interface para clientes:
- Solicitação de orçamentos
- Acompanhamento de projetos
- Chat com suporte
- Geolocalização
- Dashboard personalizado

### 4. App Móvel Operador (`/mobile_op`)
Interface para operadores:
- Check-in/Check-out
- GPS e localização
- Gestão de tarefas
- Relatórios de campo

### 5. Sistema de Chat (`/chat`)
Chat em tempo real:
- Comunicação cliente-empresa
- Notificações em tempo real
- Histórico de conversas

## 🛠️ Instalação e Configuração

### Pré-requisitos
- PHP 8.0+
- Node.js 16+
- Java 17+
- MySQL 8.0+
- MongoDB
- Docker (opcional)

### 1. Configuração da API PHP

```bash
cd api
composer install
cp .env.example .env
# Configure as variáveis de ambiente no arquivo .env
php -S localhost:8000
```

### 2. Configuração do Backend Java

```bash
cd back
docker-compose up --build
# ou
./mvnw compile quarkus:dev
```

### 3. Configuração dos Apps Móveis

```bash
# App Cliente
cd mobile
npm install
quasar dev

# App Operador
cd mobile_op
npm install
quasar dev
```

### 4. Sistema de Chat

```bash
cd chat
npm install
npm start
```

### 5. MongoDB

```bash
cd mongo
docker-compose up -d
```

## 📊 Funcionalidades Principais

### Gestão de Empresas
- Cadastro e gerenciamento de empresas
- Controle de usuários e permissões
- Configurações personalizadas

### Máquinas e Equipamentos
- Cadastro de máquinas pesadas
- Categorização e especificações técnicas
- Controle de disponibilidade
- Sistema de franquia e preços

### Orçamentos e Projetos
- Criação de orçamentos automatizados
- Gestão de projetos complexos
- Propostas comerciais em PDF
- Histórico completo

### Operadores e Funcionários
- Gestão de operadores
- Certificações e qualificações
- Check-in/out com GPS
- Relatórios de atividades

### Geolocalização
- Rastreamento em tempo real
- Cálculo de distâncias
- Otimização de rotas

### Sistema Financeiro
- Integração com OMIE
- Controle de pagamentos
- Relatórios financeiros

## 🔐 Autenticação e Segurança

- **JWT (JSON Web Tokens)** para autenticação
- **Criptografia bcrypt** para senhas
- **Validação de dados** em todas as camadas
- **Controle de acesso** baseado em roles

## 📱 Apps Móveis

Os apps móveis são construídos com **Capacitor** e podem ser compilados para:
- **Android**
- **iOS**
- **PWA (Progressive Web App)**

### Compilação para Mobile

```bash
# Android
quasar build -m capacitor -T android

# iOS
quasar build -m capacitor -T ios

# PWA
quasar build -m pwa
```

## 🧪 Testes

```bash
# API PHP
cd api
php tests/run_tests.php

# Frontend
cd mobile
npm run test

# Linting
npm run lint
```

## 📈 Machine Learning

O módulo ML (`/ml`) inclui:
- Algoritmos KNN para recomendações
- Análise preditiva de demanda
- Otimização de recursos

## 🔄 Integração com APIs Externas

- **OMIE** - Sistema financeiro e fiscal
- **Correios** - CEP e endereços
- **Mapas** - Geolocalização e rotas

## 📋 Variáveis de Ambiente

Configure as seguintes variáveis no arquivo `.env`:

```env
# Database
DB_HOST=localhost
DB_NAME=fortis_db
DB_USER=user
DB_PASS=password

# JWT
JWT_SECRET=your_secret_key

# OMIE API
OMIE_APP_KEY=your_app_key
OMIE_APP_SECRET=your_app_secret

# Chat
CHAT_PORT=3000
MONGO_URI=mongodb://localhost:27017/fortis_chat
```

## 📚 Documentação da API

A API REST segue os padrões RESTful:

- `GET /v1/user/` - Listar usuários
- `POST /v1/user/` - Criar usuário
- `GET /v1/budget/{uuid}` - Buscar orçamento
- `POST /v1/project/` - Criar projeto
- `GET /v1/machine/` - Listar máquinas

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto é propriedade da **FORTIS**. Todos os direitos reservados.

## 👥 Equipe

Desenvolvido pela equipe **FORTIS**.

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema, entre em contato com a equipe de desenvolvimento.

---

## 🚀 Deploy

### Produção

1. Configure as variáveis de ambiente de produção
2. Execute os builds de produção:

```bash
# API
composer install --no-dev --optimize-autoloader

# Frontend
quasar build

# Backend Java
./mvnw clean package
```

3. Configure o servidor web (Apache/Nginx)
4. Configure SSL/HTTPS
5. Execute migrations do banco de dados

### Docker

```bash
# Build e execução completa
docker-compose up --build

# Apenas serviços específicos
docker-compose up mongodb quarkus-app
```

Este README fornece uma visão completa do sistema FORTIS e suas funcionalidades. Para informações mais detalhadas sobre módulos específicos, consulte a documentação individual de cada componente.
