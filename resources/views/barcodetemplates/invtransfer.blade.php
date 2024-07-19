<button onclick="print2()" style="margin: 3px; float: right; color: blue">  <strong>300dpi&#x2399;</strong></button> 
<button onclick="print203dpi()" style="margin: 3px; float: right; color: blue">  <strong>203dpi &#x2399;</strong></button> 
<div class="selection"  style="margin: 3px; float: right; color: blue">
<input type="checkbox"   onclick="checkall()" id="check">  
<label for="1"  >Select All</label>
</div>
<!-- <button onclick="PrintElem()" style="float: right; color: blue">  <strong>PRINT TO EPSON &#x2399;</strong></button>  -->
<br>
<br>
<div id="printMe">
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Printing</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin-top: 5;
    margin-left: 25;
    padding: 25px;
    background-color: #f8f9fa;
  }
  .container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 60px;
  }
  .box {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .box:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }
  .label {
    font-weight: bold;
    color: #333;
  }
  .value {
    margin-bottom: 10px;
    color: #555;
  }
</style>
<style>
        .container2 {
            font-size: 0;
            position: relative;
            width: 3px;
            height: 30px;
        }

        .bar {
            background-color: black;
            height: 30px;
            position: absolute;
            top: 0;
        }

        /* Adjust the size of the entire container */
        .scaled-container {
            transform: scale(0.7); /* Change the scale as needed */
            transform-origin: top left; /* Ensure scaling is from the top left corner */
        }
    </style>
</head>
<body>

<div class="container">
@foreach($new as $key => $item)
  <div class="box">
    <div class="label">Brand:</div>
    <div class="value">{{$brand}}</div>
    <div class="label">Model:</div>
    <div class="value">{{$model}}</div>
    <div class="scaled-container">
      <div class="container2"> {!! $item['br'] !!}</div>
    </div>  
    <div class="value">{{$item['code']}}</div>
    
    <!-- Add a checkbox for selection -->
    <div class="selection">
      <input type="checkbox" name="selected_items[]" value="{{$item['code']}}" id="select_{{$key}}">
      <label for="select_{{$key}}">Print Selection</label>
    </div>
  </div>
@endforeach
  <!-- Repeat for as many boxes as needed -->
</div>
      </div >
</body>
</html>
<script type="text/javascript" src="http://192.168.1.19:7771/script/BrowserPrint-3.1.250.min.js"></script>
<script> 
 
  var ddd = @json($new2);
       
  function gettingId(mapID){
    let checkbox = document.getElementById('select_'+mapID);
        if (checkbox && checkbox.checked) {
            return checkbox.value;
        }
  }
 
  
  function generateZPLForTemplate(brand, model, serial) {
            return `
  ^XA
                ^LH0,0
                ^FO60,40^GB900,350,3^FS  // Draw a rectangle (border)
                ^FO80,60^ADN,36,26^FD BRAND:  ${brand} ^FS
                ^FO80,120^ADN,36,26^FD MODEL:  ${model} ^FS
                ^FO100,200^BY4^BCN,130,Y,N,N^FD${serial}^FS
                ^XZ

            `;
        }
  function generateZPL203di(brand, model, serial) {
      return `
      ^XA
      ^LH0,0
      ^FO53,30^GB700,350,3^FS  // Draw a rectangle (border)
      ^FO70,60^ADN,36,20^FD BRAND:  ${brand} ^FS
      ^FO70,110^ADN,36,20^FD MODEL:  ${model} ^FS
      ^FO92,200^BY2^BCN,130,Y,N,N^FD${serial}^FS
      ^XZ

      `;
  }
  function getlabels(ddd){
    const labels = [];
    if (Array.isArray(ddd)) {
        ddd.forEach(res => {
          if(res.code == gettingId(res.code)){
            labels.push({brand: '{{$brand}}', model: '{{$model}}', serial: res.code})
          }
          
        });
    } else {
        console.error("Data is not an array:", ddd);
    }
       
    return labels
  }
  function print2(){
    labels = getlabels(ddd)
    
    BrowserPrint.getDefaultDevice('printer', function(printer) {
                if (printer) {
                    // Example data for multiple labels
                     
                    console.log(labels)
                    // Combine ZPL commands for multiple layouts
                    let combinedZPL = '';
                    labels.forEach(label => {
                        combinedZPL += generateZPLForTemplate(label.brand, label.model, label.serial);
                    });

                    // Send the combined ZPL to the printer
                    printer.send(combinedZPL, function(response) {
                        alert('Print successful')
                        console.log('Print successful', response);
                    }, function(error) {
                        alert('Print failed')
                        console.error('Print failed', error);
                    });
                } else {
                  alert('No printer found')
                    console.error('No printer found');
                }
            }, function(error) {
                alert('Error getting default printer')
                console.error('Error getting default printer', error);
            });
  }
  function print203dpi(){
    labels = getlabels(ddd)
                BrowserPrint.getDefaultDevice('printer', function(printer) {
                if (printer) {
                    // Example data for multiple labels
                     
                    console.log(labels)
                    // Combine ZPL commands for multiple layouts
                    let combinedZPL = '';
                    labels.forEach(label => {
                        combinedZPL += generateZPL203di(label.brand, label.model, label.serial);
                    });

                    // Send the combined ZPL to the printer
                    printer.send(combinedZPL, function(response) {
                        alert('Print successful')
                        console.log('Print successful', response);
                    }, function(error) {
                        alert('Print failed')
                        console.error('Print failed', error);
                    });
                } else {
                  alert('No printer found')
                    console.error('No printer found');
                }
            }, function(error) {
                alert('Error getting default printer')
                console.error('Error getting default printer', error);
            });
  }
  function checkall(){
    let iden
    let checkbox = document.getElementById('check');
        if (checkbox && !checkbox.checked) {
          iden = false
        }else{
          iden = true
        }
    ddd.forEach(res => {
        console.log(res.code);
        // Get the checkbox element
        var checkbox = document.getElementById('select_' + res.code);
        // Check the checkbox
        checkbox.checked = iden; // Set to true to check the checkbox
    });
  }
  function PrintElem()
        {
        

         var printContents = document.getElementById('printMe').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        return true;
        }
</script>