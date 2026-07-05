<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['patient_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "Find Doctors";

$dept_filter = $_GET['department_id'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT d.*, dept.department_name FROM doctors d
        LEFT JOIN departments dept ON d.department_id = dept.department_id
        WHERE d.status = 'Active'";
$params = [];
if ($dept_filter) { $sql .= " AND d.department_id = ?"; $params[] = $dept_filter; }
if ($search) { $sql .= " AND (d.full_name LIKE ? OR d.specialization LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$doctors = $stmt->fetchAll();

$depts = $pdo->query("SELECT * FROM departments")->fetchAll();

include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">Find a Doctor</h3>

<form method="GET" class="row mb-4">
  <div class="col-md-5 mb-2">
    <input type="text" name="search" class="form-control" placeholder="Search by name or specialization" value="<?php echo htmlspecialchars($search); ?>">
  </div>
  <div class="col-md-4 mb-2">
    <select name="department_id" class="form-select">
      <option value="">All Departments</option>
      <?php foreach ($depts as $d): ?>
        <option value="<?php echo $d['department_id']; ?>" <?php echo $dept_filter == $d['department_id'] ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($d['department_name']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3 mb-2">
    <button type="submit" class="btn btn-primary w-100">Search</button>
  </div>
</form>

<div class="row">
  <?php if (count($doctors) === 0): ?>
    <p class="text-muted">No doctors found matching your criteria.</p>
  <?php endif; ?>
  <?php foreach ($doctors as $doc): ?>
    <div class="col-md-4 mb-3">
      <div class="card p-3">
        <h5><?php echo htmlspecialchars($doc['full_name']); ?></h5>
        <p class="mb-1"><span class="badge" style="background-color:#B08D57;"><?php echo htmlspecialchars($doc['specialization']); ?></span></p>
        <p class="text-muted mb-1"><?php echo htmlspecialchars($doc['department_name']); ?></p>
        <p class="mb-1"><small>Available: <?php echo htmlspecialchars($doc['available_days']); ?>, <?php echo htmlspecialchars($doc['available_time']); ?></small></p>
        <a href="book_appointment.php?doctor_id=<?php echo $doc['doctor_id']; ?>" class="btn btn-primary btn-sm mt-2">Book Appointment</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>
