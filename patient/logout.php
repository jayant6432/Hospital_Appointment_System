<?php
session_start();
unset($_SESSION['patient_id'], $_SESSION['patient_name']);
session_destroy();
header("Location: ../index.php");
exit;
