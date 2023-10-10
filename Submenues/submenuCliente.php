<?php

require_once 'Clientes.php';

// Menú de Gestionar Clientes
function menuClientes()
{
    echo "\nMenú de Gestionar Clientes\n";
    echo "1. Alta de Cliente\n";
    echo "2. Modificar Cliente\n";
    echo "3. Eliminar Cliente\n";
    echo "4. Listar Clientes\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    switch ($opcion) {
        case 1:
            altaCliente();
            menuClientes();
            break;
        case 2:
            modificarCliente();
            menuClientes();
            break;
        case 3:
            eliminarCliente();
            menuClientes();
            break;
        case 4:
            listarClientes();
            menuClientes();
            break;
        case 0:
            menuPrincipal();
            break;
        default:
            echo "Opción inválida. Intente nuevamente.\n";
            menuClientes();
            break;
    }
}

// Función para dar de alta un cliente
function altaCliente()
{
    global $clientes;

    echo "\nAlta de Cliente\n";

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

    echo "Cliente agregado exitosamente.\n";
}

// Función para modificar un cliente
function modificarCliente()
{
    global $clientes;

    echo "\nModificar Cliente\n";

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

        // Actualizar los campos del cliente si se ingresan nuevos valores
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

        echo "Cliente modificado exitosamente.\n";
    } else {
        echo "No se encontró un cliente con ese DNI.\n";
    }
}


// Función para eliminar un cliente
function eliminarCliente()
{
    global $clientes;

    echo "\nEliminar Cliente\n";

    // Solicitar DNI del cliente a eliminar
    echo "Ingrese el DNI del cliente que desea eliminar: ";
    $dni = trim(fgets(STDIN));

    // Buscar el cliente por su DNI
    $clienteEncontrado = buscarClientePorDNI($dni);

    if ($clienteEncontrado) {
        // Mostrar la información completa del cliente
        echo "Información del Cliente:\n";
        echo "DNI: " . $clienteEncontrado->getDni() . "\n";
        echo "Nombre: " . $clienteEncontrado->getNombre() . "\n";
        echo "Dirección: " . $clienteEncontrado->getDireccion() . "\n";
        echo "Teléfono: " . $clienteEncontrado->getTelefono() . "\n";
        echo "Email: " . $clienteEncontrado->getEmail() . "\n";

        // Confirmar eliminación
        echo "¿Está seguro de que desea eliminar este cliente? (S/N): ";
        $opcion = strtoupper(trim(fgets(STDIN)));

        if ($opcion === 'S') {
            // Eliminar el cliente de la lista
            $key = array_search($clienteEncontrado, $clientes);
            if ($key !== false) {
                unset($clientes[$key]);
                echo "El cliente fue eliminado exitosamente.\n";
            } else {
                echo "No se pudo eliminar el cliente.\n";
            }
        } else {
            echo "La eliminación ha sido cancelada.\n";
        }
    } else {
        echo "No se encontró un cliente con ese DNI.\n";
    }
}

// Función para listar clientes
function listarClientes()
{
    global $clientes;

    echo "\nListado de Clientes\n";

    if (empty($clientes)) {
        echo "No hay clientes registrados en el sistema.\n";
    } else {
        foreach ($clientes as $cliente) {
            echo "DNI: " . $cliente->getDni() . "\n";
            echo "Nombre: " . $cliente->getNombre() . "\n";
            echo "Dirección: " . $cliente->getDireccion() . "\n";
            echo "Teléfono: " . $cliente->getTelefono() . "\n";
            echo "Email: " . $cliente->getEmail() . "\n";
            echo "---------------------------\n";
        }
    }
}


// Función para buscar un cliente por DNI
function buscarClientePorDNI($dni)
{
    global $clientes;

    foreach ($clientes as $cliente) {
        if ($cliente->getDNI() == $dni) {
            return $cliente;
        }
    }
    return null;
}
