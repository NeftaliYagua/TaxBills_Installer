<?php

include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Package.php';
include_once 'includes/main/WebUI.php';

include_once 'include/Webservices/Utils.php';
//include_once 'vtlib/tools/console.php';
// Creando módulo
$module = new Vtiger_Module();
$module->name = "TaxBills";
$module->parent = 'Inventory';
$module->save();

// iniciando tablas en la base de datos
$module->initTables();
// Creando bloques
$block = new Vtiger_Block();
$block->label = 'LBL_' . strtoupper($module->name) . '_INFORMATION';
$module->addBlock($block);

$blockemision = new Vtiger_Block();
$blockemision->label = 'LBL_EMISION_INFORMATION';
$module->addBlock($blockemision);

$blockdian = new Vtiger_Block();
$blockdian->label = 'LBL_DIAN_INFORMATION';
$module->addBlock($blockdian);

$blockcf = new Vtiger_Block();
$blockcf->label = 'LBL_CUSTOM_INFORMATION';
$module->addBlock($blockcf);
// Creando campo Clave
$field1 = new Vtiger_Field();
$field1->name = "einvoice";
$field1->label = 'E-Invoice';
$field1->isunique=true;
$field1->uitype = 2;
$field1->column = $field1->name;
$field1->columntype = 'VARCHAR(255)';
$field1->typeofdata = 'V~M';
$block->addField($field1);

$module->setEntityIdentifier($field1);

// Creando Campos Generales
$field2 = new Vtiger_Field();
$field2->name = 'assigned_user_id';
$field2->label = 'Assigned To';
$field2->table = 'vtiger_crmentity';
$field2->column = 'smownerid';
$field2->uitype = 53;
$field2->typeofdata = 'V~M';
$block->addField($field2);

$field3 = new Vtiger_Field();
$field3->name = 'createdtime';
$field3->label = 'Created Time';
$field3->table = 'vtiger_crmentity';
$field3->column = 'createdtime';
$field3->uitype = 70;
$field3->typeofdata = 'T~O';
$field3->displaytype = 2;
$block->addField($field3);

$field4 = new Vtiger_Field();
$field4->name = 'modifiedtime';
$field4->label = 'Modified Time';
$field4->table = 'vtiger_crmentity';
$field4->column = 'modifiedtime';
$field4->uitype = 70;
$field4->typeofdata = 'T~O';
$field4->displaytype = 2;
$block->addField($field4);
// Campos de emision

$field5 = new Vtiger_Field();
$field5->name = "pdf";
$field5->label = 'PDF';
$field5->uitype = 2;
$field5->column = $field5->name;
$field5->columntype = 'VARCHAR(255)';
$field6->presence=1;
$field5->typeofdata = 'V~M';
$blockemision->addField($field5);

$field6 = new Vtiger_Field();
$field6->name = "zip";
$field6->label = 'ZIP';
$field6->uitype = 2;
$field6->column = $field6->name;
$field6->columntype = 'text';
$field6->typeofdata = 'V~M';
$field6->presence=1;
$blockemision->addField($field6);

$field7 = new Vtiger_Field();
$field7->name = "xml";
$field7->label = 'XML';
$field7->uitype = 2;
$field6->presence=1;
$field7->column = $field7->name;
$field7->columntype = 'text';
$field7->typeofdata = 'V~M';
$blockemision->addField($field7);

$field8 = new Vtiger_Field();
$field8->name = "status";
$field8->label = 'status';
$field8->uitype = 56;
$field8->column = $field8->name;
$field8->columntype = 'varchar(3)';
$field8->typeofdata = 'C~O';
$blockemision->addField($field8);
/*
$field9 = new Vtiger_Field();
$field9->name = "status2";
$field9->label = 'status2';
$field9->uitype = 56;
$field9->column = $field9->name;
$field9->columntype = 'varchar(3)';
$field9->typeofdata = 'C~O';
$blockemision->addField($field9);

$field10 = new Vtiger_Field();
$field10->name = "status3";
$field10->label = 'status3';
$field10->uitype = 2;
$field10->column = $field10->name;
$field10->columntype = 'varchar(3)';
$field10->typeofdata = 'C~O';
$blockdian->addField($field10);
*/

// Create default custom filter (mandatory)
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$module->addFilter($filter1);
// Add fields to the filter created
$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2);

// Set sharing access of this module
$module->setDefaultSharing();

// Enable and Disable available tools
//$module->enableTools(Array('Import', 'Export', 'Merge'));
$module->enableTools(Array('Import', 'Export'));
$module->disableTools('Merge');

// Initialize Webservice support
$module->initWebservice();



$targetpath = 'modules/' . $module->name;

if (!is_file($targetpath)) {
    mkdir($targetpath);
    mkdir($targetpath . '/language');

    $templatepath = 'vtlib/ModuleDir/6.0.0';

    $moduleFileContents = file_get_contents($templatepath . '/ModuleName.php');
    $replacevars = array(
        'ModuleName' => $module->name,
        '<modulename>' => strtolower($module->name),
        '<entityfieldlabel>' => $field1->label,
        '<entitycolumn>' => $field1->column,
        '<entityfieldname>' => $field1->name,
    );

    foreach ($replacevars as $key => $value) {
        $moduleFileContents = str_replace($key, $value, $moduleFileContents);
    }
    file_put_contents($targetpath . '/' . $module->name . '.php', $moduleFileContents);
}


Settings_MenuEditor_Module_Model::addModuleToApp($module->name, $module->parent);

