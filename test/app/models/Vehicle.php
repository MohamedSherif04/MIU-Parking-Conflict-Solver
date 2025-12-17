<?php

class Vehicle extends Model
{
    public function addVehicle($data)
    {
        $this->db->query("INSERT INTO vehicles (license_plate, owner_id, model, color) VALUES (:plate, :owner, :model, :color)");
        $this->db->bind(':plate', $data['license_plate']);
        $this->db->bind(':owner', $data['owner_id']);
        $this->db->bind(':model', $data['model']);
        $this->db->bind(':color', $data['color']);
        return $this->db->execute();
    }

    public function findVehicleByPlate($plate)
    {
        $this->db->query("SELECT * FROM vehicles WHERE license_plate = :plate");
        $this->db->bind(':plate', $plate);
        return $this->db->single();
    }

    public function getVehiclesByUserId($user_id)
    {
        $this->db->query("SELECT * FROM vehicles WHERE owner_id = :uid");
        $this->db->bind(':uid', $user_id);
        return $this->db->resultSet();
    }

    // --- NEW METHOD ---
    public function updateVehicle($data)
    {
        // We do not allow changing License Plate here as it is the Primary Key
        $this->db->query("UPDATE vehicles SET model = :model, color = :color WHERE license_plate = :plate AND owner_id = :uid");
        $this->db->bind(':model', $data['model']);
        $this->db->bind(':color', $data['color']);
        $this->db->bind(':plate', $data['license_plate']);
        $this->db->bind(':uid', $data['owner_id']);
        return $this->db->execute();
    }
}