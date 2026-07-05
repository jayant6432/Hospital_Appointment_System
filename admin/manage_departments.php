<?php
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

$base = '../';
$page_title = "Manage Departments";
$error = ""; $success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['department_name']);
    $desc = trim($_POST['description']);
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO departments (department_name, description) VALUES (?, ?)");
        $stmt->execute([$name, $desc]);
        $success = "Department added successfully.";
    } else {
        $error = "Department name is required.";
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM departments WHERE department_id = ?");
    try {
        $stmt->execute([$_GET['delete']]);
    } catch (PDOException $e) {
        $error = "Cannot delete: department has associated doctors.";
    }
}

$depts = $pdo->query("SELECT * FROM departments ORDER BY department_id DESC")->fetchAll();
include '../includes/header.php';
?>

<h3 class="mb-4" style="color:#1F3864;">Manage Departments</h3>

<div class="row">
  <div class="col-md-5 mb-4">
    <div class="card p-3">
      <h5>Add New Department</h5>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
      <form method="POST">
        <div class="mb-2"><input type="text" name="department_name" class="form-control" placeholder="Department Name" required></div>
        <div class="mb-2"><input type="text" name="description" class="form-control" placeholder="Description"></div>
        <button type="submit" class="btn btn-primary w-100">Add Department</button>
      </form>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card p-3">
      <h5>All Departments</h5>
      <table class="table table-sm">
        <thead><tr><th>Name</th><th>Description</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($depts as $d): ?>
          <tr>
            <td><?php echo htmlspecialchars($d['department_name']); ?></td>
            <td><?php echo htmlspecialchars($d['description']); ?></td>
            <td><a href="?delete=<?php echo $d['department_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this department?');">Delete</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
