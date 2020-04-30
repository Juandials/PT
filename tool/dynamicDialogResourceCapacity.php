<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 *
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU Affero General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/
include_once ("../tool/projeqtor.php");
$keyDownEventScript=NumberFormatter52::getKeyDownEvent();
$mode = RequestHandler::getValue('mode',false,null);
$idResource=RequestHandler::getValue('idResource',false,null);
$idCapacity=RequestHandler::getValue('id',false,null);
$res = new Resource($idResource,true);
$resCap = new ResourceCapacity();
$listRescap = $resCap->getSqlElementsFromCriteria(array('idResource'=>$idResource),null,null,'endDate desc');
if(isset($listRescap[0])){
  if($listRescap[0]->endDate != ''){
    $date = new DateTime($listRescap[0]->endDate);
    $date->add(new DateInterval('P1D'));
    $date = date_format($date, 'Y-m-d');
  }
}
?>
  <table>
    <tr>
      <td>
       <form dojoType="dijit.form.Form" id='resourceCapacityForm' name='resourceCapacityForm' onSubmit="return false;">
        <input id="idResource" name="idResource" type="hidden" value="<?php echo $idResource;?>" />
        <input id="mode" name="mode" type="hidden" value="<?php echo $mode;?>" />
        <input id="idResourceCapacity" name="idResourceCapacity" type="hidden" value="<?php echo $idCapacity;?>" />
         <table>
           <tr>
             <td class="dialogLabel" >
               <label for="resourceCapacity" ><?php echo i18n("colCapacity");?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <div id="resourceCapacity" name="resourceCapacity" value="<?php echo htmlDisplayNumericWithoutTrailingZeros($res->capacity); ?>" 
                 dojoType="dijit.form.NumberTextBox"
                 constraints="{min:0,max:999}" 
                 style="width:100px" class="input"
                 hasDownArrow="true"
               >
               <?php echo $keyDownEventScript;?>
               </div>
             </td>    
           </tr>
           <tr>
             <td colspan="2">
               <table>
                 <tr>
                   <td class="dialogLabel" >
                     <label for="resourceCapacityStartDate" ><?php echo i18n("colStartDate");?>&nbsp;:&nbsp;</label>
                   </td>
                   <td>
                     <input id="resourceCapacityStartDate" name="resourceCapacityStartDate" value="<?php if(isset($date)){ echo $date;} ?>"  
			                 dojoType="dijit.form.DateTextBox"  required
			                 constraints="{datePattern:browserLocaleDateFormatJs}"
                       onChange=" var end=dijit.byId('resourceCapacityEndDate');end.set('dropDownDefaultValue',this.value);
                       var start = dijit.byId('resourceCapacityStartDate').get('value');end.constraints.min=start;"
			                 style="width:100px" />
                   </td>
                   <td class="dialogLabel" >
                     <label for="resourceCapacityEndDate" ><?php echo i18n("colEndDate");?>&nbsp;:&nbsp;</label>
                   </td>
                   <td>
                   <input id="resourceCapacityEndDate" name="resourceCapacityEndDate" value=""  
		                 dojoType="dijit.form.DateTextBox" 
		                 constraints="{datePattern:browserLocaleDateFormatJs}" required
		                 style="width:100px" />
                   </td>
                 </tr>
               </table>
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" >
               <label for="resourceCapacityDescription" ><?php echo i18n("colDescription");?>&nbsp;:&nbsp;</label>
             </td>
             <td> 
               <textarea dojoType="dijit.form.Textarea" 
                id="resourceCapacityDescription" name="resourceCapacityDescription"
                style="width:400px;"
                maxlength="4000"
                class="input"></textarea>   
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" >
               <label for="resourceCapacityIdle" ><?php echo i18n("colIdle");?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <div id="resourceCapacityIdle" name="resourceCapacityIdle"
                 dojoType="dijit.form.CheckBox" type="checkbox" >
               </div>
             </td>    
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
         </table>
        </form>
      </td>
    </tr>
    <tr>
      <td align="center">
        <input type="hidden" id="resourceCapacityAction">
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogResourceCapacity').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="submit" id="dialogResourceCapacitySubmit" onclick="protectDblClick(this);saveResourceCapacity(<?php echo $res->capacity;?>);return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>
