<?php
class basic {
  protected $record;
  protected $record_pos;

  public $jffnms_filter_record = 1;
  public $jffnms_order_field = 'id';
  public $jffnms_order_type = 'desc';

  function jffnms_class()
  {
    $class_name = get_class($this);
    return strtolower(preg_replace('/Jffnms(\S+)/','$1', $class_name));
  }
  //Generic item list handling functions
  //------------------------------------
    
  function fetch()
  {
    if ($result = $this->get_current()) $this->record_pos++;
    return $result;
  }

  function get_current()
  { 
    reset ($this->record);
    for ($i=0; $i < $this->record_pos; $i++) next($this->record);
    return current($this->record);
  }

  function count()   { return count($this->record); }
  function _count()   { return count($this->record); }
  function count_all()   { return count($this->get_all()); }
  
  function slice($init,$span)
  {
    $this->record = array_slice($this->record,intval($init),intval($span)+1);
    reset($this->record);
    return true;
  }  
    
  function get()
  { 
    $params = func_get_args(); 
    $this->record_pos = 0;
    $this->record = call_user_func_array(array($this,'get_all'),$params);
    return $this->count();
  }
    
  function get_empty_record ()
  {
    //Get the 'Unknown' Record 1
    $aux = current(get_db_list(  $this->jffnms_class(),  1,   array($this->jffnms_class().'.*'))); //table,ids,fields  

    foreach ($aux as $key=>$value)   //fill the values with the default data
      $aux[$key] = (isset($this->jffnms_insert[$key])?$this->jffnms_insert[$key]:$value);
    return $aux;
  }
    
  function field_values ($fields = NULL)
  {
    $values = array();
    $all_records = $this->get_all();
      
    while (list(,$row) = each ($all_records)) 
      while (list($field, $value) = each ($row))
        if ($fields===NULL)
          $values[$field][]=$value;
        else 
          if (isset($fields[$field]))
            $values[$field][$value]=$row[$fields[$field]];
    unset ($values['id']);
      
    while (list ($field, $value) = each ($values))
    {
      $values[$field] = array_unique($value);
      asort ($values[$field]);
      $values[$field] = array(''=>'') + $values[$field];
    }
    reset ($values);
    return $values;
  }

  public function get_all($ids = NULL, $fields = array())
  { 
    return get_db_list($this->jffnms_class(), $ids,
      array($this->jffnms_class().'.*'), //table,ids,fields  
      array(array($this->jffnms_class().'.id','>',$this->jffnms_filter_record)), //where
      array(array($this->jffnms_class().'.'.$this->jffnms_order_field,$this->jffnms_order_type)) ); //order 
  }

  function add($filter=NULL)
  {
    if (!isset($this->jffnms_insert))
      die("basic-> add(): missing jffnms_insert property for class ".get_class($this)."\n");
    return db_insert($this->jffnms_class(), $this->jffnms_insert);
  }

  function update($id,$data)
  {
    return db_update($this->jffnms_class(), $id,$data);
  }

  function del($id)
  {
    return db_delete($this->jffnms_class(),$id);
  }
}
?>
