<?php
// Inkludo skedarin e lidhjes me bazën e të dhënave
include 'db.php';

// Shto rezervimin nëse formulari është dërguar
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['update'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $contact = $_POST['contact'];
    $totalAmount = $_POST['totalAmount']; // Shuma totale
    $prepaymentAmount = $_POST['prepaymentAmount']; // Shuma e para-pagesës
    $debtAmount = $totalAmount - $prepaymentAmount; // Kalkulimi i borxhit
    $comment = $_POST['comment']; // Komenti

    // Query për shtimin e rezervimit në bazën e të dhënave
    $sql = "INSERT INTO rezervimet (first_name, last_name, date, time, contact, total_amount, prepayment_amount, debt_amount, comment)
            VALUES ('$firstName', '$lastName', '$date', '$time', '$contact', '$totalAmount', '$prepaymentAmount', '$debtAmount', '$comment')";

    if ($conn->query($sql) === TRUE) {
        echo "Rezervimi u shtua me sukses!";
    } else {
        echo "Gabim: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frizer Admin</title>
    <link rel="stylesheet" href="style.css">
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

        input[type="text"], input[type="date"], input[type="time"] {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            font-size: 1rem;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4cae4c;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Frizer Admin</h1>

        <!-- Formulari për shtimin e rezervimeve -->
        <form method="POST">
            <label for="firstName">Emri:</label>
            <input type="text" id="firstName" name="firstName" required>

            <label for="lastName">Mbiemri:</label>
            <input type="text" id="lastName" name="lastName" required>

            <label for="date">Data:</label>
            <input type="date" id="date" name="date" required>

            <label for="time">Ora:</label>
            <input type="time" id="time" name="time" required>

            <label for="contact">Kontakti (Numri i telefonit):</label>
            <input type="text" id="contact" name="contact" required>

            <label for="totalAmount">Shuma Totale (Për shërbimin):</label>
            <input type="number" id="totalAmount" name="totalAmount" step="0.01" min="0" required>

            <label for="prepaymentAmount">Shuma e Para-pagesës:</label>
            <input type="number" id="prepaymentAmount" name="prepaymentAmount" step="0.01" min="0" required>

            <label for="debtAmount">Borxhi (Shuma totale - Para-pagese):</label>
            <input type="number" id="debtAmount" name="debtAmount" step="0.01" min="0" disabled>

            <label for="comment">Komenti:</label>
            <textarea id="comment" name="comment"></textarea>

            <button type="submit">Rezervo</button>
        </form>
    </div>
</body>
</html>
