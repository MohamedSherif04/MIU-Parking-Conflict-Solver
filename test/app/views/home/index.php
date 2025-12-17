<?php require_once '../app/views/layouts/header.php'; ?>

<div class="hero-section" style="padding-top: 80px;"> <div style="margin-bottom: 40px;">
        <h1 style="font-size: 3.5rem; margin-bottom: 15px; color: var(--headline-color);">MIU Parking Conflict Solver</h1>
        <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto; color: var(--text-color); opacity: 0.9;">
            The MIU Parking Conflict Solver is a mediated communication platform designed to resolve parking blocks
            swiftly and privately. Search for a vehicle by plate number to notify the owner instantly without
            sharing personal phone numbers, keeping the campus traffic flowing smoothly.
        </p>
    </div>

    <div class="card search-card" style="max-width: 800px; padding: 40px; transform: scale(1.05);">
        <h3 style="font-size: 1.8rem; margin-bottom: 20px;">Find a Vehicle</h3>
        
        <div class="form-group">
            <label for="search_plate" style="font-size: 1.2rem;">License Plate Number</label>
            <div class="search-box">
                <input type="text" id="search_plate" placeholder="e.g. 123-ABC" class="large-input" style="font-size: 1.3rem; padding: 15px;">
                <button type="button" class="btn" onclick="searchPlate()" style="font-size: 1.2rem; padding: 15px 30px;">Search</button>
            </div>
            <div id="search_result" class="result-box" style="font-size: 1.2rem; margin-top: 15px;"></div>
        </div>

        <div class="action-buttons" style="margin-top: 30px; border-top: 1px solid #333; padding-top: 20px;">
            <p style="font-size: 1.1rem;">Need to file a report?</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <button onclick="window.location.href='<?php echo URLROOT; ?>/dashboard/index'" class="btn secondary" style="font-size: 1.1rem;">Go to Dashboard</button>
            <?php else: ?>
                <button onclick="window.location.href='<?php echo URLROOT; ?>/auth/login'" class="btn secondary" style="font-size: 1.1rem;">Login to Report</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>