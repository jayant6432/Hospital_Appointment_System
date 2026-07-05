<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "All Appointments";

$filter = $_GET['status'] ?? '';
$sql = "SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name, dept.department_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN doctors d ON a.doctor_id = d.doctor_id
        LEFT JOIN departments dept ON d.department_id = dept.department_id";
$params = [];
if ($filter) { $sql .= " WHERE a.status = ?"; $params[] = $filter; }
$sql .= " ORDER BY a.appointment_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">All Appointments</h3>

<div class="mb-3">
  <a href="?" class="btn btn-sm btn-outline-secondary <?php echo !$filter?'active':''; ?>">All</a>
  <a href="?status=Pending" class="btn btn-sm btn-outline-warning">Pending</a>
  <a href="?status=Confirmed" class="btn btn-sm btn-outline-success">Confirmed</a>
  <a href="?status=Completed" class="btn btn-sm btn-outline-primary">Completed</a>
  <a href="?status=Cancelled" class="btn btn-sm btn-outline-secondary">Cancelled</a>
</div>

<div class="card p-3">
  <table class="table table-sm">
    <thead><tr><th>Patient</th><th>Doctor</th><th>Department</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($appointments as $a): ?>
      <tr>
        <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
        <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
        <td><?php echo htmlspecialchars($a['department_name']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
        <td><span class="badge bg-<?php echo $a['status']=='Confirmed'?'success':($a['status']=='Pending'?'warning':($a['status']=='Completed'?'primary':'secondary')); ?>"><?php echo $a['status']; ?></span></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include '../includes/footer.php'; ?>
