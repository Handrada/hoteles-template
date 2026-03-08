<?php 
include 'db.php'; 
$hotel = $_GET['hotel'] ?? 'General';

// Lógica para registrar nuevo pago/vencimiento
if (isset($_POST['nuevo_pago'])) {
    $concepto = $_POST['concepto'];
    $monto = $_POST['monto'];
    $vence = $_POST['fecha_vencimiento'];
    $cat = $_POST['categoria'];
    
    $conn->query("INSERT INTO cumplimientos (hotel_name, concepto, monto, fecha_vencimiento, categoria) 
                  VALUES ('$hotel', '$concepto', '$monto', '$vence', '$cat')");
}

// Marcar como pagado
if (isset($_GET['pagar'])) {
    $id = $_GET['pagar'];
    $conn->query("UPDATE cumplimientos SET pagado = 1 WHERE id = $id");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold mb-8 text-stone-800 italic uppercase italic">Control de Vencimientos: <?php echo $hotel; ?></h2>

        <form method="POST" class="bg-white p-6 rounded-xl shadow-sm border mb-10 grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="concepto" placeholder="Ej: Hosting Vercel" required class="border p-2 rounded text-sm">
            <input type="number" step="0.01" name="monto" placeholder="Monto $" required class="border p-2 rounded text-sm">
            <input type="date" name="fecha_vencimiento" required class="border p-2 rounded text-sm">
            <button name="nuevo_pago" class="bg-stone-900 text-white rounded text-xs font-bold uppercase tracking-widest hover:bg-stone-700 transition">Agendar Pago</button>
        </form>

        <div class="space-y-4">
            <?php
            $query = "SELECT *, DATEDIFF(fecha_vencimiento, CURDATE()) as dias FROM cumplimientos WHERE hotel_name='$hotel' AND pagado=0 ORDER BY fecha_vencimiento ASC";
            $res = $conn->query($query);
            while($p = $res->fetch_assoc()):
                // Lógica de semáforo
                $status_class = "border-green-200 bg-green-50 text-green-700";
                if($p['dias'] <= 5) $status_class = "border-red-200 bg-red-50 text-red-700 animate-pulse";
                elseif($p['dias'] <= 15) $status_class = "border-yellow-200 bg-yellow-50 text-yellow-700";
            ?>
            <div class="flex items-center justify-between p-5 rounded-2xl border-2 <?php echo $status_class; ?> shadow-sm">
                <div class="flex items-center gap-5">
                    <div class="text-2xl"><i class="fas fa-clock-rotate-left"></i></div>
                    <div>
                        <h4 class="font-bold text-base uppercase"><?php echo $p['concepto']; ?></h4>
                        <p class="text-[10px] font-bold tracking-widest uppercase opacity-70">Vence en <?php echo $p['dias']; ?> días (<?php echo $p['fecha_vencimiento']; ?>)</p>
                    </div>
                </div>
                <div class="flex items-center gap-8">
                    <span class="text-xl font-black font-mono">$<?php echo number_format($p['monto'], 2); ?></span>
                    <a href="?hotel=<?php echo $hotel; ?>&pagar=<?php echo $p['id']; ?>" class="bg-white px-4 py-2 rounded-lg text-[10px] font-bold shadow-sm hover:shadow-md transition">MARCAR PAGADO</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>