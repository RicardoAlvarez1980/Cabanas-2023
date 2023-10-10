<?php

require_once 'Cabanas.php'; 

// Menú de Gestionar Cabañas
function menuCabanas()
{
    echo "\nMenú de Gestionar Cabañas\n";
    echo "1. Alta de Cabaña\n";
    echo "2. Modificar Cabaña\n";
    echo "3. Eliminar Cabaña\n";
    echo "4. Listar Cabañas\n";
    echo "0. Volver al Menú Principal\n";

    $opcion = leerOpcion("Seleccione una opción: ");

    switch ($opcion) {
        case 1:
            altaCabana();
            menuCabanas();
            break;
        case 2:
            modificarCabana();
            menuCabanas();
            break;
        case 3:
            eliminarCabana();
            menuCabanas();
            break;
        case 4:
            listarCabanas();
            menuCabanas();
            break;
        case 0:
            menuPrincipal();
            break;
        default:
            echo "Opción inválida. Intente nuevamente.\n";
            menuCabanas();
            break;
    }
}

// Función para dar de alta una cabaña
function altaCabana()
{
    global $cabanas;

    echo "\nAlta de Cabaña\n";

    while (true) {
        // Solicitar datos de la cabaña al usuario
        echo "Ingrese el número de cabaña: ";
        $numero = trim(fgets(STDIN));

        // Validar que el número de cabaña no esté duplicado
        $cabanaExistente = buscarCabanaPorNumero($numero);
        if ($cabanaExistente) {
            echo "Ya existe una cabaña con ese número. ¿Desea intentar nuevamente? (S/N): ";
            $opcion = strtoupper(trim(fgets(STDIN)));
            if ($opcion !== 'S') {
                // Devolver al menú principal
                return;
            }
        } else {
            break; // Continuar si el número de cabaña es único
        }
    }

    echo "Ingrese la capacidad de la cabaña: ";
    $capacidad = intval(trim(fgets(STDIN)));
    echo "Ingrese la descripción de la cabaña: ";
    $descripcion = trim(fgets(STDIN));
    echo "Ingrese el costo diario de la cabaña: ";
    $costoDiario = floatval(trim(fgets(STDIN)));

    // Crear una nueva instancia de Cabanas
    $cabana = new Cabanas($numero, $capacidad, $descripcion, $costoDiario);
    $cabanas[] = $cabana;

    echo "La cabaña nº " . $cabana->getNumero() . " fue agregada exitosamente.\n";
}
// Función para modificar una cabaña
function modificarCabana()
{
    global $cabanas;

    echo "\nModificar Cabaña\n";

    // Solicitar número de cabaña a modificar
    echo "Ingrese el número de cabaña que desea modificar (o deje en blanco para volver al Menú Principal): ";
    $numero = trim(fgets(STDIN));

    if (empty($numero)) {
        return; // Volver al Menú Principal si se ingresa un número en blanco
    }

    // Buscar la cabaña por su número
    $cabanaEncontrada = buscarCabanaPorNumero($numero);

    if ($cabanaEncontrada) {
        // Mostrar la información actual de la cabaña
        echo "Información actual de la Cabaña:\n";
        echo "Número: " . $cabanaEncontrada->getNumero() . "\n";
        echo "Capacidad: " . $cabanaEncontrada->getCapacidad() . "\n";
        echo "Descripción: " . $cabanaEncontrada->getDescripcion() . "\n";
        echo "Costo Diario: $" . $cabanaEncontrada->getCostoDiario() . "\n";

        // Solicitar los nuevos datos al usuario
        echo "Ingrese la nueva capacidad de la cabaña (deje en blanco para mantener el valor actual): ";
        $nuevaCapacidad = trim(fgets(STDIN));
        echo "Ingrese la nueva descripción de la cabaña (deje en blanco para mantener el valor actual): ";
        $nuevaDescripcion = trim(fgets(STDIN));
        echo "Ingrese el nuevo costo diario de la cabaña (deje en blanco para mantener el valor actual): ";
        $nuevoCostoDiario = trim(fgets(STDIN));

        // Actualizar los campos de la cabaña si se ingresan nuevos valores
        if (!empty($nuevaCapacidad)) {
            $cabanaEncontrada->setCapacidad(intval($nuevaCapacidad));
        }
        if (!empty($nuevaDescripcion)) {
            $cabanaEncontrada->setDescripcion($nuevaDescripcion);
        }
        if (!empty($nuevoCostoDiario)) {
            $cabanaEncontrada->setCostoDiario(floatval($nuevoCostoDiario));
        }

        echo "Cabaña modificada exitosamente.\n";
    } else {
        echo "No se encontró una cabaña con ese número.\n";
    }
}

// Función para eliminar una cabaña
function eliminarCabana()
{
    global $cabanas;

    echo "\nEliminar Cabaña\n";

    // Solicitar número de cabaña a eliminar
    echo "Ingrese el número de cabaña que desea eliminar: ";
    $numero = trim(fgets(STDIN));

    // Buscar la cabaña por su número
    $cabanaEncontrada = buscarCabanaPorNumero($numero);

    if ($cabanaEncontrada) {
        // Mostrar la información completa de la cabaña
        echo "Información de la Cabaña:\n";
        echo "Número: " . $cabanaEncontrada->getNumero() . "\n";
        echo "Capacidad: " . $cabanaEncontrada->getCapacidad() . "\n";
        echo "Descripción: " . $cabanaEncontrada->getDescripcion() . "\n";
        echo "Costo Diario: $" . $cabanaEncontrada->getCostoDiario() . "\n";

        // Confirmar eliminación
        echo "¿Está seguro de que desea eliminar esta cabaña? (S/N): ";
        $opcion = strtoupper(trim(fgets(STDIN)));

        if ($opcion === 'S') {
            // Eliminar la cabaña de la lista
            $key = array_search($cabanaEncontrada, $cabanas);
            if ($key !== false) {
                unset($cabanas[$key]);
                echo "La cabaña fue eliminada exitosamente.\n";
            } else {
                echo "No se pudo eliminar la cabaña.\n";
            }
        } else {
            echo "La eliminación ha sido cancelada.\n";
        }
    } else {
        echo "No se encontró una cabaña con ese número.\n";
    }
}

// Función para listar cabañas
function listarCabanas()
{
    global $cabanas;

    echo "\nListado de Cabañas\n";

    if (empty($cabanas)) {
        echo "No hay cabañas registradas en el sistema.\n";
    } else {
        foreach ($cabanas as $cabana) {
            echo "Número: " . $cabana->getNumero() . "\n";
            echo "Capacidad: " . $cabana->getCapacidad() . "\n";
            echo "Descripción: " . $cabana->getDescripcion() . "\n";
            echo "Costo Diario: $" . $cabana->getCostoDiario() . "\n";
            echo "---------------------------\n";
        }
    }
}
// Función para buscar una cabaña por número
function buscarCabanaPorNumero($numero)
{
    global $cabanas;

    foreach ($cabanas as $cabana) {
        if ($cabana->getNumero() == $numero) {
            return $cabana;
        }
    }
    return null;
}
?>
