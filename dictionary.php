<?php
  // Connect function to connect to local mysql as root.
  // Pass in boolean variable for connecting to DB directly.
  function connect ($connectDB) {
    if ($connectDB === true) {
      return new mysqli('localhost', 'root', '', 'slots');
    } else {
      return new mysqli('localhost', 'root');
    }
  }

  // Function to reset database item with default settings.
  function resetDB () {
    // Set variable to connect with db.
    $conn = connect(true);
    
    // SQL update query for defaults.
    $sql = 'UPDATE users
            SET Credits=777, Spins=123
            WHERE ID=123123';
    
    // If the query was successful:
    if($conn->query($sql) === true) {
      
      // Create new SQL query string && results variable
      $sql = 'SELECT * FROM users WHERE ID=123123';
      $results = $conn->query($sql);

      if ($results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {
          
          // Create variable to gather average return.
          // Lifetime Credits / Lifetime Spins
          $avg = $row['Credits'] / $row['Spins'];
          
          // Create array/object for JSON encode.
          $object = array('ID' => $row['ID'], 'Name' => $row['Name'], 
                          'Credits' => $row['Credits'], 'Spins' => $row['Spins'],
                          'Average' => $avg);
          
          echo json_encode($object);
          $conn->close();
        }
      }
    }
  }


  // Function to initialize the database & demonstration.
  function init ($db) {      
    // Set variable to connect with mysql.
    $conn = connect(false);
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    // SQL use query to check for db existance
    $sql = 'USE ' . $db;

    // If the database was not found,
    // then create the database and execute checkTable function.
    if ($conn->query($sql) === false) {
      $sql = 'CREATE DATABASE ' . $db;
      if ($conn->query($sql) === true) {      
        $conn->close();
        checkTable();
      }
    }
  }

  // Function to check if table exists and initialize it.
  function checkTable () {
    // Set variable to connect with db.
    $conn = connect(true);
    
    if ($conn->connect_error) {
      die('Connection failed: ' . $conn->connect_error);
    }
    
    // SQL query to create table for users
    $sql = 'CREATE TABLE users (
      ID INT(6) UNSIGNED PRIMARY KEY,
      Name VARCHAR(30) NOT NULL,
      Credits INT(11) NOT NULL,
      Spins INT(11) NOT NULL,
      Salt VARCHAR(24) NOT NULL
    )';
    
    // If query to create table for users succeeds:
    if ($conn->query($sql) === true) {
      // then create random salt value
      $charset = '0123456789abcdefghijklmnopqrstuvwxyz';
      $saltLength = 24;

      $salt = "";
      for ($i = 0; $i < $saltLength; $i++) {
        $salt .= $charset[mt_rand(0, strlen($charset) - 1)];
      }
      
      // SQL query to insert default data into table.
      // Player ID, Player Name, Credits, Lifetime Spins, Salt Value
      $sql = 'INSERT INTO users (ID, Name, Credits, Spins, Salt)
        VALUES (123123, "Joan", 777, 123, "' . $salt . '")';
      if ($conn->query($sql) === true) {
        $conn->close();
      }
    }
  }

  // Function to validate the hash and update values as necessary.
  function validate ($hashType, $id, $coinsBet, $coinsWon) {
    // Set variable to connect with db.
    $conn = connect(true);
    
    if ($conn->connect_error) {
      die('Connection failed: ' . $conn->connect_error);
    }
    
    // SQL query to get user from table    
    $sql = 'SELECT * FROM users WHERE ID = ' . $id;
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {    
        
        // FOR TESTING ONLY
        // If the hash type submit from testing is set to fail,
        // then create bogus md5 hash value;
        // else, set the hash value to a valid md5 hash.
        if ($hashType === 'fail') {   
            $hash = md5('fail');
        } else {
            $hash = md5($row['Salt']);    
        }
        
        // Variable to hold valid md5 hash of salt value.
        $valiHash = md5($row['Salt']);
        
        // If supplied hash is the same as the valid md5 hash,
        // then compute winnings, and update as necessary.
        if ($valiHash === $hash) {
          
          // If value of coins won is less than 0,
          // then subtract the coins bet value from the inital
          // credits value. Else, get the negative coins bet value
          // and add this too the coins won value, then add the final
          // value to the initial credits value.
          if ($coinsWon < 0) {
            $creditsFinal = $row['Credits'] - $coinsBet;
          } else {
            $creditsFinal = (-1 * $coinsBet) + $coinsWon;
            $creditsFinal = $row['Credits'] + $creditsFinal;
          }
          
          // Increment spins value
          $spinsFinal = $row['Spins']++;
          
          // SQL query to update users with new values
          $sql = 'UPDATE users 
                  SET Credits=' . $creditsFinal . ', 
                      Spins=' . $spinsFinal . ' 
                  WHERE ID=' . $id;
          
          // If SQL query succeeded, then create variable to gather average return.
          if ($conn->query($sql) === true) {
             // Lifetime Credits / Lifetime Spins
            $avg = $creditsFinal / $spinsFinal;
            
            // Create array/object for JSON encode.
            $object = array('ID' => $id, 'Name' => $row['Name'], 
                            'Credits' => $creditsFinal, 'Spins' => $spinsFinal,
                            'Average' => $avg);
            
            echo json_encode($object);
            $conn->close();
          }
        } else {
          // If supplied hash is NOT the same as the valid md5 hash,
          // then echo out statement, and display JSON object.
          echo 'Hash does not validate<br /><br />';
          
          $sql = 'SELECT * FROM users WHERE ID=123123';
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              
              // Create variable to gather average return.
              // Lifetime Credits / Lifetime Spins
              $avg = $row['Credits'] / $row['Spins'];
            
              // Create array/object for JSON encode.
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