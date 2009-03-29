<?php

# SiT! forms

class Form
{
  var $row = array();
  var $name;
  var $submitLabell;
  var $tableName;
  var $type; // SEARCH, UPDATE, ADD
  
  public function __construct($name, $submitLabel, $tableName, $type)
  {
  	$this->name = $name;
    $this->submitLabel = $submitLabel;
    $this->tableName = $tableName;
    $this->type = $type;
  }

  public function addRow(/*Row*/ $row)
  {
    $this->row[] = $row;
  }

  private function generateHTML()
  {
    global $strSubmit;
    
    echo "<form action='{$_SERVER['PHP_SLEF']}' id='{$this->name}' name='{$this->name}' method='POST'>";
    echo "<table class='vertical'>";
    foreach($this->row AS $r)
    {
      echo $r->generateHTML();
    }
    echo "</table>";
    echo "<p align='center'><input type='submit' name='submit' value='{$this->submitLabel}' /></p>";
    echo "</form>";
  }
  
    private function processForm()
    {
        global $_REQUEST;
      	$toReturn = array();
        foreach ($this->row AS $r)
        {
        	$toReturn = array_merge ($toReturn, $r->getDB());
        }
        
    //    print_r($toReturn);
        switch ($this->type)
        {
            case 'search':
            	$sql = "SELECT * FROM `{$this->tableName}` ";
                if (count($toReturn) > 0)
                {
                	$sql .= "WHERE ";
                    foreach ($toReturn AS $d)
                    {
                        $v = cleanvar($_REQUEST[$d->name]);
                    	$a[] = "{$d->field} = '{$v}' ";
                    }
                    $sql .= implode(" AND ", $a);
                }
                
                echo $sql;
                break;
            case'insert':
                $sql = "INSERT INTO `{$this->tableName}` ";
                if (count($toReturn) > 0)
                {
                    $sql .= " (";
                    foreach ($toReturn AS $d)
                    {
                        $a[] = "'{$d->field}''";
                    }
                    $sql .= implode(",", $a);
                    
                    unset($a);
                    $sql .= ") VALUES (";
                    foreach ($toReturn AS $d)
                    {
                        $v = cleanvar($_REQUEST[$d->name]);
                        $a[] = "'{$v}'";
                    }
                    $sql .= implode(",", $a);
                    $sql .= ")";
                }
                
                echo $sql;
                break;
        }
    }
  
  public function run()
  {
  	global $_REQUEST;

    $submit = cleanvar($_REQUEST['submit']);
    
    if (empty($submit))
    {
    	echo $this->generateHTML();
    }
    else
    {
    	echo $this->processForm();
    }
  }
}

abstract class Component
{
    var $name;
    var $dbFieldName;
    abstract function generateHTML();
    abstract function getDB(); // Returns array
}

class db
{
	var $name;
    var $field;
    
    public function __construct($name, $field)
    {
    	$this->name = $name;
        $this->field = $field;
    } 
}

class Row extends Component
{
    var $components;
    
    public function addComponent(/*Component*/ $component)
    {
        $this->components[] = $component;
    }
    
    public function generateHTML()
    {
         $toReturn = "<tr>";
        
        foreach ($this->components AS $comp)
        {
          $toReturn .= $comp->generateHTML();
        }
        
        return $toReturn."</tr>";
    }
    
    public function getDB()
    {
    	$toReturn = array();
        foreach ($this->components AS $comp)
        {
        	$toReturn = array_merge($toReturn, $comp->getDB());
        }
        
        return $toReturn;
    }
}// ROW

class Cell extends Component
{
    var $components = array();
    var $isHeader = false;
    
    public function addComponent(/*component*/ $component)
    {
        $this->components[] = $component;
    }
    
    public function setIsHeader($header = TRUE)
    {
        $this->isHeader = $header;
    }
    
    public function generateHTML()
    {
        $toReturn = "";
        foreach ($this->components AS $component)
        {
            $toReturn .= $component->generateHTML();
        }
        
        if ($this->isHeader) $toReturn = "<th>{$toReturn}</th>";
        else $toReturn = "<td>{$toReturn}</td>";
        
        return $toReturn;
    }
    
    public function getDB()
    {
        $toReturn = array();
        foreach ($this->components AS $comp)
        {
           $toReturn =  array_merge($toReturn, $comp->getDB());
        }
        
        return $toReturn;
    }
}

class Label extends Component
{
    var $label = "";
    public function __construct($label = "")
    {
        $this->label = $label;
    }
    
    public function generateHTML()
    {
        return "{$this->label}";
    }
    
    public function getDB()
    {
    	return array();
    }
} // LABEL

class SingleLineEntry extends Component
{
    var $size = 30;

    public function __construct($name = "text", $size = 30, $dbField)
    {
        $this->name = $name;
        $this->size = $size;
        $this->dbFieldName = $dbField; 
    }

    public function generateHTML()
    {
        return "<input type='text' id='{$this->name}' name='{$this->name}' size='{$this->size}' />";
    }
    
    public function getDB()
    {
    	$db = new db($this->name, $this->dbFieldName);
        
        return array($db);
    }
}

class DatePicker extends Component
{
    var $name;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    public function generateHTML()
    {
        global $CONFIG, $iconset;
        
        $divid = "datediv".str_replace('.','',$name);
        $html = "<img src='{$CONFIG['application_webpath']}images/icons/{$iconset}/16x16/pickdate.png' ";
        $html .= "onmouseup=\"toggleDatePicker('$divid','{$name}')\" width='16' height='16' alt='date picker' style='cursor: pointer; vertical-align: bottom;' />";
        $html .= "\n<div id='$divid' style='position: absolute;'></div>\n";
        return $html;
    }
    
    public function getDB()
    {
    	return array();
    } 
}

class DateC extends Component
{
    var $components = array();
    public function __construct($name)
    {
        $this->components[] = new SingleLineEntry(name,10, "test");
        $this->components[] = new DatePicker("{$name}picker");
    }
      
    public function generateHTML()
    {
        $toReturn = "";
        foreach ($this->components AS $component) $toReturn .= $component->generateHTML();
        return $toReturn;
    }
    
    public function getDB()
    {
        return array();
    }
}

?>