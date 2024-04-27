<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="resources/sheet.css" >
<style type="text/css">.ritz .waffle a { color: inherit; }.ritz .waffle .s2{border-bottom:1px SOLID #000000;background-color:#ffffff;}.ritz .waffle .s6{border-right:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s1{background-color:#ffffff;text-align:center;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s0{background-color:#ffffff;text-align:left;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s4{border-right:1px SOLID #000000;background-color:#ffffff;}.ritz .waffle .s7{border-bottom:1px SOLID #000000;border-right:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s5{border-bottom:1px SOLID #000000;border-right:1px SOLID #000000;background-color:#ffffff;text-align:left;font-weight:bold;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s8{border-bottom:1px SOLID #000000;background-color:#ffffff;text-align:center;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}.ritz .waffle .s3{border-bottom:1px SOLID #000000;background-color:#ffffff;text-align:left;color:#000000;font-family:'Arial';font-size:10pt;vertical-align:bottom;white-space:nowrap;direction:ltr;padding:2px 3px 2px 3px;}</style>
<div class="ritz grid-container" dir="ltr">
   <table class="waffle" cellspacing="0" cellpadding="0">
      <thead>
         <tr>
            <th class="row-header freezebar-origin-ltr"></th>
            <th id="0C0" style="width:12px;" class="column-headers-background"></th>
            <th id="0C1" style="width:16px;" class="column-headers-background"></th>
            <th id="0C2" style="width:194px;" class="column-headers-background"></th>
         </tr>
      </thead>
      <tbody>
         <tr style="height: 11px">
            <th id="0R0" style="height: 11px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 11px"></div>
            </th>
            <td class="s0" dir="ltr"></td>
            <td class="s0" dir="ltr"></td>
            <td class="s1" dir="ltr"></td>
         </tr>
         <tr style="height: 41px">
            <th id="0R1" style="height: 41px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 41px"></div>
            </th>
            <td></td>
            <td></td>
            <td class="s1" dir="ltr">BARCODE GENERATOR<br>GRPO SYSTEM</td>
         </tr>
         <tr style="height: 20px">
            <th id="0R2" style="height: 20px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 20px"></div>
            </th>
            <td></td>
            <td></td>
            <td class="s2"></td>
         </tr>
         <tr style="height: 10px">
            <th id="0R3" style="height: 10px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 10px"></div>
            </th>
            <td></td>
            <td></td>
            <td class="s3"></td>
         </tr>
         @foreach($new as $item)
         <tr style="height: 20px">
            <th id="0R4" style="height: 20px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 20px"></div>
            </th>
            <td></td>
            <td class="s4"></td>
            <td class="s5" dir="ltr">Brand: <span style="font-weight:normal;">{{$brand}}</span></td>
         </tr>
         <tr style="height: 20px">
            <th id="0R5" style="height: 20px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 20px"></div>
            </th>
            <td></td>
            <td class="s4"></td>
            <td class="s5" dir="ltr">Model: <span style="font-weight:normal;">{{$model}}</span></td>
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
 
         </tr>
         <tr style="height: 20px">
            <th id="0R7" style="height: 20px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 20px"></div>
            </th>
            <td></td>
            <td class="s4"></td>
            <td class="s7" dir="ltr">{{$item['code']}}</td>
         </tr>
         <tr style="height: 10px">
            <th id="0R8" style="height: 10px;" class="row-headers-background">
               <div class="row-header-wrapper" style="line-height: 10px"></div>
            </th>
            <td></td>
            <td></td>
            <td class="s8" dir="ltr"></td>
         </tr>
         @endforeach
      </tbody>
   </table>
</div>
