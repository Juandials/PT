<?php

/*
 * @author: qCazelles 
 */

require_once "../tool/projeqtor.php";
scriptLog('   ->/view/hierarchicalBudgetMain.php');
//florent
$paramScreen=RequestHandler::getValue('paramScreen');
$paramLayoutObjectDetail=RequestHandler::getValue('paramLayoutObjectDetail');
$paramRightDiv=RequestHandler::getValue('paramRightDiv');
$currentScreen='HierarchicalBudget';
setSessionValue('currentScreen', $currentScreen);
$positionListDiv=changeLayoutObjectDetail($paramScreen,$paramLayoutObjectDetail);
$positonRightDiv=changeLayoutActivityStream($paramRightDiv);
$codeModeLayout=Parameter::getUserParameter('paramScreen');
if ($positionListDiv=='top'){
  $listHeight=HeightLayoutListDiv($currentScreen);
}
if($positonRightDiv=="bottom"){
  $rightHeightHierarchicalBudget=getHeightLaoutActivityStream($currentScreen);
}else{
  $rightWidthHierarchicalBudget=getWidthLayoutActivityStream($currentScreen);
}
$tableWidth=WidthDivContentDetail($positionListDiv,$currentScreen);
$activModeStream=Parameter::getUserParameter('modeActiveStreamGlobal');
//////

if (! isset($comboDetail)) {
	$comboDetail=false;
}
$objectClass='Budget';
Security::checkValidClass($objectClass);
$objectType='';
if (array_key_exists('objectType',$_REQUEST)) {
	$objectType=$_REQUEST['objectType'];
}
$objectId=RequestHandler::getId('objectId');
$obj=new $objectClass;

$displayWidthList="1980";
if (RequestHandler::isCodeSet('destinationWidth')) {
	//$displayWidthList=RequestHandler::getNumeric('destinationWidth');
}
$rightWidthVal=0;
if (isset($rightWidth)) {
	if (substr($rightWidth,-1)=="%") {
		$rightWidthVal=(intval(str_replace('%', '', $rightWidth))/100)*$displayWidthList;
	} else {
		$rightWidthVal=intval(str_replace('px', '', $rightWidth));
	}
} else {
	$detailRightDivWidth=Parameter::getUserParameter('contentPaneRightDetailDivWidth'.$currentScreen);
	if (!$detailRightDivWidth) $detailRightDivWidth=0;
	if($detailRightDivWidth or $detailRightDivWidth==="0"){
		$rightWidthVal=$detailRightDivWidth;
	} else {
		$rightWidth=0;//15/100*$displayWidthList;
	}
}
$displayWidthList-=$rightWidthVal;

$hideTypeSearch=false;
$hideClientSearch=false;
$hideParentBudgetSearch=false;
$hideNameSearch=false;
$hideIdSearch=false;
$hideShowIdleSearch=false;
$hideEisSearch=false;
$referenceWidth=50;
if ($comboDetail) {
	$screenWidth=getSessionValue('screenWidth',$displayWidthList);
	$displayWidthList=round($screenWidth*0.55,0)+150;
}
if ($displayWidthList<1400) {
	$referenceWidth=40;
	if ($displayWidthList<1250) {
		$hideParentBudgetSearch=true;
		$referenceWidth=30;
		if ($displayWidthList<1165) {
			$hideClientSearch=true;
			$hideEisSearch=true;
			if ($displayWidthList<1025) {
				$hideTypeSearch=true;
				if ($displayWidthList<700) {
					$hideIdSearch=true;
					if ($displayWidthList<650) {
						$hideShowIdleSearch=true;
						if ($displayWidthList<550) {
							$hideNameSearch=true;
						}
					}
				}
			}
		}
	}
}
$extrahiddenFields=$obj->getExtraHiddenFields('*','*');

$showFullAmount = false;
if(getSessionValue('showFullAmountHierarchicalBudget')){
  $showFullAmount=(getSessionValue('showFullAmountHierarchicalBudget')=='true')?true:false;
}else{
  $showFullAmount=(Parameter::getGlobalParameter('ImputOfAmountProvider')== 'HT')?false:true;
}
$showClosed=(getSessionValue('listShowIdleBudget')=='on')?true:false;

$budgetParent=getSessionValue('listBudgetParentFilter');
?>
<input type="hidden" name="objectClassManual" id="objectClassManual" value="HierarchicalBudget" />
<input type="hidden" name="HierarchicalBudget" id="HierarchicalBudget" value="true" />
<div id="mainDivContainer" class="container" dojoType="dijit.layout.BorderContainer" onclick="hideDependencyRightClick();">
<div dojoType="dijit.layout.ContentPane" region="top" id="listHeaderDiv" style="width:50%;">
<table width="100%" class="listTitle" >
  <tr >
    <td style="width:50px;min-width:43px;" align="center">
       <div style="position:absolute;left:0px;width:43px;top:0px;height:36px;" class="iconHighlight">&nbsp;</div>
       <div style="position:absolute; top:0px;left:5px ;" class="icon<?php echo $currentScreen;?>32 icon<?php echo $currentScreen;?> iconSize32" /></div>
    </td>
    <td class="title" style="height:35px;width:20%;">
      <div style="width:100%;height:100%;position:relative;">
        <div id="menuName" style="width:100%;position:absolute;top:8px;text-overflow:ellipsis;overflow:hidden;"><span id="classNameSpan" style="padding-left:5px;"><?php echo i18n("menuHierarchicalBudget");?></span></div>
      </div>
    </td>
                   <td style="vertical-align: middle; text-align:right;" width="5px" class="allSearchTD parentBudgetSearchTD allSearchFixLength">
                 <span class="nobr">&nbsp;&nbsp;&nbsp;
                <?php echo i18n("colParentBudget");?>
                &nbsp;</span>
              </td>
              <td width="5px" class="allSearchTD parentBudgetSearchTD">
                <select title="<?php echo i18n('filterOnBudgetParent')?>" type="text" class="filterField roundedLeft" dojoType="dijit.form.FilteringSelect"
                <?php echo autoOpenFilteringSelect();?> 
                data-dojo-props="queryExpr: '*${0}*',autoComplete:false"
                id="listBudgetParentFilter" name="listBudgetParentFilter" style="width:<?php echo $referenceWidth*4;?>px" value="<?php if(!$comboDetail and sessionValueExists('listBudgetParentFilter')){ echo getSessionValue('listBudgetParentFilter'); }?>" >
                  <?php 
                   //gautier #indentBudget
                   htmlDrawOptionForReference('idBudgetItem',$budgetParent,$obj,false);?>
                  <script type="dojo/method" event="onChange" >
                    var callBack=function() {refreshHierarchicalBudgetList();};
                    saveDataToSession('listBudgetParentFilter', this.value, false,callBack);
                  </script>
                </select>
              </td>
    <td style="height:35px;width:80%; text-align:right">
    <?php if($positionListDiv == 'left'){ $style='float:right;margin-left:40%;width:12%';}else{$style='float:right;margin-right: 2%;width:12%;';}?>
        <table style="float:right; margin-right:5px">
          <tr>
            <td>
              <label for="showFullAmount" class="Label" style="text-shadow: 0px 0px;margin-left: 5px;width:150px;text-align:left"><?php echo i18n('showFullAmount')?></label>  
            </td>
            <td>
              <div title="<?php echo i18n('showFullAmount')?>" dojoType="dijit.form.CheckBox" type="checkbox" class="whiteCheck"
                id="showFullAmount" name="showFullAmount" <?php if ($showFullAmount) echo "checked=ckecked"?>>
                <script type="dojo/method" event="onChange" >
                  var callBack=function() {refreshHierarchicalBudgetList();};
                  saveDataToSession('showFullAmountHierarchicalBudget', this.checked, false,callBack);
                  
                </script>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <label for="showClosed" class="Label" style="text-shadow: 0px 0px;margin-left: 5px;width:150px;text-align:left"><?php echo i18n('labelShowIdle')?></label>  
            </td>
            <td>
              <div title="<?php echo i18n('labelShowIdle')?>" dojoType="dijit.form.CheckBox" type="checkbox" class="whiteCheck"
                id="showClosed" name="showClosed" <?php if ($showClosed) echo "checked=ckecked"?>>
                <script type="dojo/method" event="onChange" >
                  var callBack=function() {refreshHierarchicalBudgetList();};
                  saveDataToSession('listShowIdleBudget', ((this.checked)?'on':'off'), false,callBack);
                  var selectedParent=dijit.byId('listBudgetParentFilter').get('value');
                  if (! selectedParent || selectedParent==' ') selectedParent=null;
                  refreshList('idBudgetParent','showIdle',dijit.byId('showClosed').get('value'),selectedParent,'listBudgetParentFilter');
                </script>
              </div>
            </td>
          </tr>
        </table>
    </td>
  </tr>
</table>
</div>
  <div id="listDiv" dojoType="dijit.layout.ContentPane" region="<?php  echo $positionListDiv;?>" splitter="true" 
   style="<?php if($positionListDiv=='top'){echo "height:".$listHeight;}else{ echo "width:".$tableWidth[0];}?>;overflow-y: none;">
    <script type="dojo/connect" event="resize" args="evt">
         if (switchedMode) return;
         var paramDiv=<?php  echo json_encode($positionListDiv); ?>;
         var paramMode=<?php  echo json_encode($codeModeLayout); ?>;
         if(paramDiv=="top" && paramMode!='switch'){
             saveContentPaneResizing("contentPaneTopDetailDivHeight<?php echo $currentScreen;?>", dojo.byId("listDiv").offsetHeight, true);
          }else{
            saveContentPaneResizing("contentPaneTopDetailDivWidth<?php  echo $currentScreen;?>", dojo.byId("listDiv").offsetWidth, true);
          }
         var headerHeight=dojo.byId('hierarchicalBudgetListHeader').offsetHeight;
         var totalHeight=dojo.byId('listDiv').offsetHeight;
         dojo.byId('hierarchicalBudgetListDiv').style.height=(totalHeight-headerHeight)+'px';

         dojo.byId('hierarchicBudgetScrollSpace').style.width=(dojo.byId('hierarchicalBudgetListDiv').offsetWidth-dojo.byId('hierarchicalBudgetListDiv').clientWidth)+'px';
    </script>
    <form dojoType="dijit.form.Form" id="listForm" action="" method="" >
      <input type="hidden" name="objectClass" id="objectClass" value="Budget" />
      <input type="hidden" id="objectId" name="objectId" value="<?php if (isset($_REQUEST['objectId']))  { echo htmlEncode($_REQUEST['objectId']);}?>"/>
    </form>
    <div id="hierarchicalListDiv" dojoType="dijit.layout.ContentPane" name="hierarchicalListDiv"  style="overflow-x:auto;overflow-y:hidden;height:100%">
    <?php include 'hierarchicalBudgetView.php'?>
    </div>
  </div>
  <div id="contentDetailDiv" dojoType="dijit.layout.ContentPane" region="center"   style="width:<?php  echo $tableWidth[1]; ?>;">
      <script type="dojo/connect" event="resize" args="evt">
           var paramDiv=<?php  echo json_encode($positionListDiv); ?>;
           var paramRightDiv=<?php echo json_encode($positonRightDiv);?>;
           var paramMode=<?php  echo json_encode($codeModeLayout); ?>;
           if (checkValidatedSize(paramDiv,paramRightDiv, paramMode, paramMode)){
            return;
           }
           if(paramDiv=="top" && paramMode!='switch'){
             saveContentPaneResizing("contentPaneDetailDivHeight<?php  echo $currentScreen;?>", dojo.byId("contentDetailDiv").offsetHeight, true);
           }else if(paramMode!='switch'){
              saveContentPaneResizing("contentPaneDetailDivWidth<?php  echo $currentScreen;?>", dojo.byId("contentDetailDiv").offsetWidth, true);
              var param=dojo.byId('objectClass').value;
              var paramId=dojo.byId('objectId').value;
              if(paramId !='' && multiSelection==false){
                loadContent("objectDetail.php?objectClass"+param+"&objectId="+paramId, "detailDiv", 'listForm');  
              }else if(multiSelection==true){
               loadContent('objectMultipleUpdate.php?objectClass=' + param,
                  'detailDiv');
              }
            }
      </script>
     <div class="container" dojoType="dijit.layout.BorderContainer"  liveSplitters="false">
        <div id="detailBarShow" class="dijitAccordionTitle" onMouseover="hideList('mouse');" onClick="hideList('click');"
          <?php  if (RequestHandler::isCodeSet('switchedMode') and RequestHandler::getValue('switchedMode')=='on') echo ' style="display:block;"'?>>
          <div id="detailBarIcon" align="center"></div>
        </div>
        <div id="detailDiv" dojoType="dijit.layout.ContentPane" region="center" >
          <?php  $noselect=true; //include 'objectDetail.php'; ?>
        </div>
    <?php if (Module::isModuleActive('moduleActivityStream')) {?>
        <div id="detailRightDiv" dojoType="dijit.layout.ContentPane" region="<?php echo $positonRightDiv; ?>" splitter="true" 
             style="<?php  if($positonRightDiv=="bottom"){echo "height:".$rightHeightHierarchicalBudget;}else{ echo "width:".$rightWidthHierarchicalBudget;}?>">
              <script type="dojo/connect" event="resize" args="evt">
                var paramDiv=<?php echo json_encode($positionListDiv); ?>;
                var paramMode=<?php echo json_encode($codeModeLayout); ?>;
                var paramRightDiv=<?php echo json_encode($positonRightDiv); ?>;
                var activModeStream=<?php echo json_encode($activModeStream);?>;
                hideSplitterStream (paramRightDiv);
                if (checkValidatedSizeRightDiv(paramDiv,paramRightDiv, paramMode)){
                    return;
                }
                if(paramRightDiv=='trailing'){
                   saveContentPaneResizing("contentPaneRightDetailDivWidth<?php  echo $currentScreen;?>", dojo.byId("detailRightDiv").offsetWidth, true);
                   var newWidth=dojo.byId("detailRightDiv").offsetWidth;
                   dojo.query(".activityStreamNoteContainer").forEach(function(node, index, nodelist) {
                      node.style.maxWidth=(newWidth-30)+"px";
                   });
                }else{
                  saveContentPaneResizing("contentPaneRightDetailDivHeight<?php  echo $currentScreen;?>", dojo.byId("detailRightDiv").offsetHeight, true);
                  var newHeight=dojo.byId("detailRightDiv").offsetHeight;
                  if (dojo.byId("noteNoteStream")) dojo.byId("noteNoteStream").style.height=(newHeight-40)+'px';
               }
                 
              </script>
              <script type="dojo/connect" event="onLoad" args="evt">
                scrollInto();
	         </script>
            <?php include 'objectStream.php'?>
      </div> 
      <?php }?>  
    </div>
  </div>
</div>