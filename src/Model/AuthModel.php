<?php
namespace App\Model;

use App\DB\Database;

Class AuthModel extends Database {

    public function register($fname, $lname, $phone, $email, $hashPassword):void
    {
        $sql = "INSERT INTO users(fname,lname,phone,email,password)
                values (:fname,:lname,:phone,:email,:password)";
        $ps = $this->conn->prepare($sql);
        $ps->execute([
            ':fname' => $fname,
            ':lname' => $lname,
            ':phone' => $phone,
            ':email' => $email,
            ':password' => $hashPassword
        ]);
    }

    public function getPasswordHash(int $userId): string
    {
        $sql = "SELECT password FROM users WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':id', $userId);

        $stmt->execute();

        return $stmt->fetchColumn(); // diret hashString
    }

    public function updatePassword(int $userId, string $newHashedPassword): bool
    {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
          $ps = $this->conn->prepare($sql);
        return $ps->execute([
            ':password' => $newHashedPassword,
            ':id' => $userId
        ]);
    }
}


?>
