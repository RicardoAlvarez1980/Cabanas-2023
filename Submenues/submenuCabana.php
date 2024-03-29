<?php

require_once 'Cabanas.php'; 

// Función para cargar cabañas desde la base de datos
function cargarCabanasDesdeBD()
{
    global $cabanas;

    // Limpia el arreglo de cabañas existente
    $cabanas = [];

    // Realiza la consulta para cargar cabañas desde la base de datos
    $conexion = Conexion::obtenerInstancia();
    $pdo = $conexion->obtenerConexion();
    $stmt = $pdo->query("SELECT * FROM cabanas");

    // Recorre los resultados y crea instancias de Cabaña
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cabana = new Cabanas(
            $row['numero'],
            $row['capacidad'],
            $row['descripcion'],
            $row['costo_diario']
        );
        $cabanas[] = $cabana;
    }
}

// Menú de Gestionar Cabañas
function menuCabanas()
{
    $opcionesDisponibles = [
        '1' => 'altaCabana',
        '2' => 'modificarCabana',
        '3' => 'eliminarCabana',
        '4' => 'listarCabanas',
        '0' => 'menuPrincipal',
    ];

    echo "=================================";
    echo "\nMenú Para Gestionar Cabañas\n";
    echo "=================================\n";
    echo "1. Alta de Cabaña\n";
    echo "2. Modificar Cabaña\n";
    echo "3. Eliminar Cabaña\n";
    echo "4. Listar Cabañas\n";
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

// Función para dar de alta una cabaña
function altaCabana()
{
    global $cabanas; // Esto es para mantener la lógica existente

    echo "=======================";
    echo "\nAlta de Cabaña\n";
    echo "=======================\n";

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

    // Aquí, después de agregar la cabaña en memoria, también la insertamos en la base de datos
    $conexion = Conexion::obtenerInstancia(); // Obtenemos una instancia de la conexión
    $pdo = $conexion->obtenerConexion();

    // Preparar la consulta SQL
    $stmt = $pdo->prepare("INSERT INTO cabanas (numero, capacidad, descripcion, costo_diario) VALUES (?, ?, ?, ?)");

    // Ejecutar la consulta con los datos de la cabaña
    $stmt->execute([$numero, $capacidad, $descripcion, $costoDiario]);

    echo "Cabaña agregada exitosamente.\n";
}

// Función para modificar una cabaña
function modificarCabana()
{
    global $cabanas;

    echo "=======================";
    echo "\nModificar Cabaña\n";
    echo "=======================\n";

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
        echo "----------------------------------\n";
        echo "Cabaña Número: " . $cabanaEncontrada->getNumero() . "\n";
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
        // Actualizar la cabaña en la base de datos
        actualizarCabanaEnBaseDeDatos($cabanaEncontrada);

        echo "Cabaña modificada exitosamente.\n";
    } else {
        echo "No se encontró una cabaña con ese número.\n";
    }
}
function actualizarCabanaEnBaseDeDatos($cabana)
{
    $conexion = Conexion::obtenerInstancia(); // Obtener una instancia de la conexión
    $pdo = $conexion->obtenerConexion();

    // Preparar la consulta SQL de actualización
    $stmt = $pdo->prepare("UPDATE cabanas SET capacidad=?, descripcion=?, costo_diario=? WHERE numero=?");

    // Ejecutar la consulta con los nuevos datos de la cabaña
    $stmt->execute([
        $cabana->getCapacidad(),
        $cabana->getDescripcion(),
        $cabana->getCostoDiario(),
        $cabana->getNumero()
    ]);
}
// Función para eliminar una cabaña

function eliminarCabana()
{
    global $cabanas, $reservas;

    echo "=======================";
    echo "\nEliminar Cabaña\n";
    echo "=======================\n";

    // Solicitar número de cabaña a eliminar
    echo "Ingrese el número de cabaña que desea eliminar: ";
    $numero = trim(fgets(STDIN));

    // Buscar la cabaña por su número
    $cabanaEncontrada = buscarCabanaPorNumero($numero);

    if ($cabanaEncontrada) {
        // Verificar si hay reservas asociadas a la cabaña
        $reservasAsociadas = false;
        foreach ($reservas as $reserva) {
            if ($reserva->getCabana()->getNumero() === $numero) {
                $reservasAsociadas = true;
                break;
            }
        }

        if ($reservasAsociadas) {
            echo "No se puede eliminar esta cabaña porque tiene reservas asociadas.\n";
        } else {
            // Eliminar la cabaña de la base de datos
            $conexion = Conexion::obtenerInstancia(); // Obtener una instancia de la conexión
            $pdo = $conexion->obtenerConexion();
            $pdo->beginTransaction();

            try {
                // Preparar la consulta SQL de eliminación de la cabaña
                $stmt = $pdo->prepare("DELETE FROM cabanas WHERE numero=?");

                // Ejecutar la consulta para eliminar la cabaña de la base de datos
                $stmt->execute([$numero]);

                $pdo->commit();
                echo "La cabaña fue eliminada exitosamente.\n";
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo "Error al eliminar la cabaña.\n";
                echo "La cabaña tiene reservas activas.\n";
            }
        }
    } else {
        echo "No se encontró una cabaña con ese número.\n";
    }
}

// Función para listar cabañas
function listarCabanas()
{
    global $cabanas;

    echo "=================================";
    echo "\nListado de Cabañas\n";
    echo "=================================\n";

    // Listar cabañas en memoria
    if (empty($cabanas)) {
        echo "No hay cabañas registradas.\n";
    } else {
        // Ordenar el array de reservas por el número de reserva
        usort($cabanas, function ($a, $b) {
        return $a->getNumero() - $b->getNumero();
        });
        echo "Cabañas registradas:\n";
        echo "-------------------------------\n";
        foreach ($cabanas as $cabana) {
            echo "Número: " . $cabana->getNumero() . "\n";
            echo "Capacidad: " . $cabana->getCapacidad() . "\n";
            echo "Descripción: " . $cabana->getDescripcion() . "\n";
            echo "Costo Diario: $" . $cabana->getCostoDiario() . "\n";
            echo "-------------------------------\n";
        }
    }
}

// Función para buscar una cabaña por número
function buscarCabanaPorNumero($numero)
{
    global $cabanas;

    // Primero, buscar en la memoria
    foreach ($cabanas as $cabana) {
        if ($cabana->getNumero() == $numero) {
            return $cabana;
        }
    }

    // Si no se encuentra en memoria, buscar en la base de datos
    $conexion = Conexion::obtenerInstancia(); // Obtener una instancia de la conexión
    $pdo = $conexion->obtenerConexion();

    $stmt = $pdo->prepare("SELECT * FROM cabanas WHERE numero = ?");
    $stmt->execute([$numero]);
    $cabanaDesdeBD = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cabanaDesdeBD) {
        // Crear una instancia de Cabanas desde los datos de la base de datos
        $cabana = new Cabanas(
            $cabanaDesdeBD['numero'],
            $cabanaDesdeBD['capacidad'],
            $cabanaDesdeBD['descripcion'],
            $cabanaDesdeBD['costo_diario']
        );
        return $cabana;
    }

    return null;
}

?>
