CREATE TABLE IF NOT EXISTS pais (
    pais_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    denominacion VARCHAR(25)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS producto (
    producto_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    denominacion VARCHAR(40),
    precio DECIMAL(6, 2)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS provincia (
    provincia_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    denominacion VARCHAR(25),
    pais INT(11),
    INDEX(pais),
    FOREIGN KEY (pais) REFERENCES pais (pais_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS domicilio (
    domicilio_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    calle VARCHAR(60),
    altura VARCHAR(8),
    localidad VARCHAR(30),
    provincia INT(11),
    INDEX(provincia),
    FOREIGN KEY (provincia) REFERENCES provincia (provincia_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cliente (
    cliente_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    denominacion VARCHAR(60),
    identificacion_tributaria VARCHAR(25),
    domicilio INT(11),
    FOREIGN KEY (domicilio) REFERENCES domicilio (domicilio_id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS datodecontacto (
    datodecontacto_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    denominacion VARCHAR(30),
    valor VARCHAR(30),
    cliente INT(11),
    INDEX(cliente),
    FOREIGN KEY (cliente) REFERENCES cliente (cliente_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedido (
    pedido_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    fecha DATE,
    estado INT(1),
    cliente INT(11),
    INDEX(cliente),
    FOREIGN KEY (cliente) REFERENCES cliente (cliente_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS productopedido (
    productopedido_id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pedido INT(11),
    INDEX(pedido),
    FOREIGN KEY (pedido) REFERENCES pedido (pedido_id)
        ON DELETE CASCADE,
    producto INT(11),
    FOREIGN KEY (producto) REFERENCES producto (producto_id)
        ON DELETE CASCADE,
    cantidad INT(4)
) ENGINE=InnoDB;