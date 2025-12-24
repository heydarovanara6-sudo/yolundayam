<?php
// delete_entry.php - Secure deletion of a session
session_start();
require_once 'inc/class.inc.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = SessionAnonymizer::getId($_GET['id'] ?? '');
$manager = new DrivingExperienceManager();

try {
    $manager->deleteExperience($id);
    header('Location: dashboard.php?message=deleted');
} catch (Exception $e) {
    echo "Error deleting session: " . $e->getMessage();
}
