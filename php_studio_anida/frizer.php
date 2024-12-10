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
    // Kërkesa SQL për të marrë vetëm ID, datën, orën dhe statusin e disponueshmërisë
    $sql = "SELECT id, date, time FROM rezervimet WHERE date = '$selected_date' ORDER BY time";
    $result = $conn->query($sql);
} else {
    $result = null;
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

        <!-- Formulari për zgjedhjen e datës -->
        <form method="GET">
            <label for="datepicker">Zgjidh datën:</label>
            <!-- Input për datën -->
            <input type="text" id="datepicker" name="date" required readonly>

            <button type="submit">Shiko Rezervimet</button>
        </form>

        <!-- Shfaqja e rezervimeve për datën e zgjedhur -->
        <?php if ($result !== null && $result->num_rows > 0): ?>
            <h2>Rezervimet për datën <?= isset($_GET['date']) ? $_GET['date'] : '' ?></h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Statusi</th>
                </tr>
                <?php
                $index = 1; // Krijo variabël për ID-në që fillon nga 1
                while ($row = $result->fetch_assoc()) {
                    // Kontrollo nëse ora është e zënë (ne e konsiderojmë si të zënë nëse ka një rezervim për atë orë)
                    $time = $row['time'];
                    $status = "E Lirë"; // Parazgjedhje është "E Lirë"
                    
                    // Kontrollo nëse kjo orë është e zënë
                    $check_sql = "SELECT COUNT(*) AS count FROM rezervimet WHERE date = '$selected_date' AND time = '$time'";
                    $check_result = $conn->query($check_sql);
                    $check_row = $check_result->fetch_assoc();
                    
                    if ($check_row['count'] > 0) {
                        $status = "E Zënë"; // Nëse ka një rezervim për atë orë, është e zënë
                    }

                    // Shfaq të dhënat
                    echo "<tr>";
                    echo "<td>" . $index . "</td>"; // ID që fillon nga 1 dhe rritet për çdo rresht
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . $row['time'] . "</td>";
                    echo "<td>" . $status . "</td>";
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
                showAnim: "slideDown"  // Animacioni për shfaqjen e kalendarit
            });
        });
    </script>
</body>
</html>
