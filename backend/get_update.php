<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
requireLogin();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) jsonResponse(false, 'Invalid ID');

$result = $conn->query("SELECT * FROM updates WHERE id = $id");
$row    = $result ? $result->fetch_assoc() : null;
if ($row) jsonResponse(true, 'Found', $row);
else      jsonResponse(false, 'Post not found');
