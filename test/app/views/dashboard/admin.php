<?php require_once '../app/views/layouts/header.php'; ?>

<h1>Admin Dashboard</h1>

<?php 
// Check for escalated reports to notify admin
$escalatedCount = 0;
foreach($data['reports'] as $r) { if($r['status'] == 'Escalated') $escalatedCount++; }
if($escalatedCount > 0): 
?>
    <div class="error">
        ⚠️ Action Required: You have <?php echo $escalatedCount; ?> escalated reports pending intervention!
    </div>
<?php endif; ?>

<div class="dashboard-grid">
    
    <div class="card full-width">
        <h3>Manage Users</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['users'] as $user): ?>
                    <tr>
                        <td><?php echo $user['university_id']; ?></td>
                        <td><?php echo $user['full_name']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <?php if($user['role'] != 'Admin'): ?>
                                <a href="<?php echo URLROOT; ?>/dashboard/editUser/<?php echo $user['user_id']; ?>" class="btn small secondary">Edit</a>
                                
                                <form action="<?php echo URLROOT; ?>/dashboard/deleteUser" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" class="btn small" style="background-color:var(--error-color)">Delete</button>
                                </form>
                            <?php else: ?>
                                <span style="color:#666;">(Admin)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card full-width">
        <h3>Report History & Status</h3>
        <table>
            <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Reporter</th>
                    <th>Blocked Plate</th>
                    <th>Status</th> <th>Time Elapsed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['reports'] as $report): ?>
                    <tr class="<?php echo $report['status'] == 'Escalated' ? 'escalated' : ''; ?>">
                        <td><?php echo $report['report_id']; ?></td>
                        <td><?php echo $report['reporter_name']; ?></td>
                        <td><?php echo $report['blocked_plate']; ?></td>
                        
                        <td>
                            </td>

                        <td>
                            <?php 
                                $start = strtotime($report['created_at']);
                                $diff = round((time() - $start) / 60);
                                echo $diff . " mins ago";
                            ?>
                        </td>
                        <td>
                            <?php if ($report['status'] != 'Resolved'): ?>
                                <form action="<?php echo URLROOT; ?>/report/resolve" method="POST">
                                    <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                                    <button type="submit" class="btn small">Force Resolve</button>
                                </form>
                            <?php else: ?>
                                ✔️ Done
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once '../app/views/layouts/footer.php'; ?>