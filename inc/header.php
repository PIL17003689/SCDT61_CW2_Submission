<!doctype html>
<?php 
  session_start();
  require_once 'inc/functions.php'; 
?>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
  </head>
  <body>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Online Shop</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
        <?php if (!isset($_SESSION['user'])) 
        {
          echo '
          <li class="nav-item">
            <a class="nav-link" href="./login.php">Login</a>
          </li>';
        }
        else { echo '
          <li class="nav-item">
            <a class="nav-link" href="./login.php">Log Out</a>
          </li>'; 

          $currentUser = $_SESSION['user']['ID'];
          $currentUserInfo = $controllers->members()->get_member_by_role($currentUser);
          if ($currentUserInfo) {
            $currentUserRole = $currentUserInfo['role'];
            switch ($currentUserRole) {
                case 'admin': 
                    echo '
                      <li class="nav-item active">
                        <a class="nav-link" href="./index-admin.php">Home</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="./Inventory.php">Equipment</a>
                      </li> 
                      <li class="nav-item">
                        <a class="nav-link" href="./UserManagement.php">Users</a>
                      </li> ';
                      break;
                case 'user':
                    echo '
                    <li class="nav-item active">
                      <a class="nav-link" href="./index.php">Home</a>
                    </li>';
                    break;
            }
          }

        }?>
    </ul>
  </div>
</nav>