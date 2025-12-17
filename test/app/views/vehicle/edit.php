<?php require_once '../app/views/layouts/header.php'; ?>

<div class="container" style="max-width: 600px; margin-top: 50px;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 20px;">Car Details</h2>
        <p style="text-align: center; color: #888;">Modify your vehicle information below.</p>

        <form action="<?php echo URLROOT; ?>/vehicle/edit/<?php echo $data['vehicle']['license_plate']; ?>" method="POST">
            
            <div class="form-group">
                <label>License Plate</label>
                <input type="text" value="<?php echo $data['vehicle']['license_plate']; ?>" disabled 
                       style="background-color: rgba(255,255,255,0.1); cursor: not-allowed;">
                <small style="color: #666;">License plate cannot be changed.</small>
            </div>

            <div class="form-group">
                <label for="model">Car Model</label>
                <input type="text" name="model" id="model" value="<?php echo $data['vehicle']['model']; ?>" required>
            </div>

            <div class="form-group">
                <label for="color">Car Color</label>
                <input type="text" name="color" id="color" value="<?php echo $data['vehicle']['color']; ?>" required>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn full-width-btn">Save Changes</button>
                <a href="<?php echo URLROOT; ?>/dashboard/index" class="btn secondary full-width-btn" style="text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>