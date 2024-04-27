<!-- <button onclick="PrintElem()"style="float: right; color: blue">  <strong>PRINT ME &#x2399;</strong></button> -->

<!-- <div id="printMe"> -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="resources/sheet.css" >
<style type="text/css">.ritz .waffle a { color: inherit; }.ritz .waffle .s3{border-bottom:1px SOLID #000000;background-color:#ffffff;}.ritz .waffle .s6{background-color:#ffffff;text-align:center;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s8{border-right:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:0;}.ritz .waffle .s0{background-color:#ffffff;text-align:left;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s1{border-bottom:1px SOLID #000000;background-color:#ffffff;text-align:left;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s11{border-bottom:1px SOLID #000000;border-right:1px SOLID #000000;background-color:#ffffff;text-align:left;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s4{border-right:1px SOLID #000000;background-color:#ffffff;}.ritz .waffle .s9{border-bottom:1px SOLID #000000;border-right:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s2{border-bottom:1px SOLID #000000;background-color:#ffffff;text-align:center;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s7{border-right:1px SOLID #000000;background-color:#ffffff;text-align:left;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s5{background-color:#ffffff;text-align:left;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s10{border-bottom:1px SOLID #000000;background-color:#ffffff;text-align:left;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}</style>
<div class="ritz grid-container" dir="ltr">
   <table class="waffle" cellspacing="0" cellpadding="0">
      <thead>
         <tr>
            <th class="row-header freezebar-origin-ltr"></th>
            <th id="0C0" style="width:12px;" class="column-headers-background"> </th>
            <th id="0C1" style="width:16px;" class="column-headers-background"> </th>
            <th id="0C2" style="width:194px;" class="column-headers-background"> </th>
            <th id="0C3" style="width:17px;" class="column-headers-background"> </th>
         </tr>
      </thead>
      <tbody>
         <tr style="height: 11px">
            <th id="0R0" style="height: 11px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 11px"></div>
            </th>
            <td class="s0" dir="ltr"></td>
            <td class="s1" dir="ltr"></td>
            <td class="s2" dir="ltr"></td>
            <td class="s3"></td>
         </tr>
         <tr style="height: 41px">
            <th id="0R1" style="height: 41px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 41px"></div>
            </th>
            <td class="s4"></td>
            <td class="s5"></td>
            <td class="s6" dir="ltr">BARCODE GENERATOR<br>GRPO SYSTEM</td>
            <td class="s7"></td>
         </tr>
         <tr style="height: 20px">
            <th id="0R2" style="height: 20px;" class="row-headers-background">
              
            </th>
            <td class="s4"></td>
            <td class="s5"></td>
            <td class="s3"></td>
            <td class="s7"></td>
         </tr>
          
         @foreach($data as $item)
         <!-- start -->
         <tr style="height: 20px">
            <th id="0R5" style="height: 20px;" class="row-headers-background">
              
            </th>
            <td class="s4"></td>
            <td class="s5"></td>
            <td class="s3"></td>
            <td class="s7"></td>
         </tr>
         <tr style="height: 30px">
            <th id="0R6" style="height: 30px;" class="row-headers-background">
                
            </th>
            <td class="s4"></td>
            <td class="s7"></td>
            <td class="s8">
               <div  > 
            <!-- CODE HERE -->
               
            {!! $item['br'] !!}</div>
            </td>
            <td class="s7"></td>
         </tr>
         <tr style="height: 20px">
            <th id="0R7" style="height: 20px;" class="row-headers-background">
                
            </th>
            <td class="s4"></td>
            <td class="s7"></td>
            <td class="s9" dir="ltr">{{$item['code']}}</td>
            <td class="s7"></td>
         </tr>
         <tr style="height: 20px">
            <th id="0R8" style="height: 20px;" class="row-headers-background">
               
            </th>
            <td class="s4"></td>
            <td class="s5"></td>
            <td class="s3"></td>
            <td class="s7"></td>
         </tr>
        <!-- end -->
        @endforeach
         
         <tr style="height: 20px">
            <th id="0R14" style="height: 20px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 20px"> </div>
            </th>
            <td class="s4"></td>
            <td class="s10"></td>
            <td class="s10"></td>
            <td class="s11"></td>
         </tr>
      </tbody>
   </table>
</div>
<!-- </div> -->
<!-- <script>
 function PrintElem()
        {   
         html2canvas(document.getElementById('printMe')).then(function(canvas) {
    // Create an image
    
    var img = canvas.toDataURL("image/png");

    // Create a link to download the image
    var link = document.createElement('a');
    link.download = 'screenshot.png';
    link.href = img;
    link.click();
});
        }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.3/html2canvas.min.js"></script> -->