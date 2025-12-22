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
                        
                        <?php if ($notif['related_id'] != NULL && strpos($notif['message'], 'Is the issue resolved?') !== false): ?>
                            <div class="verification-box" style="margin-top:10px; padding:15px; background:#232946; border:1px solid var(--highlight); border-radius:5px;">
                                <p style="margin:0 0 10px 0; color:#fff;">Please confirm status:</p>
                                
                                <form action="<?php echo URLROOT; ?>/report/verify" method="POST">
                                    <input type="hidden" name="report_id" value="<?php echo $notif['related_id']; ?>">
                                    
                                    <label style="display:flex; align-items:center; cursor:pointer; gap:10px; margin-bottom:10px;">
                                        <input type="checkbox" name="is_solved" value="yes" style="width:20px; height:20px;">
                                        <span style="font-size:1rem; font-weight:bold; color:var(--success-color);">Yes, the issue is Solved.</span>
                                    </label>

                                    <button type="submit" class="btn small full-width-btn">Confirm Status</button>
                                    <small style="display:block; margin-top:5px; color:#aaa;">(If unchecked, report will be escalated)</small>
                                </form>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="card full-width" style="text-align: center; padding: 40px; border: 2px solid var(--highlight);">
        <h2 style="font-size: 2rem; margin-bottom: 20px;">Report Conflict</h2>
        
        <div class="form-group" style="max-width: 600px; margin: 0 auto;">
            <label style="font-size: 1.2rem;">Search Plate to Report</label>
            <input type="text" id="search_plate" placeholder="Enter Plate Number (e.g., ABC-123)" style="padding: 15px; font-size: 1.1rem;">
            <button type="button" class="btn secondary" onclick="searchPlate()" style="margin-top:10px; padding: 10px 20px; font-size: 1.1rem;">Search</button>
            <div id="search_result" style="margin-top: 15px; font-weight: bold; font-size: 1.2rem;"></div>
        </div>

        <form action="<?php echo URLROOT; ?>/report/submit" method="POST" id="reportForm" style="max-width: 600px; margin: 20px auto 0;">
            <input type="hidden" name="blocked_plate" id="blocked_plate_input" required>
            
            <div class="form-group">
                <label style="font-size: 1.2rem;">Description</label>
                <textarea name="description" placeholder="Describe the situation..." required style="padding: 15px; font-size: 1.1rem; height: 120px;"></textarea>
            </div>
            
            <button type="submit" class="btn" id="submit_btn" disabled style="opacity: 0.5; cursor: not-allowed; font-size: 1.2rem; padding: 15px 30px;">Submit Report</button>
        </form>
    </div>

    <div class="card full-width" style="text-align: center;">
        <h3>My Vehicles</h3>
        
        <?php if (empty($data['vehicles'])): ?>
            <p style="margin-bottom: 20px;">You have no registered vehicles.</p>
        <?php else: ?>
            <ul style="list-style: none; padding: 0; margin-bottom: 30px;">
                <?php foreach ($data['vehicles'] as $vehicle): ?>
                    <li style="background: rgba(255,255,255,0.05); padding: 15px; margin: 10px auto; max-width: 500px; border-radius: 8px; border: 1px solid var(--highlight); display: flex; justify-content: space-between; align-items: center;">
                        <div style="text-align: left;">
                            <strong style="font-size: 1.3rem; display:block;"><?php echo $vehicle['license_plate']; ?></strong>
                            <span style="color: #ccc;"><?php echo $vehicle['model']; ?> (<?php echo $vehicle['color']; ?>)</span>
                        </div>
                        <a href="<?php echo URLROOT; ?>/vehicle/edit/<?php echo $vehicle['license_plate']; ?>" class="btn small secondary">Edit Details</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <hr style="border: 0; border-top: 1px solid #333; margin: 20px 0;">

        <h4>Add a New Vehicle</h4>
        <form action="<?php echo URLROOT; ?>/vehicle/add" method="POST" style="display:flex; gap:10px; flex-wrap:wrap; justify-content: center; max-width: 600px; margin: 0 auto;">
            <input type="text" name="license_plate" placeholder="Plate (ABC-123)" required style="padding:10px; flex:1; min-width: 120px;">
            <input type="text" name="model" placeholder="Model (e.g. Toyota)" required style="padding:10px; flex:1; min-width: 120px;">
            <input type="text" name="color" placeholder="Color" required style="padding:10px; flex:1; min-width: 100px;">
            <button type="submit" class="btn small">Add Car</button>
        </form>
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
                <?php if(empty($data['reports'])): ?>
                    <tr><td colspan="4" style="text-align:center;">No reports found.</td></tr>
                <?php else: ?>
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
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once '../app/views/layouts/footer.php'; ?>