<?php
// Fetch Google Spreadsheet page using cURL
function fetchSpreadsheetData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

// Sample Google Spreadsheet URL (replace with actual public URL)
$url = "https://docs.google.com/spreadsheets/d/your-sheet-id/htmlview";
$html = fetchSpreadsheetData($url);

// Parse HTML using DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

// Extract table data
$tables = $dom->getElementsByTagName('table');
$data = [];

if ($tables->length > 0) {
    $rows = $tables->item(0)->getElementsByTagName('tr');
    foreach ($rows as $row) {
        $cols = $row->getElementsByTagName('td');
        if ($cols->length > 0) {
            $data[] = [
                'email' => trim($cols->item(0)->textContent),
                'name' => trim($cols->item(1)->textContent),
                'division' => trim($cols->item(2)->textContent)
            ];
        }
    }
}

// Get selected division from URL query parameter
$selectedDivision = isset($_GET['division']) ? strtolower($_GET['division']) : 'all';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Spreadsheet Scraper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="mb-4">Google Spreadsheet Data</h2>

    <form method="GET" class="mb-3">
        <label for="division" class="form-label">Select Division:</label>
        <select name="division" id="division" class="form-select">
            <option value="all">All</option>
            <option value="it">IT Division</option>
            <option value="marketing">Marketing Division</option>
            <option value="finance">Finance Division</option>
        </select>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Division</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <?php
                if ($selectedDivision !== 'all' && stripos($row['division'], $selectedDivision) === false) {
                    continue;
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['division']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
