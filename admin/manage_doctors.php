<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "Manage Doctors";
$error = ""; $success = "";

// Add new doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $dept = $_POST['department_id'];
    $spec = trim($_POST['specialization']);
    $phone = trim($_POST['phone']);
    $days = trim($_POST['available_days']);
    $time = trim($_POST['available_time']);

    if ($name && $email && $password) {
        $check = $pdo->prepare("SELECT doctor_id FROM doctors WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = "A doctor with this email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO doctors (full_name, email, password, department_id, specialization, phone, available_days, available_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $dept, $spec, $phone, $days, $time]);
            $success = "Doctor added successfully.";
        }
    } else {
        $error = "Please fill all required fields.";
    }
}

// Toggle status
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE doctors SET status = IF(status='Active','Inactive','Active') WHERE doctor_id = ?");
    $stmt->execute([$_GET['toggle']]);
    header("Location: manage_doctors.php");
    exit;
}

$doctors = $pdo->query("SELECT d.*, dept.department_name FROM doctors d LEFT JOIN departments dept ON d.department_id = dept.department_id ORDER BY d.doctor_id DESC")->fetchAll();
$depts = $pdo->query("SELECT * FROM departments")->fetchAll();

include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">Manage Doctors</h3>

<div class="row">
  <div class="col-md-5 mb-4">
    <div class="card p-3">
      <h5>Add New Doctor</h5>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="mb-2"><input type="text" name="full_name" class="form-control" placeholder="Full Name" required></div>
        <div class="mb-2"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
        <div class="mb-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
        <div class="mb-2">
          <select name="department_id" class="form-select" required>
            <option value="">Select Department</option>
            <?php foreach ($depts as $d): ?>
              <option value="<?php echo $d['department_id']; ?>"><?php echo htmlspecialchars($d['department_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-2"><input type="text" name="specialization" class="form-control" placeholder="Specialization" required></div>
        <div class="mb-2"><input type="text" name="phone" class="form-control" placeholder="Phone"></div>
        <div class="mb-2"><input type="text" name="available_days" class="form-control" placeholder="Available Days (e.g. Mon-Sat)" value="Mon-Sat"></div>
        <div class="mb-2"><input type="text" name="available_time" class="form-control" placeholder="Available Time" value="10:00 AM - 4:00 PM"></div>
        <button type="submit" class="btn btn-primary w-100">Add Doctor</button>
      </form>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card p-3">
      <h5>All Doctors</h5>
      <table class="table table-sm">
        <thead><tr><th>Name</th><th>Department</th><th>Specialization</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($doctors as $d): ?>
          <tr>
            <td><?php echo htmlspecialchars($d['full_name']); ?></td>
            <td><?php echo htmlspecialchars($d['department_name']); ?></td>
            <td><?php echo htmlspecialchars($d['specialization']); ?></td>
            <td><span class="badge bg-<?php echo $d['status']=='Active'?'success':'secondary'; ?>"><?php echo $d['status']; ?></span></td>
            <td><a href="?toggle=<?php echo $d['doctor_id']; ?>" class="btn btn-sm btn-outline-secondary"><?php echo $d['status']=='Active'?'Deactivate':'Activate'; ?></a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
