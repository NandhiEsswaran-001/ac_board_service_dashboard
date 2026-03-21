CREATE DATABASE IF NOT EXISTS ac_service_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ac_service_db;

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    full_name  VARCHAR(100) NOT NULL,
    role       ENUM('owner','staff','technician') DEFAULT 'staff',
    must_change_password TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, full_name, role, must_change_password) VALUES
('admin',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Owner', 'owner', 1),
('staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff One',   'staff', 1);

CREATE TABLE IF NOT EXISTS board_services (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    customer_name   VARCHAR(100) NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    address         TEXT,
    ac_brand        VARCHAR(100),
    ac_model        VARCHAR(100),
    problem         TEXT NOT NULL,
    customer_remarks TEXT,
    remark_checks   TEXT,
    parts_inside    TEXT,
    approx_amount   DECIMAL(10,2) DEFAULT 0,
    final_amount    DECIMAL(10,2) DEFAULT 0,
    parts_replaced  TEXT,
    status          ENUM('Pending','In Process','Completed','Delivered') DEFAULT 'Pending',
    notes           TEXT,
    created_by      INT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS field_services (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    service_report_no INT,
    customer_name     VARCHAR(100) NOT NULL,
    phone             VARCHAR(20)  NOT NULL,
    address           TEXT NOT NULL,
    service_date      DATE NOT NULL,
    assigned_employee INT,
    ac_type           VARCHAR(100),
    product_company   VARCHAR(100),
    purchase_date     DATE,
    unit_location     VARCHAR(150),
    problem           TEXT NOT NULL,
    work_done         TEXT,
    parts_used        TEXT,
    service_charge    ENUM('Yes','No') DEFAULT 'No',
    service_call_items TEXT,
    ampere            VARCHAR(30),
    voltage           VARCHAR(30),
    grill_temp        VARCHAR(30),
    sd_pressure       VARCHAR(30),
    warranty_text     VARCHAR(100),
    service_amount    DECIMAL(10,2) DEFAULT 0,
    payment_status    ENUM('Pending','Paid','Partial') DEFAULT 'Pending',
    status            ENUM('Scheduled','In Progress','Completed') DEFAULT 'Scheduled',
    notes             TEXT,
    created_by        INT,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_employee) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by)        REFERENCES users(id) ON DELETE SET NULL
);
