function toggle(record,location) 
{
  var location = location + '_' + record;
/*  location now encodes the placement & the record number */
  if (document.getElementById(location).innerHTML=='')
    loadXMLDoc(record,location); 
  else
    hide(location);

}

function hide(location)
{
  document.getElementById(location).innerHTML='';
}

function loadXMLDoc(record,location)
{
  xmlhttp=null;
  if (window.XMLHttpRequest)
    {// code for Firefox, Opera, IE7, etc.
      xmlhttp=new XMLHttpRequest();
    }
  else if (window.ActiveXObject)
    {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  if (xmlhttp!=null)
    {
      get_url = "narrow_lookup.php?narrow_lookup=&lookup_pcircs=";
      get_url += record;
      get_url += "&location=" + location;
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4)
        {
          if (xmlhttp.status==200)
          {
            document.getElementById(location).innerHTML=xmlhttp.responseText;
          }
          else
          {
            alert("Problem retrieving data:" + xmlhttp.statusText);
          }
        }
      }
      xmlhttp.open("GET",get_url,true);
      xmlhttp.send(null);
    }
  else
    {
      alert("Your browser does not support XMLHTTP.");
    }
}



