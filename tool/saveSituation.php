<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2017 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
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

/** ===========================================================================
 * Save a situation : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 */

require_once "../tool/projeqtor.php";

// Get the situation info
$refType=RequestHandler::getClass('situationRefType');
$refId=RequestHandler::getId('situationRefId');
$action = RequestHandler::getValue('action');
$situationSituation=RequestHandler::getValue('situationSituation');
$situationComment=RequestHandler::getValue('situationComment');
$situationId=RequestHandler::getId('situationId');
$situationId=trim($situationId);
if ($situationId=='') {
  $situationId=null;
}
$idProject=RequestHandler::getId('idProject');
$idRessource=RequestHandler::getId('ressource');
$situationType=null;
$date = RequestHandler::getValue('situationDate');
$time = RequestHandler::getValue('situationTime');
$dateTime = $date.' '.substr($time, 1);

Sql::beginTransaction();
// get the modifications (from request)
if ($refType=='CallForTender' or $refType=='Tender' or $refType=='ProviderOrder' or $refType=='ProviderBill') {
  $situationType='expense';
}else{
  $situationType='income';
}
if ($situationId) {
	$situation=new Situation($situationId);
} else {
	$situation=new Situation();
}

if($action == 'remove'){
  $result=$situation->delete();
}else{
  $situation->idUser=getCurrentUserId();
  $situation->refId=$refId;
  $situation->refType=$refType;
  $situation->idProject = $idProject;
  $situation->idResource=$idRessource;
  $situation->name=$situationSituation;
  $situation->situationType=$situationType;
  $situation->date = $dateTime;
  $situation->comment=$situationComment;
  $result=$situation->save();
}
// Message of correct saving
displayLastOperationStatus($result);

$actualSituation = new Situation();
$obj = new $refType($refId);
$critWhere = array('refType'=>$refType,'refId'=>$refId,'idProject'=>$obj->idProject);
$situationList = $situation->getSqlElementsFromCriteria($critWhere,null,null, 'date desc');
if(count($situationList) > 0){
	$actualSituation = $situationList[0];
}
if($actualSituation->id and $actualSituation->id != $obj->idSituation){
	$obj->idSituation = $actualSituation->id;
	$obj->save();
	$projectSituation = SqlElement::getSingleSqlElementFromCriteria('ProjectSituation', array('idProject'=>$actualSituation->idProject));
	$projectName = SqlList::getNameFromId('Project', $actualSituation->idProject);
	$projectSituation->name = i18n('ProjectSituation').' - '.$projectName;
	if($actualSituation->situationType == 'expense'){
		$projectSituation->refIdExpense = $actualSituation->refId;
		$projectSituation->refTypeExpense = $actualSituation->refType;
		$projectSituation->idResourceExpense = $actualSituation->idResource;
		$projectSituation->situationNameExpense = $actualSituation->name;
		$projectSituation->situationDateExpense = $actualSituation->date;
	}else{
		$projectSituation->refIdIncome = $actualSituation->refId;
		$projectSituation->refTypeIncome = $actualSituation->refType;
		$projectSituation->idResourceIncome = $actualSituation->idResource;
		$projectSituation->situationNameIncome = $actualSituation->name;
		$projectSituation->situationDateIncome = $actualSituation->date;
	}
	$projectSituation->save();
}
?>