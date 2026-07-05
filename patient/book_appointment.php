<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['patient_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "Book Appointment";
$pid = $_SESSION['patient_id'];
$error = ""; $success = "";

$doctor_id = $_GET['doctor_id'] ?? ($_POST['doctor_id'] ?? null);
if (!$doctor_id) { header("Location: doctors.php"); exit; }

$stmt = $pdo->prepare("SELECT d.*, dept.department_name FROM doctors d LEFT JOIN departments dept ON d.department_id = dept.department_id WHERE d.doctor_id = ?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch();
if (!$doctor) { header("Location: doctors.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $reason = trim($_POST['reason']);

    // Prevent duplicate slot booking for the same doctor
    $check = $pdo->prepare("SELECT appointment_id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'");
    $check->execute([$doctor_id, $date, $time]);

    if ($check->fetch()) {
        $error = "This time slot is already booked. Please choose a different time.";
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        $error = "Please select a valid future date.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$pid, $doctor_id, $date, $time, $reason]);
        $success = "Appointment request submitted! You will be notified once the doctor confirms.";
    }
}

include '../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card p-4">
      <h4 style="color:#1F3864;">Book Appointment</h4>
      <p class="mb-3">
        <strong><?php echo htmlspecialchars($doctor['full_name']); ?></strong> — <?php echo htmlspecialchars($doctor['specialization']); ?><br>
        <small class="text-muted"><?php echo htmlspecialchars($doctor['department_name']); ?> | Available: <?php echo htmlspecialchars($doctor['available_days']); ?>, <?php echo htmlspecialchars($doctor['available_time']); ?></small>
      </p>

      <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <a href="my_appointments.php" class="btn btn-primary">View My Appointments</a>
      <?php else: ?>
      <form method="POST">
        <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">
        <div class="mb-3">
          <label class="form-label">Preferred Date</label>
          <input type="date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Preferred Time</label>
          <select name="appointment_time" class="form-select" required>
            <option value="10:00 AM">10:00 AM</option>
            <option value="11:00 AM">11:00 AM</option>
            <option value="12:00 PM">12:00 PM</option>
            <option value="1:00 PM">1:00 PM</option>
            <option value="3:00 PM">3:00 PM</option>
            <option value="4:00 PM">4:00 PM</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Reason for Visit</label>
          <textarea name="reason" class="form-control" rows="3" placeholder="Briefly describe your symptoms/reason"></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Confirm Booking</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
