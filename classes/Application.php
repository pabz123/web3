<?php
/**
 * Application Model
 * Handles job application operations
 */
class Application extends Model {
    protected $table = 'applications';
    
    /**
     * Get applications by user
     * @param int $userId
     * @return array
     */
    public function getByUser($userId) {
        $sql = "SELECT a.*, j.title, j.company, j.location 
                FROM {$this->table} a
                LEFT JOIN jobs j ON a.job_id = j.id
                WHERE a.user_id = ?
                ORDER BY a.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
    
    /**
     * Get applications by job
     * @param int $jobId
     * @return array
     */
    public function getByJob($jobId) {
        $sql = "SELECT a.*, u.name, u.email, u.phone 
                FROM {$this->table} a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.job_id = ?
                ORDER BY a.created_at DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $jobId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
    
    /**
     * Check if user has already applied to job
     * @param int $userId
     * @param int $jobId
     * @return bool
     */
    public function hasApplied($userId, $jobId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ? AND job_id = ?");
        $stmt->bind_param("ii", $userId, $jobId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['count'] > 0;
    }
    
    /**
     * Update application status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        return $this->update($id, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get application statistics
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                COUNT(*) as total_applications,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN status = 'reviewed' THEN 1 END) as reviewed_count,
                COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted_count,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
                FROM {$this->table}";
        
        $result = $this->conn->query($sql);
        $row = $result->fetch_assoc();
        return $row;
    }
}
