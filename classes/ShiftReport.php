<?php

class ShiftReport {
    private $conn;
    private $userId;
    private $username;
    private $date;
    private $lastClosingTime = null;
    
    public function __construct($conn, $userId, $date = null) {
        $this->conn = $conn;
        $this->userId = (int) $userId;
        $this->date = $date ? $date : self::getBusinessDate();
        
        // جلب اسم المستخدم للبحث في جدول الإغلاقات
        $this->username = $this->getUsernameById($this->userId);
        
        // تحديد وقت آخر إغلاق لهذا المستخدم اليوم
        $this->setLastClosingTime();
    }

    /**
     * يوم العمل (ينتهي الساعة 4 صباحاً) — نفس منطق connect.php
     */
    public static function getBusinessDate() {
        $now = new DateTime('now');
        if ((int) $now->format('H') < 4) {
            $now->modify('-1 day');
        }
        return $now->format('Y-m-d');
    }
    
    private function getUsernameById($id) {
        $query = "SELECT uname FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return $row['uname'];
        }
        return '';
    }
    
    private function setLastClosingTime() {
        // نستخدم crtime لأنه الأدق — جدول closed_orders يخزن اسم الكاشير في user
        $query = "SELECT MAX(crtime) as last_time 
                  FROM closed_orders 
                  WHERE user = ? AND date = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $this->username, $this->date);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $this->lastClosingTime = $row['last_time'];
        }
        $stmt->close();
    }
    
    /**
     * Helper to append time condition
     */
    private function getTimeCondition() {
        if ($this->lastClosingTime) {
            return " AND crtime > '" . $this->lastClosingTime . "'";
        }
        return "";
    }
    
    /**
     * Get basic shift totals (Sales, Invoices count)
     */
    public function getTotals() {
        $timeCond = $this->getTimeCondition();
        $query = "SELECT 
                    COUNT(*) as total_orders,
                    COALESCE(SUM(fat_total), 0) as total_gross,
                    COALESCE(SUM(fat_disc), 0) as total_discount,
                    COALESCE(SUM(fat_net), 0) as total_net
                  FROM ot_head 
                  WHERE DATE(pro_date) = ? 
                  AND user = ? 
                  AND (pro_tybe = 9 OR pro_tybe = 3 OR pro_tybe = 10 OR pro_tybe = 11)
                  AND isdeleted = 0" . $timeCond;
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $this->date, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Get payments breakdown by Fund (Safe/Bank)
     */
    public function getPaymentBreakdown() {
        $timeCond = str_replace('crtime', 'oh.crtime', $this->getTimeCondition());
        
        $query = "SELECT 
                    ah.aname as fund_name,
                    oh.acc1 as fund_id,
                    COUNT(*) as count,
                    COALESCE(SUM(oh.pro_value), 0) as total
                  FROM ot_head oh
                  LEFT JOIN acc_head ah ON oh.acc1 = ah.id
                  WHERE DATE(oh.pro_date) = ? 
                  AND oh.user = ? 
                  AND oh.pro_tybe = 1 
                  AND oh.isdeleted = 0" . $timeCond . "
                  GROUP BY oh.acc1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $this->date, $this->userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Get Returns (Mardoood)
     */
    public function getReturns() {
         $timeCond = $this->getTimeCondition();
         $query = "SELECT 
                    COUNT(*) as count,
                    COALESCE(SUM(fat_net), 0) as total
                  FROM ot_head 
                  WHERE DATE(pro_date) = ? 
                  AND user = ? 
                  AND pro_tybe = 11
                  AND isdeleted = 0" . $timeCond;
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $this->date, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Get Expenses (Masrofat)
     */
    public function getExpenses() {
        $timeCond = $this->getTimeCondition();
        $query = "SELECT 
                   COALESCE(SUM(pro_value), 0) as total
                  FROM ot_head 
                  WHERE DATE(pro_date) = ? 
                  AND user = ? 
                  AND pro_tybe = 2 
                  AND isdeleted = 0" . $timeCond;
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $this->date, $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Get Items Breakdown
     */
    public function getItemsBreakdown() {
         $timeCond = str_replace('crtime', 'oh.crtime', $this->getTimeCondition());
         $query = "SELECT 
                    mi.iname,
                    mi.barcode,
                    SUM(fd.qty_out) as qty,
                    SUM(fd.det_value) as value
                   FROM fat_details fd
                   JOIN ot_head oh ON fd.fatid = oh.id
                   JOIN myitems mi ON fd.item_id = mi.id
                   WHERE DATE(oh.pro_date) = ?
                   AND oh.user = ?
                   AND (oh.pro_tybe = 9 OR oh.pro_tybe = 3 OR oh.pro_tybe = 10 OR oh.pro_tybe = 11)
                   AND oh.isdeleted = 0
                   AND fd.isdeleted = 0" . $timeCond . "
                   GROUP BY fd.item_id
                   ORDER BY value DESC";
                   
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $this->date, $this->userId);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
