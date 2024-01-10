<?php

require_once './inc/functions.php';

// Retrieve all equipment data using the equipment controller
$equipment = $controllers->equipment()->get_all_equipments();

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
    if((isset($_POST['AddItemButton'])) || (isset($_POST['UpdateItemButton']))){
        $name = InputProcessor::ProcessString($_POST['name']);
        $description = InputProcessor::ProcessString($_POST['description']);
        if ($_FILES['image']['error'] === 0) {
            // Get the temporary file path
            $sourcePath = $_FILES['image']['tmp_name'];
        
            // Define the directory
            $uploadDirectory = './uploads';
        
            // Create the directory if it doesn't exist
            if (!file_exists($uploadDirectory) && !is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0755, true);
            }
        
            // Construct the directory with a unique name 
            $destinationPath = $uploadDirectory . '/' . uniqid('item_', true) . '_' . basename($_FILES['image']['name']);
        
            // Move file to directory
            if (move_uploaded_file($sourcePath, $destinationPath)) {
                $image = file_get_contents($destinationPath); // Read the file content
        
                $args = [
                    'name' => $name['value'],
                    'description' => $description['value'],
                    'image' => $destinationPath,
                ];
                
                if(isset($_POST['AddItemButton']))
                {
                    $newItem = $controllers->equipment()->create_equipment($args);
                    if ($newItem) 
                    {
                        echo 'Success.';
                        redirect('Inventory');
                    }
                } elseif(isset($_POST['UpdateItemButton'])) 
                {
                    $args['id'] = $_POST['itemId'];
                    $oldItem = $controllers->equipment()->update_equipment($args);
                    if ($oldItem) 
                    {
                        echo 'Success.';
                        redirect('Inventory');
                    }
                }
            } 
        }
    } 
    elseif(isset($_POST['DeleteItemButton'])) {
        $itemId = (int)$_POST['id'];
        $delItem = $controllers->equipment()->delete_equipment($itemId);

        if ($delItem) {
            // Redirecting the user when completed 
            redirect('Inventory');
        } else {
            echo 'Failed to delete item.';
        }
    }
}
?>

<!-- HTML for displaying the equipment inventory -->
<div class="container mt-4">
    <h2>Equipment Inventory</h2> 
    <table class="table table-striped"> 
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th> 
                <th>Name</th> 
                <th>Description</th> 
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipment as $equip): ?> <!-- Loop through each equipment item -->
                <tr>
                    <td><?= htmlspecialchars($equip['id']) ?></td>
                    <td>
                        <?php
                        // Determine MIME type based on file extension
                        $mimeType = 'image/jpeg';

                        // Convert BLOB data to base64 encoding
                        $base64Data = base64_encode($equip['image']);
                        // Generate the data URI
                        $dataUri = 'data:' . $mimeType . ';base64,' . $base64Data;
                        ?>

                        <?php if ($mimeType === 'video/mp4'): ?>
                            <!-- Video -->
                            <video width="100" height="auto" controls>
                                <source src="<?= htmlspecialchars($dataUri) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <!-- Image -->
                            <img src="<?= $equip['image'] ?>" alt="Image of <?= htmlspecialchars($equip['description']) ?>" 
                                style="width: 100px; height: auto;">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($equip['name']) ?></td>
                    <td><?= htmlspecialchars($equip['description']) ?></td>
                    <td> <?php
                    if($currentUserRole == "admin"): ?>
                        <button class="btn btn-info" type="button" data-bs-toggle="modal" data-bs-target="#UpdateItem<?= $equip['id'] ?>">Update Item</button>
                        <button class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#DeleteItem<?= $equip['id'] ?>">Delete Item</button>
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php 
    if($currentUserRole == "admin")
    {
        echo '<button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#AddItem">Add Item</button>';
    }?>
</div>

<!-- Adding a New Item -->
<div class="modal" tabindex="-1" role="dialog" id="AddItem">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add a New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add your modal content here -->
                <form id="addItemForm" method="post" enctype="multipart/form-data" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                    <div class="mb-3">
                        <label for="itemName" class="form-label">Item Name:</label>
                        <input type="text" class="form-control" id="itemName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="itemDescription" class="form-label">Item Description:</label>
                        <textarea class="form-control" id="itemDescription" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Item Image:</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <button name="AddItemButton" type="submit" class="btn btn-primary">Add Item</button>
                </form>
            </div>
            <div class="modal-footer">
                
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php foreach ($equipment as $equip): ?>
<!-- Updating an Item --> 
<div class="modal" tabindex="-1" role="dialog" id="UpdateItem<?= $equip['id'] ?>" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update an Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Add your modal content here -->
        <p>This is the content of the pop-up.</p>
        <?php if (isset($equip['id'])): ?>
        <form id="updateItemForm<?= $equip['id'] ?>" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $equip['id'] ?>">
                <div class="mb-3">
                    <label for="itemName" class="form-label">Item Name:</label>
                    <input type="text" class="form-control" id="itemName" name="name" value="<?= htmlspecialchars($equip['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="itemDescription" class="form-label">Item Description:</label>
                    <textarea class="form-control" id="itemDescription" name="description" required><?= htmlspecialchars($equip['description']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Item Image:</label> 
                    <img src="<?= $equip['image'] ?>" style="width: 100px; height: auto;">
                    <input type="file" class="form-control" id="image" name="image" required>
                </div>
                <!-- Include other fields for updating, such as name, description, etc. -->
                <input type="hidden" name="itemId" value=<?=$equip['id']?>/>
            <button name= "UpdateItemButton" type="submit" class="btn btn-primary">Update Item</button>
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
<?php foreach ($equipment as $equip): ?>
<div class="modal" tabindex="-1" role="dialog" id="DeleteItem<?= $equip['id'] ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete an Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <p>Are you sure you want to delete this item?</p>
                <!-- Using PHP_Self refers to the current page to execute the action on -->
                <form id="deleteItemForm<?= $equip['id'] ?>" action=" <?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" name="id" value="<?= $equip['id'] ?>">
                    <button name= "DeleteItemButton" type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div> 
<?php endforeach; ?>