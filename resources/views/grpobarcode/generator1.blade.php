<button onclick="PrintElem()" style="float: right; color: blue">  <strong>PRINT ME &#x2399;</strong></button>

<div id="printMe">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="resources/sheet.css" >
<style type="text/css">.ritz .waffle a { color: inherit; }.ritz .waffle .s2{border-bottom:1px SOLID #000000;background-color:#ffffff;}.ritz .waffle .s6{border-right:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s1{background-color:#ffffff;text-align:center;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s0{background-color:#ffffff;text-align:left;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s4{border-right:1px SOLID #000000;background-color:#ffffff;}.ritz .waffle .s7{border-bottom:1px SOLID #000000;border-right:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s5{border-bottom:1px SOLID #000000;border-right:1px SOLID #000000;background-color:#ffffff;text-align:left;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s8{border-bottom:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s3{border-bottom:1px SOLID #000000;background-color:#ffffff;text-align:left;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}</style>
<div class="ritz grid-container" dir="ltr">
   <table class="waffle" cellspacing="0" cellpadding="0">
      
      <tbody>
          
          
      <tr>
                <th class="row-header freezebar-origin-ltr"></th>
                <th id="0C0" style="width: 12px;" class="column-headers-background"></th>
                <th id="0C1" style="width: 16px;" class="column-headers-background"></th>
                <th id="0C2" style="width: 194px;" class="column-headers-background"></th>
                <th id="0C3" style="width: 17px;" class="column-headers-background"></th>
                <th id="0C4" style="width: 214px;" class="column-headers-background"></th>
            </tr>
          
         <tr style="height: 10px">
            <th id="0R3" style="height: 10px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 10px"></div>
            </th>
            <td></td>
            <td></td>
            <td class="s3"></td>
             <!-- <td></td>
                <td class="s3"></td> -->
         </tr>
         @php $d=0;@endphp
         @foreach($new as $key => $item)
          @php $d+= 1@endphp
         
         <tr style="height: 20px">
              <th id="0R4" style="height: 20px;" class="row-headers-background"><div class="row-header-wrapper" style="line-height: 20px;"></div></th>
                <td></td>
              
        
                <td class="s4"></td>
            <td class="s5" dir="ltr">Brand: <span style="font-weight:normal;">{{$brand}}</span></td>
         
            
            <!-- <td class="s4"></td>
                <td class="s5" dir="ltr">Brand: <span style="font-weight: normal;">{{$brand}}</span></td> -->
          
         </tr>
         <tr style="height: 20px">
            <th id="0R5" style="height: 20px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 20px"></div>
            </th>
            <td></td>
           
            <td class="s4"></td>
            <td class="s5" dir="ltr">Model: <span style="font-weight:normal;">{{$model}}</span></td>
          
            
            <!-- <td class="s4"></td>
                <td class="s5" dir="ltr">Model: <span style="font-weight: normal;">{{$model}}</span></td> -->
       
         </tr>
         <tr style="height: 29px">
            <th id="0R6" style="height: 29px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 29px"></div>
            </th>
            <td></td>
            <td class="s4"></td>
            
            <td class="s6" dir="ltr">
            <div style="width: 100%; text-align: center; display: flex; justify-content: center;">
               {!! $item['br'] !!}
               </div>
            </td>
         
                <!-- <td class="s4"></td>
                <td class="s6" dir="ltr">{!! $item['br'] !!}</td> -->
          
         </tr>
         <tr style="height: 20px">
            <th id="0R7" style="height: 20px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 20px"></div>
            </th>
            <td></td>
         
            <td class="s4"></td>
            <td class="s7" dir="ltr">{{$item['code']}}</td>
         
            
             <!-- <td class="s4"></td>
                <td class="s7" dir="ltr">{{$item['code']}}</td>
          -->
         </tr>
         <tr style="height: 10px">
            <th id="0R8" style="height: 10px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 10px"></div>
            </th>
            <td></td>
            <td></td>
            <td class="s8" dir="ltr"></td>
              <!-- <td></td>
                <td class="s8" dir="ltr"></td> -->
         </tr> 
          @if($d == 9)
          @php $d = 0 @endphp
            <tr style="height: 10px">
               <th id="0R8" style="height: 10px;" class="row-headers-background">
                  <div class="row-header-wrapper" style="line-height: 10px"></div>
               </th>
               <td></td>
               <td></td>
               <td class="s8" dir="ltr"><br><br></td>
            </tr> 
         
          @endif
         @endforeach
      </tbody>
   </table>
</div>
</div>

<script>
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
 <style>
   @media print {
    /* Add styles here that you want to apply to the print version */
    body, html {
        -webkit-print-color-adjust: exact; /* Chrome, Safari */
        color-adjust: exact;              /* Firefox */
    }
    .print-section {
        display: block; /* Ensures print sections are visible */
        width: 100%; /* Adjust width as necessary */
        /* additional styling */
    }
}

 </style>