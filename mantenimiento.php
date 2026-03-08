<?php 
include 'db.php'; 
$hotel = $_GET['hotel'] ?? 'General';

// 1. Lógica para INSERTAR O ACTUALIZAR
if (isset($_POST['guardar'])) {
    $fecha = $_POST['fecha'];
    $hab = $_POST['habitacion'];
    $desc = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $tiempo = $_POST['tiempo'];
    $id = $_POST['id_registro']; // Si tiene ID, es edición

    if ($id == "") {
        // Nuevo registro
        $sql = "INSERT INTO incidencias (hotel_name, fecha, habitacion, descripcion, estado, tiempo_estimado) 
                VALUES ('$hotel', '$fecha', '$hab', '$desc', '$estado', '$tiempo')";
    } else {
        // Actualizar existente
        $sql = "UPDATE incidencias SET fecha='$fecha', habitacion='$hab', descripcion='$desc', 
                estado='$estado', tiempo_estimado='$tiempo' WHERE id=$id";
    }
    $conn->query($sql);
    header("Location: mantenimiento.php?hotel=$hotel"); // Recargar para limpiar POST
}

// 2. Lógica para BORRAR
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM incidencias WHERE id=$id");
    header("Location: mantenimiento.php?hotel=$hotel");
}

// 3. Lógica para CAMBIO DE ESTADO RÁPIDO
if (isset($_GET['toggle_status'])) {
    $id = $_GET['toggle_status'];
    $nuevo_estado = $_GET['nuevo_estado'];
    $conn->query("UPDATE incidencias SET estado='$nuevo_estado' WHERE id=$id");
    header("Location: mantenimiento.php?hotel=$hotel");
}

// 4. Lógica para CARGAR DATOS EN EL FORMULARIO (EDITAR)
$edit_data = ['id'=>'', 'fecha'=>'', 'habitacion'=>'', 'descripcion'=>'', 'estado'=>'En proceso', 'tiempo_estimado'=>''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM incidencias WHERE id=$id");
    $edit_data = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-stone-800">Mantenimiento: <?php echo $hotel; ?></h2>
            <p class="text-xs text-stone-400 uppercase tracking-widest">Gestión de Operaciones</p>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-stone-200 mb-10">
            <h3 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-6">
                <?php echo $edit_data['id'] ? 'Editar Incidencia #'.$edit_data['id'] : 'Registrar Nueva Incidencia'; ?>
            </h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <input type="hidden" name="id_registro" value="<?php echo $edit_data['id']; ?>">
                
                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-stone-500 uppercase">Fecha</label>
                    <input type="date" name="fecha" required value="<?php echo $edit_data['fecha']; ?>" class="border-b-2 border-stone-100 p-2 focus:border-stone-900 outline-none transition">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-stone-500 uppercase">Habitación / Área</label>
                    <input type="text" name="habitacion" required value="<?php echo $edit_data['habitacion']; ?>" placeholder="Ej: 204 o Alberca" class="border-b-2 border-stone-100 p-2 focus:border-stone-900 outline-none transition">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-stone-500 uppercase">Tiempo Estimado</label>
                    <input type="text" name="tiempo" value="<?php echo $edit_data['tiempo_estimado']; ?>" placeholder="Ej: 45 min" class="border-b-2 border-stone-100 p-2 focus:border-stone-900 outline-none transition">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-stone-500 uppercase">Estado Inicial</label>
                    <select name="estado" class="border-b-2 border-stone-100 p-2 focus:border-stone-900 outline-none transition bg-white">
                        <option value="En proceso" <?php echo $edit_data['estado'] == 'En proceso' ? 'selected' : ''; ?>>En proceso</option>
                        <option value="Resuelto" <?php echo $edit_data['estado'] == 'Resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex flex-col gap-2">
                    <label class="text-[10px] font-bold text-stone-500 uppercase">Descripción de la Incidencia</label>
                    <textarea name="descripcion" required class="border-b-2 border-stone-100 p-2 focus:border-stone-900 outline-none transition h-12" placeholder="Detalle técnico del problema..."><?php echo $edit_data['descripcion']; ?></textarea>
                </div>

                <div class="flex items-end">
                    <button name="guardar" class="w-full bg-stone-900 text-white py-3 rounded-lg font-bold text-xs uppercase tracking-widest hover:bg-stone-800 transition shadow-lg">
                        <?php echo $edit_data['id'] ? 'Guardar Cambios' : 'Registrar'; ?>
                    </button>
                </div>
            </form>
            <?php if($edit_data['id']): ?>
                <a href="mantenimiento.php?hotel=<?php echo $hotel; ?>" class="text-[10px] text-red-500 mt-4 block text-center uppercase font-bold tracking-widest">Cancelar Edición</a>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-[10px] uppercase tracking-[0.2em] text-stone-400">
                        <th class="p-6">Status</th>
                        <th class="p-6">Info Habitación</th>
                        <th class="p-6">Descripción</th>
                        <th class="p-6">Tiempo</th>
                        <th class="p-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php
                    $result = $conn->query("SELECT * FROM incidencias WHERE hotel_name='$hotel' ORDER BY id DESC");
                    while($row = $result->fetch_assoc()):
                        $is_resolved = $row['estado'] == 'Resuelto';
                    ?>
                    <tr class="border-b border-stone-50 hover:bg-stone-50 transition">
                        <td class="p-6">
                            <a href="?hotel=<?php echo $hotel; ?>&toggle_status=<?php echo $row['id']; ?>&nuevo_estado=<?php echo $is_resolved ? 'En proceso' : 'Resuelto'; ?>" 
                               class="flex items-center gap-2 group">
                                <span class="w-3 h-3 rounded-full <?php echo $is_resolved ? 'bg-green-500' : 'bg-yellow-500 animate-pulse'; ?>"></span>
                                <span class="text-[10px] font-bold uppercase <?php echo $is_resolved ? 'text-green-600' : 'text-yellow-600'; ?>">
                                    <?php echo $row['estado']; ?>
                                </span>
                            </a>
                        </td>
                        <td class="p-6 italic text-stone-400">
                            <span class="text-stone-900 font-bold not-italic">#<?php echo $row['habitacion']; ?></span><br>
                            <?php echo date('d M', strtotime($row['fecha'])); ?>
                        </td>
                        <td class="p-6 text-stone-600 max-w-xs truncate"><?php echo $row['descripcion']; ?></td>
                        <td class="p-6 font-mono text-xs"><?php echo $row['tiempo_estimado'] ?: '---'; ?></td>
                        <td class="p-6">
                            <div class="flex justify-center gap-4">
                                <a href="?hotel=<?php echo $hotel; ?>&edit=<?php echo $row['id']; ?>" class="text-stone-400 hover:text-stone-900 transition"><i class="fas fa-edit"></i></a>
                                <a href="?hotel=<?php echo $hotel; ?>&delete=<?php echo $row['id']; ?>" onclick="return confirm('¿Borrar registro?')" class="text-stone-300 hover:text-red-500 transition"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>