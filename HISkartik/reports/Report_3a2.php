<?php
namespace app\reports;
use Yii;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
?>

<?php

class Report_3a2 extends Report{
	protected $name = 'report_3a2';
	//protected $pdf;
	//protected $csv;
	//protected $searchMinInclusive; //assumed is date
	//protected $searchMaxInclusive; //assumed is date
	//protected $generateDate;
	protected $storedProcedureName = 'report_3a2'; //Enter just 'report1_query' if //CALL report1_query(:startDate, :endDate) // leave :startDate... as is 
	//protected $storedProcedureArg1Name = 'startDate';
	//protected $storedProcedureArg2Name = 'endDate';
	//protected $storedProcedureGridviewCols = [];
	//protected $rawData;
	//protected $pdf_dataIterator;
	
	public function export_pdf(){}
	
	protected function pdf_layout(){}
	protected function pdf_pageGroupSplitter(){}
	protected function pdf_header(){}
	protected function pdf_footer(){}
	protected function pdf_gridLoop(){}


}







?>