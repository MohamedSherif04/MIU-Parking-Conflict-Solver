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
                <?php if (isset($_SESSION['user_id'])): ?>
                    
                    <?php 
                        // We instantiate the model directly here to ensure the badge 
                        // is available on every page (header is global)
                        require_once '../app/models/Notification.php';
                        $nModel = new Notification();
                        $unreadCount = $nModel->getUnreadCount($_SESSION['user_id']);
                    ?>

                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <li><a href="<?php echo URLROOT; ?>/dashboard/admin">Admin Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?php echo URLROOT; ?>/dashboard/index">
                                Dashboard
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge"><?php echo $unreadCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li><a href="<?php echo URLROOT; ?>/auth/logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo URLROOT; ?>/auth/login">Login</a></li>
                    <li><a href="<?php echo URLROOT; ?>/auth/register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="main-content container">