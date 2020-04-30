<?php
/*
 * @author: qCazelles
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/view/contractGanttList.php');

$startDate=date('Y-m-d');
$endDate=null;
$user=getSessionUser();
$saveDates=false;
$typeGanttContract='GanttSupplierContract';
$paramStart=SqlElement::getSingleSqlElementFromCriteria('Parameter', array(
    'idUser'=>$user->id, 
    'idProject'=>null, 
    'parameterCode'=>'planningStartDate'));
if ($paramStart->id) {
  $startDate=$paramStart->parameterValue;
  $saveDates=true;
}
$paramEnd=SqlElement::getSingleSqlElementFromCriteria('Parameter', array(
    'idUser'=>$user->id, 
    'idProject'=>null, 
    'parameterCode'=>'planningEndDate'));
if ($paramEnd->id) {
  $endDate=$paramEnd->parameterValue;
  $saveDates=true;
}
$saveShowResource=Parameter::getUserParameter('contractGanttShowResource');
$showClosedContract=Parameter::getUserParameter('contractShowClosed');

if ($showClosedContract) {
  $_REQUEST['idle']=true;
}

$proj=null;
if (sessionValueExists('project')) {
  $proj=getSessionValue('project');
  if (strpos($proj, ",")) {
    $proj="*";
  }
}
if ($proj=='*' or !$proj) {
  $proj=null;
}
$objectClass=(RequestHandler::isCodeSet('objectClass'))?RequestHandler::getClass('objectClass'):'';
if ($objectClass==='ClientContract') {
  $typeGanttContract='GanttClientContract';
}
?>
<input type="hidden" name="objectGantt" id="objectGantt"
	value="<?php echo $objectClass;?>" />
<div id="mainPlanningDivContainer"
	dojoType="dijit.layout.BorderContainer">
	<div dojoType="dijit.layout.ContentPane" region="top"
		id="listHeaderDiv" height="27px"
		style="z-index: 3; position: relative; overflow: visible !important;">
		<form dojoType="dijit.form.Form" id="listForm" action="" method="">
      <?php
      $objectClass=(RequestHandler::isCodeSet('objectClass'))?RequestHandler::getClass('objectClass'):'';
      $objectId=(RequestHandler::isCodeSet('objectId'))?RequestHandler::getId('objectId'):'';
      ?>
       <input type="hidden" id="objectClass" name="objectClass"
				value="<?php echo $objectClass;?>" /> <input type="hidden"
				id="objectId" name="objectId" value="<?php echo $objectId;?>" />
			<table width="100%" height="27px" class="listTitle">
				<tr height="27px">
					<td style="vertical-align: top; min-width: 100px; width: 25%">
						<table>
							<tr height="32px">
								<td width="50px" style="min-width: 50px" align="center">
                    <?php echo formatIcon($typeGanttContract, 32, null, true);?>
              </td>
								<td width="400px"><span class="title"
									style="max-width: 200px; white-space: normal"><?php echo i18n('menu'.$typeGanttContract);?></span></td>
							</tr>
						</table>
					</td>
					<td style="width: 30%;">
						<table style="width: 100%;">
							<tr>
								<td align="right">&nbsp;&nbsp;&nbsp;<?php echo i18n("displayStartDate");?>&nbsp;&nbsp;</td>
								<td>
									<div dojoType="dijit.form.DateTextBox"
										<?php if (sessionValueExists('browserLocaleDateFormatJs')) { echo ' constraints="{datePattern:\''.getSessionValue('browserLocaleDateFormatJs').'\'}" '; }?>
										id="startDatePlanView" name="startDatePlanView"
										invalidMessage="<?php echo i18n('messageInvalidDate')?>"
										type="text" maxlength="10"
										style="width: 100px; text-align: center;"
										class="input roundedLeft" hasDownArrow="true"
										value="<?php if(sessionValueExists('startDatePlanView')){ echo getSessionValue('startDatePlanView'); }else{ echo $startDate; } ?>">
										<script type="dojo/method" event="onChange">
                      saveDataToSession('startDatePlanView',formatDate(dijit.byId('startDatePlanView').get("value")), false);
                      refreshJsonPlanning();
                    </script>
									</div>
								</td>
							</tr>
							<tr>
								<td align="right">&nbsp;&nbsp;&nbsp;<?php echo i18n("displayEndDate");?>&nbsp;&nbsp;</td>
								<td>
									<div dojoType="dijit.form.DateTextBox"
										<?php if (sessionValueExists('browserLocaleDateFormatJs')) {echo ' constraints="{datePattern:\''.getSessionValue('browserLocaleDateFormatJs').'\'}" ';}?>
										id="endDatePlanView" name="endDatePlanView"
										invalidMessage="<?php echo i18n('messageInvalidDate')?>"
										type="text" maxlength="10"
										style="width: 100px; text-align: center;"
										class="input roundedLeft" hasDownArrow="true"
										value="<?php if(sessionValueExists('endDatePlanView')){ echo getSessionValue('endDatePlanView'); }else{ echo $endDate; }  ?>">
										<script type="dojo/method" event="onChange">
                      saveDataToSession('endDatePlanView',formatDate(dijit.byId('endDatePlanView').get("value")), false);
                      refreshJsonPlanning();
                    </script>
									</div>
								</td>
							</tr>
						</table>
					</td>
					<td style="width: 20%;">
						<table>
							<tr>
								<td style="width: 35px;">
									<button title="<?php echo i18n('printPlanning')?>"
										dojoType="dijit.form.Button" id="listPrint" name="listPrint"
										iconClass="dijitButtonIcon dijitButtonIconPrint"
										class="detailButton" showLabel="false">
										<script type="dojo/connect" event="onClick" args="evt">
                                  <?php $ganttPlanningPrintOldStyle=Parameter::getGlobalParameter('ganttPlanningPrintOldStyle');
                                  if (!$ganttPlanningPrintOldStyle) {
                                    $ganttPlanningPrintOldStyle="NO";
                                  }
                                  if ($ganttPlanningPrintOldStyle=='YES') {
                                    ?>
                                   showPrint("../tool/jsonPlanning.php?portfolio=true", 'planning');
                                  <?php } else { ?>
                                    showPrint("planningPrint.php", 'planning');
                                  <?php }?>   
                    </script>
									</button>
								</td>
								<td style="width: 35px;">
									<button title="<?php echo i18n('reportPrintPdf')?>"
										dojoType="dijit.form.Button" id="listPrintPdf"
										name="listPrintPdf"
										iconClass="dijitButtonIcon dijitButtonIconPdf"
										class="detailButton" showLabel="false">
										<script type="dojo/connect" event="onClick" args="evt">
                                  var paramPdf='<?php echo Parameter::getGlobalParameter("pdfPlanningBeta");?>';
                                  if(paramPdf!='false') planningPDFBox();
                                  else showPrint("../tool/jsonPlanning_pdf.php?portfolio=true", 'planning', null, 'pdf');
                    </script>
									</button>
								</td>
							</tr>
						</table>
					</td>
					<td style="width: 15%; text-align: right;">
						<table>
							<tr style="white-space: nowrap;">
								<td style="width: 120px; padding-right: 8px;">
                  <?php echo i18n("labelShowIdleShort");?>
                </td>
								<td style="width: 50px;">
									<div title="<?php echo i18n('showIdleElements')?>"
										dojoType="dijit.form.CheckBox" class="whiteCheck"
										type="checkbox" id="listShowIdle" name="listShowIdle"
										<?php if ($showClosedContract=='1') { echo ' checked="checked" '; }?>>
										<script type="dojo/method" event="onChange">
                              saveUserParameter('contractShowClosed',((this.checked)?'1':'0'));
                             refreshJsonPlanning();
                    </script>
									</div>&nbsp;
								</td>
              </tr>
              <?php if (strtoupper(Parameter::getGlobalParameter('displayResourcePlan'))!='NO') {?>
							<tr style="white-space: nowrap;">
								<td style="width: 120px; padding-right: 8px;">
                <?php echo i18n("labelShowResourceShort");?>
                </td>
								<td style="white-space: nowrap; width: 50px;">
									<div title="<?php echo i18n('showResources')?>"
										dojoType="dijit.form.CheckBox" class="whiteCheck"
										type="checkbox" id="listShowResource" name="listShowResource"
										<?php if ($saveShowResource=='1') { echo ' checked="checked" '; }?>>
										<script type="dojo/method" event="onChange">
                              saveUserParameter('contractGanttShowResource',((this.checked)?'1':'0'));
                              refreshJsonPlanning();
                            </script>
									</div>&nbsp;
							  </td>             
              </tr>
              <?php }?>
						</table>
					</td>
				</tr>
			</table>
		</form>
		<div id="listBarShow" class="dijitAccordionTitle"
			onMouseover="showList('mouse')" onClick="showList('click');">
			<div id="listBarIcon" align="center"></div>
		</div>

		<div dojoType="dijit.layout.ContentPane" id="planningJsonData"
			jsId="planningJsonData" style="display: none">
		  <?php
    include '../tool/jsonContractGantt.php';
    ?>
		</div>
	</div>
	<div dojoType="dijit.layout.ContentPane" region="center"
		id="gridContainerDiv">
		<div id="submainPlanningDivContainer"
			dojoType="dijit.layout.BorderContainer"
			style="border-top: 1px solid #ffffff;">
        <?php
        
$leftPartSize=Parameter::getUserParameter('planningLeftSize');
        if (!$leftPartSize) {
          $leftPartSize='325px';
        }
        ?>
	   <div dojoType="dijit.layout.ContentPane" region="left" splitter="true" 
      style="width:<?php echo $leftPartSize;?>; height:100%; overflow-x:scroll; overflow-y:hidden;" class="ganttDiv" 
      id="leftGanttChartDIV" name="leftGanttChartDIV"
      onScroll="dojo.byId('ganttScale').style.left=(this.scrollLeft)+'px'; this.scrollTop=0;" 
      onWheel="leftMouseWheel(event);">
				<script type="dojo/method" event="onUnload">
         var width=this.domNode.style.width;
         setTimeout("saveUserParameter('planningLeftSize','"+width+"');",1);
         return true;
      </script>
			</div>
			<div dojoType="dijit.layout.ContentPane" region="center"
				style="height: 100%; overflow: hidden;" class="ganttDiv"
				id="GanttChartDIV" name="GanttChartDIV">
				<div id="mainRightPlanningDivContainer"
					dojoType="dijit.layout.BorderContainer">
					<div dojoType="dijit.layout.ContentPane" region="top"
						style="width: 100%; height: 45px; overflow: hidden;"
						class="ganttDiv" id="topGanttChartDIV" name="topGanttChartDIV"></div>
					<div dojoType="dijit.layout.ContentPane" region="center"
						style="width: 100%; overflow-x: scroll; overflow-y: scroll; position: relative; top: -10px;"
						class="ganttDiv" id="rightGanttChartDIV" name="rightGanttChartDIV"
						onScroll="dojo.byId('rightside').style.left='-'+(this.scrollLeft+1)+'px';
                    dojo.byId('leftside').style.top='-'+(this.scrollTop)+'px';">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>