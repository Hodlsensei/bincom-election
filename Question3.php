<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Polling Unit Results</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }
        header {
            background: #1a3c6e;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 { font-size: 20px; }
        header a { color: white; text-decoration: none; font-size: 13px; opacity: 0.8; }
        header a:hover { opacity: 1; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        h2 { color: #1a3c6e; margin-bottom: 6px; font-size: 18px; }
        .desc { color: #888; font-size: 13px; margin-bottom: 24px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: span 2; }
        label { font-size: 13px; color: #444; font-weight: 600; }
        input, select {
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            background: #fafafa;
        }
        input:focus, select:focus { outline: none; border-color: #1a3c6e; background: white; }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1a3c6e;
            margin: 20px 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #eaf3ff;
        }
        .party-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px; }
        .party-item { display: flex; flex-direction: column; gap: 6px; }
        .party-item label { font-size: 12px; color: #555; font-weight: 600; }
        .party-item input { padding: 8px 12px; font-size: 14px; }
        button[type="submit"] {
            background: #1a3c6e;
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
        }
        button[type="submit"]:hover { background: #16335c; }
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert.success { background: #eafaf1; border-left: 4px solid #27ae60; color: #1e8449; }
        .alert.error { background: #fdf2f2; border-left: 4px solid #e74c3c; color: #c0392b; }
    </style>
</head>
<body>
    <header>
        <h1>➕ Add New Polling Unit Results</h1>
        <a href="index.php">← Back to Home</a>
    </header>

    <div class="container">
        <?php
        include 'db.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $polling_unit_id = intval($_POST['polling_unit_id']);
            $entered_by_user = mysqli_real_escape_string($conn, $_POST['entered_by_user']);
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $date_entered = date('Y-m-d H:i:s');

            // Get all parties
            $parties = mysqli_query($conn, "SELECT partyid, partyname FROM party");

            $success = true;
            $errors = [];

            while ($party = mysqli_fetch_assoc($parties)) {
                $partyid = $party['partyid'];
                $score = isset($_POST['party_' . $partyid]) ? intval($_POST['party_' . $partyid]) : 0;

                $insert = "INSERT INTO announced_pu_results 
                    (polling_unit_uniqueid, party_abbreviation, party_score, entered_by_user, date_entered, user_ip_address)
                    VALUES ($polling_unit_id, '$partyid', $score, '$entered_by_user', '$date_entered', '$user_ip')";

                if (!mysqli_query($conn, $insert)) {
                    $success = false;
                    $errors[] = mysqli_error($conn);
                }
            }

            if ($success) {
                echo "<div class='alert success'>✅ Results successfully saved for the selected polling unit!</div>";
            } else {
                echo "<div class='alert error'>❌ Error saving results: " . implode(', ', $errors) . "</div>";
            }
        }
        ?>

        <div class="card">
            <h2>Enter Results for a New Polling Unit</h2>
            <p class="desc">Fill in the form below to store election results for all parties at a new polling unit.</p>

            <form method="POST" action="">
                <div class="section-title">Polling Unit Information</div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="lga_select">Select LGA first:</label>
                        <select id="lga_select" onchange="filterPollingUnits()">
                            <option value="">-- Select LGA --</option>
                            <?php
                            $lgas = mysqli_query($conn, "SELECT lga_id, lga_name FROM lga WHERE state_id = 25 ORDER BY lga_name");
                            while ($row = mysqli_fetch_assoc($lgas)) {
                                echo "<option value='{$row['lga_id']}'>" . htmlspecialchars($row['lga_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="polling_unit_id">Select Polling Unit:</label>
                        <select name="polling_unit_id" id="polling_unit_id" required>
                            <option value="">-- Select LGA first --</option>
                            <?php
                            $pus = mysqli_query($conn, "SELECT uniqueid, polling_unit_name, lga_id FROM polling_unit WHERE lga_id IN (SELECT lga_id FROM lga WHERE state_id = 25) ORDER BY polling_unit_name");
                            while ($row = mysqli_fetch_assoc($pus)) {
                                echo "<option value='{$row['uniqueid']}' data-lga='{$row['lga_id']}'>" . htmlspecialchars($row['polling_unit_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label for="entered_by_user">Entered By (Your Name):</label>
                        <input type="text" name="entered_by_user" id="entered_by_user" placeholder="e.g. Emmanuel" required>
                    </div>
                </div>

                <div class="section-title">Party Scores</div>
                <div class="party-grid">
                    <?php
                    $parties = mysqli_query($conn, "SELECT partyid, partyname FROM party ORDER BY partyname");
                    while ($party = mysqli_fetch_assoc($parties)) {
                        echo "<div class='party-item'>";
                        echo "<label>" . htmlspecialchars($party['partyname']) . " (" . htmlspecialchars($party['partyid']) . ")</label>";
                        echo "<input type='number' name='party_{$party['partyid']}' min='0' value='0' required>";
                        echo "</div>";
                    }
                    ?>
                </div>

                <button type="submit">💾 Save Results</button>
            </form>
        </div>
    </div>

    <script>
        function filterPollingUnits() {
            const lgaId = document.getElementById('lga_select').value;
            const puSelect = document.getElementById('polling_unit_id');
            const options = puSelect.querySelectorAll('option');

            options.forEach(opt => {
                if (opt.value === '') {
                    opt.style.display = '';
                } else if (lgaId === '' || opt.dataset.lga === lgaId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });

            puSelect.value = '';
        }
    </script>
</body>
</html>