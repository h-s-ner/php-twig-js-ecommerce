<?php
namespace App\Service;
use App\Model\AuthModel;
use App\Repository\UserRepository;

class FormService
{
    public function validateRequired(array $fields): array
    {
        $errors = [];
        foreach ($fields as $fieldName => $value) {
            if (empty(trim($value))) {
                $errors[] = ucfirst($fieldName) . " is required";
            }
        }
        return $errors;
    }
    public function validateEmail(string $email, ?UserRepository $userRepository = null): array
    {
        $errors = [];
        if (empty($email)){
            $errors[]="Email is required";
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[]="Invalid Email";
        }
        else if ($userRepository != null && is_array($userRepository->findUserByEmail($email))){
            $errors[]="Email already exists";
        }
        return $errors;
    }

    public function validatePassword(string $password,int $minLength = 6): array
    {
        $errors = [];
        if (empty($password)) {
            $errors[] = "Password is required";
        }
        else{
            $errors = array_merge($errors,$this->validatePasswordLength($password,$minLength));
        }
        return $errors;
    }
    public function validatePasswordLength(string $password,int $minLength = 6)
    {
         $errors = [];
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least $minLength characters";
        }
        return $errors;

    }

    public function validatePrice($price, float $min = 0, float $max = 999999.99): array
    {
        $errors = [];

        if ($price === '' || $price === null) {
            $errors[] = "Price is required";
        } elseif (!is_numeric($price)) {
            $errors[] = "Price must be a number";
        } else {
            $price = (float) $price;
            if ($price < $min || $price > $max) {
                $errors[] = "Price must be between $min and $max";
            }
        }

        return $errors;
    }

    /**
     * Validiert Lagerbestand
     */
    public function validateStock($stock): array
    {
        $errors = [];

        if ($stock === '' || $stock === null) {
            $errors[] = "Stock is required";
        } elseif (!ctype_digit((string)$stock)) {
            $errors[] = "Stock must be a non-negative integer";
        } else {
            $stock = (int) $stock;
        }

        return $errors;
    }
    public function validateImage(array $file, array $allowedExtensions = ['jpg','jpeg','png','gif'], int $maxSize = 2_000_000): array
    {
        $errors = [];

        if (empty($file['name'])) {
            return $errors; // optional
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            $errors[] = "Invalid image type";
        }

        if ($file['size'] > $maxSize) {
            $errors[] = "Image too large";
        }

        if ($file['error'] !== 0) {
            $errors[] = "Upload error";
        }

        // MIME-Type Check
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, ['image/jpg','image/jpeg','image/png','image/gif'])) {
            $errors[] = "Invalid image content";
        }

        return $errors;
    }
}
?>