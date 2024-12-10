<?php
// Informacionet për lidhjen me bazën e të dhënave
$host = 'localhost'; // Ose adresa IP e serverit të bazës së të dhënave
$username = 'root'; // Emri i përdoruesit të bazës së të dhënave
$password = ''; // Fjalëkalimi i përdoruesit të bazës së të dhënave
$dbname = 'frizer'; // Emri i bazës së të dhënave

// Lidhja me MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// Kontrollimi i gabimeve në lidhjen me bazën e të dhënave
if ($conn->connect_error) {
    die("Lidhja me bazën e të dhënave ka dështuar: " . $conn->connect_error);
}

// Kërko rezervimet për datën e zgjedhur
if (isset($_GET['date'])) {
    $selected_date = $_GET['date'];
    // Kërkesa SQL për të marrë rezervimet për këtë datë dhe renditur sipas orës
    $sql = "SELECT * FROM rezervimet WHERE date = '$selected_date' ORDER BY time";
    $result = $conn->query($sql);
} else {
    $result = null;
}

// Kërkimi për rezervime nga emri, mbiemri ose numri
$search_result = null;
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql_search = "SELECT * FROM rezervimet WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR id LIKE '%$search%'";
    $search_result = $conn->query($sql_search);
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shiko Rezervimet</title>
    <link rel="stylesheet" href="style.css">
    <!-- Përfshirja e jQuery dhe jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        select, button, input {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Shiko Rezervimet</h1>

        <!-- Formulari për kërkimin -->
        <form method="GET">
            <label for="search">Kërko nga emri, mbiemri, ose numri i rezervimit:</label>
            <input type="text" id="search" name="search" placeholder="Emri, Mbiemri, ose Numri i Rezervimit">
            <button type="submit">Kërko</button>
        </form>

        <!-- Shfaqja e rezultateve të kërkimit -->
        <?php if ($search_result !== null && $search_result->num_rows > 0): ?>
            <h2>Rezultatet e kërkimit:</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Emri</th>
                    <th>Mbiemri</th>
                    <th>Data</th>
                    <th>Ora</th>
                </tr>
                <?php
                while ($row = $search_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["first_name"] . "</td>";
                    echo "<td>" . $row["last_name"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>" . $row["time"] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        <?php elseif ($search_result !== null): ?>
            <p>Nuk ka rezervime që përputhen me kërkimin tuaj.</p>
        <?php endif; ?>

        <!-- Formulari për zgjedhjen e datës -->
        <form method="GET">
            <label for="datepicker">Zgjidh datën:</label>
            <input type="text" id="datepicker" name="date" required readonly>
            <button type="submit">Shiko Rezervimet</button>
        </form>

        <!-- Shfaqja e rezervimeve për datën e zgjedhur -->
        <?php if ($result !== null && $result->num_rows > 0): ?>
            <h2>Rezervimet për datën <?= isset($_GET['date']) ? $_GET['date'] : '' ?></h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Emri</th>
                    <th>Mbiemri</th>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Kontakti</th>
                    <th>Shuma Totale</th>
                    <th>Para-pagese</th>
                    <th>Borxhi</th>
                    <th>Komenti</th>
                    <th>Akcion</th>
                </tr>
                <?php
                $index = 1; // Fillimi i ID-ve nga 1 për secilën ditë
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $index . "</td>"; // ID që fillon nga 1 për çdo rezervim
                    echo "<td>" . $row["first_name"] . "</td>";
                    echo "<td>" . $row["last_name"] . "</td>";
                    echo "<td>" . $row["date"] . "</td>";
                    echo "<td>" . $row["time"] . "</td>";
                    echo "<td>" . $row["contact"] . "</td>";
                    echo "<td>" . number_format($row["total_amount"], 2) . " €</td>";
                    echo "<td>" . number_format($row["prepayment_amount"], 2) . " €</td>";
                    echo "<td>" . number_format($row["debt_amount"], 2) . " €</td>";
                    echo "<td>" . $row["comment"] . "</td>";
                    echo "<td>
                            <a href='admin.php?edit_id=" . $row["id"] . "'>Redakto</a> | 
                            <a href='admin.php?delete_id=" . $row["id"] . "' onclick='return confirm(\"A jeni te sigurt se doni te fshini kete rezervim?\")'>Fshi</a>
                          </td>";
                    echo "</tr>";
                    $index++; // Rrit ID-në për çdo rezervim
                }
                ?>
            </table>
        <?php elseif ($result !== null): ?>
            <p>Nuk ka rezervime për këtë datë.</p>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            // Aktivizo kalendarin për inputin e datës
            $("#datepicker").datepicker({
                dateFormat: "yy-mm-dd",  // Formati i datës që do të dërgohet në backend
                minDate: 0,  // Lejo përdoruesit të zgjedhë datën e sotme ose më të vonshme
                maxDate: "+1Y",  // Ofron mundësinë për të zgjedhur deri në një vit përpara
                changeMonth: true, // Mundëson zgjedhjen e muajit
                changeYear: true,  // Mundëson zgjedhjen e vitit
                showAnim: "slideDown"  // Animacioni për shfaqjen e kalendarit
            });
        });
    </script>
</body>
</html>
