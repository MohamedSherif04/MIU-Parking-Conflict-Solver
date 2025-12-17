<?php require_once '../app/views/layouts/header.php'; ?>

<h1>Student Dashboard</h1>

<div class="dashboard-grid">

    <div class="card full-width">
        <h3>Notifications</h3>
        <?php if (empty($data['notifications'])): ?>
            <p>No new notifications.</p>
        <?php else: ?>
            <ul class="notification-list">
                <?php foreach ($data['notifications'] as $notif): ?>
                    <li class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>">
                        <?php echo $notif['message']; ?>
                        <span class="notification-time"><?php echo $notif['created_at']; ?></span>
                        
                        <?php if (strpos($notif['message'], 'Is the issue resolved?') !== false): ?>
                            <div class="verification-box" style="margin-top:10px; padding:10px; background:#232946; border-radius:5px;">
                                <p style="margin:0 0 5px 0; font-size:0.9rem; color:#fff;">Please verify:</p>
                                <form action="<?php echo URLROOT; ?>/report/verify" method="POST" style="display:inline;">
                                    <input type="hidden" name="report_id" value="<?php echo $notif['related_id'] ?? 0; ?>">
                                    <button type="submit" name="status" value="true" class="btn small" style="background-color:var(--success-color); color:#000;">True (Resolved)</button>
                                    <button type="submit" name="status" value="false" class="btn small" style="background-color:var(--error-color); color:#fff;">False (Not Yet)</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Report Conflict</h3>
        <div class="form-group">
            <label>Search Plate to Report</label>
            <input type="text" id="search_plate" placeholder="Enter Plate Number (e.g., ABC-123)">
            <button type="button" class="btn secondary" onclick="searchPlate()">Search</button>
            <div id="search_result" style="margin-top: 10px; font-weight: bold;"></div>
        </div>

        <form action="<?php echo URLROOT; ?>/report/submit" method="POST" id="reportForm">
            <input type="hidden" name="blocked_plate" id="blocked_plate_input" required>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Describe the situation..." required></textarea>
            </div>
            
            <button type="submit" class="btn" id="submit_btn" disabled style="opacity: 0.5; cursor: not-allowed;">Submit Report</button>
        </form>
    </div>

    <div class="card">
        <h3>My Vehicles</h3>
        <ul>
            <?php foreach ($data['vehicles'] as $vehicle): ?>
                <li><?php echo $vehicle['model']; ?> (<?php echo $vehicle['license_plate']; ?>)</li>
            <?php endforeach; ?>
        </ul>
        </div>

    <div class="card full-width">
        <h3>Report History</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Blocked Plate</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['reports'] as $report): ?>
                    <tr>
                        <td><?php echo $report['report_id']; ?></td>
                        <td><?php echo $report['blocked_plate']; ?></td>
                        <td>
                            <span style="
                                color: <?php echo $report['status'] == 'Resolved' ? 'var(--success-color)' : ($report['status'] == 'Escalated' ? 'var(--error-color)' : '#fff'); ?>;
                                font-weight: bold;">
                                <?php echo $report['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $report['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>