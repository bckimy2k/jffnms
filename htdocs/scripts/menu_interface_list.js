
function add (name, value)
{
  select = document.getElementById('use_interfaces');
  if (!select)
    select = document.getElementById('use_interfaces[]');
  size = select.length;
  already_added = false;
  
  for (i=0; i < size; i++)
    if (select.options[i] && select.options[i].value == value)
      already_added = true; 

  if (!already_added)
  {
    select[size] = new Option(name, value);
    select[size].selected = true;
  }
}
    
