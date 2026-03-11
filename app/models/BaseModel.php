<?php
require_once __DIR__ . '/../../config/db_module.php';

class BaseModel
{
    protected $table;
    protected $link;

    public function __construct($table)
    {
        $this->table = $table;
        $this->link = null;
        taoKetNoi($this->link);
    }

    
    //select *
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table}";

        $result = chayTruyVanTraVeDL($this->link, $sql);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    //select theo Id
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = '$id'";

        $result = chayTruyVanTraVeDL($this->link, $sql);

        return mysqli_fetch_assoc($result);
    }

    //insert
    public function create($data)
    {
        //lấy key và nối chuỗi
        $arrayKeys = array_keys($data);
        $columns = implode(', ', $arrayKeys);

        //lấy value và nối chuỗi
        $arrayValues = array_values($data);
        $values = "'" . implode("', '", $arrayValues) . "'";
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($values)";

        chayTruyVanKhongTraVeDL($this->link, $sql);
        
        return mysqli_insert_id($this->link);
    }

    //update
    public function update($id, $data)
    {
        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key = '$value'";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = '$id'";

        chayTruyVanKhongTraVeDL($this->link, $sql);
        
        return mysqli_affected_rows($this->link);
    }

    //delete
    public function delete($id)
    {
        if (!empty($id)){
            $sql = "DELETE FROM {$this->table} WHERE id = '$id'";   //xóa có điều kiện
        }
        else {
            $sql = "DELETE FROM {$this->table}";                    //xóa hết
        }
        
        chayTruyVanKhongTraVeDL($this->link, $sql);
        
        return mysqli_affected_rows($this->link);
    }
    
    // Dùng cho SELECT phức tạp (có JOIN, WHERE...)
    // public function query($sql)
    // {
    //     $result = chayTruyVanTraVeDL($this->link, $sql);
        
    //     $data = [];
    //     while ($row = mysqli_fetch_assoc($result)) {
    //         $data[] = $row;
    //     }
    //     return $data;
    // }

    // // Dùng cho INSERT/UPDATE/DELETE phức tạp
    // public function execute($sql)
    // {
    //     chayTruyVanKhongTraVeDL($this->link, $sql);
    //     return mysqli_affected_rows($this->link);
    // }

    public function __destruct()
    {
        if ($this->link) {
            mysqli_close($this->link);
        }
    }
}
?>