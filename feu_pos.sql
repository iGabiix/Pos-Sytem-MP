-- Tamaraw POS - MySQL 8 / MariaDB 10.4+ (XAMPP)
-- Import into a NEW database only. This file intentionally never drops data.
CREATE DATABASE IF NOT EXISTS feu_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE feu_pos;

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock_quantity INT NOT NULL DEFAULT 0,
  image VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NULL,
  created_at DATETIME NOT NULL,
  deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  customer_id INT NULL,
  sold_by INT NOT NULL,
  quantity INT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  request_key VARCHAR(64) NULL UNIQUE,
  created_at DATETIME NOT NULL,
  INDEX sales_created_at (created_at),
  CONSTRAINT sales_product_fk FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT sales_customer_fk FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT sales_staff_fk FOREIGN KEY (sold_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- DEMO ONLY: admin / TamarawDemo!2026. Change this password before using real data.
START TRANSACTION;
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('1', 'Tamaraw Classic Tee', '450.00', '46', 'sample-shirt.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('2', 'Green & Gold Hoodie', '1250.00', '20', 'sample-hoodie.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('3', 'Everyday Canvas Tote', '295.00', '31', 'sample-tote.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('4', 'Campus Notes Journal', '185.00', '9', 'sample-notebook.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('5', 'Golden Hour Tumbler', '595.00', '24', 'sample-tumbler.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('6', 'Tamaraw ID Lanyard', '95.00', '8', 'sample-lanyard.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('7', 'Campus Club Cap', '350.00', '6', 'sample-cap.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO products (id, name, price, stock_quantity, image, created_at, deleted_at) VALUES ('8', 'Green & Gold Pen', '45.00', '3', 'sample-pen.svg', '2026-09-03 08:00:00', NULL);
INSERT INTO customers (id, full_name, email, phone, created_at, deleted_at) VALUES ('1', 'Andrea Garcia', 'andrea.garcia@example.com', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO customers (id, full_name, email, phone, created_at, deleted_at) VALUES ('2', 'Luis Mendoza', 'luis.mendoza@example.com', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO customers (id, full_name, email, phone, created_at, deleted_at) VALUES ('3', 'Sofia Ramos', 'sofia.ramos@example.com', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO customers (id, full_name, email, phone, created_at, deleted_at) VALUES ('4', 'Ethan Torres', 'ethan.torres@example.com', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO customers (id, full_name, email, phone, created_at, deleted_at) VALUES ('5', 'Isabella Lim', 'isabella.lim@example.com', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO users (id, username, full_name, password, avatar, created_at, deleted_at) VALUES ('1', 'admin', 'Alex Reyes', '$2y$10$DBKqkwL6mUkwKl0S779Ju.4h88prnXwxi36munfS5j585xL0e4riC', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO users (id, username, full_name, password, avatar, created_at, deleted_at) VALUES ('2', 'mika', 'Mika Santos', '$2y$10$3Xm.IUKsEHwRiIg6qr03y.szqiAKo.k5tR2WIY2xkA6HKzTLFvu9O', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO users (id, username, full_name, password, avatar, created_at, deleted_at) VALUES ('3', 'josh', 'Josh Dela Cruz', '$2y$10$lkEIUWbdLyByIA6WHk2/dOn27e5mv470It21BXFOkG3REtB/mg1y.', NULL, '2026-09-03 08:00:00', NULL);
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('1', '1', '1', '1', '2', '900.00', '254d1830a1da6b874dcf9e88b921ddc195bc2c5724495c0e0d178a36f001ebce', '2026-09-27 09:00:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('2', '4', NULL, '2', '1', '185.00', 'b1327b3374c0d7f1146f88505a8bb367e412842fd6c58331c32ae514837ad258', '2026-09-27 10:07:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('3', '2', '2', '2', '1', '1250.00', 'cfc06ac8d4503bc7d4785dc5f0daf192131e05e462242754d1456bc8627b7a44', '2026-09-28 11:14:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('4', '3', '3', '1', '2', '590.00', '9532323e6d602bb894254b0d08bb6df2e0658938e6e82b6adcfd864033b73e15', '2026-09-28 12:21:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('5', '5', '4', '3', '2', '1190.00', '5c4bad1480fdc14bd9d21187697dbe83e41218827c1f8d21e616cdc0501337d5', '2026-09-29 13:28:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('6', '8', NULL, '1', '1', '45.00', '71e9688066aed24d7fcf7b75610371bbf0ac8a002c1d6cb3c88fd845a97d255f', '2026-09-29 14:35:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('7', '1', '5', '2', '1', '450.00', '3f5c7e2c6972f145367ff904d98322d7ba0246446b039208962a774dc33bc635', '2026-09-30 15:42:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('8', '4', '1', '3', '2', '370.00', '33509462a58a805d2ba65cd908237be5018c4086d379501c1cc4c78a5bb60a86', '2026-09-30 09:49:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('9', '2', '3', '1', '2', '2500.00', '10540fd2ea4c5cee0e64727ed4a3a9da004f73f7ab216d837da3d4887bf8d80e', '2026-10-01 10:56:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('10', '6', NULL, '2', '1', '95.00', 'b1560ebaea5ef2d9a36e06b0e234a548038fb353c512691ef659acf32cb075fe', '2026-10-01 11:03:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('11', '5', '2', '3', '3', '1785.00', '6d5a3e8bb329595a4a3bd101524394148139d2277beea9b2218dcd6057069d8d', '2026-10-02 12:10:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('12', '1', '5', '1', '2', '900.00', '90d8a3d35aff37b7f842c0fa08f99c1ffe2e46b4c83c15013ee0a225a4812eb2', '2026-10-02 13:17:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('13', '1', '1', '1', '1', '450.00', '99ed6d9aa59b607b3c9a138231080aff7687e132168f28de6bec9f5a7e120b95', '2026-10-03 14:24:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('14', '5', NULL, '2', '1', '595.00', '64239c3dc1d45e5180166a2df2b26b33f165d1e10e2e8f9209ce5aa97a7bf93e', '2026-10-03 15:31:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('15', '3', '3', '1', '2', '590.00', '05db4e553567ecd55a61f2bb6069b1c980efb08250e588d91f04848c8e246266', '2026-10-03 09:38:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('16', '4', '4', '3', '1', '185.00', '37e7812e7185208034edc63f4f3155a17df12548c2517a7de8cecb8bcd98653d', '2026-10-03 10:45:00');
INSERT INTO sales (id, product_id, customer_id, sold_by, quantity, total_price, request_key, created_at) VALUES ('17', '7', '2', '1', '1', '350.00', 'c3776c97edb90dcba0a543116bb3285dfc6f45a44de3e16cbfec31e5c27d9e81', '2026-10-03 11:52:00');
COMMIT;
