<?php require_once '../app/views/layouts/header.php'; ?>

<div class="auth-container">
    <h2>Login</h2>
    <?php if (isset($data['error'])): ?>
        <p class="error"><?php echo $data['error']; ?></p>
    <?php endif; ?>
    <form action="<?php echo URLROOT; ?>/auth/login" method="POST">
        <div class="form-group">
            <label for="university_id">University ID</label>
            <input type="text" name="university_id" id="university_id" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <p>Don't have an account? <a href="<?php echo URLROOT; ?>/auth/register">Register</a></p>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>