CREATE TABLE IF NOT EXISTS `user` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    name VARCHAR(256) NOT NULL,
    email VARCHAR(64),
    phone VARCHAR(16),
    cpf VARCHAR(16),
    password VARCHAR(128),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME
);

CREATE TABLE IF NOT EXISTS `client` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    name VARCHAR(256) NOT NULL,
    email VARCHAR(64),
    phone VARCHAR(16),
    cpf VARCHAR(16),
    password VARCHAR(128),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME
);

CREATE TABLE IF NOT EXISTS `status` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    name VARCHAR(128) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME
);

INSERT INTO status (uuid, name) VALUES ('TST1', 'Agendado'), ('TST2', 'Pendente'), ('TST3', 'Realizado');

CREATE TABLE IF NOT EXISTS `company` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    name VARCHAR(256) NOT NULL,
    cnpj VARCHAR(32),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME
);

CREATE TABLE IF NOT EXISTS `service_order` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    code VARCHAR(32) NOT NULL,
    client_id INT NOT NULL,
    status_id INT NOT NULL,
    company_id INT NOT NULL,
    start_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    address VARCHAR(512) NOT NULL,
    service_load VARCHAR(32),
    service_width VARCHAR(32),
    service_height VARCHAR(32),
    service_length VARCHAR(32),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME,
    FOREIGN KEY (client_id) REFERENCES `client`(id),
    FOREIGN KEY (status_id) REFERENCES `status`(id),
    FOREIGN KEY (company_id) REFERENCES `company`(id)
);

CREATE TABLE IF NOT EXISTS `employee` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    company_id INT NOT NULL,
    name VARCHAR(256) NOT NULL,
    role VARCHAR(64) NOT NULL,
    deleted_at DATETIME,
    FOREIGN KEY (company_id) REFERENCES `company`(id)
);

CREATE TABLE IF NOT EXISTS `service_order_employee` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    service_order_id INT NOT NULL,
    employee_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME,
    FOREIGN KEY (service_order_id) REFERENCES `service_order`(id),
    FOREIGN KEY (employee_id) REFERENCES `employee`(id)
);

CREATE TABLE IF NOT EXISTS `gear` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    company_id INT NOT NULL,
    name VARCHAR(256) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME,
    FOREIGN KEY (company_id) REFERENCES `company`(id)
);

CREATE TABLE IF NOT EXISTS `service_order_gear` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(128) NOT NULL,
    service_order_id INT NOT NULL,
    gear_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME,
    FOREIGN KEY (service_order_id) REFERENCES `service_order`(id),
    FOREIGN KEY (gear_id) REFERENCES `gear`(id)
);