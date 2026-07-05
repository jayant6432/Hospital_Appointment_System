<?php
require_once 'config/db.php';
$base = '';
$page_title = "Home";
include 'includes/header.php';

$depts = $pdo->query("SELECT * FROM departments")->fetchAll();
$doctorCount = $pdo->query("SELECT COUNT(*) FROM doctors WHERE status='Active'")->fetchColumn();
?>

<div class="hero mb-5">
  <h1 class="fw-bold">Book Your Hospital Appointment Online</h1>
  <p class="lead">Skip the queue. Find the right doctor and book a time slot in minutes.</p>
  <?php if (!isset($_SESSION['patient_id'])): ?>
    <a href="patient/register.php" class="btn btn-light btn-lg fw-bold">Get Started</a>
  <?php else: ?>
    <a href="patient/doctors.php" class="btn btn-light btn-lg fw-bold">Find a Doctor</a>
  <?php endif; ?>
</div>

<div class="row text-center mb-5">
  <div class="col-md-4">
    <div class="card p-4">
      <h3 class="fw-bold" style="color:#1F3864;"><?php echo count($depts); ?></h3>
      <p class="mb-0">Departments</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-4">
      <h3 class="fw-bold" style="color:#1F3864;"><?php echo $doctorCount; ?></h3>
      <p class="mb-0">Active Doctors</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-4">
      <h3 class="fw-bold" style="color:#1F3864;">24/7</h3>
      <p class="mb-0">Online Booking</p>
    </div>
  </div>
</div>

<h4 class="mb-3">Our Departments</h4>
<div class="row">
  <?php foreach ($depts as $d): ?>
    <div class="col-md-4 mb-3">
      <div class="card p-3">
        <h5><?php echo htmlspecialchars($d['department_name']); ?></h5>
        <p class="text-muted mb-0"><?php echo htmlspecialchars($d['description']); ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>
