<?php require_once '../app/views/layouts/header.php'; ?>

<div class="auth-container">
    <h2>Create Account</h2>
    <?php if (isset($data['error'])): ?>
        <p class="error"><?php echo $data['error']; ?></p>
    <?php endif; ?>
    
    <form action="<?php echo URLROOT; ?>/auth/register" method="POST">
        <div class="form-group">
            <label for="university_id">University ID</label>
            <input type="text" name="university_id" id="university_id" required 
                placeholder="ex: 2023/01234" onkeyup="checkAdmin()">
        </div>
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" required pattern="\d{1,11}" maxlength="11">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--text-color); margin: 20px 0;">

        <div id="vehicle-section">
            <h3>Vehicle Details</h3>
            <p style="font-size: 0.8rem; margin-top:0; color: var(--highlight);">* Required for all Students</p>

            <div class="form-group">
                <label for="license_plate">License Plate</label>
                <input type="text" name="license_plate" id="license_plate" placeholder="e.g. 123-ABC" required>
            </div>
            <div class="form-group">
                <label for="vehicle_model">Car Model</label>
                <input type="text" name="vehicle_model" id="vehicle_model" placeholder="e.g. Toyota Corolla" required>
            </div>
            <div class="form-group">
                <label for="vehicle_color">Car Color</label>
                <input type="text" name="vehicle_color" id="vehicle_color" placeholder="e.g. Black" required>
            </div>
        </div>

        <button type="submit" class="btn full-width-btn">Register</button>
    </form>
    <p>Already have an account? <a href="<?php echo URLROOT; ?>/auth/login">Login</a></p>
</div>

<script>
function checkAdmin() {
    const idInput = document.getElementById('university_id').value;
    const vehicleSection = document.getElementById('vehicle-section');
    const vehicleInputs = vehicleSection.querySelectorAll('input');

    // If ID contains '#', treat as Admin (Hide vehicle inputs)
    if (idInput.includes('#')) {
        vehicleSection.style.display = 'none';
        vehicleInputs.forEach(input => input.required = false); // Remove 'required'
    } else {
        vehicleSection.style.display = 'block';
        vehicleInputs.forEach(input => input.required = true); // Enforce 'required'
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?>