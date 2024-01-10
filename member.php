<?php 
    session_start(); 
    require_once 'inc/functions.php';

    if (!isset($_SESSION['user']))
    {
        redirect('login', ["error" => "You need to be logged in to view this page"]);
    }

    $title = 'Member Page'; 
    require __DIR__ . "/inc/header.php"; 


    $currentUser = $_SESSION['user']['ID'];
    $currentUserInfo = $controllers->members()->get_member_by_role($currentUser);

    if ($currentUserInfo)
    {
        $currentUserRole = $currentUserInfo['role'];
        switch ($currentUserRole) {
            case 'admin': 
                header("Location: index-admin.php");
                exit();
            case 'user':
                header("Location: index.php");
                exit();
        }
    } 
    else 
    {
        echo "User info not found or error occurred.";
        exit();
    }
?>

<h1>Welcome <?= $_SESSION['user']['firstname'] ?? 'Member' ?>!</h1>

<?php require __DIR__ . "/inc/footer.php"; ?>