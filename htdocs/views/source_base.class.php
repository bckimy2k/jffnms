<?php

class Source_base
{
  public $only_rootmap;

  function __construct(&$View)
  {
    global $Sanitizer;
    $this->only_rootmap = $Sanitizer->get_int('only_rootmap', 1);
  }
}
