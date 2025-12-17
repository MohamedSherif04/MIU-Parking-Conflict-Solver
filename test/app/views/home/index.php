<?php require_once '../app/views/layouts/header.php'; ?>

<div class="hero-section">
    <h1>Parking Conflict Solver</h1>
    <p>Search for a vehicle owner simply by their license plate number.</p>

    <div class="card search-card">
        <h3>Find a Vehicle</h3>
        <div class="form-group">
            <label for="search_plate">License Plate Number</label>
            <div class="search-box">
                <input type="text" id="search_plate" placeholder="e.g. 123-ABC" class="large-input">
                <button type="button" class="btn" onclick="searchPlate()">Search</button>
            </div>
            <div id="search_result" class="result-box"></div>
        </div>

        <div class="action-buttons">
            <p>Need to file a report?</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <button onclick="window.location.href='<?php echo URLROOT; ?>/dashboard'" class="btn secondary">Go to
                    Dashboard</button>
            <?php else: ?>
                <button onclick="window.location.href='<?php echo URLROOT; ?>/auth/login'" class="btn secondary">Login to
                    Report</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>