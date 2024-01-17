<?php 
    session_start(); 
    require_once 'inc/functions.php';

    // Checks whether the user is logged in
    if (!isset($_SESSION['user']))
    {
        // Redirects if not
        redirect('login', ["error" => "You need to be logged in to view this page"]);
    }

    $title = 'Member Page'; 
    require __DIR__ . "/inc/header.php"; 

    // Grabs the current session role
    $currentUser = $_SESSION['user']['ID'];
    $currentUserInfo = $controllers->members()->get_member_by_role($currentUser);

    if ($currentUserInfo)
    {
        // Checks what the current session role is
        $currentUserRole = $currentUserInfo['role'];
        switch ($currentUserRole) {
            // Relocation
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
        // Error handling
        echo "User info not found or error occurred.";
        exit();
    }
?>

<h1>Welcome <?= $_SESSION['user']['firstname'] ?? 'Member' ?>!</h1>

<?php require __DIR__ . "/inc/footer.php"; ?>