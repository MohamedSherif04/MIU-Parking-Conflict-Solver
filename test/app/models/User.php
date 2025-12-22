<?php

class User extends Model
{
    public function register($data)
    {
        $this->db->query("INSERT INTO users (university_id, full_name, phone_number, password_hash) VALUES (:uid, :name, :phone, :pass)");
        $this->db->bind(':uid', $data['university_id']);
        $this->db->bind(':name', $data['full_name']);
        $this->db->bind(':phone', $data['phone_number']);
        $this->db->bind(':pass', password_hash($data['password'], PASSWORD_DEFAULT));
        return $this->db->execute();
    }

    public function login($university_id, $password)
    {
        $this->db->query("SELECT * FROM users WHERE university_id = :uid");
        $this->db->bind(':uid', $university_id);
        $row = $this->db->single();

        if ($row) {
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }

    public function findUserByUniversityId($university_id)
    {
        $this->db->query("SELECT * FROM users WHERE university_id = :uid");
        $this->db->bind(':uid', $university_id);
        return $this->db->single();
    }
    public function getAllUsers()
    {
        $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    public function deleteUser($id)
    {
        $this->db->query("DELETE FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE user_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateUser($data) {
        $this->db->query("UPDATE users SET full_name = :name, phone_number = :phone WHERE user_id = :id");
        $this->db->bind(':name', $data['full_name']);
        $this->db->bind(':phone', $data['phone_number']);
        $this->db->bind(':id', $data['user_id']);
        return $this->db->execute();
    }
}