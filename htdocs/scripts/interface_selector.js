function operation (op)
{
  orig = document.getElementById('selector[]');
  size = orig.length;
  for (i=0; i < size; i++) 
    if (orig.options[i] && orig.options[i].selected == true)
    {
      if (op=='add')
      {
        opener.add(orig.options[i].text, orig.options[i].value);
        orig.options[i].className='select_mark';
      } else {
        opener.del(orig.options[i].value);
        orig.options[i].className='';
      }
    }
}
      
function view_now (field)
{
  if (document.getElementById('selector[]').selectedIndex==-1)   // Nothing selected
    opener.go_select(field);          // Show all host interfaces
  else
  {
    operation('add');            // add selected interface
    opener.document.getElementById('selector_form').submit();  // submit form
    close_popup();
  }
}
      
function close_popup()
{
  opener.popups[window.name] = null;      // remove myself from the popups list
  this.close();            // close this window
}
      
function add (name, value)
{
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
    
function del (value)
{
  select = document.getElementById('use_interfaces[]');
  size = select.length;
  
  for (i=0; i < size; i++)
    if (select.options[i] && select.options[i].value == value)
      select.options[i]=null;
}

function select_all(field_aux)
{
  field = document.getElementById(field_aux);
  for (i = 0; i < field.length; i++)
    field[i].selected = ! field[i].selected;
}
