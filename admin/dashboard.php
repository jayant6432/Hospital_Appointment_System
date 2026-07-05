<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "Admin Dashboard";

$totalPatients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$totalDoctors = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pendingAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn();

$byDept = $pdo->query("SELECT dept.department_name, COUNT(a.appointment_id) cnt
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.doctor_id
    JOIN departments dept ON d.department_id = dept.department_id
    GROUP BY dept.department_name")->fetchAll();

$recent = $pdo->query("SELECT a.*, p.full_name AS patient_name, d.full_name AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    JOIN doctors d ON a.doctor_id = d.doctor_id
    ORDER BY a.created_at DESC LIMIT 8")->fetchAll();

include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">Admin Dashboard</h3>

<div class="row mb-4">
  <div class="col-md-3 mb-2"><div class="card p-3 text-center"><h4 class="fw-bold" style="color:#1F3864;"><?php echo $totalPatients; ?></h4><small class="text-muted">Total Patients</small></div></div>
  <div class="col-md-3 mb-2"><div class="card p-3 text-center"><h4 class="fw-bold" style="color:#1F3864;"><?php echo $totalDoctors; ?></h4><small class="text-muted">Total Doctors</small></div></div>
  <div class="col-md-3 mb-2"><div class="card p-3 text-center"><h4 class="fw-bold" style="color:#1F3864;"><?php echo $totalAppointments; ?></h4><small class="text-muted">Total Appointments</small></div></div>
  <div class="col-md-3 mb-2"><div class="card p-3 text-center"><h4 class="fw-bold" style="color:#B08D57;"><?php echo $pendingAppointments; ?></h4><small class="text-muted">Pending Approval</small></div></div>
</div>

<div class="d-flex gap-2 mb-4">
  <a href="manage_doctors.php" class="btn btn-primary btn-sm">Manage Doctors</a>
  <a href="manage_departments.php" class="btn btn-primary btn-sm">Manage Departments</a>
  <a href="all_appointments.php" class="btn btn-primary btn-sm">View All Appointments</a>
</div>

<div class="row">
  <div class="col-md-5 mb-3">
    <div class="card p-3">
      <h6>Appointments by Department</h6>
      <table class="table table-sm mb-0">
        <?php foreach ($byDept as $row): ?>
          <tr><td><?php echo htmlspecialchars($row['department_name']); ?></td><td class="text-end"><?php echo $row['cnt']; ?></td></tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
  <div class="col-md-7 mb-3">
    <div class="card p-3">
      <h6>Recent Appointments</h6>
      <table class="table table-sm mb-0">
        <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['patient_name']); ?></td>
            <td><?php echo htmlspecialchars($r['doctor_name']); ?></td>
            <td><?php echo htmlspecialchars($r['appointment_date']); ?></td>
            <td><span class="badge bg-<?php echo $r['status']=='Confirmed'?'success':($r['status']=='Pending'?'warning':($r['status']=='Completed'?'primary':'secondary')); ?>"><?php echo $r['status']; ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
