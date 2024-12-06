<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();


// Define a function to get the database connection
function getDbConnection() {
    // Using constants for DB connection
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Get the database connection
$db = getDbConnection();

// Fetch all transactions
$sql = "SELECT * FROM transactions";
$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all transactions
$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>omen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css"> <!-- TailwindCSS for modern design -->
    <style>
        /* Custom Styling */
        .table-container {
            margin: 20px auto;
            max-width: 1200px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #e1e1e1;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f4f4f9;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .filter-container label {
            font-size: 14px;
            color: #333;
        }
        .no-results {
            text-align: center;
            padding: 20px;
            color: #999;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="container mx-auto py-10">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Transaction History</h1>

        <!-- Filtering Form (without submit button) -->
        <div class="filter-container">
            <div class="flex flex-col w-full sm:w-1/2 md:w-1/3">
                <label for="filter_id">Filter by ID:</label>
                <input type="text" id="filter_id" placeholder="Enter ID to filter" onkeyup="filterTransactions()">
            </div>
            <div class="flex flex-col w-full sm:w-1/2 md:w-1/3">
                <label for="filter_name">Filter by Customer Name:</label>
                <input type="text" id="filter_name" placeholder="Enter name to filter" onkeyup="filterTransactions()">
            </div>
        </div>

        <!-- Transaction Table -->
        <div class="table-container">
            <table id="transactions_table" class="table-auto w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Date</th>
                        <th>Total Price</th>
                        <th>Tax</th>
                        <th>Total with Tax</th>
                        <th>Payment</th>
                        <th>Change</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transactions) > 0): ?>
                        <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['id']) ?></td>
                            <td><?= htmlspecialchars($transaction['customer_name']) ?></td>
                            <td><?= htmlspecialchars($transaction['transaction_date']) ?></td>
                            <td><?= number_format($transaction['total_price'], 2) ?> PHP</td>
                            <td><?= number_format($transaction['tax'], 2) ?> PHP</td>
                            <td><?= number_format($transaction['total_with_tax'], 2) ?> PHP</td>
                            <td><?= number_format($transaction['payment'], 2) ?> PHP</td>
                            <td><?= number_format($transaction['change_amount'], 2) ?> PHP</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="no-results"><td colspan="8">No transactions found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function filterTransactions() {
            const filterId = document.getElementById('filter_id').value.toLowerCase();
            const filterName = document.getElementById('filter_name').value.toLowerCase();
            const rows = document.getElementById('transactions_table').getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const id = cells[0].textContent.toLowerCase();
                const name = cells[1].textContent.toLowerCase();
                
                if (id.includes(filterId) && name.includes(filterName)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>