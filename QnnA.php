<?php
class QnA {
  private $conn;

  public function __construct($host, $dbname, $port, $username, $password, $options) {
      try {
          $this->conn = new PDO("mysql:host={$host};dbname={$dbname};port={$port}", $username, $password, $options);
      } catch (PDOException $e) {
          die("Connection failed: " . $e->getMessage());
      }
  }



  public function getQnA() {
    $sql = "SELECT * FROM otazky";    
    $statement = $this->conn->query($sql);
    // Vykonanie dotazu na databáze
    $statement->execute();
    // Načítať výsledok dotazu do asociatívneho poľa
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if ($result) {
        echo '<div class="container">';
        foreach ($result as $row) {
            echo '<div class="accordion">
                    <div class="question">'.$row['otazka'] .
                    '</div>
                    <div class="answer">'. $row['odpoved'] .'</div>
                </div>';
        }
        echo '</div>';
    }    
    return $result;
}


public function insertQnA($otazka, $odpoved) {
    try {
        // Zabezpečiť, že sa nevkladá tá istá otázka a odpoveď
        $sql = "SELECT COUNT(*) FROM otazky WHERE otazka = :otazka AND odpoved = :odpoved";
        $checkqna = $this->conn->prepare($sql);

        $checkqna->bindValue(':otazka', $otazka);
        $checkqna->bindValue(':odpoved', $odpoved);

        $checkqna->execute();
        $count = $checkqna->fetchColumn();
        // Ak záznam už existuje, vypíše sa o tom správa a metóda sa ukončí
        if ($count > 0) {
            echo "Táto otázka a odpoveď už existujú v databáze.";
            return false;
        }
        // Vložiť novú otázku a odpoveď
        $sqlin = "INSERT INTO otazky (otazka, odpoved) VALUES (:otazka, :odpoved)";
        $sqlst = $this->conn->prepare($sqlin);
        $sqlst->bindParam(':otazka', $otazka);
        $sqlst->bindParam(':odpoved', $odpoved);
        $sqlst->execute();

        if ($sqlst) {
            header("Location: http://localhost/sablona/thankyou.php");
        } else {
            echo "Failed to insert data.";
        }

        echo "Otázka a odpoveď boli úspešne vložené do databázy.";
        return true;
    } catch (PDOException $e) {
        echo "Chyba pri vkladaní dát do databázy: " . $e->getMessage();
        return false;
    }
}

  
  public function __destruct() {
      $this->conn = null;
  }
}
