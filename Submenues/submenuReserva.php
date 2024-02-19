<?php
require_once 'Cabanas.php';
require_once 'Clientes.php';
require_once 'Reservas.php';

// Función para cargar reservas desde la base de datos
function cargarReservasDesdeBD()
{
    global $reservas;

    // Limpia el arreglo de reservas existente
    $reservas = [];

    // Realiza la consulta para cargar reservas desde la base de datos
    $conexion = Conexion::obtenerInstancia();
    $pdo = $conexion->obtenerConexion();
    $stmt = $pdo->query("SELECT R.*, C.*, Ca.* FROM reservas R
                        INNER JOIN clientes C ON R.cliente_dni = C.dni
                        INNER JOIN cabanas Ca ON R.cabana_numero = Ca.numero");

    // Recorre los resultados y crea instancias de Reserva con detalles completos
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cliente = new Clientes(
            $row['dni'],
            $row['nombre'],
            $row['direccion'],
            $row['telefono'],
            $row['email']
        );

        $cabana = new Cabanas(
            $row['numero'],
            $row['capacidad'],
            $row['descripcion'],
            $row['costo_diario']
        );

        $reserva = new Reservas(
            $row['numero_reserva'],
            $row['fecha_inicio'],
            $row['fecha_fin'],
            $cliente,
            $cabana
        );

        $reservas[] = $reserva;
    }
}
// Menú de Gestionar Reservas
function menuReservas()
{
    $opcionesDisponibles = [
        '1' => 'altaReserva',
        '2' => 'modificarReserva',
        '3' => 'eliminarReserva',
        '4' => 'listarReservas',
        '0' => 'menuPrincipal',
    ];

    echo "=================================";
    echo "\nMenú de Gestionar Reservas\n";
    echo "=================================\n";
    echo "1. Alta de Reserva\n";
    echo "2. Modificar Reserva\n";
    echo "3. Eliminar Reserva\n";
    echo "4. Listar Reservas\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    if (isset($opcionesDisponibles[$opcion])) {
        $funcion = $opcionesDisponibles[$opcion];
        call_user_func($funcion);
    } else {
        echo "Opción inválida. Intente nuevamente.\n";
        menuReservas();
    }
}

function formatoFechaDDMMYYYY($fecha)
{
    // Convertir la fecha de YYYY-MM-DD a DD/MM/YYYY
    $fechaFormateada = date("d/m/Y", strtotime($fecha));
    return $fechaFormateada;
}



// Nueva función para buscar cabañas libres en un rango de fechas
function buscarCabañasLibresEnFechas($fechaInicio, $fechaFin)
{
    global $cabanas, $reservas;

    $cabañasLibres = [];

    foreach ($cabanas as $cabana) {
        // Verificar si la cabaña está libre para las fechas dadas
        $cabañaOcupada = false;

        foreach ($reservas as $reserva) {
            // Convertir las fechas de la reserva a objetos DateTime
            $inicioReserva = new DateTime($reserva->getFechaInicio());
            $finReserva = new DateTime($reserva->getFechaFin());
            $inicioBusqueda = new DateTime($fechaInicio);
            $finBusqueda = new DateTime($fechaFin);

            // Verificar si hay superposición de fechas
            if (
                $inicioReserva < $finBusqueda &&
                $finReserva > $inicioBusqueda &&
                $reserva->getCabana()->getNumero() === $cabana->getNumero()
            ) {
                $cabañaOcupada = true;
                break; // No es necesario seguir verificando
            }
        }

        // Si la cabaña no está ocupada, se agrega a la lista de cabañas libres
        if (!$cabañaOcupada) {
            $cabañasLibres[] = $cabana;
        }
    }

    return $cabañasLibres;
}
// Modificar la función altaReserva para buscar cabañas disponibles en las fechas seleccionadas
function altaReserva()
{
    global $reservas, $clientes, $cabanas;

    echo "\nAlta de Reserva\n";

    // Solicitar datos de la reserva al usuario
    echo "---------------------\n";
    echo "Clientes Disponibles:\n";
    echo "---------------------\n";
    // ...

    echo "Ingrese el DNI del cliente que realiza la reserva: \n";
    echo "-----------------------------------------------\n";
    $dniCliente = trim(fgets(STDIN));

    // Buscar el cliente por su DNI
    $clienteSeleccionado = buscarClientePorDNI($dniCliente);

    if (!$clienteSeleccionado) {
        echo "No se encontró un cliente con ese DNI. La reserva no se puede completar.\n";
        return;
    }

    // Ingresar y validar la fecha de inicio de la reserva
    echo "Ingrese la fecha de inicio en formato DD/MM/YYYY: ";
    $fechaInicio = formatoFechaDDMMYYYY(trim(fgets(STDIN)));

    // Ingresar y validar la fecha de fin de la reserva
    echo "Ingrese la fecha de fin en formato DD/MM/YYYY: ";
    $fechaFin = formatoFechaDDMMYYYY(trim(fgets(STDIN)));

    // Buscar cabañas disponibles en las fechas seleccionadas
    $cabañasDisponibles = buscarCabañasLibresEnFechas($fechaInicio, $fechaFin);

    // Mostrar detalles de las cabañas disponibles
    if (empty($cabañasDisponibles)) {
        echo "No hay cabañas disponibles para las fechas seleccionadas.\n";
        return;
    }

    echo "---------------------\n";
    echo "Cabañas Disponibles:\n";
    echo "---------------------\n";
    foreach ($cabañasDisponibles as $cabana) {
        echo "Número: " . $cabana->getNumero() . "\n";
        echo "Capacidad: " . $cabana->getCapacidad() . "\n";
        echo "Descripción: " . $cabana->getDescripcion() . "\n";
        echo "Costo Diario: $" . $cabana->getCostoDiario() . "\n";
        echo "---------------------------\n";
    }

    // Ingresar el número de la cabaña a reservar
    echo "Ingrese el número de la cabaña a reservar: ";
    $numeroCabana = trim(fgets(STDIN));

    // Buscar la cabaña por su número
    $cabanaSeleccionada = buscarCabanaPorNumero($numeroCabana);

    if (!$cabanaSeleccionada) {
        echo "No se encontró una cabaña con ese número. La reserva no se puede completar.\n";
        return;
    }

    // Crear una nueva instancia de Reservas con los datos proporcionados
    $reserva = new Reservas(count($reservas) + 1, $fechaInicio, $fechaFin, $clienteSeleccionado, $cabanaSeleccionada);
    $reservas[] = $reserva;

    // Aquí, después de agregar la reserva en memoria, también la insertamos en la base de datos
    $conexion = Conexion::obtenerInstancia(); // Obtenemos una instancia de la conexión
    $pdo = $conexion->obtenerConexion();

    // Preparar la consulta SQL para insertar la reserva en la base de datos
    $stmt = $pdo->prepare("INSERT INTO reservas (fecha_inicio, fecha_fin, cliente_dni, cabana_numero) VALUES (?, ?, ?, ?)");

    // Ejecutar la consulta con los datos de la reserva
    $stmt->execute([$fechaInicio, $fechaFin, $dniCliente, $numeroCabana]);

    echo "Reserva agregada exitosamente.\n";
}


// Función para modificar una reserva
function modificarReserva()
{
    global $reservas, $cabanas, $clientes;

    echo "\nModificar Reserva\n";

    // Mostrar la lista de reservas con el nombre del cliente y la fecha de reserva
    echo "---------------------------------\n";
    echo "Lista de Reservas:\n";
    echo "---------------------------------\n";
    foreach ($reservas as $reserva) {
        echo "ID de Reserva: " . $reserva->getNumero() . " - Cliente: " . $reserva->getCliente()->getNombre() . " - Fecha de Reserva: " . formatoFechaDDMMYYYY($reserva->getFechaInicio()) . "\n";
    }
    echo "---------------------------------\n";

    // Solicitar número de reserva a modificar
    echo "Ingrese el número de reserva que desea modificar (o deje en blanco para volver al Menú Principal): ";
    $numeroReserva = intval(trim(fgets(STDIN)));

    if (empty($numeroReserva)) {
        return; // Volver al Menú Principal si se ingresa un número de reserva en blanco
    }

    // Buscar la reserva por su número
    $reservaEncontrada = buscarReservaPorNumero($numeroReserva);

    if ($reservaEncontrada) {
        // Mostrar la información actual de la reserva
        echo "Información actual de la Reserva:\n";
        echo "Número de Reserva: " . $reservaEncontrada->getNumero() . "\n";
        echo "Fecha de Inicio: " . formatoFechaDDMMYYYY($reservaEncontrada->getFechaInicio()) . "\n";
        echo "Fecha de Fin: " . formatoFechaDDMMYYYY($reservaEncontrada->getFechaFin()) . "\n";
        echo "Cliente: " . $reservaEncontrada->getCliente()->getNombre() . "\n";
        echo "Cabaña: " . $reservaEncontrada->getCabana()->getNumero() . "\n";

        // Solicitar las nuevas fechas al usuario
        // Solicitar la nueva fecha de inicio al usuario
        echo "Ingrese la nueva fecha de inicio (o deje en blanco para mantener la actual): ";
        $nuevaFechaInicio = trim(fgets(STDIN));

        // Solicitar la nueva fecha de fin al usuario
        echo "Ingrese la nueva fecha de fin (o deje en blanco para mantener la actual): ";
        $nuevaFechaFin = trim(fgets(STDIN));

        // Validar y formatear las nuevas fechas si se proporcionaron
        if (!empty($nuevaFechaInicio)) {
            $nuevaFechaInicio = formatoFechaDDMMYYYY($nuevaFechaInicio);
        }

        if (!empty($nuevaFechaFin)) {
            $nuevaFechaFin = formatoFechaDDMMYYYY($nuevaFechaFin);
        }

        // Actualizar los campos de la reserva si se ingresan nuevos valores
        if (!empty($nuevaFechaInicio)) {
            $reservaEncontrada->setFechaInicio($nuevaFechaInicio);
        }
        if (!empty($nuevaFechaFin)) {
            $reservaEncontrada->setFechaFin($nuevaFechaFin);
        }
        // Aquí, después de actualizar los datos en memoria, también la actualizamos en la base de datos
        $conexion = Conexion::obtenerInstancia(); // Obtenemos una instancia de la conexión
        $pdo = $conexion->obtenerConexion();

        // Preparar la consulta SQL para actualizar la reserva en la base de datos
        $stmt = $pdo->prepare("UPDATE reservas SET fecha_inicio = ?, fecha_fin = ? WHERE numero_reserva = ?");

        // Ejecutar la consulta con los datos actualizados de la reserva
        $stmt->execute([$reservaEncontrada->getFechaInicio(), $reservaEncontrada->getFechaFin(), $numeroReserva]);

        echo "Reserva modificada exitosamente.\n";
    } else {
        echo "No se encontró una reserva con ese número.\n";
    }
}



// Función para eliminar una reserva
function eliminarReserva()
{
    global $reservas, $cabanas, $clientes;

    echo "\nEliminar Reserva\n";

    // Mostrar la lista de reservas con el nombre del cliente y la fecha de reserva
    echo "---------------------------------\n";
    echo "Lista de Reservas:\n";
    echo "---------------------------------\n";
    foreach ($reservas as $reserva) {
        echo "ID de Reserva: " . $reserva->getNumero() . " - Cliente: " . $reserva->getCliente()->getNombre() . " - Fecha de Reserva: " . formatoFechaDDMMYYYY($reserva->getFechaInicio()) . "\n";
    }
    echo "---------------------------------\n";

    // Solicitar número de reserva a eliminar
    echo "Ingrese el número de reserva que desea eliminar: ";
    $numeroReserva = intval(trim(fgets(STDIN)));

    // Buscar la reserva por su número
    $reservaEncontrada = null;
    foreach ($reservas as $reserva) {
        if ($reserva->getNumero() === $numeroReserva) {
            $reservaEncontrada = $reserva;
            break;
        }
    }

    if ($reservaEncontrada) {
        // Mostrar la información completa de la reserva
        echo "Información de la Reserva:\n";
        echo "Número de Reserva: " . $reservaEncontrada->getNumero() . "\n";
        echo "Fecha de Inicio: " . formatoFechaDDMMYYYY($reservaEncontrada->getFechaInicio()) . "\n";
        echo "Fecha de Fin: " . formatoFechaDDMMYYYY($reservaEncontrada->getFechaFin()). "\n";
        echo "Cliente: " . $reservaEncontrada->getCliente()->getNombre() . "\n";
        echo "Cabaña: " . $reservaEncontrada->getCabana()->getNumero() . "\n";

        // Confirmar eliminación
        echo "¿Está seguro de que desea eliminar esta reserva? (S/N): ";
        $opcion = strtoupper(trim(fgets(STDIN)));

        if ($opcion === 'S') {
            // Eliminar la reserva de la lista en memoria
            $key = array_search($reservaEncontrada, $reservas);
            if ($key !== false) {
                unset($reservas[$key]);
                echo "La reserva fue eliminada exitosamente.\n";
            } else {
                echo "No se pudo eliminar la reserva.\n";
            }

            // Eliminar la reserva de la base de datos
            $conexion = Conexion::obtenerInstancia(); // Obtenemos una instancia de la conexión
            $pdo = $conexion->obtenerConexion();

            // Preparar la consulta SQL de eliminación
            $stmt = $pdo->prepare("DELETE FROM reservas WHERE numero_reserva = ?");

            // Ejecutar la consulta
            $stmt->execute([$numeroReserva]);

        } 
    } else {
        echo "No se encontró una reserva con ese número.\n";
    }
}

// Función para listar reservas con información completa de cliente y cabaña
function listarReservas()
{
    global $reservas;

    cargarReservasDesdeBD(); // Cargar reservas desde la base de datos

    echo "=================================";
    echo "\nListado de Reservas\n";
    echo "=================================";

    if (empty($reservas)) {
        echo "\nNo hay reservas registradas en el sistema.\n";
    } else {
        foreach ($reservas as $reserva) {
            echo "\nNúmero de Reserva: " . $reserva->getNumero() . "\n";
            echo "Fecha de Inicio: " . formatoFechaDDMMYYYY($reserva->getFechaInicio()) . "\n";
            echo "Fecha de Fin: " . formatoFechaDDMMYYYY($reserva->getFechaFin()) . "\n";

            $cliente = $reserva->getCliente();
            $cabana = $reserva->getCabana();

            echo "Cliente:\n";
            echo "  DNI: " . $cliente->getDni() . "\n";
            echo "  Nombre: " . $cliente->getNombre() . "\n";
            echo "  Dirección: " . $cliente->getDireccion() . "\n";
            echo "  Teléfono: " . $cliente->getTelefono() . "\n";
            echo "  Email: " . $cliente->getEmail() . "\n";

            echo "Cabaña:\n";
            echo "  Número: " . $cabana->getNumero() . "\n";
            echo "  Capacidad: " . $cabana->getCapacidad() . "\n";
            echo "  Descripción: " . $cabana->getDescripcion() . "\n";
            echo "  Costo Diario: $" . $cabana->getCostoDiario() . "\n";

            echo "Total de días reservados: " . $reserva->calcularDiferenciaDias() . " días\n";
            echo "Costo Total de la reserva: $" . $reserva->calcularCostoTotal() . "\n";
            echo "---------------------------\n";
        }
    }
}

function buscarReservaPorNumero($numero)
{
    global $reservas;

    foreach ($reservas as $reserva) {
        if ($reserva->getNumero() == $numero) {
            return $reserva;
        }
    }

    // Si no se encuentra en memoria, buscar en la base de datos
    $conexion = Conexion::obtenerInstancia(); // Obtener una instancia de la conexión
    $pdo = $conexion->obtenerConexion();

    $stmt = $pdo->prepare("SELECT * FROM reservas WHERE numero_reserva = ?");
    $stmt->execute([$numero]);
    $reservaDesdeBD = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reservaDesdeBD) {
        // Crear una instancia de Reservas desde los datos de la base de datos
        $cliente = buscarClientePorDNI($reservaDesdeBD['dni_cliente']);
        $cabana = buscarCabanaPorNumero($reservaDesdeBD['numero_cabana']);

        $reserva = new Reservas(
            $reservaDesdeBD['numero'],
            $reservaDesdeBD['fecha_inicio'],
            $reservaDesdeBD['fecha_fin'],
            $cliente,
            $cabana
        );

        return $reserva;
    }
    return null;
}
