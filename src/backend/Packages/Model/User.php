<?php

namespace Package\Model;

use Package\Core\Model;

class User {
  private $connection;
  private const table = 'Users';

  public function __construct(){
    $this->connection = Model::connect();
  }

  public function listLimit($start, $total){
    $query = "SELECT * FROM Users LIMIT {$start}, {$total}";
    return $this->connection->query($query)->fetchAll()->toJson()->get();
  }

  public function insert($datas = []){
    return $this->connection->insert(self::table, $datas);
  }

  public function listAll($fields = "*"){
    return (
      $this->connection
      ->select(self::table, $fields)
      ->fetchAll()
      ->toJson()
      ->get()
    );
  }

  public function get($conditions = [], $fields = '*'){
    return (
      $this->connection
      ->select(self::table, $fields)
      ->where($conditions)
      ->fetch()
      ->get()
    );
  }

  public function update($field, $value, $set = []){
    return (
      $this->connection
      ->update(self::table, $field, $value, $set)
    );
  }

}


?>
