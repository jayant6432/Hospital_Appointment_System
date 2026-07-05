<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . " | HealPoint" : "HealPoint Hospital"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo isset($base) ? $base : ''; ?>assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#1F3864;">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?php echo isset($base) ? $base : ''; ?>index.php">HealPoint Hospital</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['patient_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>patient/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>patient/doctors.php">Find Doctors</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>patient/my_appointments.php">My Appointments</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>patient/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['patient_name']); ?>)</a></li>
        <?php elseif (isset($_SESSION['doctor_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>doctor/dashboard.php">Doctor Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>doctor/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['doctor_name']); ?>)</a></li>
        <?php elseif (isset($_SESSION['admin_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>admin/dashboard.php">Admin Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>admin/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>patient/login.php">Patient Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>patient/register.php">Register</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>doctor/login.php">Doctor Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo $base ?? ''; ?>admin/login.php">Admin Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container my-4">
