<html>
  <head>
    <title>
        SGI Problem #2
    </title>
  </head>

  <body>
    <?php
      // Require dictionary functions
      require('./dictionary.php');
    
      // Initiate database
      init('slots');
    
      if (isset($_GET['path'])) {
        switch ($_GET['path']) {
          case 'bgWin':
            validate('happy', 123123, 25, 500);
            break;
            
          case 'smWin':
            validate('happy', 123123, 5, 40);
            break;
            
          case 'bgLoss':
            validate('happy', 123123, 450, -500);
            break;
            
          case 'smLoss':
            validate('happy', 123123, 50, -40);
            break;
            
          case 'reset':
            resetDB(true);
            break;
            
          case 'fail':
            validate('fail', 123123, 100, 100);
            break;
            
          default:
            resetDB(true);
        }
      } else {
        resetDB(true);
      }
    ?>
    
    <div style="margin-top: 50px;">
      <button>
        <a href='index.php?path=bgWin'>
          Big Win
        </a>
      </button>
      
      <button>
        <a href='index.php?path=smWin'>
          Small Win
        </a>
      </button>
      
      <button>
        <a href='index.php?path=bgLoss'>
          Big Loss
        </a>
      </button>
      
      <button>
        <a href='index.php?path=smLoss'>
          Small Loss
        </a>
      </button>
      
      <button>
        <a href='index.php?path=fail'>
          Bad Hash
        </a>
      </button>

      <button>
        <a href='index.php?path=reset'>
          Reset Data
        </a>
      </button>    
    </div>
  </body>
</html>