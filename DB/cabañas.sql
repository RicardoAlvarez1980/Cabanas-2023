CREATE TABLE Cabanas (
  numero INT PRIMARY KEY,
  capacidad INT,
  descripcion TEXT,
  costo_diario NUMERIC(10, 2)
);

CREATE TABLE Clientes (
  dni INT PRIMARY KEY,
  nombre VARCHAR(255),
  direccion VARCHAR(255),
  telefono VARCHAR(15),
  email VARCHAR(255)
);

CREATE TABLE Reservas (
  numero_reserva SERIAL PRIMARY KEY,
  fecha_inicio DATE,
  fecha_fin DATE,
  cliente_dni INT,
  cabana_numero INT
);

ALTER TABLE Reservas ADD FOREIGN KEY (cliente_dni) REFERENCES Clientes (dni);

ALTER TABLE Reservas ADD FOREIGN KEY (cabana_numero) REFERENCES Cabanas (numero);
