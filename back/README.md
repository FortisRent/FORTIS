# quarkus-api-template-java11

Pequeno CRUD com Quarkus (Jackson), JUnit, Maven e Java 23. 

Pra rodar:  docker-compose up --build (na pasta raiz do projeto)

USER: 	u234488260_2025_erp
DB: 	u234488260_2025_erp
PASS: 	t1O9&VeM|iF~

CREATE DATABASE IF NOT EXISTS 55ERP;
USE 55ERP;

-- Usuários
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    nome VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL,
    cpf VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL
) ENGINE=InnoDB;

-- Tabela: Empresas
CREATE TABLE company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    responsible_id INT,
    nome VARCHAR(128) NOT NULL,
    cnpj VARCHAR(32) NOT NULL,
    logo_url VARCHAR(32),
    FOREIGN KEY (responsible_id) REFERENCES user(id)
) ENGINE=InnoDB;

-- Serviços
CREATE TABLE service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    company_id INT,
    name VARCHAR(128) NOT NULL,
    description VARCHAR(256),
    amount INT NOT NULL,
    FOREIGN KEY (company_id) REFERENCES company(id)
) ENGINE=InnoDB;

-- Departamento
CREATE TABLE department (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    company_id INT,
    name VARCHAR(128) NOT NULL,
    description VARCHAR(256),
    FOREIGN KEY (company_id) REFERENCES company(id)
) ENGINE=InnoDB;

-- Funcionário
CREATE TABLE employee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    department_id INT,
    salary INT NOT NULL,
    FOREIGN KEY (department_id) REFERENCES department(id)
) ENGINE=InnoDB;

-- Benefícios
CREATE TABLE benefit_bonus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    file_url VARCHAR(128),
    amount INT NOT NULL,
    description VARCHAR(128),
    employee_id INT,
    FOREIGN KEY (employee_id) REFERENCES employee(id)
) ENGINE=InnoDB;

-- Clientes
CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    name VARCHAR(128) NOT NULL,
    email VARCHAR(128),
    phone VARCHAR(32),
    cpf VARCHAR(128) NOT NULL
) ENGINE=InnoDB;

-- Projetos
CREATE TABLE project (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    name VARCHAR(128) NOT NULL,
    client_id INT,
    start_date DATE,
    due_date DATE,
    FOREIGN KEY (client_id) REFERENCES client(id)
) ENGINE=InnoDB;

-- Tarefas
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    name VARCHAR(128) NOT NULL,
    project_id INT,
    responsible_id INT,
    start_date DATE,
    due_date DATE,
    is_completed BOOLEAN,
    FOREIGN KEY (project_id) REFERENCES project(id),
    FOREIGN KEY (responsible_id) REFERENCES employee(id)
) ENGINE=InnoDB;

-- Notas fiscais
CREATE TABLE invoice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    file_url VARCHAR(128),
    amount INT NOT NULL,
    description VARCHAR(128)
) ENGINE=InnoDB;

-- Contratos
CREATE TABLE contract (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    client_id INT,
    file_url VARCHAR(128),
    amount INT NOT NULL,
    description VARCHAR(128),
    FOREIGN KEY (client_id) REFERENCES client(id)
) ENGINE=InnoDB;
