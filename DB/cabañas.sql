CREATE TABLE Cabanas (
    numero SERIAL PRIMARY KEY,
    capacidad INTEGER,
    descripcion TEXT,
    costoDiario DECIMAL(10, 2)
);

CREATE TABLE Clientes (
    id SERIAL PRIMARY KEY,
    nombre TEXT,
    direccion TEXT,
    telefono TEXT,
    email TEXT
);

CREATE TABLE Reservas (
    numero SERIAL PRIMARY KEY,
    cliente_id INTEGER REFERENCES Clientes(id),
    cabana_numero INTEGER REFERENCES Cabanas(numero),
    fechaInicio DATE,
    fechaFin DATE
);