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

/* ============================================================================
 * Line defines right to the application for a menu and a profile.
 */  
require_once('_securityCheck.php'); 
class ProjectSituationMain extends SqlElement {
  
  public $_sec_Description;
  public $id;
  public $idProject;
  public $name;
  public $idle;
  public $situationNameExpense;
  public $refTypeExpense;
  public $refIdExpense;
  public $situationDateExpense;
  public $idResourceExpense;
  public $situationNameIncome;
  public $refTypeIncome;
  public $refIdIncome;
  public $situationDateIncome;
  public $idResourceIncome;
  
  public $_sec_SituationExpense;
  public $_spe_SituationExpense;
  
  public $_sec_SituationIncome;
  public $_spe_SituationIncome;
  
  public $_nbColMax=2;
  public $_readOnly = true;
  
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="4%" ># ${id}</th>
    <th field="nameProject" width="8%" >${idProject}</th>
    <th field="name" width="8%" >${name}</th>
    <th field="refTypeExpense" width="8%" formatter="translateFormatter">${refTypeExpense}</th>
    <th field="refIdExpense" width="4%">${refIdExpense}</th>
    <th field="situationNameExpense" width="11%">${situationNameExpense}</th>
    <th field="situationDateExpense" width="6%" formatter="dateTimeFormatter">${situationDateExpense}</th>
    <th field="nameResourceExpense" formatter="thumbName22" width="8%">${idResourceExpense}</th>
    <th field="refTypeIncome" width="8%" formatter="translateFormatter">${refTypeIncome}</th>
    <th field="refIdIncome" width="4%">${refIdIncome}</th>
    <th field="situationNameIncome" width="11%">${situationNameIncome}</th>
    <th field="situationDateIncome" width="6%" formatter="dateTimeFormatter">${situationDateIncome}</th>
    <th field="nameResourceIncome" formatter="thumbName22" width="8%">${idResourceIncome}</th>
    <th field="idle" width="4%" formatter="booleanFormatter" >${idle}</th>';

  
  private static $_fieldsAttributes=array(
        '_sec_Description'=>'hidden',
  );
  
  private static $_colCaptionTransposition = array();
  
  /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */
  function __construct($id = NULL, $withoutDependentObjects=false) {
  	parent::__construct($id,$withoutDependentObjects);
  	
  }
  
  /** ==========================================================================
   * Destructor
   * @return void
   */
  function __destruct() {
  	parent::__destruct();
  }
  
  function save() {
  	return parent::save();
  }
  
  protected function getStaticColCaptionTransposition($fld=null) {
  	return self::$_colCaptionTransposition;
  }
  
  protected function getStaticLayout() {
  	return self::$_layout;
  }
  
  protected function getStaticFieldsAttributes() {
  	return self::$_fieldsAttributes;
  }
  
  public function drawSpecificItem($item){
  	global $print;
  	$result="";
  	if($item == 'SituationExpense'){
  		drawProjectSituation('Expense', $this);
  	}else if($item == 'SituationIncome'){
  		drawProjectSituation('Income', $this);
  	}
  	return $result;
  }
  
  }
?>
