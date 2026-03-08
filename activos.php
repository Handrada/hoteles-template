<?php 
include 'db.php'; 
$hotel = $_GET['hotel'] ?? 'General';

if (isset($_POST['add_activo'])) {
    $nombre = $_POST['nombre'];
    $area = $_POST['area'];
    $estado = $_POST['estado'];
    $manto = $_POST['manto'];
    $conn->query("INSERT INTO activos (hotel_name, nombre_activo, habitacion_area, estado_actual, ultima_mantenimiento) 
                  VALUES ('$hotel', '$nombre', '$area', '$estado', '$manto')");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <h2 class="text-2xl font-bold mb-8 text-stone-800 italic uppercase">Inventario de Activos: <?php echo $hotel; ?></h2>

        <form method="POST" class="bg-white p-6 rounded-2xl shadow-sm border mb-10 grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="nombre" placeholder="Equipo (Ej: Aire AC)" required class="border p-2 rounded text-sm">
            <input type="text" name="area" placeholder="Ubicación (Ej: Hab 102)" class="border p-2 rounded text-sm">
            <select name="estado" class="border p-2 rounded text-sm">
                <option value="Excelente">Excelente</option>
                <option value="Regular">Regular</option>
                <option value="Requiere Cambio">Requiere Cambio</option>
            </select>
            <input type="date" name="manto" class="border p-2 rounded text-sm">
            <button name="add_activo" class="bg-stone-900 text-white rounded text-[10px] font-bold uppercase tracking-widest hover:bg-stone-700 transition">Registrar Activo</button>
        </form>

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-stone-50 text-[10px] uppercase tracking-[0.2em] text-stone-400">
                    <tr>
                        <th class="p-5">Activo / Equipo</th>
                        <th class="p-5">Ubicación</th>
                        <th class="p-5">Estado Salud</th>
                        <th class="p-5">Último Manto.</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php
                    $activos = $conn->query("SELECT * FROM activos WHERE hotel_name='$hotel'");
                    while($a = $activos->fetch_assoc()):
                        $status_color = $a['estado_actual'] == 'Excelente' ? 'text-green-600' : ($a['estado_actual'] == 'Regular' ? 'text-yellow-600' : 'text-red-600 font-black');
                    ?>
                    <tr class="border-t border-stone-50 hover:bg-stone-50 transition">
                        <td class="p-5 font-bold text-stone-800 uppercase"><?php echo $a['nombre_activo']; ?></td>
                        <td class="p-5 italic text-stone-400"><?php echo $a['habitacion_area']; ?></td>
                        <td class="p-5 font-bold <?php echo $status_color; ?>"><?php echo $a['estado_actual']; ?></td>
                        <td class="p-5 font-mono text-xs text-stone-500"><?php echo $a['ultima_mantenimiento']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>