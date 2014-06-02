    <script language = "javascript">
      var XMLHttpRequestObject = false; 

      if (window.XMLHttpRequest) {
        XMLHttpRequestObject = new XMLHttpRequest();
      } else if (window.ActiveXObject) {
        XMLHttpRequestObject = new ActiveXObject("Microsoft.XMLHTTP");
      }

      function getData(dataSource) 
      { 
        if(XMLHttpRequestObject) {
          XMLHttpRequestObject.open("GET", dataSource); 

          XMLHttpRequestObject.onreadystatechange = function() 
          { 
            if (XMLHttpRequestObject.readyState == 4 && 
              XMLHttpRequestObject.status == 200) { 
                if(XMLHttpRequestObject.responseText){
            var targetDiv = document.getElementById("targetDiv");

            targetDiv.innerHTML = "<div>" + XMLHttpRequestObject.responseText + "</div>";
                }
            } 
          } 

          XMLHttpRequestObject.send(null); 
        }
      }

      function narrowLookup(keyEvent) 
      {
        keyEvent = (keyEvent) ? keyEvent: window.event;
        input = (keyEvent.target) ? keyEvent.target : 
          keyEvent.srcElement;

        if (keyEvent.type == "keyup") {
          var targetDiv = document.getElementById("targetDiv");
          targetDiv.innerHTML = "<div></div>";

// by commenting out the if clause, the list expands to show all when
// the user deletes all conditions
//
//          if (input.value) {
            getData("narrow_lookup.php?narrow_lookup=" + 
              input.value + "&lookup_pcircs=<? global $lookup_pcircs; echo $lookup_pcircs; ?>");
//          } 
        }
      }
    </script>

