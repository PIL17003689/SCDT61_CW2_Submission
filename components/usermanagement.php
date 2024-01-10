<?php

require_once './inc/functions.php';

// Initialize a variable to store any error message from the query string
$message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Retrieving all members
$member = $controllers->members()->get_all_members();

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    // Check the user role
    $currentUser = $_SESSION['user']['ID'];
    $currentUserInfo = $controllers->members()->get_member_by_role($currentUser);
    $currentUserRole = $currentUserInfo['role'];
} else {
    redirect('login', ["error" => "You need to be logged in to view this page"]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if((isset($_POST['UpdateMemberButton'])))
    {
        $fname = InputProcessor::ProcessString($_POST['firstname']);
        $lname = InputProcessor::ProcessString($_POST['lastname']);
        $role = InputProcessor::ProcessString($_POST['role']);

        $args = [
            'id' => $_POST['memId'],
            'name' => $fname['value'],
            'description' => $lname['value'],
            'role' => $role['value'],
        ];
        $args_ = [

        ];
        // Work on <---

        $oldMem = $controllers->Members()->update_member($args);
        $oldRoles = $controllers->Members()->update_user_roles($args_);
        if ($oldMem && $oldRoles) 
        {
            echo 'Success.';
            redirect('UserManagement');
        }
    }
    elseif(isset($_POST['DeleteMemberButton'])) 
    {
        // Include necessary files and initialize controllers
        $delMember = $controllers->Member()->delete_member($memId);

        if ($delMember) {
            // Optionally, redirect or perform other actions after successful deletion
            redirect('UserManagement');
        } else {
            // Handle deletion failure
            echo 'Failed to delete item.';
        }
    }
}
?>

<!-- HTML for displaying the equipment inventory -->
<div class="container mt-4">
    <h2>User Inventory</h2> 
    <table class="table table-striped"> 
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th> 
                <th>Roles</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($member as $mem): ?> <!-- Loop through each equipment item -->
                <tr>
                    <td><?= htmlspecialchars_decode($mem['ID'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars_decode($mem['firstname'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars_decode($mem['lastname'], ENT_QUOTES) ?></td>
                    <td><?php echo $memRole = $controllers->members()->get_member_by_role($mem['ID'])['role']; ?></td>
                    <td> <?php
                    if($currentUserRole == "admin"): ?>
                        <button class="btn btn-info" type="button" data-bs-toggle="modal" data-bs-target="#UpdateUser<?= $mem['ID'] ?>">Update User</button>
                        <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#DeleteUser<?= $mem['ID'] ?>">Delete User</button>
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php foreach ($member as $mem): ?>
<!-- Updating a User --> 
<div class="modal" tabindex="-1" role="dialog" id="UpdateUser<?= $mem['ID'] ?>" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update an Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Add your modal content here -->
        <p>This is the content of the pop-up.</p>
        <?php if (isset($mem['ID'])): ?>
        <form id="updateMemberForm<?= $mem['ID'] ?>" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <input type="hidden" name="ID" value="<?= $mem['ID'] ?>">
                <div class="mb-3">
                    <label for="memFirstName" class="form-label">Item Name:</label>
                    <input type="text" class="form-control" id="memFirstName" name="firstname" value="<?= htmlspecialchars($mem['firstname']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="memLastName" class="form-label">Item Description:</label>
                    <textarea class="form-control" id="memLastName" name="lastname" required><?= htmlspecialchars($mem['lastname']) ?></textarea>
                </div>
                <div class="mb-3">
                    <div class="dropdown">
                        <button class="dropbtn">Dropdown</button>
                        <label for="memRole" class="form-label">User Role:</label>
                        <textarea class="form-control" id="memRole" name="role" required><?= htmlspecialchars($memRole) ?></textarea>
                    </div>
                </div>
                <!-- Include other fields for updating, such as name, description, etc. -->
                <input type="hidden" name="memId" value=<?=$mem['ID']?>/>
            <button name= "UpdateMemberButton" type="submit" class="btn btn-primary">Update Item</button>
        </form>
        <?php endif ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Deleting an Item -->
<?php foreach ($member as $mem): ?>
<div class="modal" tabindex="-1" role="dialog" id="DeleteUser<?= $mem['ID'] ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete an Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add your modal content here -->
                <p>Are you sure you want to delete this item?</p>
                <form id="deleteMemberForm<?= $mem['ID'] ?>" action=" <?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" name="id" value="<?= $mem['ID'] ?>">
                    <button name= "DeleteMemberButton" type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> 
<?php endforeach; ?>