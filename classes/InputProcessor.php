<?php

// Class for processing various types of input
class InputProcessor {

    // Static method to process and validate a string
    public static function processString(string $string, int $max = 0): array {

        // Check if the string is empty
        if (empty($string)) {
            return self::returnInput("String is empty.", false);
        }

        // Sanitize the string to prevent XSS attacks
        $string = htmlspecialchars($string);

        // Set a maximum length for the string if not already set
        $max = $max == 0 ? strlen($string) : $max;
        
        // Validate the string length
        if (strlen($string) <= $max) {
            return self::returnInput($string, true);
        } else {
            return self::returnInput("String cannot be more than $max characters.", false);
        }
               
    }

    // Static method to process and validate an email address
    public static function processEmail(string $email): array {

        // Check if the email is empty
        if (empty($email)) {
            return self::returnInput("Email is empty.", false);
        }

        // Sanitize the email to ensure it's in a proper format
        $value = filter_var($email, FILTER_SANITIZE_EMAIL);

        // Validate the sanitized email
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return self::returnInput($email, true);
        } else {
            return self::returnInput("Email is invalid.", false);
        }
               
    }

    // Static method to process and validate a password
    public static function processPassword(string $password, string $password_v = null) {

        // Check if the verification password is provided and matches
        if (!empty($password_v)) {
            if ($password != $password_v) {
                return self::returnInput("Passwords do not match.", false);
            }
        }

        // Check if the password is empty
        if (empty($password)) {
            return self::returnInput("Password is empty.", false);
        }

        // Sanitize the password
        $password = htmlspecialchars($password);

        // Validate the password against a regex pattern
        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            return self::returnInput($password, true);
        } else {
            return self::returnInput('Password must have a minimum of 8 characters, at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character', false);
        }

    }

    public static function processFile(string $fieldName, array $allowedTypes = [], int $maxFileSize = 0): array {
        // Check if the file was uploaded without errors
        if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return self::returnInput("Error uploading file.", false);
        }
    
        // Check if the file is an allowed type
         $fileType = $_FILES[$fieldName]['type'];
        if (!empty($allowedTypes) && !in_array($fileType, $allowedTypes)) {
            return self::returnInput("Invalid file type.", false);
        }
        // Check if the file size exceeds the maximum limit
       if ($maxFileSize > 0 && $_FILES[$fieldName]['size'] > $maxFileSize) {
            return self::returnInput("File size exceeds the maximum limit.", false);
        }
    
        // Move the uploaded file to a specified directory
        $uploadDirectory = './uploads/';
        $uploadedFileName = basename($_FILES[$fieldName]['name']);
        $uploadFilePath = $uploadDirectory . $uploadedFileName;
    
        // File upload successful, return the uploaded file path
        return self::returnInput($uploadFilePath, true);
    }

    // Private static method to format the return value of the input processing
    private static function returnInput(string $value, bool $isValid) : array {
        return [
            'value' => $isValid ? $value : '', // Return the value if valid
            'error' => $isValid ? '' : $value, // Return the error message if invalid
            'valid' => $isValid // Boolean flag for validity
        ];
    }
}

?>
