<?php
  function connect ($connectDB) {
    if ($connectDB === true) {
      return new mysqli('localhost', 'root', '', 'slots');
    } else {
      return new mysqli('localhost', 'root');
    }
  }

  function resetDB ($displayData) {
    $conn = connect(true);
    $sql = 'UPDATE users
            SET Credits=777, Spins=123
            WHERE ID=123123';
    
    if($conn->query($sql) === true) {
      $sql = 'SELECT * FROM users WHERE ID=123123';
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $avg = $row['Credits'] / $row['Spins'];
          $object = array('ID' => $row['ID'], 'Name' => $row['Name'], 
                          'Credits' => $row['Credits'], 'Spins' => $row['Spins'],
                          'Average' => $avg);
          
          if ($displayData) {
            echo json_encode($object);
          }
          $conn->close();
        }
      }
    }
  }

  function init ($db) {      
    $conn = connect(false);
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    $sql = 'USE ' . $db;

    if ($conn->query($sql) === false) {
      $sql = 'CREATE DATABASE ' . $db;
      if ($conn->query($sql) === true) {      
        $conn->close();
        checkTable();
      }
    }
  }

  function checkTable () {
    $conn = connect(true);
    
    if ($conn->connect_error) {
      die('Connection failed: ' . $conn->connect_error);
    }
    
    $sql = 'CREATE TABLE users (
      ID INT(6) UNSIGNED PRIMARY KEY,
      Name VARCHAR(30) NOT NULL,
      Credits INT(11) NOT NULL,
      Spins INT(11) NOT NULL,
      Salt VARCHAR(24) NOT NULL
    )';
    
    if ($conn->query($sql) === true) {
      $charset = '0123456789abcdefghijklmnopqrstuvwxyz';
      $saltLength = 24;

      $salt = "";
      for ($i = 0; $i < $saltLength; $i++) {
        $salt .= $charset[mt_rand(0, strlen($charset) - 1)];
      }
      
      $sql = 'INSERT INTO users (ID, Name, Credits, Spins, Salt)
        VALUES (123123, "Joan", 777, 123, "' . $salt . '")';
      if ($conn->query($sql) === true) {
        $conn->close();
      }
    }
  }

  function validate ($hashType, $id, $coinsBet, $coinsWon) {
    $conn = connect(true);
    
    if ($conn->connect_error) {
      die('Connection failed: ' . $conn->connect_error);
    }
    
    $sql = 'SELECT * FROM users WHERE ID = ' . $id;
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {    
        
        if ($hashType === 'fail') {   
            $hash = md5('fail');             
        } else {
            $hash = md5($row['Salt']);    
        }
        
        $valiHash = md5($row['Salt']);
        if ($valiHash === $hash) {
          if ($coinsWon < 0) {
            $creditsFinal = $row['Credits'] - $coinsBet;
          } else {
            $creditsFinal = (-1 * $coinsBet) + $coinsWon;
            $creditsFinal = $row['Credits'] + $creditsFinal;
          }
          $spinsFinal = $row['Spins'] + 1;
          
          $sql = 'UPDATE users 
                  SET Credits=' . $creditsFinal . ', 
                      Spins=' . $spinsFinal . ' 
                  WHERE ID=' . $id;
          
          if ($conn->query($sql) === true) {
            $avg = $creditsFinal / $spinsFinal;
            
            $object = array('ID' => $id, 'Name' => $row['Name'], 
                            'Credits' => $creditsFinal, 'Spins' => $spinsFinal,
                            'Average' => $avg);
            
            echo json_encode($object);
            $conn->close();
          } else {
            die('Error updating: ' . $conn->error);
          }
        } else {
          echo 'Hash does not validate<br /><br />';
          
          $sql = 'SELECT * FROM users WHERE ID=123123';
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              $avg = $row['Credits'] / $row['Spins'];
              $object = array('ID' => $row['ID'], 'Name' => $row['Name'], 
                              'Credits' => $row['Credits'], 'Spins' => $row['Spins'],
                              'Average' => $avg);
              
              echo json_encode($object);
              $conn->close();
            }
          }
        }
      }
    }
  }
?>