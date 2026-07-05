<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['patient_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "Dashboard";
$pid = $_SESSION['patient_id'];

$upcoming = $pdo->prepare("SELECT a.*, d.full_name AS doctor_name, d.specialization FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    WHERE a.patient_id = ? AND a.appointment_date >= CURDATE() AND a.status != 'Cancelled'
    ORDER BY a.appointment_date ASC LIMIT 5");
$upcoming->execute([$pid]);
$upcomingList = $upcoming->fetchAll();

$counts = $pdo->prepare("SELECT status, COUNT(*) c FROM appointments WHERE patient_id = ? GROUP BY status");
$counts->execute([$pid]);
$statusCounts = ['Pending'=>0,'Confirmed'=>0,'Cancelled'=>0,'Completed'=>0];
foreach ($counts->fetchAll() as $row) { $statusCounts[$row['status']] = $row['c']; }

include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">Welcome, <?php echo htmlspecialchars($_SESSION['patient_name']); ?></h3>

<div class="row mb-4">
  <?php foreach ($statusCounts as $status => $count): ?>
    <div class="col-md-3 mb-2">
      <div class="card p-3 text-center">
        <h4 class="fw-bold" style="color:#1F3864;"><?php echo $count; ?></h4>
        <small class="text-muted"><?php echo $status; ?></small>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Upcoming Appointments</h5>
  <a href="doctors.php" class="btn btn-primary btn-sm">+ Book New Appointment</a>
</div>

<div class="card p-3">
  <?php if (count($upcomingList) === 0): ?>
    <p class="text-muted mb-0">No upcoming appointments. <a href="doctors.php">Book one now</a>.</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>Doctor</th><th>Specialization</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($upcomingList as $a): ?>
        <tr>
          <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
          <td><?php echo htmlspecialchars($a['specialization']); ?></td>
          <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
          <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
          <td><span class="badge bg-<?php echo $a['status']=='Confirmed'?'success':($a['status']=='Pending'?'warning':'secondary'); ?>"><?php echo $a['status']; ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
