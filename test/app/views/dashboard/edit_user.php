<?php require_once '../app/views/layouts/header.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 50px;">
    <div class="card">
        <h2>Edit User: <?php echo $data['user']['full_name']; ?></h2>
        
        <form action="<?php echo URLROOT; ?>/dashboard/updateUser" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $data['user']['user_id']; ?>">
            
            <h3>User Information</h3>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo $data['user']['full_name']; ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="<?php echo $data['user']['phone_number']; ?>" required>
            </div>

            <?php if (!empty($data['vehicle'])): ?>
                <h3>Vehicle Information</h3>
                <input type="hidden" name="original_plate" value="<?php echo $data['vehicle']['license_plate']; ?>">
                <div class="form-group">
                    <label>License Plate</label>
                    <input type="text" name="license_plate" value="<?php echo $data['vehicle']['license_plate']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Model</label>
                    <input type="text" name="model" value="<?php echo $data['vehicle']['model']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" value="<?php echo $data['vehicle']['color']; ?>" required>
                </div>
            <?php else: ?>
                <p><em>This user has no registered vehicles.</em></p>
            <?php endif; ?>

            <div style="margin-top: 20px;">
                <button type="submit" class="btn">Save Changes</button>
                <a href="<?php echo URLROOT; ?>/dashboard/admin" class="btn secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>