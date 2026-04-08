<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polling Unit Results</title>
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
        .bar { background: #1a3c6e; height: 10px; border-radius: 4px; }
        .no-results { color: #e74c3c; font-size: 14px; padding: 10px 0; }
    </style>
</head>
<body>
    <header>
        <h1>🏛️ Polling Unit Results</h1>
        <a href="index.php">← Back to Home</a>
    </header>

    <div class="container">
        <div class="card">
            <h2>Select a Polling Unit</h2>
            <p class="desc">Choose a polling unit to view its announced election results.</p>

            <form method="GET" action="">
                <label for="polling_unit">Polling Unit:</label>
                <select name="polling_unit_id" id="polling_unit">
                    <option value="">-- Select Polling Unit --</option>
                    <?php
                    include 'db.php';
                    $result = mysqli_query($conn, "SELECT uniqueid, polling_unit_name FROM polling_unit WHERE lga_id IN (SELECT lga_id FROM lga WHERE state_id = 25) ORDER BY polling_unit_name");
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selected = (isset($_GET['polling_unit_id']) && $_GET['polling_unit_id'] == $row['uniqueid']) ? 'selected' : '';
                        echo "<option value='{$row['uniqueid']}' $selected>" . htmlspecialchars($row['polling_unit_name']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit">View Results</button>
            </form>
        </div>

        <?php
        if (isset($_GET['polling_unit_id']) && $_GET['polling_unit_id'] != '') {
            $pu_id = intval($_GET['polling_unit_id']);

            // Get polling unit name
            $pu_query = mysqli_query($conn, "SELECT polling_unit_name FROM polling_unit WHERE uniqueid = $pu_id");
            $pu = mysqli_fetch_assoc($pu_query);

            // Get results
            $results_query = mysqli_query($conn, "
                SELECT p.partyname, apr.party_score
                FROM announced_pu_results apr
                JOIN party p ON apr.party_abbreviation = p.partyid
                WHERE apr.polling_unit_uniqueid = $pu_id
                ORDER BY apr.party_score DESC
            ");

            echo "<div class='card'>";
            if ($pu) {
                echo "<div class='results-title'>Results for: " . htmlspecialchars($pu['polling_unit_name']) . "</div>";
                echo "<div class='results-sub'>Polling Unit ID: $pu_id</div>";
            }

            if (mysqli_num_rows($results_query) > 0) {
                // Get max score for bar chart
                $rows = mysqli_fetch_all($results_query, MYSQLI_ASSOC);
                $max = max(array_column($rows, 'party_score'));

                echo "<table>";
                echo "<tr><th>Party</th><th>Votes</th><th style='width:200px'>Distribution</th></tr>";
                foreach ($rows as $row) {
                    $width = $max > 0 ? round(($row['party_score'] / $max) * 100) : 0;
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['partyname']) . "</td>";
                    echo "<td>" . number_format($row['party_score']) . "</td>";
                    echo "<td><div class='bar-wrap'><div class='bar' style='width:{$width}%'></div></div></td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='no-results'>No results found for this polling unit.</p>";
            }
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>