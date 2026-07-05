<?php
session_start();
unset($_SESSION['doctor_id'], $_SESSION['doctor_name']);
session_destroy();
header("Location: ../index.php");
exit;
