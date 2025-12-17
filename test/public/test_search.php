<?php
// Test Script to debug Search
require_once '../app/config/config.php';

$url = URLROOT . '/report/search';
$plate = 'ABC-123'; // Known plate from seeder

echo "<h1>Search Debugger</h1>";
echo "<p>Testing URL: <strong>$url</strong></p>";
echo "<p>Searching for Plate: <strong>$plate</strong></p>";

// Use CURL to simulate request if possible, or just HTML form
?>

<form action="<?php echo $url; ?>" method="POST" target="_blank">
    <input type="hidden" name="plate" value="<?php echo $plate; ?>">
    <button type="submit">Test Search (Opens in new tab)</button>
</form>

<p>Click the button above. You should see raw JSON output like <code>{"status":"found"...}</code>. <br>
If you see an HTML error page, then the Controller is not returning JSON correctly.</p>
