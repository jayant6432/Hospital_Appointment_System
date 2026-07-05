<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['patient_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "My Appointments";
$pid = $_SESSION['patient_id'];

// Cancel appointment action
if (isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ? AND patient_id = ? AND status = 'Pending'");
    $stmt->execute([$_GET['cancel'], $pid]);
    header("Location: my_appointments.php");
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, d.full_name AS doctor_name, d.specialization FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE a.patient_id = ? ORDER BY a.appointment_date DESC");
$stmt->execute([$pid]);
$appointments = $stmt->fetchAll();

include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">My Appointments</h3>

<div class="card p-3">
  <?php if (count($appointments) === 0): ?>
    <p class="text-muted mb-0">You have no appointments yet. <a href="doctors.php">Book one now</a>.</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($appointments as $a): ?>
        <tr>
          <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
          <td><?php echo htmlspecialchars($a['specialization']); ?></td>
          <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
          <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
          <td><?php echo htmlspecialchars($a['reason']); ?></td>
          <td><span class="badge bg-<?php echo $a['status']=='Confirmed'?'success':($a['status']=='Pending'?'warning':($a['status']=='Completed'?'primary':'secondary')); ?>"><?php echo $a['status']; ?></span></td>
          <td>
            <?php if ($a['status'] === 'Pending'): ?>
              <a href="?cancel=<?php echo $a['appointment_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Cancel this appointment?');">Cancel</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
