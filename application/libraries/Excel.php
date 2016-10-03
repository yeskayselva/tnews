<?php if( !defined('BASEPATH')) exit('No direct script access allowed');
 
 
require_once APPPATH."/libraries/PHPExcel.php"; 
 
class Excel extends PHPExcel { 
 
     
    public function __construct() {
    	
         
    }
    
	function create_excel_file($header,$file_name_path)
	{

		$CI =& get_instance();
		$CI->load->library('PHPExcel');		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
				    ->setCreator("Notionpress Publishing Solutions")
				    ->setLastModifiedBy("Notionpress Publishing Solutions")
				    ->setSubject("PRIS/EPZ Orders");
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$styleArray = array(
		    'font' => array(
		        'bold' => true
		    ),
		    'alignment' => array(
		        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		    ),
		 'borders' => array(
		        'allborders' => array(
		            'style' => PHPExcel_Style_Border::BORDER_THIN,
		            'color' => array('argb' => '00000000'),
		        ),
		    ),
		);

		$objPHPExcel->getActiveSheet()->getStyle('A1:AD1')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()
		    ->getStyle('D1')
		    ->getNumberFormat()
		    ->setFormatCode(
		        PHPExcel_Style_NumberFormat::FORMAT_GENERAL
		    );
		$objPHPExcel->getActiveSheet()->fromArray($header, NULL, 'A1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		$objWriter->save(str_replace(__FILE__,$file_name_path,__FILE__));

		$objPHPExcel->disconnectWorksheets();
		unset($objPHPExcel);
		return  $file_name_path;

		//return $file_name_path;
	}
	
	public function download_excel($data,$file = FALSE){
		
		$file_name_path =  FCPATH.'/report_files/';	
		
		if($file == FALSE)
		$file = date('Ymd').'_.xls';
		$filename = $file_name_path.$file;
		
		$this->create_excel_file($data,$filename);
		
		$file_url = base_url('report_files/'.$file);
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
		readfile($file_url); // do the double-download-dance (dirty but worky)*/
	}
}
