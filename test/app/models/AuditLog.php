<?php

class AuditLog extends Model
{
    public function log($actor_id, $action_type, $details)
    {
        $this->db->query("INSERT INTO audit_log (actor_id, action_type, details) VALUES (:actor, :action, :details)");
        $this->db->bind(':actor', $actor_id);
        $this->db->bind(':action', $action_type);
        $this->db->bind(':details', $details);
        return $this->db->execute();
    }
}
