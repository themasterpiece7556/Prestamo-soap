USE prestamo_equipos;
SELECT * FROM solicitantes;
SELECT * FROM equipos;
USE prestamo_equipos;

INSERT INTO solicitantes (documento, username, nombre, correo, password_hash) VALUES
('1001001001', 'juan_perez',     'Juan Pérez',          'juan.perez@email.com',     '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001002', 'maria_gomez',    'María Gómez',         'maria.gomez@email.com',    '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001003', 'carlos_lopez',   'Carlos López',        'carlos.lopez@email.com',   '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001004', 'ana_martinez',   'Ana Martínez',        'ana.martinez@email.com',   '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001005', 'luis_rodriguez', 'Luis Rodríguez',      'luis.rodriguez@email.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001006', 'laura_sanchez',  'Laura Sánchez',       'laura.sanchez@email.com',  '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001007', 'diego_ramirez',  'Diego Ramírez',       'diego.ramirez@email.com',  '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001008', 'sofia_torres',   'Sofía Torres',        'sofia.torres@email.com',   '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001009', 'andres_morales', 'Andrés Morales',      'andres.morales@email.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2'),
('1001001010', 'valentina_castro','Valentina Castro',   'valentina.castro@email.com','$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.7.gJb3c7J8u5v8jR9h/2K9X9/K2');

USE prestamo_equipos;

INSERT INTO equipos (codigo, nombre, tipo, estado) VALUES
('EQ-1001', 'Portátil Lenovo ThinkPad E14',    'Computador Portátil', 'Disponible'),
('EQ-1002', 'Portátil HP ProBook 450 G8',      'Computador Portátil', 'Prestado'),
('EQ-1003', 'Proyector Epson PowerLite E20',    'Proyector',           'Disponible'),
('EQ-1004', 'Tablet Samsung Galaxy Tab S8',     'Tablet',              'Disponible'),
('EQ-1005', 'Cámara Canon EOS Rebel T7',        'Cámara',              'Mantenimiento'),
('EQ-1006', 'Switch Cisco Catalyst 2960',       'Redes',               'Disponible'),
('EQ-1007', 'Portátil Dell Latitude 3420',      'Computador Portátil', 'Prestado'),
('EQ-1008', 'Micrófono Inalámbrico Shure BLX4', 'Audio',               'Disponible'),
('EQ-1009', 'Monitor LG 24 pulgadas Full HD',   'Pantalla',            'Disponible'),
('EQ-1010', 'Proyector BenQ MX560',             'Proyector',           'Mantenimiento');