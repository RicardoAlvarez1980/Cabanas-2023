<?php

require_once 'Clientes.php';
require_once 'Reservas.php';


function cargarClientesDesdeBD()
{
    global $clientes;

    // Limpia el arreglo de clientes existente
    $clientes = [];

    // Realiza la consulta para cargar clientes desde la base de datos
    $conexion = Conexion::obtenerInstancia();
    $pdo = $conexion->obtenerConexion();
    $stmt = $pdo->query("SELECT * FROM clientes");

    // Recorre los resultados y crea instancias de Cliente
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cliente = new Clientes(
            $row['dni'],
            $row['nombre'],
            $row['direccion'],
            $row['telefono'],
            $row['email']
        );
        $clientes[] = $cliente;
    }
}

//Funcion que ejecuta el menú clientes.
function menuClientes()
{
    $opcionesDisponibles = [
        '1' => 'altaCliente',
        '2' => 'modificarCliente',
        '3' => 'eliminarCliente',
        '4' => 'listarClientes',
        '0' => 'menuPrincipal',
    ];

    echo "=================================";
    echo "\nMenú de Gestionar Clientes\n";
    echo "=================================\n";
    echo "1. Alta de Cliente\n";
    echo "2. Modificar Cliente\n";
    echo "3. Eliminar Cliente\n";
    echo "4. Listar Clientes\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    if (isset($opcionesDisponibles[$opcion])) {
        $funcion = $opcionesDisponibles[$opcion];
        call_user_func($funcion);
    } else {
        echo "Opción inválida. Intente nuevamente.\n";
        menuClientes();
    }
}

// Función para dar de alta un cliente
function altaCliente()
{
    global $clientes;

    echo "=======================";
    echo "\nAlta de Cliente\n";
    echo "=======================\n";

    while (true) {
        // Solicitar datos del cliente al usuario
        echo "Ingrese el DNI del cliente: ";
        $dni = trim(fgets(STDIN));

        // Validar que el DNI del cliente no esté duplicado
        $clienteExistente = buscarClientePorDNI($dni);
        if ($clienteExistente) {
            echo "Ya existe un cliente con ese DNI. ¿Desea intentar nuevamente? (S/N): ";
            $opcion = strtoupper(trim(fgets(STDIN)));
            if ($opcion !== 'S') {
                // Devolver al menú principal
                return;
            }
        } else {
            break; // Continuar si el DNI del cliente es único
        }
    }

    echo "Ingrese el nombre del cliente: ";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese la dirección del cliente: ";
    $direccion = trim(fgets(STDIN));
    echo "Ingrese el teléfono del cliente: ";
    $telefono = trim(fgets(STDIN));
    echo "Ingrese el email del cliente: ";
    $email = trim(fgets(STDIN));

    // Crear una nueva instancia de Clientes
    $cliente = new Clientes($dni, $nombre, $direccion, $telefono, $email);
    $clientes[] = $cliente;

    echo "Cliente agregado exitosamente en memoria.\n";

    // Aquí, después de agregar el cliente en memoria, también lo insertamos en la base de datos
    $conexion = Conexion::obtenerInstancia(); // Obtenemos una instancia de la conexión
    $pdo = $conexion->obtenerConexion();

    // Preparar la consulta SQL
    $stmt = $pdo->prepare("INSERT INTO clientes (dni, nombre, direccion, telefono, email) VALUES (?, ?, ?, ?, ?)");

    // Ejecutar la consulta con los datos del cliente
    $stmt->execute([$dni, $nombre, $direccion, $telefono, $email]);
    echo "Cliente agregado exitosamente en la base de datos.\n";
}
// Función para modificar un cliente
function modificarCliente()
{
    global $clientes;
    echo "=======================";
    echo "\nModificar Cliente\n";
    echo "=======================\n";

    // Solicitar DNI del cliente a modificar
    echo "Ingrese el DNI del cliente que desea modificar (o deje en blanco para volver al Menú Principal): ";
    $dni = trim(fgets(STDIN));

    if (empty($dni)) {
        return; // Volver al Menú Principal si se ingresa un DNI en blanco
    }

    // Buscar el cliente por su DNI
    $clienteEncontrado = buscarClientePorDNI($dni);

    if ($clienteEncontrado) {
        // Mostrar la información actual del cliente
        echo "Información actual del Cliente:\n";
        echo "DNI: " . $clienteEncontrado->getDni() . "\n";
        echo "Nombre: " . $clienteEncontrado->getNombre() . "\n";
        echo "Dirección: " . $clienteEncontrado->getDireccion() . "\n";
        echo "Teléfono: " . $clienteEncontrado->getTelefono() . "\n";
        echo "Email: " . $clienteEncontrado->getEmail() . "\n";

        // Solicitar los nuevos datos al usuario
        echo "Ingrese el nuevo nombre del cliente (deje en blanco para mantener el valor actual): ";
        $nuevoNombre = trim(fgets(STDIN));
        echo "Ingrese la nueva dirección del cliente (deje en blanco para mantener el valor actual): ";
        $nuevaDireccion = trim(fgets(STDIN));
        echo "Ingrese el nuevo teléfono del cliente (deje en blanco para mantener el valor actual): ";
        $nuevoTelefono = trim(fgets(STDIN));
        echo "Ingrese el nuevo email del cliente (deje en blanco para mantener el valor actual): ";
        $nuevoEmail = trim(fgets(STDIN));

        // Actualizar los campos del cliente en memoria si se ingresan nuevos valores
        if (!empty($nuevoNombre)) {
            $clienteEncontrado->setNombre($nuevoNombre);
        }
        if (!empty($nuevaDireccion)) {
            $clienteEncontrado->setDireccion($nuevaDireccion);
        }
        if (!empty($nuevoTelefono)) {
            $clienteEncontrado->setTelefono($nuevoTelefono);
        }
        if (!empty($nuevoEmail)) {
            $clienteEncontrado->setEmail($nuevoEmail);
        }

        echo "Cliente modificado exitosamente en memoria.\n";

        // Aquí, después de modificar el cliente en memoria, también actualizamos los datos en la base de datos
        $conexion = Conexion::obtenerInstancia(); // Obtenemos una instancia de la conexión
        $pdo = $conexion->obtenerConexion();

        // Preparar la consulta SQL de actualización
        $stmt = $pdo->prepare("UPDATE clientes SET nombre=?, direccion=?, telefono=?, email=? WHERE dni=?");

        // Ejecutar la consulta con los nuevos datos del cliente o mantener los valores actuales si están en blanco
        $stmt->execute([
            !empty($nuevoNombre) ? $nuevoNombre : $clienteEncontrado->getNombre(),
            !empty($nuevaDireccion) ? $nuevaDireccion : $clienteEncontrado->getDireccion(),
            !empty($nuevoTelefono) ? $nuevoTelefono : $clienteEncontrado->getTelefono(),
            !empty($nuevoEmail) ? $nuevoEmail : $clienteEncontrado->getEmail(),
            $dni
        ]);

        echo "Cliente modificado exitosamente en la base de datos.\n";
    } else {
        echo "No se encontró un cliente con ese DNI.\n";
    }
}
// Función para eliminar un cliente
function eliminarCliente()
{
    global $clientes, $reservas;
    echo "=======================";
    echo "\nEliminar Cliente\n";
    echo "=======================\n";

    // Solicitar DNI del cliente a eliminar
    echo "Ingrese el DNI del cliente que desea eliminar: ";
    $dni = trim(fgets(STDIN));

    // Buscar el cliente por su DNI
    $clienteEncontrado = buscarClientePorDNI($dni);

    if ($clienteEncontrado) {
        // Verificar si hay reservas asociadas al cliente
        $reservasAsociadas = false;
        foreach ($reservas as $reserva) {
            if ($reserva->getCliente()->getDni() === $dni) {
                $reservasAsociadas = true;
                break;
            }
        }

        if ($reservasAsociadas) {
            echo "No se puede eliminar este cliente porque tiene reservas asociadas.\n";
        } else {
            // Eliminar la cabaña de la lista en memoria
            $key = array_search($clienteEncontrado, $clientes);
            if ($key !== false) {
                unset($clientes[$key]);
            } else {
                echo "No se pudo eliminar el cliente en memoria.\n";
            }
            // Eliminar el cliente de la base de datos
            $conexion = Conexion::obtenerInstancia(); // Obtenemos una instancia de la conexión
            $pdo = $conexion->obtenerConexion();
            $pdo->beginTransaction();

            try {
                // Eliminar todas las reservas asociadas al cliente
                $stmt = $pdo->prepare("DELETE FROM reservas WHERE dni_cliente=?");
                $stmt->execute([$dni]);

                // Eliminar el cliente de la tabla de clientes
                $stmt = $pdo->prepare("DELETE FROM clientes WHERE dni=?");
                $stmt->execute([$dni]);

                $pdo->commit();
                echo "El cliente fue eliminado exitosamente.\n";
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo "Error al eliminar el cliente: " . $e->getMessage() . "\n";
                echo "El cliente tiene reservas activas.\n";
            }
        }
    } else {
        echo "No se encontró un cliente con ese DNI.\n";
    }
}




function listarClientes()
{
    global $clientes;
    echo "=================================";
    echo "\nListado de Clientes\n";
    echo "=================================";

    // Listar clientes en memoria
    if (empty($clientes)) {
        echo "No hay clientes registrados en la memoria.\n";
    } else {
        echo "\nClientes registrados:\n";
        echo "------------------------------\n";
        foreach ($clientes as $cliente) {
            echo "DNI: " . $cliente->getDni() . "\n";
            echo "Nombre: " . $cliente->getNombre() . "\n";
            echo "Dirección: " . $cliente->getDireccion() . "\n";
            echo "Teléfono: " . $cliente->getTelefono() . "\n";
            echo "Email: " . $cliente->getEmail() . "\n";
            echo "------------------------------\n";
        }
    }
}

// Función para buscar un cliente por DNI
function buscarClientePorDNI($dni)
{
    global $clientes;

    // Buscar cliente en memoria
    foreach ($clientes as $cliente) {
        if ($cliente->getDNI() == $dni) {
            return $cliente;
        }
    }

    // Buscar cliente en la base de datos
    $conexion = Conexion::obtenerInstancia(); // Obtener una instancia de la conexión
    $pdo = $conexion->obtenerConexion();

    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE dni = :dni");
    $stmt->bindParam(':dni', $dni);
    $stmt->execute();

    $clienteDesdeBD = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($clienteDesdeBD) {
        // Crear una instancia de Cliente desde los datos de la base de datos
        $cliente = new Clientes(
            $clienteDesdeBD['dni'],
            $clienteDesdeBD['nombre'],
            $clienteDesdeBD['direccion'],
            $clienteDesdeBD['telefono'],
            $clienteDesdeBD['email']
        );
        return $cliente;
    }
    return null;
}
// Función para buscar clientes por nombre o parte del nombre
function buscarClientesPorNombre()
{
    echo "==================================";
    echo "\nBúsqueda de Clientes por Nombre\n";
    echo "==================================\n";
    echo "Ingrese el nombre o parte del nombre a buscar: \n";
    $nombre = trim(fgets(STDIN));

    echo "=====================================";
    echo "\nClientes encontrados:\n";
    echo "=====================================\n";

    global $clientes;

    $resultadosMemoria = [];

    if (empty($clientes)) {
        echo "No hay clientes.\n";
    } else {
        foreach ($clientes as $cliente) {
            // Realizar la búsqueda en la memoria
            if (stripos($cliente->getNombre(), $nombre) !== false) {
                $resultadosMemoria[] = $cliente;
            }
        }

        if (empty($resultadosMemoria)) {
            echo "No se encontraron clientes que coincidan con la búsqueda.\n";
        } else {
            foreach ($resultadosMemoria as $cliente) {
                echo "DNI: " . $cliente->getDni() . "\n";
                echo "Nombre: " . $cliente->getNombre() . "\n";
                echo "Dirección: " . $cliente->getDireccion() . "\n";
                echo "Teléfono: " . $cliente->getTelefono() . "\n";
                echo "Email: " . $cliente->getEmail() . "\n";
                echo "---------------------------\n";
            }
        }
    }
}
