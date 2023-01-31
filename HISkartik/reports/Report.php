<?php
namespace app\reports;
use Yii;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
?>

<?php

class Report{
	protected $name;
	protected $pdf;
	protected $csv;
	protected $searchMinInclusive; //assumed is date
	protected $searchMaxInclusive; //assumed is date
	protected $generateDate;
	protected $storedProcedureName; //Enter just 'report1_query' if //CALL report1_query(:startDate, :endDate) // leave :startDate... as is 
	protected $storedProcedureArg1Name = 'startDate';
	protected $storedProcedureArg2Name = 'endDate';
	protected $storedProcedureGridviewCols = [];
	protected $rawData;
	protected $pdf_dataIterator;
	const reportList = [
			'Report_3a1' => 'Report 3A1: Senarai Bil-Bil Hospital Yang Sudah Dibayar',
			'Report_3a2' => 'Report 3A2: Senarai Bil-Bil Hospital Yang Belum Dibayar',
			'Report_3a3' => 'Report 3A3: Senarai Bil-Bil Hospital Yang Dikecualikan Keseluruhannya',
			'Report_3a4' => 'Report 3A4: Senarai Bil-Bil Hospital Yang Dibatalkan',
			'Report_3a5' => 'Report 3A5: Senarai Bil-Bil Hospital',
			'Report_3b1' => 'Report 3B1: Senarai Bayaran Bil Hospital',
			'Report_3b2' => 'Report 3B2: Senarai Bayaran Bil Lain-Lain Hospital',
			'Report_3b3' => 'Report 3B3: Senarai Bayaran Deposit Hospital',
			'Report_3b4' => 'Report 3B4: Senarai Bayaran Hospital Yang Dikecualikan',
			'Report_3c' => 'Report 3C: Senarai Pungutan Menurut Operator',
			'Report_3d' => 'Report 3D: Senarai Bayaran Yang Dikembalikan',
			'Report_4' => 'Report 4: Senarai Bil-Bil Hospital Yang Belum Dijelaskan (Swasta)',
			'Report_5' => 'Report 5: Senarai Bil-Bil Hospital Yang Belum Dijelaskan (Bagi Pesakit Awam)',
			'Report_6' => 'Report 6: Senarai Bil-Bil Hospital Yang Belum Dijelaskan (Awam)',
			'Report_15' => 'Report 15: Penyata Bulanan Pesakit-Pesakit Yang Meminta Pengecualian Bayaran',
			'Report_16a' => 'Report 16a: Senarai Bil-Bil Hospital Yang Belum Dibayar',
			'Report_16b' => 'Report 16b: Senarai Bil-Bil Hospital Yang Belum Dibayar Menurut Kelas',
			'Report_16b1' => 'Report 16b1: Senarai Bil-Bil Hospital Warganegara Yang Belum Dibayar Menurut Kelas',
			'Report_16b2' => 'Report 16b2: Senarai Bil-Bil Hospital Orang Asing Yang Belum Dibayar Menurut Kelas',
			'Report_17' => 'Report 17: Senarai Bil-Bil Hospital',
		];
	
	public function __construct($searchMinInclusive, $searchMaxInclusive){ //require arg format 'Y-m-d'
		$this->searchMinInclusive = $searchMinInclusive;
		$this->searchMaxInclusive = $searchMaxInclusive;
		$this->generateDate = date('Y-m-d');
		$this->loadStoredProcedureData();
	}
	
	protected function loadStoredProcedureData(){
        $this->rawData = \yii::$app->db->createCommand('CALL '.$this->storedProcedureName.'(:'.$this->storedProcedureArg1Name.', :'.$this->storedProcedureArg2Name.')')
        ->bindValue( $this->storedProcedureArg1Name, $this->searchMinInclusive)
        ->bindValue( $this->storedProcedureArg2Name, $this->searchMaxInclusive)
        ->queryAll();
	}
	
	public function export_csv(){
		$filename = $this->name.'_'.$this->searchMinInclusive.'_to_'.$this->searchMaxInclusive.'_AsOn_'.$this->generateDate.'.csv'; 
		$data = new ArrayDataProvider(['allModels' => $this->rawData]);
		
        $exporter = new CsvGrid([
            'dataProvider' => $data,
			'columns' => $this->storedProcedureGridviewCols,
			//will split to multiple csvs if goes over this limit. 
			//'Open Office' and 'MS Excel 97-2003' allows maximum 65536 rows per CSV file, 'MS Excel 2007' - 1048576.
            'maxEntriesPerFile' => 60000, 
			
        ]);
		
		return $exporter->export()->send($filename); // displays dialog for saving `items.csv.zip`!
	}

	public function export_pdf(){}
	
	protected function pdf_layout(){}
	protected function pdf_pageGroupSplitter(){}
	protected function pdf_header(){}
	protected function pdf_footer(){}
	protected function pdf_gridLoop(){}


}







?>