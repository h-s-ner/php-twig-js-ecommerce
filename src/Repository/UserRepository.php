<?php
namespace App\Repository;

use App\DB\Database;
use App\Entity\User;
use \PDO;

class UserRepository extends Database
{
    public function findById(int $id) :?User
    {
        $sql = "SELECT * FROM users WHERE id= :id";
        $ps = $this->conn->prepare($sql);
        $ps->bindParam(':id',$id,PDO::PARAM_INT);
        $ps->execute();
        $row = $ps->fetch();
        if ($row){
            return User::fromDatabase($row);
        }
        return null;
    }

    public function findUserByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email= :email";
        $ps = $this->conn->prepare($sql);
        $ps->execute([
            ':email' => $email
        ]);
        $row = $ps->fetch();
        if($row)
        {
            return User::fromDatabase($row);
        }
        return null;
    }

    /**
     * @return User[]
    */
    public function findAll():array{
        $sql = "SELECT id, fname, lname, email, active, phone, role FROM users" ;
        $ps = $this->conn->prepare($sql);
        $ps->execute();
        $rows = $ps->fetchAll();
        return array_map(
            fn($row): User => User::fromDatabase($row),
            $rows
        );
    }

    public function updateUserDetails(
        int $id,
        string $fname,
        string $lname,
        string $phone
    )
    {
        $sql = "Update users set fname = :fname, lname = :lname, phone = :phone  WHERE id= :id";
        $ps = $this->conn->prepare($sql);
        $ps->bindValue(':id', $id, PDO::PARAM_INT);
        $ps->bindValue(':fname',$fname);
        $ps->bindValue(':lname',$lname);
        $ps->bindValue( ':phone', $phone);
        $ps->execute();
    }

    public function updateUserStatus(int $id, int $status){
        $sql = "Update users set active=:active WHERE id= :id";
        $ps = $this->conn->prepare($sql);
        $ps->bindValue(':id', $id, PDO::PARAM_INT);
        $ps->bindValue(':active',$status,PDO::PARAM_INT);
        $ps->execute();

    }
}
?>