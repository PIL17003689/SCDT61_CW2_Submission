<?php

// Class for handling member-related operations
class MemberController {

    // Protected property to store the database controller instance
    protected $db;

    // Constructor to initialize the MemberController with a DatabaseController instance
    public function __construct(DatabaseController $db)
    {
        // Assign the provided DatabaseController instance to the db property
        $this->db = $db;
    }

    // Method to retrieve a member record by its ID
    public function get_member_by_id(int $id)
    {
        // SQL query to select a member by its ID
        $sql = "SELECT * FROM users WHERE id = :id";
        $args = ['id' => $id];
        // Execute the query and return the fetched member record
        return $this->db->runSQL($sql, $args)->fetch();
    }

    // Method to retrieve a member record by email
    public function get_member_by_email(string $email)
    {
        // SQL query to select a member by email
        $sql = "SELECT * FROM users WHERE email = :email";
        $args = ['email' => $email];
        // Execute the query and return the fetched member record
        return $this->db->runSQL($sql, $args)->fetch();
    }

    // Method to retrieve all member records
    public function get_all_members()
    {
        // SQL query to select all members
        $sql = "SELECT * FROM users";
        // Execute the query and return all fetched records
        return $this->db->runSQL($sql)->fetchAll();
    }

    // Method to update an existing member record
    public function update_member(array $member)
    {
        // SQL query to update a member's information
        $sql = "UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email WHERE id = :id";
        return $this->db->runSQL($sql, $member);
        $sql = "UPDATE user_roles SET user_roles = :roles WHERE user_id = :id";
        // Execute the query with the provided updated data
        return $this->db->runSQL($sql, $member);
    }

    // Method to delete a member record by its ID
    public function delete_member(int $id)
    {
        // SQL query to delete a member by its ID
        $sql = "DELETE FROM users WHERE id = :id";
        $args = ['id' => $id];
        // Execute the query
        return $this->db->runSQL($sql, $args);
    }

    // Method to register a new member
    public function register_member(array $member)
    {
        try {
            // SQL query to insert a new member record
            $sql = "INSERT INTO users(firstname, lastname, email, password) 
                    VALUES (:firstname, :lastname, :email, :password)";

            // Execute the query with the provided member data
            $this->db->runSQL($sql, $member);

            // SQL query to auto assign the user role
            $sql = "INSERT INTO user_roles (user_id, role_id) 
                    VALUES (LAST_INSERT_ID(), (SELECT ID FROM Roles WHERE name = 'user'));";

            // Execute the query with the provided member data
            $this->db->runSQL($sql, $member);
            return true;

        } catch (PDOException $e) {
            // Handle specific error codes (like duplicate entry)
            if ($e->getCode() == 23000) { // Possible duplicate entry
                return false;
            }
            throw $e;
        }
    }   

    // Method to validate member login
    public function login_member(string $email, string $password)
    {
        // Retrieve the member by email
        $member = $this->get_member_by_email($email);

        // If member exists, verify the password
        if ($member) {
            $auth = password_verify($password,  $member['password']);
            // Return member data if authentication is successful, otherwise return false
            return $auth ? $member : false;
        }
        return false;
    }

    public function get_member_by_role($userID) 
    {
        try {
            $sql = "SELECT users.*, roles.name AS role
                    FROM users
                    JOIN user_roles ON users.id = user_roles.user_id
                    JOIN roles ON user_roles.role_id = roles.id
                    WHERE users.id = :user_id;";

            $stmt = $this->db->runSQL($sql, ['user_id' => $userID]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log or output the error for debugging
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    public function update_member_roles() 
    {
        $sql = "UPDATE user_roles SET role_id = :role_id WHERE user_id = :user_id";
        $this->db->runSQL($sql);
        return true;
    }
}
?>
