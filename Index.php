<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bincom Election Results</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }
        header {
            background: #1a3c6e;
            color: white;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        header h1 { font-size: 24px; }
        header p { font-size: 13px; opacity: 0.8; }
        .container { max-width: 900px; margin: 60px auto; padding: 0 20px; }
        h2 { text-align: center; color: #1a3c6e; margin-bottom: 10px; font-size: 22px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 40px; font-size: 14px; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
            border-top: 4px solid #1a3c6e;
        }
        .card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .card .icon { font-size: 40px; margin-bottom: 15px; }
        .card h3 { color: #1a3c6e; margin-bottom: 10px; font-size: 16px; }
        .card p { color: #888; font-size: 13px; line-height: 1.5; }
        footer { text-align: center; margin-top: 60px; color: #aaa; font-size: 12px; }
    </style>
</head>
<body>
    <header>
        <div>
            <h1>🗳️ Bincom Election Results Portal</h1>
            <p>2011 Nigerian General Elections — Delta State</p>
        </div>
    </header>

    <div class="container">
        <h2>What would you like to do?</h2>
        <p class="subtitle">Select an option below to view or manage election results</p>

        <div class="cards">
            <a href="question1.php" class="card">
                <div class="icon">🏛️</div>
                <h3>Polling Unit Results</h3>
                <p>View election results for any individual polling unit</p>
            </a>
            <a href="question2.php" class="card">
                <div class="icon">📊</div>
                <h3>LGA Summed Results</h3>
                <p>View total results across all polling units in an LGA</p>
            </a>
            <a href="question3.php" class="card">
                <div class="icon">➕</div>
                <h3>Add New Results</h3>
                <p>Store election results for a new polling unit</p>
            </a>
        </div>

        <footer>Bincom Dev Center &mdash; Technical Interview Test &mdash; Emmanuel</footer>
    </div>
</body>
</html>