<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['doctor_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "Doctor Dashboard";
$did = $_SESSION['doctor_id'];

// Handle status update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['new_status'])) {
    $allowed = ['Confirmed', 'Cancelled', 'Completed'];
    if (in_array($_POST['new_status'], $allowed)) {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ? AND doctor_id = ?");
        $stmt->execute([$_POST['new_status'], $_POST['appointment_id'], $did]);
    }
    header("Location: dashboard.php");
    exit;
}

$filter = $_GET['status'] ?? '';
$sql = "SELECT a.*, p.full_name AS patient_name, p.phone FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        WHERE a.doctor_id = ?";
$params = [$did];
if ($filter) { $sql .= " AND a.status = ?"; $params[] = $filter; }
$sql .= " ORDER BY a.appointment_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll();

$counts = $pdo->prepare("SELECT status, COUNT(*) c FROM appointments WHERE doctor_id = ? GROUP BY status");
$counts->execute([$did]);
$statusCounts = ['Pending'=>0,'Confirmed'=>0,'Cancelled'=>0,'Completed'=>0];
foreach ($counts->fetchAll() as $row) { $statusCounts[$row['status']] = $row['c']; }

include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">Welcome, <?php echo htmlspecialchars($_SESSION['doctor_name']); ?></h3>

<div class="row mb-4">
  <?php foreach ($statusCounts as $status => $count): ?>
    <div class="col-md-3 mb-2">
      <a href="?status=<?php echo $status; ?>" class="text-decoration-none">
        <div class="card p-3 text-center">
          <h4 class="fw-bold" style="color:#1F3864;"><?php echo $count; ?></h4>
          <small class="text-muted"><?php echo $status; ?></small>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Appointments <?php echo $filter ? "— " . htmlspecialchars($filter) : ''; ?></h5>
  <?php if ($filter): ?><a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Clear Filter</a><?php endif; ?>
</div>

<div class="card p-3">
  <?php if (count($appointments) === 0): ?>
    <p class="text-muted mb-0">No appointments found.</p>
  <?php else: ?>
  <table class="table align-middle">
    <thead><tr><th>Patient</th><th>Phone</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach ($appointments as $a): ?>
      <tr>
        <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
        <td><?php echo htmlspecialchars($a['phone']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
        <td><?php echo htmlspecialchars($a['appointment_time']); ?></td>
        <td><?php echo htmlspecialchars($a['reason']); ?></td>
        <td><span class="badge bg-<?php echo $a['status']=='Confirmed'?'success':($a['status']=='Pending'?'warning':($a['status']=='Completed'?'primary':'secondary')); ?>"><?php echo $a['status']; ?></span></td>
        <td>
          <?php if ($a['status'] === 'Pending'): ?>
            <form method="POST" class="d-flex gap-1">
              <input type="hidden" name="appointment_id" value="<?php echo $a['appointment_id']; ?>">
              <button type="submit" name="new_status" value="Confirmed" class="btn btn-success btn-sm">Confirm</button>
              <button type="submit" name="new_status" value="Cancelled" class="btn btn-danger btn-sm">Reject</button>
            </form>
          <?php elseif ($a['status'] === 'Confirmed'): ?>
            <form method="POST">
              <input type="hidden" name="appointment_id" value="<?php echo $a['appointment_id']; ?>">
              <button type="submit" name="new_status" value="Completed" class="btn btn-primary btn-sm">Mark Completed</button>
            </form>
          <?php else: ?>
            <span class="text-muted">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
