<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITENAME; ?></title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="<?php echo URLROOT; ?>/" class="brand">MIU Parking</a>

            <ul class="nav-links">
                <?php
                    // 1) Determine Current Role
                    $currentRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'All';

                    // 2) Load Menu Model & Fetch Items
                    require_once '../app/models/Menu.php';
                    $menuModel = new Menu();
                    $menuItems = $menuModel->getMenuItems($currentRole);

                    // 3) Detect current page (to hide Dashboard when already there)
                    $currentUrl = $_SERVER['REQUEST_URI']; // e.g. /test/public/dashboard/index
                    $onDashboard = (strpos($currentUrl, '/dashboard') !== false);

                    $isLoggedIn = isset($_SESSION['user_id']);

                    // 4) Loop through items from Database
                    foreach ($menuItems as $item):

                        // Skip sub-menu items for the main bar (items with parents)
                        if ($item['parent_id'] != NULL) continue;

                        // ---- FILTER RULES (your request) ----

                        // A) If logged in: hide Login + Register
                        $labelLower = strtolower(trim($item['label']));
                        if ($isLoggedIn && ($labelLower === 'login' || $labelLower === 'register')) {
                            continue;
                        }

                        // B) If already on dashboard: hide Dashboard
                        if ($onDashboard && $labelLower === 'dashboard') {
                            continue;
                        }
                ?>
                        <li class="nav-item">
                            <a href="<?php echo URLROOT . $item['url']; ?>">
                                <?php echo $item['label']; ?>

                                <?php
                                    // Keep badge ONLY if Dashboard link is shown and user logged in
                                    if ($item['label'] == 'Dashboard' && $isLoggedIn) {
                                        require_once '../app/models/Notification.php';
                                        $nModel = new Notification();
                                        $unreadCount = $nModel->getUnreadCount($_SESSION['user_id']);
                                        if ($unreadCount > 0) {
                                            echo "<span class='badge'>$unreadCount</span>";
                                        }
                                    }
                                ?>
                            </a>
                        </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>

    <div class="main-content container">
