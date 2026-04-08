<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LGA Summed Results</title>
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
        .desc { color: #888; font-size: 13px; margin-bottom: 20px; }
        label { display: block; font-size: 14px; color: #444; margin-bottom: 6px; font-weight: 600; }
        select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            background: #fafafa;
            margin-bottom: 16px;
        }
        button {
            background: #1a3c6e;
            color: white;
            border: none;
            padding: 11px 28px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background: #16335c; }
        .results-title { color: #1a3c6e; font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .results-sub { color: #888; font-size: 12px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #1a3c6e;
            color: white;
            padding: 10px 14px;
            text-align: left;
            font-size: 13px;
        }
        td { padding: 10px 14px; font-size: 14px; border-bottom: 1px solid #f0f0f0; }
        tr:last-child td { border-bottom: none; }
        tr:nth-child(even) td { background: #f8f9fb; }
        .bar-wrap { background: #e8edf4; border-radius: 4px; height: 10px; width: 100%; }
        .bar { background: #27ae60; height: 10px; border-radius: 4px; }
        .total-row td { font-weight: 700; background: #eaf3ff !important; color: #1a3c6e; }
        .no-results { color: #e74c3c; font-size: 14px; padding: 10px 0; }
        .info-box {
            background: #eaf3ff;
            border-left: 4px solid #1a3c6e;
            padding: 10px 14px;
            border-radius: 4px;
            font-size: 13px;
            color: #444;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>📊 LGA Summed Results</h1>
        <a href="index.php">← Back to Home</a>
    </header>

    <div class="container">
        <div class="card">
            <h2>Select a Local Government Area</h2>
            <p class="desc">View the total votes across all polling units under any LGA.</p>
            <div class="info-box">ℹ️ Results are summed from individual polling unit scores — not from the announced LGA result table.</div>

            <form method="GET" action="">
                <label for="lga">Local Government Area (LGA):</label>
                <select name="lga_id" id="lga">
                    <option value="">-- Select LGA --</option>
                    <?php
                    include 'db.php';
                    $lgas = mysqli_query($conn, "SELECT lga_id, lga_name FROM lga WHERE state_id = 25 ORDER BY lga_name");
                    while ($row = mysqli_fetch_assoc($lgas)) {
                        $selected = (isset($_GET['lga_id']) && $_GET['lga_id'] == $row['lga_id']) ? 'selected' : '';
                        echo "<option value='{$row['lga_id']}' $selected>" . htmlspecialchars($row['lga_name']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit">View Summed Results</button>
            </form>
        </div>

        <?php
        if (isset($_GET['lga_id']) && $_GET['lga_id'] != '') {
            $lga_id = intval($_GET['lga_id']);

            // Get LGA name
            $lga_query = mysqli_query($conn, "SELECT lga_name FROM lga WHERE lga_id = $lga_id");
            $lga = mysqli_fetch_assoc($lga_query);

            // Get summed results from polling units under this LGA
            $results_query = mysqli_query($conn, "
                SELECT p.partyname, SUM(apr.party_score) AS total_score
                FROM announced_pu_results apr
                JOIN party p ON apr.party_abbreviation = p.partyid
                JOIN polling_unit pu ON apr.polling_unit_uniqueid = pu.uniqueid
                WHERE pu.lga_id = $lga_id
                GROUP BY apr.party_abbreviation, p.partyname
                ORDER BY total_score DESC
            ");

            echo "<div class='card'>";
            if ($lga) {
                echo "<div class='results-title'>Summed Results for: " . htmlspecialchars($lga['lga_name']) . " LGA</div>";
                echo "<div class='results-sub'>Aggregated from all polling units in this LGA</div>";
            }

            if (mysqli_num_rows($results_query) > 0) {
                $rows = mysqli_fetch_all($results_query, MYSQLI_ASSOC);
                $max = max(array_column($rows, 'total_score'));
                $grand_total = array_sum(array_column($rows, 'total_score'));

                echo "<table>";
                echo "<tr><th>Party</th><th>Total Votes</th><th style='width:180px'>Distribution</th><th>% Share</th></tr>";
                foreach ($rows as $row) {
                    $width = $max > 0 ? round(($row['total_score'] / $max) * 100) : 0;
                    $percent = $grand_total > 0 ? round(($row['total_score'] / $grand_total) * 100, 1) : 0;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['partyname']) . "</td>";
                    echo "<td>" . number_format($row['total_score']) . "</td>";
                    echo "<td><div class='bar-wrap'><div class='bar' style='width:{$width}%'></div></div></td>";
                    echo "<td>{$percent}%</td>";
                    echo "</tr>";
                }
                echo "<tr class='total-row'>";
                echo "<td>TOTAL</td><td>" . number_format($grand_total) . "</td><td></td><td>100%</td>";
                echo "</tr>";
                echo "</table>";
            } else {
                echo "<p class='no-results'>No results found for this LGA.</p>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>