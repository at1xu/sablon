<?php
$host = "localhost";
$dbname = "formular";
$port = 3306;
$username = "root";
$password = "";
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);

try {
    $conn = new PDO("mysql:host={$host};dbname={$dbname};port={$port}", $username, $password, $options);
    
    $meno = filter_input(INPUT_GET, 'meno', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
    $sprava = filter_input(INPUT_GET, 'sprava', FILTER_SANITIZE_STRING);

    if ($meno && $email && $sprava) {
        $sql = "INSERT INTO formular (meno, email, sprava) VALUES (:meno, :email, :sprava)";
        $statement = $conn->prepare($sql);

        $insert = $statement->execute(array(':meno' => $meno, ':email' => $email, ':sprava' => $sprava));

        if ($insert) {
            header("Location: http://localhost/cvicnasablona/thankyou.php");
        } else {
            echo "Failed to insert data.";
        }
    } else {
        echo "Invalid input data.";
    }
} catch (PDOException $e) {
    die("Chyba pripojenia: " . $e->getMessage());
} finally {
    $conn = null;
}


