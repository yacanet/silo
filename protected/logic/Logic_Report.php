<?php
prado::using ('Application.Logic.Logic_Obat');
class Logic_Report extends Logic_Obat {	
    /**
	* mode dari driver
	*
	*/
	private $driver;
	/**
	* object dari driver2 report misalnya PHPExcel, TCPDF, dll.
	*
	*/
	public $rpt;	
    /**
	* object setup;	
	*/
	public $setup;	
    /**
	* object tanggal;	
	*/
    public $tgl;
	/**
	* Exported Dir
	*
	*/
	private $exportedDir;	
	/**
	* posisi row sekarang
	*
	*/
	public $currentRow=1;		
    /**
     * 
     * data report	
	*/
	public $dataReport;	
	public function __construct ($db) {
		parent::__construct ($db);	
        $this->setup = $this->getLogic ('Setup');
		$this->tgl = $this->getLogic ('Penanggalan');
	}		
    /**
     * digunakan untuk mengeset data report
     * @param type $dataReport
     */
    public function setDataReport ($dataReport) {
        $this->dataReport=$dataReport;
    }
    /**
	*
	* set mode driver
	*/
	public function setMode ($driver) {
		$this->driver = $driver;
		$path = dirname($this->getPath()).'/';								
		$host=$this->setup->getAddress().'/';				
		switch ($driver) {
            case 'excel2003' :								
                $phpexcel=BASEPATH.'protected/lib/excel/';
                define ('PHPEXCEL_ROOT',$phpexcel);
                set_include_path(get_include_path() . PATH_SEPARATOR . $phpexcel);
                
                require_once ('PHPExcel.php');                
				$this->rpt=new PHPExcel();                
                $this->exportedDir['excel_path']=$host.'exported/excel/';
				$this->exportedDir['full_path']=$path.'exported/excel/';
			break;
			case 'excel2007' :							
                //phpexcel
                $phpexcel=BASEPATH.'protected/lib/excel/';
                define ('PHPEXCEL_ROOT',$phpexcel);
                set_include_path(get_include_path() . PATH_SEPARATOR . $phpexcel);
                
                require_once ('PHPExcel.php');
				$this->rpt=new PHPExcel();                
                $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
                $cacheSettings = array( 
                    'cacheTime' => 600
                );
                PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				$this->exportedDir['excel_path']=$host.'exported/excel/';
				$this->exportedDir['full_path']=$path.'exported/excel/';
			break;					
            case 'pdf' :				
                require_once (BASEPATH.'protected/lib/tcpdf/tcpdf.php');
				$this->rpt=new TCPDF();			
				$this->rpt->setCreator ($this->Application->getID());
				$this->rpt->setAuthor ($this->setup->getSettingValue('config_name'));
				$this->rpt->setPrintHeader(false);
				$this->rpt->setPrintFooter(false);				
				$this->exportedDir['pdf_path']=$host.'exported/pdf/';	
				$this->exportedDir['full_path']=$path.'exported/pdf/';
			break;	
		}
	}
    /**
     * digunakan untuk mendapatkan driver saat ini
     */
	public function getDriver () {
        return $this->driver;
    }
    /**
	* set header logo;	
	*/
	public function setHeaderLogo () {
		$headerLogo=BASEPATH.$this->setup->getSettingValue('config_logo');       
		switch ($this->driver) {
            case 'excel2003' :
                //drawing
				$drawing = new PHPExcel_Worksheet_Drawing();		
				$drawing->setName('Logo ');
				$drawing->setDescription('Logo');			
				
				$drawing->setPath($headerLogo);
				$drawing->setHeight(90);
				$drawing->setCoordinates('A'.$this->currentRow);
				$drawing->setOffsetX(90);
				$drawing->setRotation(25);
				$drawing->getShadow()->setVisible(true);
				$drawing->getShadow()->setDirection(45);
				$drawing->setWorksheet($this->rpt->getActiveSheet());
            break;
			case 'excel2007' :
				//drawing
				$drawing = new PHPExcel_Worksheet_Drawing();		
				$drawing->setName('Logo');
				$drawing->setDescription('Logo');			
				
				$drawing->setPath($headerLogo);
				$drawing->setHeight(90);
				$drawing->setCoordinates('A'.$this->currentRow);
				$drawing->setOffsetX(10);
				$drawing->setRotation(0);
				$drawing->getShadow()->setVisible(true);
				$drawing->getShadow()->setDirection(45);
				$drawing->setWorksheet($this->rpt->getActiveSheet());
			break;			            
		}		
	}    
    /**
	* digunakan untuk mencetak header 
	*
	*/
	public function setHeader ($endColumn=null,$alignment=null,$columnHeader='C') {			
		switch ($this->driver) {
			case 'excel2003' :
			case 'excel2007' :	
                //cetak logo
                $this->setHeaderLogo();				
				$row=1;
                $sheet=$this->rpt->getActiveSheet();
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'PEMERINTAH KABUPATEN BINTAN');
				
				$row+=1;
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'DINAS KESEHATAN');
				
				$row+=1;
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'JL. TANJUNGPINANG – TG. UBAN KM. 42, BANDAR SERI BENTAN, BINTAN');
				
				$row+=1;
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'');
								
				$sheet->getStyle($columnHeader.($row-3))->getFont()->setSize('10');
				$sheet->getStyle($columnHeader.($row-2))->getFont()->setSize('12');	
				$sheet->getStyle($columnHeader.($row-1))->getFont()->setSize('10');				
				$sheet->getStyle($columnHeader.$row)->getFont()->setSize('10');
				
				
				$sheet->duplicateStyleArray(array(
												'font' => array('bold' => true),
												'alignment' => array('horizontal'=>$alignment,
														'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)					   	
												),
												$columnHeader.$this->currentRow.':'.$columnHeader.$row
											);				
				$this->currentRow=$row;
			break;			
		}		
    }
    /**
	* digunakan untuk mencetak header 
	*
	*/
	public function setHeaderPuskesmas ($endColumn=null,$alignment=null,$columnHeader='C') {			
		switch ($this->driver) {
            case 'pdf' :
                $this->rpt->AddPage();
                
                $row=6;
                $this->rpt->SetFont ('helvetica','B',10);
                $this->rpt->setXY(5,$row);
                $this->rpt->Cell (20,5,$nama_sertifikat,0,0,'L');
                $this->rpt->Cell (180,5,$nama_sertifikat,0,0,'R');
                
                $row+=15;
                $this->rpt->Image($headerLogo,90,$row,34,27); 
            break;
			case 'excel2003' :
			case 'excel2007' :	
                //cetak logo
                $this->setHeaderLogo();				
				$row=1;
                $sheet=$this->rpt->getActiveSheet();
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'DINAS KESEHATAN');
				
				$row+=1;
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'KABUPATEN BINTAN');
				
				$row+=1;
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'UPT PUSKESMAS '.$this->dataReport['nama_puskesmas']);
                $row+=1;
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,$this->dataReport['alamat_ka']);
				
				$row+=1;
				$sheet->getRowDimension($row)->setRowHeight(18);
				$sheet->mergeCells ($columnHeader.$row.':'.$endColumn.$row);
				$sheet->setCellValue($columnHeader.$row,'');
								
				$sheet->getStyle($columnHeader.($row-3))->getFont()->setSize('10');
				$sheet->getStyle($columnHeader.($row-2))->getFont()->setSize('12');	
				$sheet->getStyle($columnHeader.($row-1))->getFont()->setSize('10');				
				$sheet->getStyle($columnHeader.$row)->getFont()->setSize('10');
				
				
				$sheet->duplicateStyleArray(array(
												'font' => array('bold' => true),
												'alignment' => array('horizontal'=>$alignment,
														'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)					   	
												),
												$columnHeader.$this->currentRow.':'.$columnHeader.$row
											);				
				$this->currentRow=$row;
			break;			
		}		
    }
    /**
	* digunakan untuk mencetak laporan
	*
	*/
	public function printOut ($filename) {	
		$filename_to_write =$filename.'_'.date('Y_m_d_H_m_s');	
// 		$filename_to_write =$filename.'_';		//uncoment this line, if you in debug process        
		switch ($this->driver) {
			case 'excel2003' :
                //$writer=new PHPExcel_Writer_Excel5($this->rpt);								
                $writer=PHPExcel_IOFactory::createWriter($this->rpt, 'Excel5');
                $writer->setPreCalculateFormulas(false);
				$filename_to_write = $filename_to_write . '.xls';
				$writer->save ($this->exportedDir['full_path'].$filename_to_write);		
				$this->exportedDir['filename']=$filename;
				$this->exportedDir['excel_path'].=$filename_to_write;		
            break;
			case 'excel2007' :
				$writer=PHPExcel_IOFactory::createWriter($this->rpt, 'Excel2007');
                $writer->setPreCalculateFormulas(false);
				$filename_to_write = $filename_to_write . '.xlsx';
				$writer->save ($this->exportedDir['full_path'].$filename_to_write);		
				$this->exportedDir['filename']=$filename;
				$this->exportedDir['excel_path'].=$filename_to_write;		
			break;	
            case 'pdf' :
				$filename_to_write=$filename_to_write.'.pdf';
				$this->rpt->output ($this->exportedDir['full_path'].$filename_to_write,'F');
				$this->exportedDir['filename']=$filename;
				$this->exportedDir['pdf_path'].=$filename_to_write;		
			break;
		}
	}    
    /**
	* digunakan untuk mendapatkan link ke sebuah file hasil dari export	
	* @param obj_out object 
	* @param text in override text result
	*/
	public function setLink ($obj_out,$text='') {
		$filename=$text==''?$this->exportedDir['filename']:$text;		        
		switch ($this->driver) {
			case 'excel2003' :
                $obj_out->Text = "$filename.xls";
				$obj_out->NavigateUrl=$this->exportedDir['excel_path'];				
            break;
			case 'excel2007' :                
				$obj_out->Text = "$filename.xlsx";
				$obj_out->NavigateUrl=$this->exportedDir['excel_path'];				
			break;	
            case 'pdf' :
				$obj_out->Text = "$filename.pdf";
				$obj_out->NavigateUrl=$this->exportedDir['pdf_path'];	
			break;
		}
	}  
    /**
     * digunakan untuk daftar Barcode
     */
    public function printBarcode () {
        switch ($this->getDriver()) {
            case 'pdf' :                
                $this->rpt->addPage();                              
                $idsbbm=$this->dataReport['idsbbm'];                
                
                // set auto page breaks
                $this->rpt->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
                // define barcode style
                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => true,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0,0,0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 8,
                    'stretchtext' => 4
                );
                $str = "SELECT barcode FROM detail_sbbm WHERE idsbbm=$idsbbm";
                $this->db->setFieldTable(array('barcode'));
                $r=$this->db->getRecord($str);
                
                $row=6;
                $jumlah_record=count($r); 
                $this->rpt->SetFont ('helvetica','B',10);                
                for ($i=1;$i <= $jumlah_record;$i++) {
                    // CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.                    
                    $this->rpt->write1dbarcode($r[$i]['barcode'], 'C39', '', '', '', 18, 0.4, $style, 'N');                
                    $this->rpt->Ln();
                    $row+=5;                    
                }                
                $this->printOut('daftarbarcodesbbm');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],'Daftar Barcode');
    }
    /**
     * digunakan untuk mencetak Pengantar LPLPO
     */
    public function printSuratPengantarLPLPO ($mode='puskesmas') {
        switch ($this->getDriver()) {
            case 'pdf' :
                $this->printOut('suratpengantar');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],'Surat Pengantar LPLPO');
    }
    /**
     * digunakan untuk mencetak daftar OBAT
     */
    public function printDaftarObat ($idpuskesmas=null) {
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :        
                $this->setHeader('P'); 
                $sheet=$this->rpt->getActiveSheet();
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                    
                $row=$this->currentRow+2;                
                $sheet->mergeCells("A$row:N$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row",'LAPORAN DAFTAR OBAT');                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:N$row")->applyFromArray($styleArray);
                $row+=2;                               
                
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(55);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(12);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(15);                
                $sheet->getColumnDimension('L')->setWidth(15);
                $sheet->getColumnDimension('M')->setWidth(37);
                $sheet->getColumnDimension('N')->setWidth(13);                
                $sheet->getColumnDimension('O')->setWidth(13);                
                $sheet->getColumnDimension('P')->setWidth(13);                
               
                $sheet->getRowDimension($row)->setRowHeight(20);                
                $sheet->setCellValue("A$row",'NO');                
                $sheet->setCellValue("B$row",'KODE OBAT');                
                $sheet->setCellValue("C$row",'NO. REG. POM');                                
                $sheet->setCellValue("D$row",'NAMA OBAT');                
                $sheet->setCellValue("E$row",'NAMA MEREK');
                $sheet->setCellValue("F$row",'HARGA');                
                $sheet->setCellValue("G$row",'SATUAN');                
                $sheet->setCellValue("H$row",'KEMASAN');
                $sheet->setCellValue("I$row",'GOLONGAN');                
                $sheet->setCellValue("J$row",'BENTUK SEDIAAN');                
                $sheet->setCellValue("K$row",'FARMAKOLOGI');                
                $sheet->setCellValue("L$row",'KOMPOSISI');                
                $sheet->setCellValue("M$row",'PRODUSEN');                
                $sheet->setCellValue("N$row",'STOCK');                
                $sheet->setCellValue("O$row",'MIN STOCK');                
                $sheet->setCellValue("P$row",'MAX STOCK');                                             
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row:P$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:P$row")->getAlignment()->setWrapText(true);                                
                $nama_table=$idpuskesmas === null ? 'master_obat' : "master_obat_puskesmas WHERE idpuskesmas=$idpuskesmas ";
                $str = "SELECT kode_obat,no_reg,nama_obat,nama_merek,harga,idsatuan_obat,kemasan,idgolongan,nama_bentuk,farmakologi,komposisi,idprodusen,stock,min_stock,max_stock FROM $nama_table ORDER BY nama_obat ASC,idprodusen ASC";
                $this->db->setFieldTable(array('kode_obat','no_reg','nama_obat','nama_merek','harga','idsatuan_obat','kemasan','idgolongan','nama_bentuk','farmakologi','komposisi','idprodusen','stock','min_stock','max_stock'));
                $r=$this->db->getRecord($str);
                $row+=1;
                $row_awal=$row;
                while (list($k,$v)=each($r)) {
                    $sheet->setCellValue("A$row",$v['no']);                
                    $sheet->setCellValue("B$row",$v['kode_obat']);                
                    $sheet->setCellValue("C$row",$v['no_reg']);                                
                    $sheet->setCellValue("D$row",$v['nama_obat']);                
                    $sheet->setCellValue("E$row",$v['nama_merek']);                    
                    $sheet->setCellValueExplicit("F$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue("G$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));                
                    $sheet->setCellValue("H$row",$v['kemasan']);
                    $sheet->setCellValue("I$row",$this->DMaster->getGolonganObat($v['idgolongan']));                
                    $sheet->setCellValue("J$row",$v['nama_bentuk']); 
                    $daftar_farmakologi= json_decode($v['farmakologi'],true);                    
                    foreach ($daftar_farmakologi as $n) {
                        $farmakologi = "$n ";
                    }
                    $sheet->setCellValue("K$row",$farmakologi);                     
                    $sheet->setCellValue("L$row",$v['komposisi']);                                    
                    $sheet->setCellValue("M$row",$this->DMaster->getNamaProdusenByID($v['idprodusen']));                
                    $sheet->setCellValue("N$row",$v['stock']);                
                    $sheet->setCellValue("O$row",$v['min_stock']);                
                    $sheet->setCellValue("P$row",$v['max_stock']);                                                               
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:P$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:P$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                );																					 
                $sheet->getStyle("A$row_awal:C$row")->applyFromArray($styleArray);                
                $sheet->getStyle("N$row_awal:P$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );
                $sheet->getStyle("F$row_awal:F$row")->applyFromArray($styleArray);                
                $this->printOut('daftarobat');
            break;
        }        
        $this->setLink($this->dataReport['linkoutput'],'Daftar Obat');
    }
    /**
     * digunakan untuk mencetak LPLPO
     */
    public function printDetailLPLPO ($mode='puskesmas') {
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :        
                $sheet=$this->rpt->getActiveSheet();
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                    
                $row=1;
                $sheet->mergeCells("A$row:O$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row",'LAPORAN PEMAKAIAN DAN LEMBAR PERMINTAAN OBAT (LPLPO)');                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:O$row")->applyFromArray($styleArray);
                $row+=2;
                $row_awal=$row;                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 9)								
							);
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'PUSKESMAS');                
                $sheet->setCellValue("C$row",': ' . $this->dataReport['nama_puskesmas']);                
                $sheet->setCellValue("K$row",'BULAN');                
                $sheet->setCellValue("L$row",': '.$this->tgl->tanggal('F',$this->dataReport['tanggal_lpo']));                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("K$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'KECAMATAN');                
                $sheet->setCellValue("C$row",': ' . $this->dataReport['nama_kecamatan']);                
                $sheet->setCellValue("K$row",'TAHUN');                
                $sheet->setCellValue("L$row",': '.$this->tgl->tanggal('Y',$this->dataReport['tanggal_lpo']));                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("K$row")->applyFromArray($styleArray);
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(8);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(11);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('J')->setWidth(12);
                $sheet->getColumnDimension('K')->setWidth(12);
                $sheet->getColumnDimension('L')->setWidth(15);                
                $sheet->getColumnDimension('M')->setWidth(15);
                $sheet->getColumnDimension('N')->setWidth(12);
                $sheet->getColumnDimension('O')->setWidth(10);                
                
                $sheet->getRowDimension($row)->setRowHeight(20);                
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'NAMA OBAT');
                $sheet->setCellValue("D$row",'SAT');                
                $sheet->setCellValue("E$row",'HARGA');                
                $sheet->setCellValue("F$row",'STOK AWAL');                
                $sheet->setCellValue("G$row",'PENERIMAAN');                
                $sheet->setCellValue("H$row",'PERSEDIAAN');                
                $sheet->setCellValue("I$row",'PEMAKAIAN');                
                $sheet->setCellValue("J$row",'STOK AKHIR');                
                $sheet->setCellValue("K$row",'STOK OPTIMUM');                
                $sheet->setCellValue("L$row",'PERMINTAAN');                
                $sheet->setCellValue("M$row",'PEMBERIAN');                
                $sheet->setCellValue("N$row",'TOTAL HARGA');                
                $sheet->setCellValue("O$row",'KET');                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row:O$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:O$row")->getAlignment()->setWrapText(true);
                
                $idlpo=$this->dataReport['idlpo'];
                $str = "SELECT kode_obat,nama_obat,harga,kemasan,nama_satuan,stock_awal,penerimaan AS total_penerimaan,persediaan,pemakaian AS total_pemakaian,stock_akhir,permintaan AS qty FROM detail_lpo dl LEFT JOIN satuanobat s ON (s.idsatuan_obat=dl.idsatuan_obat) WHERE idlpo='$idlpo' ORDER BY nama_obat ASC,kode_obat ASC";
                $this->db->setFieldTable(array('kode_obat','nama_obat','harga','kemasan','nama_satuan','stock_awal','total_penerimaan','persediaan','total_pemakaian','stock_akhir','qty'));
                $r=$this->db->getRecord($str);
                $row+=1;
                $row_awal=$row;
                while (list($k,$v)=each($r)) {
                    $sheet->setCellValue("A$row",$v['no']);                
                    $sheet->mergeCells("B$row:C$row");
                    $sheet->setCellValue("B$row",$v['nama_obat'] . ' '.$v['kemasan']);                
                    $sheet->setCellValue("D$row",$v['nama_satuan']);                                                    
                    $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue("F$row",$v['stock_awal']);                
                    $sheet->setCellValue("G$row",$v['total_penerimaan']);                
                    $sheet->setCellValue("H$row",$v['persediaan']);                
                    $sheet->setCellValue("I$row",$v['total_pemakaian']);                
                    $sheet->setCellValue("J$row",$v['stock_akhir']);                                    
                    $sheet->setCellValue("L$row",$v['qty']);    
                    $totalharga=$v['harga']*$v['stock_akhir'];
                    $sheet->setCellValueExplicit("N$row",$this->toRupiah($totalharga),PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:O$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:O$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);
                
                $row+=3;
                $row_awal=$row;
                $sheet->getRowDimension($row)->setRowHeight(20);                    
                $sheet->setCellValue("A$row",'JUMLAH KUNJUNGAN RESEP');                
                $sheet->mergeCells("D$row:E$row");
                $sheet->setCellValue("D$row",'UMUM');                
                $sheet->setCellValue("F$row",'BPJS');                
                $sheet->setCellValue("G$row",'JUMLAH');                
                $row+=1;                                
                $sheet->mergeCells("F$row_awal:F$row");
                $sheet->mergeCells("G$row_awal:G$row");
                $sheet->setCellValue("D$row",'BAYAR');                
                $sheet->setCellValue("E$row",'GRATIS');                
                $row+=1;
                $sheet->mergeCells("A$row_awal:C$row");
                $sheet->setCellValue("D$row",$this->dataReport['jumlah_kunjungan_bayar']);                
                $sheet->setCellValue("E$row",$this->dataReport['jumlah_kunjungan_gratis']);                
                $sheet->setCellValue("F$row",$this->dataReport['jumlah_kunjungan_bpjs']);                
                $sheet->setCellValue("G$row",$this->dataReport['jumlah_kunjungan_bayar']+$this->dataReport['jumlah_kunjungan_gratis']+$this->dataReport['jumlah_kunjungan_bpjs']);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:G$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:G$row")->getAlignment()->setWrapText(true);
                $row+=2;
                $row_awal=$row;
                $sheet->mergeCells("A$row:C$row");
                $sheet->setCellValue("A$row",'Menyetujui');                
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",'Yang Menyerahkan');                
                $sheet->mergeCells("I$row:K$row");
                $sheet->setCellValue("I$row",'Yang Melapor');                
                $sheet->mergeCells("M$row:O$row");
                $sheet->setCellValue("M$row",'Petugas Pengelola Obat');                
                $row+=1;
                $sheet->mergeCells("A$row:C$row");
                $sheet->setCellValue("A$row",'Kepala Dinas Kesehatan');                
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",'Kepala UPTD Instalasi Farmasi');                
                $sheet->mergeCells("I$row:K$row");
                $sheet->setCellValue("I$row",'Kepala Puskesmas');                
                $sheet->mergeCells("M$row:O$row");
                $sheet->setCellValue("M$row",'Puskesmas');                
                $row+=1;
                $sheet->mergeCells("A$row:C$row");
                $sheet->setCellValue("A$row",'Kabupaten Bintan');                
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",'Kabupaten Bintan');                
                $sheet->mergeCells("I$row:K$row");
                $sheet->setCellValue("I$row",$this->dataReport['nama_puskesmas']);                
                $sheet->mergeCells("M$row:O$row");
                $sheet->setCellValue("M$row",$this->dataReport['nama_puskesmas']);                
                $row+=5;
                $sheet->mergeCells("A$row:C$row");
                $sheet->setCellValue("A$row",$this->dataReport['nama_kadis']);                
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",$this->dataReport['nama_ka_gudang']);                
                $sheet->mergeCells("I$row:K$row");
                $sheet->setCellValue("I$row",$this->dataReport['nama_ka']);                
                $sheet->mergeCells("M$row:O$row");
                $sheet->setCellValue("M$row",$this->dataReport['nama_pengelola']);                
                $row+=1;
                $sheet->mergeCells("A$row:C$row");                
                $sheet->setCellValueExplicit("A$row",$this->setup->nipFormat($this->dataReport['nip_kadis']),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->mergeCells("E$row:G$row");                
                $sheet->setCellValueExplicit("E$row",$this->setup->nipFormat($this->dataReport['nip_ka_gudang']),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->mergeCells("I$row:K$row");                            
                $sheet->setCellValueExplicit("I$row",$this->setup->nipFormat($this->dataReport['nip_ka']),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->mergeCells("M$row:O$row");                          
                $sheet->setCellValueExplicit("M$row",$this->setup->nipFormat($this->dataReport['nip_pengelola']),PHPExcel_Cell_DataType::TYPE_STRING);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));																					 
                $sheet->getStyle("A$row_awal:O$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:O$row")->getAlignment()->setWrapText(true);
                
                $this->printOut('detaillplpo');
            break;
        }        
        $this->setLink($this->dataReport['linkoutput'],'Detail LPLPO');
    }
    /**
     * digunakan untuk mencetak SBBK
     */
    public function printSuratPengantarSBBK () {
        switch ($this->getDriver()) {
            case 'pdf' :               
                
                $this->printOut('suratpengantar');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],'Surat Pengantar LPLPO');
    }
    /**
     * digunakan untuk mencetak SBBK
     */
    public function printDetailSBBK () {
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :        
                $this->setHeader('H');
                $row=$this->currentRow+2;
                $sheet=$this->rpt->getActiveSheet();
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                    
                $sheet->mergeCells("A$row:H$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row",'LAMPIRAN SURAT BUKTI BARANG KELUAR');                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleArray);
                $row+=2;
                $row_awal=$row;                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 9)								
							);
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'NO. PERMINTAAN');                
                $sheet->setCellValue("C$row",': ' . $this->dataReport['no_lpo']);                
                $sheet->setCellValue("F$row",'TANGGAL LPO');                
                $sheet->setCellValue("G$row",': '.$this->tgl->tanggal('d F Y',$this->dataReport['tanggal_lpo']));                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("F$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'NO. PENGIRIMAN');                
                $sheet->setCellValue("C$row",': ' . $this->dataReport['no_sbbk']);                
                $sheet->setCellValue("F$row",'TANGGAL SBBK');                
                $sheet->setCellValue("G$row",': '.$this->tgl->tanggal('d F Y',$this->dataReport['tanggal_sbbk']));                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("F$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'UNTUK');                
                $sheet->setCellValue("C$row",': UPT PUSKESMAS ' . $this->dataReport['nama_puskesmas']);                                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);                              
                
                $sheet->getRowDimension($row)->setRowHeight(23);                
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'NAMA OBAT');                
                $sheet->setCellValue("D$row",'SATUAN / KEMASAN');                
                $sheet->setCellValue("E$row",'HARGA (Rp.)');                
                $sheet->setCellValue("F$row",'KUANTUM / KEMASAN');                
                $sheet->setCellValue("G$row",'JUMLAH (Rp.)');                
                $sheet->setCellValue("H$row",'KET');               
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:H$row")->getAlignment()->setWrapText(true);
                
                $idsbbk=$this->dataReport['idsbbk'];
                $str = "SELECT nama_obat,kemasan,nama_satuan,harga,pemberian,harga * pemberian AS jumlah FROM detail_sbbk ds LEFT JOIN satuanobat s ON (s.idsatuan_obat=ds.idsatuan_obat) WHERE idsbbk='$idsbbk'";
                $this->db->setFieldTable(array('nama_obat','kemasan','nama_satuan','harga','pemberian','jumlah'));
                $r=$this->db->getRecord($str);
                $row+=1;
                $row_awal=$row;
                $totalQTY=0;
                $jumlah=0;                
                while (list($k,$v)=each($r)) {
                    $sheet->setCellValue("A$row",$v['no']);                
                    $sheet->mergeCells("B$row:C$row");
                    $sheet->setCellValue("B$row",$v['nama_obat'] . ' '.$v['kemasan']);                
                    $sheet->setCellValue("D$row",$v['nama_satuan']);                                    
                    $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue("F$row",$v['pemberian']);                                    
                    $sheet->setCellValueExplicit("G$row",$this->toRupiah($v['jumlah']),PHPExcel_Cell_DataType::TYPE_STRING);
                    $jumlah+=$v['jumlah'];
                    $totalQTY+=$v['pemberian'];
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:H$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:H$row")->getAlignment()->setWrapText(true);
                $row+=1;
                $sheet->setCellValue("F$row",$totalQTY);
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                );																					 
                $sheet->getStyle("F$row")->applyFromArray($styleArray); 
                $sheet->setCellValueExplicit("G$row",$this->toRupiah($jumlah),PHPExcel_Cell_DataType::TYPE_STRING);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);                
                $sheet->getStyle("G$row_awal:G$row")->applyFromArray($styleArray);                
                $row+=2;
                $row_awal=$row;
                $sheet->mergeCells("A$row:C$row");
                $sheet->setCellValue("A$row",'Yang Menerima');                
                $sheet->mergeCells("D$row:F$row");
                $sheet->setCellValue("D$row",'Dikemas Oleh');                
                $sheet->mergeCells("G$row:I$row");
                $sheet->setCellValue("G$row",'Kepala UPTD Instalasi Farmasi');                                
                $row+=1;                
                $sheet->mergeCells("G$row:I$row");
                $sheet->setCellValue("G$row",'KABUPATEN BINTAN');                                
                           
                $row+=4;
                $sheet->mergeCells("A$row:C$row");
                $sheet->setCellValue("A$row",'____________________________');
                $sheet->mergeCells("D$row:F$row");
                $sheet->setCellValue("D$row",$this->dataReport['nama_pengemas']);                
                $sheet->mergeCells("G$row:I$row");
                $sheet->setCellValue("G$row",$this->dataReport['nama_ka_gudang']);                
                $row+=1;
                $sheet->mergeCells("D$row:F$row");
                $sheet->setCellValue("D$row",'NIP: '.$this->dataReport['nip_pengemas']);                
                $sheet->mergeCells("G$row:I$row");
                $sheet->setCellValue("G$row",'NIP: '.$this->dataReport['nip_ka_gudang']);                
                $row+=2;
                $sheet->mergeCells("D$row:F$row");
                $sheet->setCellValue("D$row",'Kepala UPT Puskesmas'); 
                $row+=1;
                $sheet->mergeCells("D$row:F$row");
                $sheet->setCellValue("D$row",$this->dataReport['nama_puskesmas']); 
                $row+=4;
                $sheet->mergeCells("D$row:F$row");
                $sheet->setCellValue("D$row",$this->dataReport['nama_ka']);  
                $row+=1;
                $sheet->mergeCells("D$row:F$row");
                $sheet->setCellValue("D$row",$this->dataReport['nip_ka']);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));																					 
                $sheet->getStyle("A$row_awal:O$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:O$row")->getAlignment()->setWrapText(true);
                
                $this->printOut('detailsbbk');
            break;
        }        
        $this->setLink($this->dataReport['linkoutput'],'Detail SBBK');
    }
    /**
     * digunakan untuk mutasi obat bulanan
     */
    public function printMutasiObatBulanan () {
        $sumberdana=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana(),'none');
        $listsumber_dana=$sumberdana;
        $sumberdana['none']='JUMLAH';
        $sumberdana['total']='TOTAL';     
        $bulantahun=$this->tgl->tanggal ('Y-m',$this->dataReport['bulantahun']);
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :                        
                $row=2;
                $sheet=$this->rpt->getActiveSheet();
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                    
                $sheet->mergeCells("A$row:AD$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row",'LAPORAN BULANAN MUTASI OBAT');                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 16),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleArray);
                $row+=2;
                $row_awal=$row;                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 9)								
							);
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'KABUPATEN');                
                $sheet->setCellValue("C$row",': BINTAN');                
                $sheet->setCellValue("X$row",'BULAN');                
                $sheet->setCellValue("Y$row",': '.$this->tgl->tanggal('F',$this->dataReport['bulantahun']));                            
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'PROPINSI');                
                $sheet->setCellValue("C$row",': KEPULAUAN RIAU');                                
                $sheet->setCellValue("X$row",'TAHUN');                
                $sheet->setCellValue("Y$row",': '.$this->tgl->tanggal('Y',$this->dataReport['bulantahun']));                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);              
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('G')->setWidth(11);   
                $sheet->getColumnDimension('I')->setWidth(11);   
                $sheet->getColumnDimension('K')->setWidth(11);   
                $sheet->getColumnDimension('M')->setWidth(11);   
                $sheet->getColumnDimension('Q')->setWidth(11);
                $sheet->getColumnDimension('S')->setWidth(11);
                $sheet->getColumnDimension('U')->setWidth(11);
                $sheet->getColumnDimension('W')->setWidth(11);
                $sheet->getColumnDimension('AA')->setWidth(11);
                $sheet->getColumnDimension('AC')->setWidth(11);
                $sheet->getColumnDimension('AE')->setWidth(11);
                $sheet->getColumnDimension('AG')->setWidth(11);                
                $sheet->getColumnDimension('AK')->setWidth(11);                
                $sheet->getColumnDimension('AM')->setWidth(11);                
                $sheet->getColumnDimension('AO')->setWidth(11);
                $sheet->getColumnDimension('AQ')->setWidth(11);                
                
                $sheet->getRowDimension($row)->setRowHeight(23);                
                $row_awal=$row;
                $row_akhir=$row+1;
                $sheet->mergeCells("A$row:A$row_akhir");
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:C$row_akhir");
                $sheet->setCellValue("B$row",'NAMA OBAT');                
                $sheet->mergeCells("D$row:D$row_akhir");
                $sheet->setCellValue("D$row",'SATUAN / KEMASAN');                
                $sheet->mergeCells("E$row:E$row_akhir");
                $sheet->setCellValue("E$row",'HARGA (Rp.)');
                
                $sheet->mergeCells("F$row:O$row");
                $sheet->setCellValue("F$row",'STOCK AWAL');    
                
                $sheet->mergeCells("P$row:Y$row");
                $sheet->setCellValue("P$row",'PENERIMAAN');    
                
                $sheet->mergeCells("Z$row:AI$row");
                $sheet->setCellValue("Z$row",'PENGELUARAN');    
                
                $sheet->mergeCells("AJ$row:AS$row");
                $sheet->setCellValue("AJ$row",'STOCK AKHIR');    
                
                $sheet->mergeCells("AT$row:AT$row_akhir");
                $sheet->setCellValue("AT$row",'KET');                
                $row+=1;
                $sheet->setCellValue("F$row",'APBD I');    
                $sheet->setCellValue("G$row",'SUB TOTAL');    
                $sheet->setCellValue("H$row",'APBD II');    
                $sheet->setCellValue("I$row",'SUB TOTAL');    
                $sheet->setCellValue("J$row",'APBN');    
                $sheet->setCellValue("K$row",'SUB TOTAL');    
                $sheet->setCellValue("L$row",'HIBAH');    
                $sheet->setCellValue("M$row",'SUB TOTAL');    
                $sheet->setCellValue("N$row",'JUMLAH');    
                $sheet->setCellValue("O$row",'TOTAL');                    
                
                $sheet->setCellValue("P$row",'APBD I');    
                $sheet->setCellValue("Q$row",'SUB TOTAL');    
                $sheet->setCellValue("R$row",'APBD II');    
                $sheet->setCellValue("S$row",'SUB TOTAL');    
                $sheet->setCellValue("T$row",'APBN');    
                $sheet->setCellValue("U$row",'SUB TOTAL');    
                $sheet->setCellValue("V$row",'HIBAH');    
                $sheet->setCellValue("W$row",'SUB TOTAL');    
                $sheet->setCellValue("X$row",'JUMLAH');    
                $sheet->setCellValue("Y$row",'TOTAL');                    
                
                $sheet->setCellValue("Z$row",'APBD I');    
                $sheet->setCellValue("AA$row",'SUB TOTAL');    
                $sheet->setCellValue("AB$row",'APBD II');    
                $sheet->setCellValue("AC$row",'SUB TOTAL');    
                $sheet->setCellValue("AD$row",'APBN');    
                $sheet->setCellValue("AE$row",'SUB TOTAL');    
                $sheet->setCellValue("AF$row",'HIBAH');    
                $sheet->setCellValue("AG$row",'SUB TOTAL');    
                $sheet->setCellValue("AH$row",'JUMLAH');    
                $sheet->setCellValue("AI$row",'TOTAL');                    

                $sheet->setCellValue("AJ$row",'APBD I');    
                $sheet->setCellValue("AK$row",'SUB TOTAL');    
                $sheet->setCellValue("AL$row",'APBD II');    
                $sheet->setCellValue("AM$row",'SUB TOTAL');    
                $sheet->setCellValue("AN$row",'APBN');    
                $sheet->setCellValue("AO$row",'SUB TOTAL');    
                $sheet->setCellValue("AP$row",'HIBAH');    
                $sheet->setCellValue("AQ$row",'SUB TOTAL');    
                $sheet->setCellValue("AR$row",'JUMLAH');    
                $sheet->setCellValue("AS$row",'TOTAL');                    
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                                
                $str = "SELECT mo.idobat,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat) GROUP BY mo.idobat,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC, mo.nama_obat ASC";
                $this->db->setFieldTable(array('idobat','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
                $r=$this->db->getRecord($str); 
                $row+=1;
                $row_awal=$row;                                            
                $penerimaan=$listsumber_dana;
                $pengeluaran=$listsumber_dana;
                $stockakhir=$listsumber_dana;                
                $jumlah_awal=array(1=>0,2=>0,3=>0,4=>0);
                $totalAllItemStockAwal=$jumlah_awal;                
                $totalAllItemPenerimaan=$jumlah_awal;
                $totalAllItemPengeluaran=$jumlah_awal;
                $totalAllItemStockAkhir=$jumlah_awal;
                
                $totalAllHargaStockAwal=$jumlah_awal;                
                $totalAllHargaPenerimaan=$jumlah_awal;
                $totalAllHargaPengeluaran=$jumlah_awal;
                $totalAllHargaStockAkhir=$jumlah_awal;
                
                while (list($k,$v)=each($r)) {
                    $idobat=$v['idobat'];
                    $penerimaan[1]=0;
                    $penerimaan[2]=0;
                    $penerimaan[3]=0;
                    $penerimaan[4]=0;
                    $pengeluaran[1]=0;
                    $pengeluaran[2]=0;
                    $pengeluaran[3]=0;
                    $pengeluaran[4]=0;
                    $jumlah_penerimaan=0;
                    $jumlah_pengeluaran=0;
                    $stockakhir[1]=0;
                    $stockakhir[2]=0;
                    $stockakhir[3]=0;
                    $stockakhir[4]=0;
                    $sheet->setCellValue("A$row",$v['no']);                      
                    if ($v['kode_obat']=='') {
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['mst_nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['mst_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("F$row",0);    
                        $sheet->setCellValue("G$row",0);    
                        $sheet->setCellValue("H$row",0);    
                        $sheet->setCellValue("I$row",0);    
                        $sheet->setCellValue("J$row",0);    
                        $sheet->setCellValue("K$row",0);    
                        $sheet->setCellValue("L$row",0);    
                        $sheet->setCellValue("M$row",0);    
                        $sheet->setCellValue("N$row",0);    
                        $sheet->setCellValue("O$row",0);                    

                        $sheet->setCellValue("P$row",0);    
                        $sheet->setCellValue("Q$row",0);    
                        $sheet->setCellValue("R$row",0);    
                        $sheet->setCellValue("S$row",0);    
                        $sheet->setCellValue("T$row",0);    
                        $sheet->setCellValue("U$row",0);    
                        $sheet->setCellValue("V$row",0);    
                        $sheet->setCellValue("W$row",0);    
                        $sheet->setCellValue("X$row",0);    
                        $sheet->setCellValue("Y$row",0);                    

                        $sheet->setCellValue("Z$row",0);    
                        $sheet->setCellValue("AA$row",0);    
                        $sheet->setCellValue("AB$row",0);    
                        $sheet->setCellValue("AC$row",0);    
                        $sheet->setCellValue("AD$row",0);    
                        $sheet->setCellValue("AE$row",0);    
                        $sheet->setCellValue("AF$row",0);    
                        $sheet->setCellValue("AG$row",0);    
                        $sheet->setCellValue("AH$row",0);    
                        $sheet->setCellValue("AI$row",0);                    

                        $sheet->setCellValue("AJ$row",0);    
                        $sheet->setCellValue("AK$row",0);    
                        $sheet->setCellValue("AL$row",0);    
                        $sheet->setCellValue("AM$row",0);    
                        $sheet->setCellValue("AN$row",0);    
                        $sheet->setCellValue("AO$row",0);    
                        $sheet->setCellValue("AP$row",0);    
                        $sheet->setCellValue("AQ$row",0);    
                        $sheet->setCellValue("AR$row",0);    
                        $sheet->setCellValue("AS$row",0);                                                            
                    }else{
                        $harga=$v['harga'];
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $awalstock=$this->getFirstStock($idobat,$this->dataReport['bulansebelumnya'],$harga,'sumberdana');
                        $sheet->setCellValue("F$row",$awalstock[1]);                    
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($awalstock[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("H$row",$awalstock[2]);
                        $sheet->setCellValueExplicit("I$row",$this->toRupiah($awalstock[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("J$row",$awalstock[3]);                    
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($awalstock[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("L$row",$awalstock[4]);                
                        $sheet->setCellValueExplicit("M$row",$this->toRupiah($awalstock[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                        
                        $jumlah_awalstock=$awalstock[1]+$awalstock[2]+$awalstock[3]+$awalstock[4];                        
                        $sheet->setCellValue("N$row",$jumlah_awalstock);                        
                        $totalAllItemStockAwal[1]+=$awalstock[1];
                        $totalAllItemStockAwal[2]+=$awalstock[2];
                        $totalAllItemStockAwal[3]+=$awalstock[3];
                        $totalAllItemStockAwal[4]+=$awalstock[4];
                        
                        $totalAllHargaStockAwal[1]+=$awalstock[1]*$harga;
                        $totalAllHargaStockAwal[2]+=$awalstock[2]*$harga;
                        $totalAllHargaStockAwal[3]+=$awalstock[3]*$harga;
                        $totalAllHargaStockAwal[4]+=$awalstock[4]*$harga;
                        
                        $total=$jumlah_awalstock*$harga;                        
                        $sheet->setCellValueExplicit("O$row",$this->toRupiah($total),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        
                        $str_penerimaan="SELECT msb.idsumber_dana,SUM(dsb.qty) AS jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND status='complete' AND DATE_FORMAT(msb.tanggal_sbbm,'%Y-%m')='$bulantahun' GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";
                        $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                        $r_penerimaan=$this->db->getRecord($str_penerimaan);
                        
                        if (isset($r_penerimaan[1])) {
                            foreach ($r_penerimaan as $n) {
                                $penerimaan[$n['idsumber_dana']]=$n['jumlah'];     
                                $stock=$awalstock[$n['idsumber_dana']]+$n['jumlah'];
                                $stockakhir[$n['idsumber_dana']]=$stock;
                                $jumlah_penerimaan+=$n['jumlah'];
                            }                                           
                        }
                        $sheet->setCellValue("P$row",$penerimaan[1]);    
                        $sheet->setCellValueExplicit("Q$row",$this->toRupiah($penerimaan[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("R$row",$penerimaan[2]);    
                        $sheet->setCellValueExplicit("S$row",$this->toRupiah($penerimaan[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("T$row",$penerimaan[3]);    
                        $sheet->setCellValueExplicit("U$row",$this->toRupiah($penerimaan[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("V$row",$penerimaan[4]);    
                        $sheet->setCellValueExplicit("W$row",$this->toRupiah($penerimaan[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        

                        $sheet->setCellValue("X$row",$jumlah_penerimaan);
                        $total_penerimaan=$harga*$jumlah_penerimaan;
                        $totalAllItemPenerimaan[1]+=$penerimaan[1];
                        $totalAllItemPenerimaan[2]+=$penerimaan[2];
                        $totalAllItemPenerimaan[3]+=$penerimaan[3];
                        $totalAllItemPenerimaan[4]+=$penerimaan[4];
                        
                        $totalAllHargaPenerimaan[1]+=$penerimaan[1]*$harga;
                        $totalAllHargaPenerimaan[2]+=$penerimaan[2]*$harga;
                        $totalAllHargaPenerimaan[3]+=$penerimaan[3]*$harga;
                        $totalAllHargaPenerimaan[4]+=$penerimaan[4]*$harga;

                        $sheet->setCellValueExplicit("Y$row",$this->toRupiah($total_penerimaan),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        
                        $str_pengeluaran="SELECT msb.idsumber_dana,COUNT(ks.idkartu_stock) AS jumlah FROM master_sbbk msk,detail_sbbk dsb,kartu_stock ks,master_sbbm msb WHERE msk.idsbbk=dsb.idsbbk AND ks.idobat=$idobat AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.idsbbm=msb.idsbbm AND dsb.harga=$harga AND status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')='$bulantahun' AND ks.isdestroyed=0 GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";                        
                        $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                        $r_pengeluaran=$this->db->getRecord($str_pengeluaran);
                        
                        foreach ($r_pengeluaran as $s) {
                            $pengeluaran[$s['idsumber_dana']]=$s['jumlah'];                            
                            $jumlah_pengeluaran+=$s['jumlah'];
                        }
                        
                        $sheet->setCellValue("Z$row",$pengeluaran[1]);                            
                        $sheet->setCellValueExplicit("AA$row",$this->toRupiah($harga*$pengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AB$row",$pengeluaran[2]);                            ;    
                        $sheet->setCellValueExplicit("AC$row",$this->toRupiah($harga*$pengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AD$row",$pengeluaran[3]);                            
                        $sheet->setCellValueExplicit("AE$row",$this->toRupiah($harga*$pengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AF$row",$pengeluaran[4]);    
                        $sheet->setCellValueExplicit("AG$row",$this->toRupiah($harga*$pengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
                                                
                        $sheet->setCellValue("AH$row",$jumlah_pengeluaran);
                        $total_pengeluaran=$harga*$jumlah_pengeluaran;
                        $totalAllItemPengeluaran[1]+=$pengeluaran[1];
                        $totalAllItemPengeluaran[2]+=$pengeluaran[2];
                        $totalAllItemPengeluaran[3]+=$pengeluaran[3];
                        $totalAllItemPengeluaran[4]+=$pengeluaran[4];
                        
                        $totalAllHargaPengeluaran[1]+=$pengeluaran[1]*$harga;
                        $totalAllHargaPengeluaran[2]+=$pengeluaran[2]*$harga;
                        $totalAllHargaPengeluaran[3]+=$pengeluaran[3]*$harga;
                        $totalAllHargaPengeluaran[4]+=$pengeluaran[4]*$harga;
                        $sheet->setCellValueExplicit("AI$row",$this->toRupiah($total_pengeluaran),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        
                        $stockakhir[1]=($awalstock[1]+$penerimaan[1])-$pengeluaran[1];
                        $stockakhir[2]=($awalstock[2]+$penerimaan[2])-$pengeluaran[2];
                        $stockakhir[3]=($awalstock[3]+$penerimaan[3])-$pengeluaran[3];
                        $stockakhir[4]=($awalstock[4]+$penerimaan[4])-$pengeluaran[4];
                        
                        $sheet->setCellValue("AJ$row",$stockakhir[1]);                            
                        $sheet->setCellValueExplicit("AK$row",$this->toRupiah($harga*$stockakhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AL$row",$stockakhir[2]);    
                        $sheet->setCellValueExplicit("AM$row",$this->toRupiah($harga*$stockakhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AN$row",$stockakhir[3]);                            
                        $sheet->setCellValueExplicit("AO$row",$this->toRupiah($harga*$stockakhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AP$row",$stockakhir[4]);    
                        $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($harga*$stockakhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $jumlah_stockakhir=$stockakhir[1]+$stockakhir[2]+$stockakhir[3]+$stockakhir[4];                       
                        $sheet->setCellValue("AR$row",$jumlah_stockakhir);                        
                        $total_stockakhir=$harga*$jumlah_stockakhir;
                        
                        $totalAllItemStockAkhir[1]+=$stockakhir[1];
                        $totalAllItemStockAkhir[2]+=$stockakhir[2];
                        $totalAllItemStockAkhir[3]+=$stockakhir[3];
                        $totalAllItemStockAkhir[4]+=$stockakhir[4];
                        
                        $totalAllHargaStockAkhir[1]+=$stockakhir[1]*$harga;
                        $totalAllHargaStockAkhir[2]+=$stockakhir[2]*$harga;
                        $totalAllHargaStockAkhir[3]+=$stockakhir[3]*$harga;
                        $totalAllHargaStockAkhir[4]+=$stockakhir[4]*$harga;

                        $sheet->setCellValueExplicit("AS$row",$this->toRupiah($total_stockakhir),PHPExcel_Cell_DataType::TYPE_STRING);                                                                                                
                    }
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row",'TOTAL');                                            
                $sheet->setCellValue("F$row",$totalAllItemStockAwal[1]);                
                $sheet->setCellValueExplicit("G$row",$this->toRupiah($totalAllHargaStockAwal[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("H$row",$totalAllItemStockAwal[2]);                
                $sheet->setCellValueExplicit("I$row",$this->toRupiah($totalAllHargaStockAwal[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("J$row",$totalAllItemStockAwal[3]);                
                $sheet->setCellValueExplicit("K$row",$this->toRupiah($totalAllHargaStockAwal[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("L$row",$totalAllItemStockAwal[4]);                
                $sheet->setCellValueExplicit("M$row",$this->toRupiah($totalAllHargaStockAwal[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                
                $jumlah_all=$totalAllItemStockAwal[1]+$totalAllItemStockAwal[2]+$totalAllItemStockAwal[3]+$totalAllItemStockAwal[4];
                $jumlah_rp=$totalAllHargaStockAwal[1]+$totalAllHargaStockAwal[2]+$totalAllHargaStockAwal[3]+$totalAllHargaStockAwal[4];
                $sheet->setCellValue("N$row",$jumlah_all);                
                $sheet->setCellValueExplicit("O$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                        
                
                $sheet->setCellValue("P$row",$totalAllItemPenerimaan[1]);    
                $sheet->setCellValueExplicit("Q$row",$this->toRupiah($totalAllHargaPenerimaan[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("R$row",$totalAllItemPenerimaan[2]);    
                $sheet->setCellValueExplicit("S$row",$this->toRupiah($totalAllHargaPenerimaan[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("T$row",$totalAllItemPenerimaan[3]);    
                $sheet->setCellValueExplicit("U$row",$this->toRupiah($totalAllHargaPenerimaan[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("V$row",$totalAllItemPenerimaan[4]);    
                $sheet->setCellValueExplicit("W$row",$this->toRupiah($totalAllHargaPenerimaan[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                
                $jumlah_all=$totalAllItemPenerimaan[1]+$totalAllItemPenerimaan[2]+$totalAllItemPenerimaan[3]+$totalAllItemPenerimaan[4];
                $jumlah_rp=$totalAllHargaPenerimaan[1]+$totalAllHargaPenerimaan[2]+$totalAllHargaPenerimaan[3]+$totalAllHargaPenerimaan[4];
                
                $sheet->setCellValue("X$row",$jumlah_all);                
                $sheet->setCellValueExplicit("Y$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("Z$row",$totalAllItemPengeluaran[1]);                            
                $sheet->setCellValueExplicit("AA$row",$this->toRupiah($totalAllHargaPengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AB$row",$totalAllItemPengeluaran[2]);                            ;    
                $sheet->setCellValueExplicit("AC$row",$this->toRupiah($totalAllHargaPengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AD$row",$totalAllItemPengeluaran[3]);                    
                $sheet->setCellValueExplicit("AE$row",$this->toRupiah($totalAllHargaPengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AF$row",$totalAllItemPengeluaran[4]);    
                $sheet->setCellValueExplicit("AG$row",$this->toRupiah($totalAllHargaPengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
                       
                $jumlah_all=$totalAllItemPengeluaran[1]+$totalAllItemPengeluaran[2]+$totalAllItemPengeluaran[3]+$totalAllItemPengeluaran[4];
                $jumlah_rp=$totalAllHargaPengeluaran[1]+$totalAllHargaPengeluaran[2]+$totalAllHargaPengeluaran[3]+$totalAllHargaPengeluaran[4];
                
                $sheet->setCellValue("AH$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AI$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("AJ$row",$totalAllItemStockAkhir[1]);                            
                $sheet->setCellValueExplicit("AK$row",$this->toRupiah($totalAllHargaStockAkhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AL$row",$totalAllItemStockAkhir[2]);    
                $sheet->setCellValueExplicit("AM$row",$this->toRupiah($totalAllHargaStockAkhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AN$row",$totalAllItemStockAkhir[3]);                            
                $sheet->setCellValueExplicit("AO$row",$this->toRupiah($totalAllHargaStockAkhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AP$row",$totalAllItemStockAkhir[4]);    
                $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($totalAllHargaStockAkhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
                
                $jumlah_all=$totalAllItemStockAkhir[1]+$totalAllItemStockAkhir[2]+$totalAllItemStockAkhir[3]+$totalAllItemStockAkhir[4];
                $jumlah_rp=$totalAllHargaStockAkhir[1]+$totalAllHargaStockAkhir[2]+$totalAllHargaStockAkhir[3]+$totalAllHargaStockAkhir[4];
                
                $sheet->setCellValue("AR$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AS$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                $row+=1;                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                );																					 
                $sheet->getStyle("F$row")->applyFromArray($styleArray);                                 
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);                
                              
                $row+=2;
                $row_awal=$row;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",'Tanjungpinang, '.$this->tgl->tanggal('d F Y'));                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",'Kepala UPTD Instalasi Gudang Farmasi');                                                                                 
                $row+=4;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nama_ka_gudang']);                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nip_ka_gudang']);                

                $styleArray=array(								
                                    'font' => array('bold' => true),
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));																					 
                $sheet->getStyle("X$row_awal:AD$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row_awal:AD$row")->getAlignment()->setWrapText(true);
                
                $this->printOut('laporanmutasiobatbulan');
            break;
        }        
        $this->setLink($this->dataReport['linkoutput'],'Laporan Mutasi Obat Bulanan');
    }
    /**
     * digunakan untuk mutasi obat bulanan puskesmas
     */
    public function printMutasiObatBulananPuskesmas ($idpuskesmas) {
        $sumberdana=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana(),'none');
        $listsumber_dana=$sumberdana;
        $sumberdana['none']='JUMLAH';
        $sumberdana['total']='TOTAL';     
        $bulantahun=$this->tgl->tanggal ('Y-m',$this->dataReport['bulantahun']);
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :                        
                $row=2;
                $sheet=$this->rpt->getActiveSheet();
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                    
                $sheet->mergeCells("A$row:AD$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row",'LAPORAN BULANAN MUTASI OBAT');                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 16),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleArray);
                $row+=2;
                $row_awal=$row;                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 9)								
							);
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'KABUPATEN');                
                $sheet->setCellValue("C$row",': BINTAN');                
                $sheet->setCellValue("X$row",'BULAN');                
                $sheet->setCellValue("Y$row",': '.$this->tgl->tanggal('F',$this->dataReport['bulantahun']));                            
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'PROPINSI');                
                $sheet->setCellValue("C$row",': KEPULAUAN RIAU');                                
                $sheet->setCellValue("X$row",'TAHUN');                
                $sheet->setCellValue("Y$row",': '.$this->tgl->tanggal('Y',$this->dataReport['bulantahun']));                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'UPT. PUSKESMAS');                
                $sheet->setCellValue("C$row",': '.$this->dataReport['nama_puskesmas']);                                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);              
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('G')->setWidth(11);   
                $sheet->getColumnDimension('I')->setWidth(11);   
                $sheet->getColumnDimension('K')->setWidth(11);   
                $sheet->getColumnDimension('M')->setWidth(11);   
                $sheet->getColumnDimension('Q')->setWidth(11);
                $sheet->getColumnDimension('S')->setWidth(11);
                $sheet->getColumnDimension('U')->setWidth(11);
                $sheet->getColumnDimension('W')->setWidth(11);
                $sheet->getColumnDimension('AA')->setWidth(11);
                $sheet->getColumnDimension('AC')->setWidth(11);
                $sheet->getColumnDimension('AE')->setWidth(11);
                $sheet->getColumnDimension('AG')->setWidth(11);                
                $sheet->getColumnDimension('AK')->setWidth(11);                
                $sheet->getColumnDimension('AM')->setWidth(11);                
                $sheet->getColumnDimension('AO')->setWidth(11);
                $sheet->getColumnDimension('AQ')->setWidth(11);                
                
                $sheet->getRowDimension($row)->setRowHeight(23);                
                $row_awal=$row;
                $row_akhir=$row+1;
                $sheet->mergeCells("A$row:A$row_akhir");
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:C$row_akhir");
                $sheet->setCellValue("B$row",'NAMA OBAT');                
                $sheet->mergeCells("D$row:D$row_akhir");
                $sheet->setCellValue("D$row",'SATUAN / KEMASAN');                
                $sheet->mergeCells("E$row:E$row_akhir");
                $sheet->setCellValue("E$row",'HARGA (Rp.)');
                
                $sheet->mergeCells("F$row:O$row");
                $sheet->setCellValue("F$row",'STOCK AWAL');    
                
                $sheet->mergeCells("P$row:Y$row");
                $sheet->setCellValue("P$row",'PENERIMAAN');    
                
                $sheet->mergeCells("Z$row:AI$row");
                $sheet->setCellValue("Z$row",'PENGELUARAN');    
                
                $sheet->mergeCells("AJ$row:AS$row");
                $sheet->setCellValue("AJ$row",'STOCK AKHIR');    
                
                $sheet->mergeCells("AT$row:AT$row_akhir");
                $sheet->setCellValue("AT$row",'KET');                
                $row+=1;
                $sheet->setCellValue("F$row",'APBD I');    
                $sheet->setCellValue("G$row",'SUB TOTAL');    
                $sheet->setCellValue("H$row",'APBD II');    
                $sheet->setCellValue("I$row",'SUB TOTAL');    
                $sheet->setCellValue("J$row",'APBN');    
                $sheet->setCellValue("K$row",'SUB TOTAL');    
                $sheet->setCellValue("L$row",'HIBAH');    
                $sheet->setCellValue("M$row",'SUB TOTAL');    
                $sheet->setCellValue("N$row",'JUMLAH');    
                $sheet->setCellValue("O$row",'TOTAL');                    
                
                $sheet->setCellValue("P$row",'APBD I');    
                $sheet->setCellValue("Q$row",'SUB TOTAL');    
                $sheet->setCellValue("R$row",'APBD II');    
                $sheet->setCellValue("S$row",'SUB TOTAL');    
                $sheet->setCellValue("T$row",'APBN');    
                $sheet->setCellValue("U$row",'SUB TOTAL');    
                $sheet->setCellValue("V$row",'HIBAH');    
                $sheet->setCellValue("W$row",'SUB TOTAL');    
                $sheet->setCellValue("X$row",'JUMLAH');    
                $sheet->setCellValue("Y$row",'TOTAL');                    
                
                $sheet->setCellValue("Z$row",'APBD I');    
                $sheet->setCellValue("AA$row",'SUB TOTAL');    
                $sheet->setCellValue("AB$row",'APBD II');    
                $sheet->setCellValue("AC$row",'SUB TOTAL');    
                $sheet->setCellValue("AD$row",'APBN');    
                $sheet->setCellValue("AE$row",'SUB TOTAL');    
                $sheet->setCellValue("AF$row",'HIBAH');    
                $sheet->setCellValue("AG$row",'SUB TOTAL');    
                $sheet->setCellValue("AH$row",'JUMLAH');    
                $sheet->setCellValue("AI$row",'TOTAL');                    

                $sheet->setCellValue("AJ$row",'APBD I');    
                $sheet->setCellValue("AK$row",'SUB TOTAL');    
                $sheet->setCellValue("AL$row",'APBD II');    
                $sheet->setCellValue("AM$row",'SUB TOTAL');    
                $sheet->setCellValue("AN$row",'APBN');    
                $sheet->setCellValue("AO$row",'SUB TOTAL');    
                $sheet->setCellValue("AP$row",'HIBAH');    
                $sheet->setCellValue("AQ$row",'SUB TOTAL');    
                $sheet->setCellValue("AR$row",'JUMLAH');    
                $sheet->setCellValue("AS$row",'TOTAL');                    
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                                
                $str = "SELECT mo.idobat_puskesmas,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga FROM master_obat_puskesmas mo LEFT JOIN detail_sbbm_puskesmas dsb ON (dsb.idobat=mo.idobat) WHERE mo.idpuskesmas=$idpuskesmas GROUP BY mo.idobat_puskesmas,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC, mo.nama_obat ASC";
                $this->db->setFieldTable(array('idobat_puskesmas','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
                $r=$this->db->getRecord($str); 
                $row+=1;
                $row_awal=$row;                                            
                $penerimaan=$listsumber_dana;
                $pengeluaran=$listsumber_dana;
                $stockakhir=$listsumber_dana;                
                $jumlah_awal=array(1=>0,2=>0,3=>0,4=>0);
                $totalAllItemStockAwal=$jumlah_awal;                
                $totalAllItemPenerimaan=$jumlah_awal;
                $totalAllItemPengeluaran=$jumlah_awal;
                $totalAllItemStockAkhir=$jumlah_awal;
                
                $totalAllHargaStockAwal=$jumlah_awal;                
                $totalAllHargaPenerimaan=$jumlah_awal;
                $totalAllHargaPengeluaran=$jumlah_awal;
                $totalAllHargaStockAkhir=$jumlah_awal;
                
                while (list($k,$v)=each($r)) {
                    $idobat_puskesmas=$v['idobat_puskesmas'];
                    $penerimaan[1]=0;
                    $penerimaan[2]=0;
                    $penerimaan[3]=0;
                    $penerimaan[4]=0;
                    $pengeluaran[1]=0;
                    $pengeluaran[2]=0;
                    $pengeluaran[3]=0;
                    $pengeluaran[4]=0;
                    $jumlah_penerimaan=0;
                    $jumlah_pengeluaran=0;
                    $stockakhir[1]=0;
                    $stockakhir[2]=0;
                    $stockakhir[3]=0;
                    $stockakhir[4]=0;
                    $sheet->setCellValue("A$row",$v['no']);                      
                    if ($v['kode_obat']=='') {
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['mst_nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['mst_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("F$row",0);    
                        $sheet->setCellValue("G$row",0);    
                        $sheet->setCellValue("H$row",0);    
                        $sheet->setCellValue("I$row",0);    
                        $sheet->setCellValue("J$row",0);    
                        $sheet->setCellValue("K$row",0);    
                        $sheet->setCellValue("L$row",0);    
                        $sheet->setCellValue("M$row",0);    
                        $sheet->setCellValue("N$row",0);    
                        $sheet->setCellValue("O$row",0);                    

                        $sheet->setCellValue("P$row",0);    
                        $sheet->setCellValue("Q$row",0);    
                        $sheet->setCellValue("R$row",0);    
                        $sheet->setCellValue("S$row",0);    
                        $sheet->setCellValue("T$row",0);    
                        $sheet->setCellValue("U$row",0);    
                        $sheet->setCellValue("V$row",0);    
                        $sheet->setCellValue("W$row",0);    
                        $sheet->setCellValue("X$row",0);    
                        $sheet->setCellValue("Y$row",0);                    

                        $sheet->setCellValue("Z$row",0);    
                        $sheet->setCellValue("AA$row",0);    
                        $sheet->setCellValue("AB$row",0);    
                        $sheet->setCellValue("AC$row",0);    
                        $sheet->setCellValue("AD$row",0);    
                        $sheet->setCellValue("AE$row",0);    
                        $sheet->setCellValue("AF$row",0);    
                        $sheet->setCellValue("AG$row",0);    
                        $sheet->setCellValue("AH$row",0);    
                        $sheet->setCellValue("AI$row",0);                    

                        $sheet->setCellValue("AJ$row",0);    
                        $sheet->setCellValue("AK$row",0);    
                        $sheet->setCellValue("AL$row",0);    
                        $sheet->setCellValue("AM$row",0);    
                        $sheet->setCellValue("AN$row",0);    
                        $sheet->setCellValue("AO$row",0);    
                        $sheet->setCellValue("AP$row",0);    
                        $sheet->setCellValue("AQ$row",0);    
                        $sheet->setCellValue("AR$row",0);    
                        $sheet->setCellValue("AS$row",0);                                                            
                    }else{
                        $harga=$v['harga'];
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $awalstock=$this->getFirstStock($idobat_puskesmas,$this->dataReport['bulansebelumnya'],$harga,'sumberdanapuskesmas');
                        $sheet->setCellValue("F$row",$awalstock[1]);                    
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($awalstock[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("H$row",$awalstock[2]);
                        $sheet->setCellValueExplicit("I$row",$this->toRupiah($awalstock[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("J$row",$awalstock[3]);                    
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($awalstock[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("L$row",$awalstock[4]);                
                        $sheet->setCellValueExplicit("M$row",$this->toRupiah($awalstock[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                        
                        $jumlah_awalstock=$awalstock[1]+$awalstock[2]+$awalstock[3]+$awalstock[4];                        
                        $sheet->setCellValue("N$row",$jumlah_awalstock);                        
                        $totalAllItemStockAwal[1]+=$awalstock[1];
                        $totalAllItemStockAwal[2]+=$awalstock[2];
                        $totalAllItemStockAwal[3]+=$awalstock[3];
                        $totalAllItemStockAwal[4]+=$awalstock[4];
                        
                        $totalAllHargaStockAwal[1]+=$awalstock[1]*$harga;
                        $totalAllHargaStockAwal[2]+=$awalstock[2]*$harga;
                        $totalAllHargaStockAwal[3]+=$awalstock[3]*$harga;
                        $totalAllHargaStockAwal[4]+=$awalstock[4]*$harga;
                        
                        $total=$jumlah_awalstock*$harga;                        
                        $sheet->setCellValueExplicit("O$row",$this->toRupiah($total),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        
                        $str_penerimaan="SELECT dsb.idsumber_dana_gudang,SUM(dsb.qty) AS jumlah FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE msb.idsbbm_puskesmas=dsb.idsbbm_puskesmas AND dsb.idobat_puskesmas=$idobat_puskesmas AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm_puskesmas,'%Y-%m')='$bulantahun' GROUP BY dsb.idsumber_dana_gudang ORDER BY dsb.idsumber_dana_gudang ASC";
                        $this->db->setFieldTable(array('idsumber_dana_gudang','jumlah'));
                        $r_penerimaan=$this->db->getRecord($str_penerimaan);
                        
                        if (isset($r_penerimaan[1])) {
                            foreach ($r_penerimaan as $n) {
                                $penerimaan[$n['idsumber_dana_gudang']]=$n['jumlah'];     
                                $stock=$awalstock[$n['idsumber_dana_gudang']]+$n['jumlah'];
                                $stockakhir[$n['idsumber_dana_gudang']]=$stock;
                                $jumlah_penerimaan+=$n['jumlah'];
                            }                                           
                        }
                        $sheet->setCellValue("P$row",$penerimaan[1]);    
                        $sheet->setCellValueExplicit("Q$row",$this->toRupiah($penerimaan[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("R$row",$penerimaan[2]);    
                        $sheet->setCellValueExplicit("S$row",$this->toRupiah($penerimaan[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("T$row",$penerimaan[3]);    
                        $sheet->setCellValueExplicit("U$row",$this->toRupiah($penerimaan[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("V$row",$penerimaan[4]);    
                        $sheet->setCellValueExplicit("W$row",$this->toRupiah($penerimaan[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        

                        $sheet->setCellValue("X$row",$jumlah_penerimaan);
                        $total_penerimaan=$harga*$jumlah_penerimaan;
                        $totalAllItemPenerimaan[1]+=$penerimaan[1];
                        $totalAllItemPenerimaan[2]+=$penerimaan[2];
                        $totalAllItemPenerimaan[3]+=$penerimaan[3];
                        $totalAllItemPenerimaan[4]+=$penerimaan[4];
                        
                        $totalAllHargaPenerimaan[1]+=$penerimaan[1]*$harga;
                        $totalAllHargaPenerimaan[2]+=$penerimaan[2]*$harga;
                        $totalAllHargaPenerimaan[3]+=$penerimaan[3]*$harga;
                        $totalAllHargaPenerimaan[4]+=$penerimaan[4]*$harga;

                        $sheet->setCellValueExplicit("Y$row",$this->toRupiah($total_penerimaan),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        
                        $str_pengeluaran="SELECT dsk.idsumber_dana_gudang,COUNT(ks.idkartu_stock_puskesmas) AS jumlah FROM master_sbbk_puskesmas msk,detail_sbbk_puskesmas dsb,kartu_stock_puskesmas ks,detail_sbbm_puskesmas dsk WHERE msk.idsbbk_puskesmas=dsb.idsbbk_puskesmas AND ks.idobat_puskesmas=$idobat_puskesmas AND ks.iddetail_sbbk_puskesmas=dsb.iddetail_sbbk_puskesmas AND ks.iddetail_sbbm_puskesmas=dsk.iddetail_sbbm_puskesmas AND dsb.harga=$harga AND DATE_FORMAT(msk.tanggal_sbbk_puskesmas,'%Y-%m')='$bulantahun' AND ks.isdestroyed=0 GROUP BY dsk.idsumber_dana_gudang ORDER BY dsk.idsumber_dana_gudang ASC";                        
                        $this->db->setFieldTable(array('idsumber_dana_gudang','jumlah'));
                        $r_pengeluaran=$this->db->getRecord($str_pengeluaran);
                        
                        foreach ($r_pengeluaran as $s) {
                            $pengeluaran[$s['idsumber_dana_gudang']]=$s['jumlah'];                            
                            $jumlah_pengeluaran+=$s['jumlah'];
                        }
                        
                        $sheet->setCellValue("Z$row",$pengeluaran[1]);                            
                        $sheet->setCellValueExplicit("AA$row",$this->toRupiah($harga*$pengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AB$row",$pengeluaran[2]);                            ;    
                        $sheet->setCellValueExplicit("AC$row",$this->toRupiah($harga*$pengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AD$row",$pengeluaran[3]);                            
                        $sheet->setCellValueExplicit("AE$row",$this->toRupiah($harga*$pengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AF$row",$pengeluaran[4]);    
                        $sheet->setCellValueExplicit("AG$row",$this->toRupiah($harga*$pengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
                                                
                        $sheet->setCellValue("AH$row",$jumlah_pengeluaran);
                        $total_pengeluaran=$harga*$jumlah_pengeluaran;
                        $totalAllItemPengeluaran[1]+=$pengeluaran[1];
                        $totalAllItemPengeluaran[2]+=$pengeluaran[2];
                        $totalAllItemPengeluaran[3]+=$pengeluaran[3];
                        $totalAllItemPengeluaran[4]+=$pengeluaran[4];
                        
                        $totalAllHargaPengeluaran[1]+=$pengeluaran[1]*$harga;
                        $totalAllHargaPengeluaran[2]+=$pengeluaran[2]*$harga;
                        $totalAllHargaPengeluaran[3]+=$pengeluaran[3]*$harga;
                        $totalAllHargaPengeluaran[4]+=$pengeluaran[4]*$harga;
                        $sheet->setCellValueExplicit("AI$row",$this->toRupiah($total_pengeluaran),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        
                        $stockakhir[1]=($awalstock[1]+$penerimaan[1])-$pengeluaran[1];
                        $stockakhir[2]=($awalstock[2]+$penerimaan[2])-$pengeluaran[2];
                        $stockakhir[3]=($awalstock[3]+$penerimaan[3])-$pengeluaran[3];
                        $stockakhir[4]=($awalstock[4]+$penerimaan[4])-$pengeluaran[4];
                        
                        $sheet->setCellValue("AJ$row",$stockakhir[1]);                            
                        $sheet->setCellValueExplicit("AK$row",$this->toRupiah($harga*$stockakhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AL$row",$stockakhir[2]);    
                        $sheet->setCellValueExplicit("AM$row",$this->toRupiah($harga*$stockakhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AN$row",$stockakhir[3]);                            
                        $sheet->setCellValueExplicit("AO$row",$this->toRupiah($harga*$stockakhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AP$row",$stockakhir[4]);    
                        $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($harga*$stockakhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $jumlah_stockakhir=$stockakhir[1]+$stockakhir[2]+$stockakhir[3]+$stockakhir[4];                       
                        $sheet->setCellValue("AR$row",$jumlah_stockakhir);                        
                        $total_stockakhir=$harga*$jumlah_stockakhir;
                        
                        $totalAllItemStockAkhir[1]+=$stockakhir[1];
                        $totalAllItemStockAkhir[2]+=$stockakhir[2];
                        $totalAllItemStockAkhir[3]+=$stockakhir[3];
                        $totalAllItemStockAkhir[4]+=$stockakhir[4];
                        
                        $totalAllHargaStockAkhir[1]+=$stockakhir[1]*$harga;
                        $totalAllHargaStockAkhir[2]+=$stockakhir[2]*$harga;
                        $totalAllHargaStockAkhir[3]+=$stockakhir[3]*$harga;
                        $totalAllHargaStockAkhir[4]+=$stockakhir[4]*$harga;

                        $sheet->setCellValueExplicit("AS$row",$this->toRupiah($total_stockakhir),PHPExcel_Cell_DataType::TYPE_STRING);                                                                                                
                    }
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row",'TOTAL');                                            
                $sheet->setCellValue("F$row",$totalAllItemStockAwal[1]);                
                $sheet->setCellValueExplicit("G$row",$this->toRupiah($totalAllHargaStockAwal[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("H$row",$totalAllItemStockAwal[2]);                
                $sheet->setCellValueExplicit("I$row",$this->toRupiah($totalAllHargaStockAwal[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("J$row",$totalAllItemStockAwal[3]);                
                $sheet->setCellValueExplicit("K$row",$this->toRupiah($totalAllHargaStockAwal[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("L$row",$totalAllItemStockAwal[4]);                
                $sheet->setCellValueExplicit("M$row",$this->toRupiah($totalAllHargaStockAwal[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                
                $jumlah_all=$totalAllItemStockAwal[1]+$totalAllItemStockAwal[2]+$totalAllItemStockAwal[3]+$totalAllItemStockAwal[4];
                $jumlah_rp=$totalAllHargaStockAwal[1]+$totalAllHargaStockAwal[2]+$totalAllHargaStockAwal[3]+$totalAllHargaStockAwal[4];
                $sheet->setCellValue("N$row",$jumlah_all);                
                $sheet->setCellValueExplicit("O$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                        
                
                $sheet->setCellValue("P$row",$totalAllItemPenerimaan[1]);    
                $sheet->setCellValueExplicit("Q$row",$this->toRupiah($totalAllHargaPenerimaan[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("R$row",$totalAllItemPenerimaan[2]);    
                $sheet->setCellValueExplicit("S$row",$this->toRupiah($totalAllHargaPenerimaan[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("T$row",$totalAllItemPenerimaan[3]);    
                $sheet->setCellValueExplicit("U$row",$this->toRupiah($totalAllHargaPenerimaan[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("V$row",$totalAllItemPenerimaan[4]);    
                $sheet->setCellValueExplicit("W$row",$this->toRupiah($totalAllHargaPenerimaan[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                
                $jumlah_all=$totalAllItemPenerimaan[1]+$totalAllItemPenerimaan[2]+$totalAllItemPenerimaan[3]+$totalAllItemPenerimaan[4];
                $jumlah_rp=$totalAllHargaPenerimaan[1]+$totalAllHargaPenerimaan[2]+$totalAllHargaPenerimaan[3]+$totalAllHargaPenerimaan[4];
                
                $sheet->setCellValue("X$row",$jumlah_all);                
                $sheet->setCellValueExplicit("Y$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("Z$row",$totalAllItemPengeluaran[1]);                            
                $sheet->setCellValueExplicit("AA$row",$this->toRupiah($totalAllHargaPengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AB$row",$totalAllItemPengeluaran[2]);                            ;    
                $sheet->setCellValueExplicit("AC$row",$this->toRupiah($totalAllHargaPengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AD$row",$totalAllItemPengeluaran[3]);                    
                $sheet->setCellValueExplicit("AE$row",$this->toRupiah($totalAllHargaPengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AF$row",$totalAllItemPengeluaran[4]);    
                $sheet->setCellValueExplicit("AG$row",$this->toRupiah($totalAllHargaPengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
                       
                $jumlah_all=$totalAllItemPengeluaran[1]+$totalAllItemPengeluaran[2]+$totalAllItemPengeluaran[3]+$totalAllItemPengeluaran[4];
                $jumlah_rp=$totalAllHargaPengeluaran[1]+$totalAllHargaPengeluaran[2]+$totalAllHargaPengeluaran[3]+$totalAllHargaPengeluaran[4];
                
                $sheet->setCellValue("AH$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AI$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("AJ$row",$totalAllItemStockAkhir[1]);                            
                $sheet->setCellValueExplicit("AK$row",$this->toRupiah($totalAllHargaStockAkhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AL$row",$totalAllItemStockAkhir[2]);    
                $sheet->setCellValueExplicit("AM$row",$this->toRupiah($totalAllHargaStockAkhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AN$row",$totalAllItemStockAkhir[3]);                            
                $sheet->setCellValueExplicit("AO$row",$this->toRupiah($totalAllHargaStockAkhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AP$row",$totalAllItemStockAkhir[4]);    
                $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($totalAllHargaStockAkhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
                
                $jumlah_all=$totalAllItemStockAkhir[1]+$totalAllItemStockAkhir[2]+$totalAllItemStockAkhir[3]+$totalAllItemStockAkhir[4];
                $jumlah_rp=$totalAllHargaStockAkhir[1]+$totalAllHargaStockAkhir[2]+$totalAllHargaStockAkhir[3]+$totalAllHargaStockAkhir[4];
                
                $sheet->setCellValue("AR$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AS$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                $row+=1;                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                );																					 
                $sheet->getStyle("F$row")->applyFromArray($styleArray);                                 
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);                
                              
                $row+=2;
                $row_awal=$row;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",'Tanjungpinang, '.$this->tgl->tanggal('d F Y'));                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $nama_puskesmas=$this->dataReport['nama_puskesmas'];
                $sheet->setCellValue("X$row","Kepala UPT. Puskesmas $nama_puskesmas");                                                                                 
                $row+=4;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nama_ka_puskesmas']);                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nip_ka_puskesmas']);                

                $styleArray=array(								
                                    'font' => array('bold' => true),
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));																					 
                $sheet->getStyle("X$row_awal:AD$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row_awal:AD$row")->getAlignment()->setWrapText(true);
                
                $this->printOut('laporanmutasiobatbulan');
            break;
        }        
        $this->setLink($this->dataReport['linkoutput'],'Laporan Mutasi Obat Bulanan');
    }
    /**
     * digunakan untuk mutasi obat tahunan
     */
    public function printMutasiObatTahunan () {
        $sumberdana=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana(),'none');
        $listsumber_dana=$sumberdana;
        $sumberdana['none']='JUMLAH';
        $sumberdana['total']='TOTAL';     
        $tahun=$this->dataReport['tahun'];
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :                        
                $row=2;
                $sheet=$this->rpt->getActiveSheet();
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                    
                $sheet->mergeCells("A$row:AD$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row",'LAPORAN TAHUNAN MUTASI OBAT');                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 16),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleArray);
                $row+=2;
                $row_awal=$row;                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 9)								
							);
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'KABUPATEN');                
                $sheet->setCellValue("C$row",': BINTAN');                
                $sheet->setCellValue("X$row",'TAHUN');                
                $sheet->setCellValue("Y$row",': '.$this->dataReport['tahun']);                            
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'PROPINSI');                
                $sheet->setCellValue("C$row",': KEPULAUAN RIAU');                                               
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("F$row")->applyFromArray($styleArray);              
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('G')->setWidth(11);   
                $sheet->getColumnDimension('I')->setWidth(11);   
                $sheet->getColumnDimension('K')->setWidth(11);   
                $sheet->getColumnDimension('M')->setWidth(11);   
                $sheet->getColumnDimension('Q')->setWidth(11);
                $sheet->getColumnDimension('S')->setWidth(11);
                $sheet->getColumnDimension('U')->setWidth(11);
                $sheet->getColumnDimension('W')->setWidth(11);
                $sheet->getColumnDimension('AA')->setWidth(11);
                $sheet->getColumnDimension('AC')->setWidth(11);
                $sheet->getColumnDimension('AE')->setWidth(11);
                $sheet->getColumnDimension('AG')->setWidth(11);                
                $sheet->getColumnDimension('AK')->setWidth(11);                
                $sheet->getColumnDimension('AM')->setWidth(11);                
                $sheet->getColumnDimension('AO')->setWidth(11);
                $sheet->getColumnDimension('AQ')->setWidth(11);       
                
                $sheet->getRowDimension($row)->setRowHeight(23);                
                $row_awal=$row;
                $row_akhir=$row+1;
                $sheet->mergeCells("A$row:A$row_akhir");
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:C$row_akhir");
                $sheet->setCellValue("B$row",'NAMA OBAT');                
                $sheet->mergeCells("D$row:D$row_akhir");
                $sheet->setCellValue("D$row",'SATUAN / KEMASAN');                
                $sheet->mergeCells("E$row:E$row_akhir");
                $sheet->setCellValue("E$row",'HARGA (Rp.)');    
                
                $sheet->mergeCells("F$row:O$row");
                $sheet->setCellValue("F$row",'STOCK AWAL');    
                
                $sheet->mergeCells("P$row:Y$row");
                $sheet->setCellValue("P$row",'PENERIMAAN');    
                
                $sheet->mergeCells("Z$row:AI$row");
                $sheet->setCellValue("Z$row",'PENGELUARAN');    
                
                $sheet->mergeCells("AJ$row:AS$row");
                $sheet->setCellValue("AJ$row",'STOCK AKHIR');    
                
                $sheet->mergeCells("AT$row:AT$row_akhir");
                $sheet->setCellValue("AT$row",'KET');                
                $row+=1;
                $sheet->setCellValue("F$row",'APBD I');    
                $sheet->setCellValue("G$row",'SUB TOTAL');    
                $sheet->setCellValue("H$row",'APBD II');    
                $sheet->setCellValue("I$row",'SUB TOTAL');    
                $sheet->setCellValue("J$row",'APBN');    
                $sheet->setCellValue("K$row",'SUB TOTAL');    
                $sheet->setCellValue("L$row",'HIBAH');    
                $sheet->setCellValue("M$row",'SUB TOTAL');    
                $sheet->setCellValue("N$row",'JUMLAH');    
                $sheet->setCellValue("O$row",'TOTAL');                    
                
                $sheet->setCellValue("P$row",'APBD I');    
                $sheet->setCellValue("Q$row",'SUB TOTAL');    
                $sheet->setCellValue("R$row",'APBD II');    
                $sheet->setCellValue("S$row",'SUB TOTAL');    
                $sheet->setCellValue("T$row",'APBN');    
                $sheet->setCellValue("U$row",'SUB TOTAL');    
                $sheet->setCellValue("V$row",'HIBAH');    
                $sheet->setCellValue("W$row",'SUB TOTAL');    
                $sheet->setCellValue("X$row",'JUMLAH');    
                $sheet->setCellValue("Y$row",'TOTAL');                    
                
                $sheet->setCellValue("Z$row",'APBD I');    
                $sheet->setCellValue("AA$row",'SUB TOTAL');    
                $sheet->setCellValue("AB$row",'APBD II');    
                $sheet->setCellValue("AC$row",'SUB TOTAL');    
                $sheet->setCellValue("AD$row",'APBN');    
                $sheet->setCellValue("AE$row",'SUB TOTAL');    
                $sheet->setCellValue("AF$row",'HIBAH');    
                $sheet->setCellValue("AG$row",'SUB TOTAL');    
                $sheet->setCellValue("AH$row",'JUMLAH');    
                $sheet->setCellValue("AI$row",'TOTAL');                    

                $sheet->setCellValue("AJ$row",'APBD I');    
                $sheet->setCellValue("AK$row",'SUB TOTAL');    
                $sheet->setCellValue("AL$row",'APBD II');    
                $sheet->setCellValue("AM$row",'SUB TOTAL');    
                $sheet->setCellValue("AN$row",'APBN');    
                $sheet->setCellValue("AO$row",'SUB TOTAL');    
                $sheet->setCellValue("AP$row",'HIBAH');    
                $sheet->setCellValue("AQ$row",'SUB TOTAL');    
                $sheet->setCellValue("AR$row",'JUMLAH');    
                $sheet->setCellValue("AS$row",'TOTAL');
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                                
                $str = "SELECT mo.idobat,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat) GROUP BY mo.idobat,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC, mo.nama_obat ASC";
                $this->db->setFieldTable(array('idobat','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
                $r=$this->db->getRecord($str); 
                $row+=1;
                $row_awal=$row;                                
                $penerimaan=$listsumber_dana;
                $pengeluaran=$listsumber_dana;
                $stockakhir=$listsumber_dana;
                $jumlah_awal=array(1=>0,2=>0,3=>0,4=>0);
                $totalAllItemStockAwal=$jumlah_awal;                
                $totalAllItemPenerimaan=$jumlah_awal;
                $totalAllItemPengeluaran=$jumlah_awal;
                $totalAllItemStockAkhir=$jumlah_awal;
                
                $totalAllHargaStockAwal=$jumlah_awal;                
                $totalAllHargaPenerimaan=$jumlah_awal;
                $totalAllHargaPengeluaran=$jumlah_awal;
                $totalAllHargaStockAkhir=$jumlah_awal;
                while (list($k,$v)=each($r)) {
                    $idobat=$v['idobat'];
                    $penerimaan[1]=0;
                    $penerimaan[2]=0;
                    $penerimaan[3]=0;
                    $penerimaan[4]=0;
                    $pengeluaran[1]=0;
                    $pengeluaran[2]=0;
                    $pengeluaran[3]=0;
                    $pengeluaran[4]=0;
                    $jumlah_penerimaan=0;
                    $jumlah_pengeluaran=0;
                    $stockakhir[1]=0;
                    $stockakhir[2]=0;
                    $stockakhir[3]=0;
                    $stockakhir[4]=0;
                    $sheet->setCellValue("A$row",$v['no']);                      
                    if ($v['kode_obat']=='') {
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['mst_nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['mst_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("F$row",0);    
                        $sheet->setCellValue("G$row",0);    
                        $sheet->setCellValue("H$row",0);    
                        $sheet->setCellValue("I$row",0);    
                        $sheet->setCellValue("J$row",0);    
                        $sheet->setCellValue("K$row",0);    
                        $sheet->setCellValue("L$row",0);    
                        $sheet->setCellValue("M$row",0);    
                        $sheet->setCellValue("N$row",0);    
                        $sheet->setCellValue("O$row",0);                    

                        $sheet->setCellValue("P$row",0);    
                        $sheet->setCellValue("Q$row",0);    
                        $sheet->setCellValue("R$row",0);    
                        $sheet->setCellValue("S$row",0);    
                        $sheet->setCellValue("T$row",0);    
                        $sheet->setCellValue("U$row",0);    
                        $sheet->setCellValue("V$row",0);    
                        $sheet->setCellValue("W$row",0);    
                        $sheet->setCellValue("X$row",0);    
                        $sheet->setCellValue("Y$row",0);                    

                        $sheet->setCellValue("Z$row",0);    
                        $sheet->setCellValue("AA$row",0);    
                        $sheet->setCellValue("AB$row",0);    
                        $sheet->setCellValue("AC$row",0);    
                        $sheet->setCellValue("AD$row",0);    
                        $sheet->setCellValue("AE$row",0);    
                        $sheet->setCellValue("AF$row",0);    
                        $sheet->setCellValue("AG$row",0);    
                        $sheet->setCellValue("AH$row",0);    
                        $sheet->setCellValue("AI$row",0);                    

                        $sheet->setCellValue("AJ$row",0);    
                        $sheet->setCellValue("AK$row",0);    
                        $sheet->setCellValue("AL$row",0);    
                        $sheet->setCellValue("AM$row",0);    
                        $sheet->setCellValue("AN$row",0);    
                        $sheet->setCellValue("AO$row",0);    
                        $sheet->setCellValue("AP$row",0);    
                        $sheet->setCellValue("AQ$row",0);    
                        $sheet->setCellValue("AR$row",0);    
                        $sheet->setCellValue("AS$row",0);                                          
                    }else{
                        $harga=$v['harga'];
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $awalstock=$this->getFirstStockTahunan($idobat,$this->dataReport['tahunsebelumnya'],$harga,'sumberdana');
                        $sheet->setCellValue("F$row",$awalstock[1]);                    
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($awalstock[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("H$row",$awalstock[2]);
                        $sheet->setCellValueExplicit("I$row",$this->toRupiah($awalstock[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("J$row",$awalstock[3]);                    
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($awalstock[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("L$row",$awalstock[4]);                
                        $sheet->setCellValueExplicit("M$row",$this->toRupiah($awalstock[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING); 
                        
                        $jumlah_awalstock=$awalstock[1]+$awalstock[2]+$awalstock[3]+$awalstock[4];                        
                        $sheet->setCellValue("N$row",$jumlah_awalstock);
                        $totalAllItemStockAwal[1]+=$awalstock[1];
                        $totalAllItemStockAwal[2]+=$awalstock[2];
                        $totalAllItemStockAwal[3]+=$awalstock[3];
                        $totalAllItemStockAwal[4]+=$awalstock[4];
                        
                        $totalAllHargaStockAwal[1]+=$awalstock[1]*$harga;
                        $totalAllHargaStockAwal[2]+=$awalstock[2]*$harga;
                        $totalAllHargaStockAwal[3]+=$awalstock[3]*$harga;
                        $totalAllHargaStockAwal[4]+=$awalstock[4]*$harga;
                        
                        $total=$jumlah_awalstock*$harga;                                                
                        $sheet->setCellValueExplicit("O$row",$this->toRupiah($total),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        
                        $str_penerimaan="SELECT msb.idsumber_dana,SUM(dsb.qty) AS jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND DATE_FORMAT(msb.tanggal_sbbm,'%Y')='$tahun' GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";
                        $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                        $r_penerimaan=$this->db->getRecord($str_penerimaan);
                        
                        if (isset($r_penerimaan[1])) {
                            foreach ($r_penerimaan as $n) {
                                $penerimaan[$n['idsumber_dana']]=$n['jumlah'];     
                                $stock=$awalstock[$n['idsumber_dana']]+$n['jumlah'];
                                $stockakhir[$n['idsumber_dana']]=$stock;
                                $jumlah_penerimaan+=$n['jumlah'];
                            }                                           
                        }                       
                        $sheet->setCellValue("P$row",$penerimaan[1]);    
                        $sheet->setCellValueExplicit("Q$row",$this->toRupiah($penerimaan[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("R$row",$penerimaan[2]);    
                        $sheet->setCellValueExplicit("S$row",$this->toRupiah($penerimaan[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("T$row",$penerimaan[3]);    
                        $sheet->setCellValueExplicit("U$row",$this->toRupiah($penerimaan[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("V$row",$penerimaan[4]);    
                        $sheet->setCellValueExplicit("W$row",$this->toRupiah($penerimaan[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                        
                        $sheet->setCellValue("X$row",$jumlah_penerimaan);
                        $total_penerimaan=$harga*$jumlah_penerimaan;
                        $totalAllItemPenerimaan[1]+=$penerimaan[1];
                        $totalAllItemPenerimaan[2]+=$penerimaan[2];
                        $totalAllItemPenerimaan[3]+=$penerimaan[3];
                        $totalAllItemPenerimaan[4]+=$penerimaan[4];
                        
                        $totalAllHargaPenerimaan[1]+=$penerimaan[1]*$harga;
                        $totalAllHargaPenerimaan[2]+=$penerimaan[2]*$harga;
                        $totalAllHargaPenerimaan[3]+=$penerimaan[3]*$harga;
                        $totalAllHargaPenerimaan[4]+=$penerimaan[4]*$harga;
                        
                        $sheet->setCellValueExplicit("Y$row",$this->toRupiah($total_penerimaan),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        
                        $str_pengeluaran="SELECT msb.idsumber_dana,COUNT(ks.idkartu_stock) AS jumlah FROM master_sbbk msk,detail_sbbk dsb,kartu_stock ks,master_sbbm msb WHERE msk.idsbbk=dsb.idsbbk AND ks.idobat=$idobat AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.idsbbm=msb.idsbbm AND dsb.harga=$harga AND DATE_FORMAT(msk.tanggal_sbbk,'%Y')='$tahun' AND ks.isdestroyed=0 GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";                        
                        $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                        $r_pengeluaran=$this->db->getRecord($str_pengeluaran);
                        
                        foreach ($r_pengeluaran as $s) {
                            $pengeluaran[$s['idsumber_dana']]=$s['jumlah'];                            
                            $jumlah_pengeluaran+=$s['jumlah'];
                        }                        
                        $sheet->setCellValue("Z$row",$pengeluaran[1]);                            
                        $sheet->setCellValueExplicit("AA$row",$this->toRupiah($harga*$pengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AB$row",$pengeluaran[2]);                            ;    
                        $sheet->setCellValueExplicit("AC$row",$this->toRupiah($harga*$pengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AD$row",$pengeluaran[3]);                            
                        $sheet->setCellValueExplicit("AE$row",$this->toRupiah($harga*$pengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AF$row",$pengeluaran[4]);    
                        $sheet->setCellValueExplicit("AG$row",$this->toRupiah($harga*$pengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
                        
                        $sheet->setCellValue("AH$row",$jumlah_pengeluaran);                        
                        $total_pengeluaran=$harga*$jumlah_pengeluaran;
                        $totalAllItemPengeluaran[1]+=$pengeluaran[1];
                        $totalAllItemPengeluaran[2]+=$pengeluaran[2];
                        $totalAllItemPengeluaran[3]+=$pengeluaran[3];
                        $totalAllItemPengeluaran[4]+=$pengeluaran[4];
                        
                        $totalAllHargaPengeluaran[1]+=$pengeluaran[1]*$harga;
                        $totalAllHargaPengeluaran[2]+=$pengeluaran[2]*$harga;
                        $totalAllHargaPengeluaran[3]+=$pengeluaran[3]*$harga;
                        $totalAllHargaPengeluaran[4]+=$pengeluaran[4]*$harga;
                        $sheet->setCellValueExplicit("AI$row",$this->toRupiah($total_pengeluaran),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        
                        $stockakhir[1]=($awalstock[1]+$penerimaan[1])-$pengeluaran[1];
                        $stockakhir[2]=($awalstock[2]+$penerimaan[2])-$pengeluaran[2];
                        $stockakhir[3]=($awalstock[3]+$penerimaan[3])-$pengeluaran[3];
                        $stockakhir[4]=($awalstock[4]+$penerimaan[4])-$pengeluaran[4];
                        
                        $sheet->setCellValue("AJ$row",$stockakhir[1]);                            
                        $sheet->setCellValueExplicit("AK$row",$this->toRupiah($harga*$stockakhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AL$row",$stockakhir[2]);    
                        $sheet->setCellValueExplicit("AM$row",$this->toRupiah($harga*$stockakhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AN$row",$stockakhir[3]);                            
                        $sheet->setCellValueExplicit("AO$row",$this->toRupiah($harga*$stockakhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("AP$row",$stockakhir[4]);    
                        $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($harga*$stockakhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $jumlah_stockakhir=$stockakhir[1]+$stockakhir[2]+$stockakhir[3]+$stockakhir[4];                       
                        $sheet->setCellValue("AR$row",$jumlah_stockakhir);                        
                        $total_stockakhir=$harga*$jumlah_stockakhir;
                        
                        $totalAllItemStockAkhir[1]+=$stockakhir[1];
                        $totalAllItemStockAkhir[2]+=$stockakhir[2];
                        $totalAllItemStockAkhir[3]+=$stockakhir[3];
                        $totalAllItemStockAkhir[4]+=$stockakhir[4];
                        
                        $totalAllHargaStockAkhir[1]+=$stockakhir[1]*$harga;
                        $totalAllHargaStockAkhir[2]+=$stockakhir[2]*$harga;
                        $totalAllHargaStockAkhir[3]+=$stockakhir[3]*$harga;
                        $totalAllHargaStockAkhir[4]+=$stockakhir[4]*$harga;

                        $sheet->setCellValueExplicit("AS$row",$this->toRupiah($total_stockakhir),PHPExcel_Cell_DataType::TYPE_STRING);                                                                                                
                    }      
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row",'TOTAL');                                            
                $sheet->setCellValue("F$row",$totalAllItemStockAwal[1]);                
                $sheet->setCellValueExplicit("G$row",$this->toRupiah($totalAllHargaStockAwal[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("H$row",$totalAllItemStockAwal[2]);                
                $sheet->setCellValueExplicit("I$row",$this->toRupiah($totalAllHargaStockAwal[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("J$row",$totalAllItemStockAwal[3]);                
                $sheet->setCellValueExplicit("K$row",$this->toRupiah($totalAllHargaStockAwal[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("L$row",$totalAllItemStockAwal[4]);                
                $sheet->setCellValueExplicit("M$row",$this->toRupiah($totalAllHargaStockAwal[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                
                $jumlah_all=$totalAllItemStockAwal[1]+$totalAllItemStockAwal[2]+$totalAllItemStockAwal[3]+$totalAllItemStockAwal[4];
                $jumlah_rp=$totalAllHargaStockAwal[1]+$totalAllHargaStockAwal[2]+$totalAllHargaStockAwal[3]+$totalAllHargaStockAwal[4];
                $sheet->setCellValue("N$row",$jumlah_all);                
                $sheet->setCellValueExplicit("O$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                        
                
                $sheet->setCellValue("P$row",$totalAllItemPenerimaan[1]);    
                $sheet->setCellValueExplicit("Q$row",$this->toRupiah($totalAllHargaPenerimaan[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("R$row",$totalAllItemPenerimaan[2]);    
                $sheet->setCellValueExplicit("S$row",$this->toRupiah($totalAllHargaPenerimaan[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("T$row",$totalAllItemPenerimaan[3]);    
                $sheet->setCellValueExplicit("U$row",$this->toRupiah($totalAllHargaPenerimaan[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("V$row",$totalAllItemPenerimaan[4]);    
                $sheet->setCellValueExplicit("W$row",$this->toRupiah($totalAllHargaPenerimaan[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                
                $jumlah_all=$totalAllItemPenerimaan[1]+$totalAllItemPenerimaan[2]+$totalAllItemPenerimaan[3]+$totalAllItemPenerimaan[4];
                $jumlah_rp=$totalAllHargaPenerimaan[1]+$totalAllHargaPenerimaan[2]+$totalAllHargaPenerimaan[3]+$totalAllHargaPenerimaan[4];
                
                $sheet->setCellValue("X$row",$jumlah_all);                
                $sheet->setCellValueExplicit("Y$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("Z$row",$totalAllItemPengeluaran[1]);                            
                $sheet->setCellValueExplicit("AA$row",$this->toRupiah($totalAllHargaPengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AB$row",$totalAllItemPengeluaran[2]);                            ;    
                $sheet->setCellValueExplicit("AC$row",$this->toRupiah($totalAllHargaPengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AD$row",$totalAllItemPengeluaran[3]);                    
                $sheet->setCellValueExplicit("AE$row",$this->toRupiah($totalAllHargaPengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AF$row",$totalAllItemPengeluaran[4]);    
                $sheet->setCellValueExplicit("AG$row",$this->toRupiah($totalAllHargaPengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
                       
                $jumlah_all=$totalAllItemPengeluaran[1]+$totalAllItemPengeluaran[2]+$totalAllItemPengeluaran[3]+$totalAllItemPengeluaran[4];
                $jumlah_rp=$totalAllHargaPengeluaran[1]+$totalAllHargaPengeluaran[2]+$totalAllHargaPengeluaran[3]+$totalAllHargaPengeluaran[4];
                
                $sheet->setCellValue("AH$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AI$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("AJ$row",$totalAllItemStockAkhir[1]);                            
                $sheet->setCellValueExplicit("AK$row",$this->toRupiah($totalAllHargaStockAkhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AL$row",$totalAllItemStockAkhir[2]);    
                $sheet->setCellValueExplicit("AM$row",$this->toRupiah($totalAllHargaStockAkhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AN$row",$totalAllItemStockAkhir[3]);                            
                $sheet->setCellValueExplicit("AO$row",$this->toRupiah($totalAllHargaStockAkhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AP$row",$totalAllItemStockAkhir[4]);    
                $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($totalAllHargaStockAkhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
                
                $jumlah_all=$totalAllItemStockAkhir[1]+$totalAllItemStockAkhir[2]+$totalAllItemStockAkhir[3]+$totalAllItemStockAkhir[4];
                $jumlah_rp=$totalAllHargaStockAkhir[1]+$totalAllHargaStockAkhir[2]+$totalAllHargaStockAkhir[3]+$totalAllHargaStockAkhir[4];
                
                $sheet->setCellValue("AR$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AS$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                $row+=1;                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                );																					 
                $sheet->getStyle("F$row")->applyFromArray($styleArray);                                 
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);                
                              
                $row+=2;
                $row_awal=$row;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",'Tanjungpinang, '.$this->tgl->tanggal('d F Y'));                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",'Kepala UPTD Instalasi Farmasi');                                                                                 
                $row+=4;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nama_ka_gudang']);                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nip_ka_gudang']);                

                $styleArray=array(								
                                    'font' => array('bold' => true),
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));																					 
                $sheet->getStyle("X$row_awal:AD$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row_awal:AD$row")->getAlignment()->setWrapText(true);
                
                $this->printOut('laporanmutasiobattahunan');
            break;
        }        
        $this->setLink($this->dataReport['linkoutput'],'Laporan Mutasi Obat Tahunan');
    }
    /**
     * digunakan untuk mutasi obat Semester
     */
    public function printMutasiObatSemester () {
        $sumberdana=$this->DMaster->removeIdFromArray($this->DMaster->getListSumberDana(),'none');
        $listsumber_dana=$sumberdana;
        $sumberdana['none']='JUMLAH';
        $sumberdana['total']='TOTAL';     
        $bulantahun=$this->tgl->tanggal ('Y-m',$this->dataReport['bulantahun']);
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :                        
                $row=2;
                $sheet=$this->rpt->getActiveSheet();
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                    
                $sheet->mergeCells("A$row:AD$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row",'LAPORAN MUTASI OBAT SEMESTER');                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 16),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleArray);
                $row+=2;
                $row_awal=$row;                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 9)								
							);
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'KABUPATEN');                
                $sheet->setCellValue("C$row",': BINTAN');                
                $sheet->setCellValue("X$row",'SEMESTER');                
                $semester=$this->dataReport['semester'] == 1 ? 'I (Januari - Juni)' : 'II (Juli - Desember)';
                $sheet->setCellValue("Y$row",": $semester");                            
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'PROPINSI');                
                $sheet->setCellValue("C$row",': KEPULAUAN RIAU');                                
                $sheet->setCellValue("X$row",'TAHUN');                
                $sheet->setCellValue("Y$row",': '.$this->dataReport['tahun']);                
                $sheet->getStyle("A$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row")->applyFromArray($styleArray);              
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('G')->setWidth(11);   
                $sheet->getColumnDimension('I')->setWidth(11);   
                $sheet->getColumnDimension('K')->setWidth(11);   
                $sheet->getColumnDimension('M')->setWidth(11);   
                $sheet->getColumnDimension('Q')->setWidth(11);
                $sheet->getColumnDimension('S')->setWidth(11);
                $sheet->getColumnDimension('U')->setWidth(11);
                $sheet->getColumnDimension('W')->setWidth(11);
                $sheet->getColumnDimension('AA')->setWidth(11);
                $sheet->getColumnDimension('AC')->setWidth(11);
                $sheet->getColumnDimension('AE')->setWidth(11);
                $sheet->getColumnDimension('AG')->setWidth(11);                
                $sheet->getColumnDimension('AK')->setWidth(11);                
                $sheet->getColumnDimension('AM')->setWidth(11);                
                $sheet->getColumnDimension('AO')->setWidth(11);
                $sheet->getColumnDimension('AQ')->setWidth(11);                
                
                $sheet->getRowDimension($row)->setRowHeight(23);                
                $row_awal=$row;
                $row_akhir=$row+1;
                $sheet->mergeCells("A$row:A$row_akhir");
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:C$row_akhir");
                $sheet->setCellValue("B$row",'NAMA OBAT');                
                $sheet->mergeCells("D$row:D$row_akhir");
                $sheet->setCellValue("D$row",'SATUAN / KEMASAN');                
                $sheet->mergeCells("E$row:E$row_akhir");
                $sheet->setCellValue("E$row",'HARGA (Rp.)');
                
                $sheet->mergeCells("F$row:O$row");
                $sheet->setCellValue("F$row",'STOCK AWAL');    
                
                $sheet->mergeCells("P$row:Y$row");
                $sheet->setCellValue("P$row",'PENERIMAAN');    
                
                $sheet->mergeCells("Z$row:AI$row");
                $sheet->setCellValue("Z$row",'PENGELUARAN');    
                
                $sheet->mergeCells("AJ$row:AS$row");
                $sheet->setCellValue("AJ$row",'STOCK AKHIR');    
                
                $sheet->mergeCells("AT$row:AT$row_akhir");
                $sheet->setCellValue("AT$row",'KET');                
                $row+=1;
                $sheet->setCellValue("F$row",'APBD I');    
                $sheet->setCellValue("G$row",'SUB TOTAL');    
                $sheet->setCellValue("H$row",'APBD II');    
                $sheet->setCellValue("I$row",'SUB TOTAL');    
                $sheet->setCellValue("J$row",'APBN');    
                $sheet->setCellValue("K$row",'SUB TOTAL');    
                $sheet->setCellValue("L$row",'HIBAH');    
                $sheet->setCellValue("M$row",'SUB TOTAL');    
                $sheet->setCellValue("N$row",'JUMLAH');    
                $sheet->setCellValue("O$row",'TOTAL');                    
                
                $sheet->setCellValue("P$row",'APBD I');    
                $sheet->setCellValue("Q$row",'SUB TOTAL');    
                $sheet->setCellValue("R$row",'APBD II');    
                $sheet->setCellValue("S$row",'SUB TOTAL');    
                $sheet->setCellValue("T$row",'APBN');    
                $sheet->setCellValue("U$row",'SUB TOTAL');    
                $sheet->setCellValue("V$row",'HIBAH');    
                $sheet->setCellValue("W$row",'SUB TOTAL');    
                $sheet->setCellValue("X$row",'JUMLAH');    
                $sheet->setCellValue("Y$row",'TOTAL');                    
                
                $sheet->setCellValue("Z$row",'APBD I');    
                $sheet->setCellValue("AA$row",'SUB TOTAL');    
                $sheet->setCellValue("AB$row",'APBD II');    
                $sheet->setCellValue("AC$row",'SUB TOTAL');    
                $sheet->setCellValue("AD$row",'APBN');    
                $sheet->setCellValue("AE$row",'SUB TOTAL');    
                $sheet->setCellValue("AF$row",'HIBAH');    
                $sheet->setCellValue("AG$row",'SUB TOTAL');    
                $sheet->setCellValue("AH$row",'JUMLAH');    
                $sheet->setCellValue("AI$row",'TOTAL');                    

                $sheet->setCellValue("AJ$row",'APBD I');    
                $sheet->setCellValue("AK$row",'SUB TOTAL');    
                $sheet->setCellValue("AL$row",'APBD II');    
                $sheet->setCellValue("AM$row",'SUB TOTAL');    
                $sheet->setCellValue("AN$row",'APBN');    
                $sheet->setCellValue("AO$row",'SUB TOTAL');    
                $sheet->setCellValue("AP$row",'HIBAH');    
                $sheet->setCellValue("AQ$row",'SUB TOTAL');    
                $sheet->setCellValue("AR$row",'JUMLAH');    
                $sheet->setCellValue("AS$row",'TOTAL');                    
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                                
                $str = "SELECT mo.idobat,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.harga AS mst_harga,dsb.harga FROM master_obat mo LEFT JOIN detail_sbbm dsb ON (dsb.idobat=mo.idobat) GROUP BY mo.idobat,dsb.harga ORDER BY ISNULL(dsb.tanggal_expire),dsb.tanggal_expire ASC, mo.nama_obat ASC";
                $this->db->setFieldTable(array('idobat','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
                $r=$this->db->getRecord($str); 
                $row+=1;
                $row_awal=$row;                                            
                $penerimaan=$listsumber_dana;
                $pengeluaran=$listsumber_dana;
                $stockakhir=$listsumber_dana;                
                $jumlah_awal=array(1=>0,2=>0,3=>0,4=>0);
                $totalAllItemStockAwal=$jumlah_awal;                
                $totalAllItemPenerimaan=$jumlah_awal;
                $totalAllItemPengeluaran=$jumlah_awal;
                $totalAllItemStockAkhir=$jumlah_awal;
                
                $totalAllHargaStockAwal=$jumlah_awal;                
                $totalAllHargaPenerimaan=$jumlah_awal;
                $totalAllHargaPengeluaran=$jumlah_awal;
                $totalAllHargaStockAkhir=$jumlah_awal;
                
                $ta=$this->dataReport['tahun'];
                $semester=$this->dataReport['semester'];        
                $str_sbbm=$semester == 1 ? " msb.tanggal_sbbm >= '$ta-01-01' AND msb.tanggal_sbbm < '$ta-07-01'" :  " msb.tanggal_sbbm >= '$ta-07-01' AND  msb.tanggal_sbbm <= '$ta-12-31'";        
                $str_sbbk=$semester == 1 ? " msk.tanggal_sbbk >= '$ta-01-01' AND msk.tanggal_sbbk < '$ta-07-01'" :  " msk.tanggal_sbbk >= '$ta-07-01' AND  msk.tanggal_sbbk <= '$ta-12-31'";        
                while (list($k,$v)=each($r)) {
                    $idobat=$v['idobat'];
                    $penerimaan[1]=0;
                    $penerimaan[2]=0;
                    $penerimaan[3]=0;
                    $penerimaan[4]=0;
                    $pengeluaran[1]=0;
                    $pengeluaran[2]=0;
                    $pengeluaran[3]=0;
                    $pengeluaran[4]=0;
                    $jumlah_penerimaan=0;
                    $jumlah_pengeluaran=0;
                    $stockakhir[1]=0;
                    $stockakhir[2]=0;
                    $stockakhir[3]=0;
                    $stockakhir[4]=0;
                    $sheet->setCellValue("A$row",$v['no']);                      
                    if ($v['kode_obat']=='') {
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['mst_nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['mst_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $sheet->setCellValue("F$row",0);    
                        $sheet->setCellValue("G$row",0);    
                        $sheet->setCellValue("H$row",0);    
                        $sheet->setCellValue("I$row",0);    
                        $sheet->setCellValue("J$row",0);    
                        $sheet->setCellValue("K$row",0);    
                        $sheet->setCellValue("L$row",0);    
                        $sheet->setCellValue("M$row",0);    
                        $sheet->setCellValue("N$row",0);    
                        $sheet->setCellValue("O$row",0);                    

                        $sheet->setCellValue("P$row",0);    
                        $sheet->setCellValue("Q$row",0);    
                        $sheet->setCellValue("R$row",0);    
                        $sheet->setCellValue("S$row",0);    
                        $sheet->setCellValue("T$row",0);    
                        $sheet->setCellValue("U$row",0);    
                        $sheet->setCellValue("V$row",0);    
                        $sheet->setCellValue("W$row",0);    
                        $sheet->setCellValue("X$row",0);    
                        $sheet->setCellValue("Y$row",0);                    

                        $sheet->setCellValue("Z$row",0);    
                        $sheet->setCellValue("AA$row",0);    
                        $sheet->setCellValue("AB$row",0);    
                        $sheet->setCellValue("AC$row",0);    
                        $sheet->setCellValue("AD$row",0);    
                        $sheet->setCellValue("AE$row",0);    
                        $sheet->setCellValue("AF$row",0);    
                        $sheet->setCellValue("AG$row",0);    
                        $sheet->setCellValue("AH$row",0);    
                        $sheet->setCellValue("AI$row",0);                    

                        $sheet->setCellValue("AJ$row",0);    
                        $sheet->setCellValue("AK$row",0);    
                        $sheet->setCellValue("AL$row",0);    
                        $sheet->setCellValue("AM$row",0);    
                        $sheet->setCellValue("AN$row",0);    
                        $sheet->setCellValue("AO$row",0);    
                        $sheet->setCellValue("AP$row",0);    
                        $sheet->setCellValue("AQ$row",0);    
                        $sheet->setCellValue("AR$row",0);    
                        $sheet->setCellValue("AS$row",0);                                                            
                    }else{
                        $harga=$v['harga'];
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['nama_obat']);                
                        $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));
                        $sheet->setCellValueExplicit("E$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $awalstock=$this->getFirstStockSemester($idobat, $this->dataReport['tahun'], $this->dataReport['semester'], $harga, 'sumberdana');
                        $sheet->setCellValue("F$row",$awalstock[1]);                    
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($awalstock[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("H$row",$awalstock[2]);
                        $sheet->setCellValueExplicit("I$row",$this->toRupiah($awalstock[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("J$row",$awalstock[3]);                    
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($awalstock[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("L$row",$awalstock[4]);                
                        $sheet->setCellValueExplicit("M$row",$this->toRupiah($awalstock[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                        
                        $jumlah_awalstock=$awalstock[1]+$awalstock[2]+$awalstock[3]+$awalstock[4];                        
                        $sheet->setCellValue("N$row",$jumlah_awalstock);                        
                        $totalAllItemStockAwal[1]+=$awalstock[1];
                        $totalAllItemStockAwal[2]+=$awalstock[2];
                        $totalAllItemStockAwal[3]+=$awalstock[3];
                        $totalAllItemStockAwal[4]+=$awalstock[4];
                        
                        $totalAllHargaStockAwal[1]+=$awalstock[1]*$harga;
                        $totalAllHargaStockAwal[2]+=$awalstock[2]*$harga;
                        $totalAllHargaStockAwal[3]+=$awalstock[3]*$harga;
                        $totalAllHargaStockAwal[4]+=$awalstock[4]*$harga;
                        
                        $total=$jumlah_awalstock*$harga;                        
                        $sheet->setCellValueExplicit("O$row",$this->toRupiah($total),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        
                        $str_penerimaan="SELECT msb.idsumber_dana,SUM(dsb.qty) AS jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND dsb.idobat=$idobat AND harga=$harga AND msb.status='complete' AND DATE_FORMAT(msb.tanggal_sbbm,'%Y-%m')='$bulantahun' GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";
                        $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                        $r_penerimaan=$this->db->getRecord($str_penerimaan);
                        
                        if (isset($r_penerimaan[1])) {
                            foreach ($r_penerimaan as $n) {
                                $penerimaan[$n['idsumber_dana']]=$n['jumlah'];     
                                $stock=$awalstock[$n['idsumber_dana']]+$n['jumlah'];
                                $stockakhir[$n['idsumber_dana']]=$stock;
                                $jumlah_penerimaan+=$n['jumlah'];
                            }                                           
                        }
//                        $sheet->setCellValue("P$row",$penerimaan[1]);    
//                        $sheet->setCellValueExplicit("Q$row",$this->toRupiah($penerimaan[1]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
//                        $sheet->setCellValue("R$row",$penerimaan[2]);    
//                        $sheet->setCellValueExplicit("S$row",$this->toRupiah($penerimaan[2]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
//                        $sheet->setCellValue("T$row",$penerimaan[3]);    
//                        $sheet->setCellValueExplicit("U$row",$this->toRupiah($penerimaan[3]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                
//                        $sheet->setCellValue("V$row",$penerimaan[4]);    
//                        $sheet->setCellValueExplicit("W$row",$this->toRupiah($penerimaan[4]*$harga),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
//
//                        $sheet->setCellValue("X$row",$jumlah_penerimaan);
//                        $total_penerimaan=$harga*$jumlah_penerimaan;
//                        $totalAllItemPenerimaan[1]+=$penerimaan[1];
//                        $totalAllItemPenerimaan[2]+=$penerimaan[2];
//                        $totalAllItemPenerimaan[3]+=$penerimaan[3];
//                        $totalAllItemPenerimaan[4]+=$penerimaan[4];
//                        
//                        $totalAllHargaPenerimaan[1]+=$penerimaan[1]*$harga;
//                        $totalAllHargaPenerimaan[2]+=$penerimaan[2]*$harga;
//                        $totalAllHargaPenerimaan[3]+=$penerimaan[3]*$harga;
//                        $totalAllHargaPenerimaan[4]+=$penerimaan[4]*$harga;
//
//                        $sheet->setCellValueExplicit("Y$row",$this->toRupiah($total_penerimaan),PHPExcel_Cell_DataType::TYPE_STRING);                                                
//                        
//                        $str_pengeluaran="SELECT msb.idsumber_dana,COUNT(ks.idkartu_stock) AS jumlah FROM master_sbbk msk,detail_sbbk dsb,kartu_stock ks,master_sbbm msb WHERE msk.idsbbk=dsb.idsbbk AND ks.idobat=$idobat AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.idsbbm=msb.idsbbm AND dsb.harga=$harga AND status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')='$bulantahun' AND ks.isdestroyed=0 GROUP BY msb.idsumber_dana ORDER BY msb.idsumber_dana ASC";                        
//                        $this->db->setFieldTable(array('idsumber_dana','jumlah'));
//                        $r_pengeluaran=$this->db->getRecord($str_pengeluaran);
//                        
//                        foreach ($r_pengeluaran as $s) {
//                            $pengeluaran[$s['idsumber_dana']]=$s['jumlah'];                            
//                            $jumlah_pengeluaran+=$s['jumlah'];
//                        }
//                        
//                        $sheet->setCellValue("Z$row",$pengeluaran[1]);                            
//                        $sheet->setCellValueExplicit("AA$row",$this->toRupiah($harga*$pengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
//                        $sheet->setCellValue("AB$row",$pengeluaran[2]);                            ;    
//                        $sheet->setCellValueExplicit("AC$row",$this->toRupiah($harga*$pengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
//                        $sheet->setCellValue("AD$row",$pengeluaran[3]);                            
//                        $sheet->setCellValueExplicit("AE$row",$this->toRupiah($harga*$pengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
//                        $sheet->setCellValue("AF$row",$pengeluaran[4]);    
//                        $sheet->setCellValueExplicit("AG$row",$this->toRupiah($harga*$pengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
//                                                
//                        $sheet->setCellValue("AH$row",$jumlah_pengeluaran);
//                        $total_pengeluaran=$harga*$jumlah_pengeluaran;
//                        $totalAllItemPengeluaran[1]+=$pengeluaran[1];
//                        $totalAllItemPengeluaran[2]+=$pengeluaran[2];
//                        $totalAllItemPengeluaran[3]+=$pengeluaran[3];
//                        $totalAllItemPengeluaran[4]+=$pengeluaran[4];
//                        
//                        $totalAllHargaPengeluaran[1]+=$pengeluaran[1]*$harga;
//                        $totalAllHargaPengeluaran[2]+=$pengeluaran[2]*$harga;
//                        $totalAllHargaPengeluaran[3]+=$pengeluaran[3]*$harga;
//                        $totalAllHargaPengeluaran[4]+=$pengeluaran[4]*$harga;
//                        $sheet->setCellValueExplicit("AI$row",$this->toRupiah($total_pengeluaran),PHPExcel_Cell_DataType::TYPE_STRING);                        
//                        
//                        $stockakhir[1]=($awalstock[1]+$penerimaan[1])-$pengeluaran[1];
//                        $stockakhir[2]=($awalstock[2]+$penerimaan[2])-$pengeluaran[2];
//                        $stockakhir[3]=($awalstock[3]+$penerimaan[3])-$pengeluaran[3];
//                        $stockakhir[4]=($awalstock[4]+$penerimaan[4])-$pengeluaran[4];
//                        
//                        $sheet->setCellValue("AJ$row",$stockakhir[1]);                            
//                        $sheet->setCellValueExplicit("AK$row",$this->toRupiah($harga*$stockakhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
//                        $sheet->setCellValue("AL$row",$stockakhir[2]);    
//                        $sheet->setCellValueExplicit("AM$row",$this->toRupiah($harga*$stockakhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
//                        $sheet->setCellValue("AN$row",$stockakhir[3]);                            
//                        $sheet->setCellValueExplicit("AO$row",$this->toRupiah($harga*$stockakhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
//                        $sheet->setCellValue("AP$row",$stockakhir[4]);    
//                        $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($harga*$stockakhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
//                        
//                        $jumlah_stockakhir=$stockakhir[1]+$stockakhir[2]+$stockakhir[3]+$stockakhir[4];                       
//                        $sheet->setCellValue("AR$row",$jumlah_stockakhir);                        
//                        $total_stockakhir=$harga*$jumlah_stockakhir;
//                        
//                        $totalAllItemStockAkhir[1]+=$stockakhir[1];
//                        $totalAllItemStockAkhir[2]+=$stockakhir[2];
//                        $totalAllItemStockAkhir[3]+=$stockakhir[3];
//                        $totalAllItemStockAkhir[4]+=$stockakhir[4];
//                        
//                        $totalAllHargaStockAkhir[1]+=$stockakhir[1]*$harga;
//                        $totalAllHargaStockAkhir[2]+=$stockakhir[2]*$harga;
//                        $totalAllHargaStockAkhir[3]+=$stockakhir[3]*$harga;
//                        $totalAllHargaStockAkhir[4]+=$stockakhir[4]*$harga;
//
//                        $sheet->setCellValueExplicit("AS$row",$this->toRupiah($total_stockakhir),PHPExcel_Cell_DataType::TYPE_STRING);                                                                                                
                    }
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $sheet->mergeCells("B$row:E$row");
                $sheet->setCellValue("B$row",'TOTAL');                                            
                $sheet->setCellValue("F$row",$totalAllItemStockAwal[1]);                
                $sheet->setCellValueExplicit("G$row",$this->toRupiah($totalAllHargaStockAwal[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("H$row",$totalAllItemStockAwal[2]);                
                $sheet->setCellValueExplicit("I$row",$this->toRupiah($totalAllHargaStockAwal[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("J$row",$totalAllItemStockAwal[3]);                
                $sheet->setCellValueExplicit("K$row",$this->toRupiah($totalAllHargaStockAwal[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                $sheet->setCellValue("L$row",$totalAllItemStockAwal[4]);                
                $sheet->setCellValueExplicit("M$row",$this->toRupiah($totalAllHargaStockAwal[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                        
                
                $jumlah_all=$totalAllItemStockAwal[1]+$totalAllItemStockAwal[2]+$totalAllItemStockAwal[3]+$totalAllItemStockAwal[4];
                $jumlah_rp=$totalAllHargaStockAwal[1]+$totalAllHargaStockAwal[2]+$totalAllHargaStockAwal[3]+$totalAllHargaStockAwal[4];
                $sheet->setCellValue("N$row",$jumlah_all);                
                $sheet->setCellValueExplicit("O$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                        
                
                $sheet->setCellValue("P$row",$totalAllItemPenerimaan[1]);    
                $sheet->setCellValueExplicit("Q$row",$this->toRupiah($totalAllHargaPenerimaan[1]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("R$row",$totalAllItemPenerimaan[2]);    
                $sheet->setCellValueExplicit("S$row",$this->toRupiah($totalAllHargaPenerimaan[2]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("T$row",$totalAllItemPenerimaan[3]);    
                $sheet->setCellValueExplicit("U$row",$this->toRupiah($totalAllHargaPenerimaan[3]),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                $sheet->setCellValue("V$row",$totalAllItemPenerimaan[4]);    
                $sheet->setCellValueExplicit("W$row",$this->toRupiah($totalAllHargaPenerimaan[4]),PHPExcel_Cell_DataType::TYPE_STRING);                                                                        
                
                $jumlah_all=$totalAllItemPenerimaan[1]+$totalAllItemPenerimaan[2]+$totalAllItemPenerimaan[3]+$totalAllItemPenerimaan[4];
                $jumlah_rp=$totalAllHargaPenerimaan[1]+$totalAllHargaPenerimaan[2]+$totalAllHargaPenerimaan[3]+$totalAllHargaPenerimaan[4];
                
                $sheet->setCellValue("X$row",$jumlah_all);                
                $sheet->setCellValueExplicit("Y$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("Z$row",$totalAllItemPengeluaran[1]);                            
                $sheet->setCellValueExplicit("AA$row",$this->toRupiah($totalAllHargaPengeluaran[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AB$row",$totalAllItemPengeluaran[2]);                            ;    
                $sheet->setCellValueExplicit("AC$row",$this->toRupiah($totalAllHargaPengeluaran[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AD$row",$totalAllItemPengeluaran[3]);                    
                $sheet->setCellValueExplicit("AE$row",$this->toRupiah($totalAllHargaPengeluaran[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AF$row",$totalAllItemPengeluaran[4]);    
                $sheet->setCellValueExplicit("AG$row",$this->toRupiah($totalAllHargaPengeluaran[4]),PHPExcel_Cell_DataType::TYPE_STRING); 
                       
                $jumlah_all=$totalAllItemPengeluaran[1]+$totalAllItemPengeluaran[2]+$totalAllItemPengeluaran[3]+$totalAllItemPengeluaran[4];
                $jumlah_rp=$totalAllHargaPengeluaran[1]+$totalAllHargaPengeluaran[2]+$totalAllHargaPengeluaran[3]+$totalAllHargaPengeluaran[4];
                
                $sheet->setCellValue("AH$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AI$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $sheet->setCellValue("AJ$row",$totalAllItemStockAkhir[1]);                            
                $sheet->setCellValueExplicit("AK$row",$this->toRupiah($totalAllHargaStockAkhir[1]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AL$row",$totalAllItemStockAkhir[2]);    
                $sheet->setCellValueExplicit("AM$row",$this->toRupiah($totalAllHargaStockAkhir[2]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AN$row",$totalAllItemStockAkhir[3]);                            
                $sheet->setCellValueExplicit("AO$row",$this->toRupiah($totalAllHargaStockAkhir[3]),PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue("AP$row",$totalAllItemStockAkhir[4]);    
                $sheet->setCellValueExplicit("AQ$row",$this->toRupiah($totalAllHargaStockAkhir[4]),PHPExcel_Cell_DataType::TYPE_STRING);
                
                $jumlah_all=$totalAllItemStockAkhir[1]+$totalAllItemStockAkhir[2]+$totalAllItemStockAkhir[3]+$totalAllItemStockAkhir[4];
                $jumlah_rp=$totalAllHargaStockAkhir[1]+$totalAllHargaStockAkhir[2]+$totalAllHargaStockAkhir[3]+$totalAllHargaStockAkhir[4];
                
                $sheet->setCellValue("AR$row",$jumlah_all);                
                $sheet->setCellValueExplicit("AS$row",$this->toRupiah($jumlah_rp),PHPExcel_Cell_DataType::TYPE_STRING);                                                                
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:AT$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:AT$row")->getAlignment()->setWrapText(true);
                $row+=1;                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                                );																					 
                $sheet->getStyle("F$row")->applyFromArray($styleArray);                                 
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);                
                              
                $row+=2;
                $row_awal=$row;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",'Tanjungpinang, '.$this->tgl->tanggal('d F Y'));                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",'Kepala UPTD Instalasi Gudang Farmasi');                                                                                 
                $row+=4;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nama_ka_gudang']);                
                $row+=1;
                $sheet->mergeCells("X$row:AD$row");
                $sheet->setCellValue("X$row",$this->dataReport['nip_ka_gudang']);                

                $styleArray=array(								
                                    'font' => array('bold' => true),
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));																					 
                $sheet->getStyle("X$row_awal:AD$row")->applyFromArray($styleArray);
                $sheet->getStyle("X$row_awal:AD$row")->getAlignment()->setWrapText(true);
                
                $this->printOut('laporanmutasiobatsemester');
            break;
        }        
        $this->setLink($this->dataReport['linkoutput'],"Laporan Mutasi Obat Semester $semester");
    }
    /**
     * digunakan untuk perpetual stock
     */
    public function printPerpetualStock () {        
        $tahun=$this->dataReport['tahun'];
        $idobat=$this->dataReport['idobat'];
        $nama_obat=$this->dataReport['nama_obat'];
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('K');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                    
                
                $row_awal=$row;
                $sheet->mergeCells("A$row:K$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row","KARTU PERSEDIAAN TAHUN $tahun");                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $row+=1;
                $sheet->mergeCells("A$row:K$row");
                $sheet->setCellValue("A$row",$nama_obat);                                
                $sheet->getRowDimension($row)->setRowHeight(20);                                                 
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(18);   
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(18);   
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(18);
                
                $sheet->getRowDimension($row)->setRowHeight(23);                
                $row_awal=$row;
                $row_akhir=$row+1;
                $sheet->mergeCells("A$row:A$row_akhir");
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:B$row_akhir");
                $sheet->setCellValue("B$row",'TANGGAL');                
                $sheet->mergeCells("C$row:C$row_akhir");
                $sheet->setCellValue("C$row",'URAIAN');                
                $sheet->mergeCells("D$row:D$row_akhir");
                $sheet->setCellValue("D$row",'HARGA');    
                $sheet->mergeCells("E$row:E$row_akhir");
                $sheet->setCellValue("E$row",'SATUAN');    
                $sheet->mergeCells("F$row:G$row");
                $sheet->setCellValue("F$row",'PEMBELIAN');
                $sheet->mergeCells("H$row:I$row");
                $sheet->setCellValue("H$row",'PENGELUARAN');
                $sheet->mergeCells("J$row:K$row");
                $sheet->setCellValue("J$row",'SALDO AKHIR');
                $row+=1;                
                $sheet->setCellValue("F$row",'KUANTITAS');    
                $sheet->setCellValue("G$row",'JUMLAH');    
                $sheet->setCellValue("H$row",'KUANTITAS');    
                $sheet->setCellValue("I$row",'JUMLAH');    
                $sheet->setCellValue("J$row",'KUANTITAS');    
                $sheet->setCellValue("K$row",'JUMLAH');                  
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:K$row")->getAlignment()->setWrapText(true);
                
                $str = "SELECT ks.idobat,ks.tanggal,ks.qty,dsb.idsatuan_obat AS pembelian_idsatuan,dsb.harga AS pembelian_harga,dsb.harga*ks.qty AS pembelian_jumlah,dsk.idsatuan_obat AS pengeluaran_idsatuan,dsk.harga AS pengeluaran_harga,dsk.harga*ks.qty AS pengeluaran_jumlah,ks.sisa_stock,ks.mode,ks.keterangan FROM log_ks ks LEFT JOIN detail_sbbm dsb ON (ks.iddetail_sbbm=dsb.iddetail_sbbm) LEFT JOIN detail_sbbk dsk ON (ks.iddetail_sbbk=dsk.iddetail_sbbk) WHERE ks.idobat=$idobat AND ks.tahun=$tahun ORDER BY ks.date_added ASC";
                $this->db->setFieldTable(array('idobat','tanggal','qty','pembelian_idsatuan','pembelian_harga','pembelian_jumlah','pengeluaran_idsatuan','pengeluaran_harga','pengeluaran_jumlah','sisa_stock','mode','keterangan'));
                $r=$this->db->getRecord($str);          
                $data=array();
                $row+=1;
                $row_awal=$row;
                while (list($k,$v)=each($r)) {                    
                    $sheet->setCellValue("A$row",$v['no']);    
                    $sheet->setCellValue("B$row",$this->tgl->tanggal('d/m/Y',$v['tanggal']));                        
                    $sheet->setCellValue("C$row",$v['keterangan']);                                  
                    if ($v['mode']=='masuk') {                              
                        $sheet->setCellValueExplicit("D$row",$this->toRupiah($v['pembelian_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $nama_satuan=$this->DMaster->getNamaSatuanObat($v['pembelian_idsatuan']);
                        $sheet->setCellValue("E$row",$nama_satuan);                                    
                        $sheet->setCellValue("F$row",$v['qty']);                         
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($v['pembelian_jumlah']),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $sheet->setCellValue("H$row",'-');    
                        $sheet->setCellValue("I$row",'-');                            
                        $sheet->setCellValue("J$row",$v['sisa_stock']);    
                        $jumlah=$v['pembelian_harga']*$v['sisa_stock'];
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($jumlah),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        
                    }else {
                        $sheet->setCellValueExplicit("D$row",$this->toRupiah($v['pengeluaran_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $nama_satuan=$this->DMaster->getNamaSatuanObat($v['pengeluaran_idsatuan']);
                        $sheet->setCellValue("E$row",$nama_satuan);            
                        $sheet->setCellValue("F$row",'-');    
                        $sheet->setCellValue("G$row",'-');                            
                        $sheet->setCellValue("H$row",$v['qty']);                         
                        $sheet->setCellValueExplicit("I$row",$this->toRupiah($v['pengeluaran_jumlah']),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("J$row",$v['sisa_stock']);    
                        $jumlah=$v['pengeluaran_harga']*$v['sisa_stock'];
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($jumlah),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    }    
                    $sheet->getRowDimension($row)->setRowHeight(21);                
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:K$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("C$row_awal:C$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("G$row_awal:G$row")->applyFromArray($styleArray);                
                $sheet->getStyle("I$row_awal:I$row")->applyFromArray($styleArray);                
                $sheet->getStyle("K$row_awal:K$row")->applyFromArray($styleArray);                
                
                
                $this->printOut('laporanperpetualobat');                
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],'Laporan Perpetual Stock');
    }
    /**
     * digunakan untuk perpetual stock
     */
    public function printPerpetualStockPuskesmas () {        
        $tahun=$this->dataReport['tahun'];
        $idobat_puskesmas=$this->dataReport['idobat_puskesmas'];
        $nama_obat=$this->dataReport['nama_obat'];
        $nama_puskesmas=$this->dataReport['nama_puskesmas'];
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('K');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                    
                
                $row_awal=$row;
                $sheet->mergeCells("A$row:K$row");
                $sheet->getRowDimension($row)->setRowHeight(20);
                $sheet->setCellValue("A$row","KARTU PERSEDIAAN PUSKESMAS $nama_puskesmas TAHUN $tahun");                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                $row+=1;
                $sheet->mergeCells("A$row:K$row");
                $sheet->setCellValue("A$row",$nama_obat);                                
                $sheet->getRowDimension($row)->setRowHeight(20);                                                 
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(50);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(18);   
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(18);   
                $sheet->getColumnDimension('J')->setWidth(15);
                $sheet->getColumnDimension('K')->setWidth(18);
                
                $sheet->getRowDimension($row)->setRowHeight(23);                
                $row_awal=$row;
                $row_akhir=$row+1;
                $sheet->mergeCells("A$row:A$row_akhir");
                $sheet->setCellValue("A$row",'NO');                
                $sheet->mergeCells("B$row:B$row_akhir");
                $sheet->setCellValue("B$row",'TANGGAL');                
                $sheet->mergeCells("C$row:C$row_akhir");
                $sheet->setCellValue("C$row",'URAIAN');                
                $sheet->mergeCells("D$row:D$row_akhir");
                $sheet->setCellValue("D$row",'HARGA');    
                $sheet->mergeCells("E$row:E$row_akhir");
                $sheet->setCellValue("E$row",'SATUAN');    
                $sheet->mergeCells("F$row:G$row");
                $sheet->setCellValue("F$row",'PEMBELIAN');
                $sheet->mergeCells("H$row:I$row");
                $sheet->setCellValue("H$row",'PENGELUARAN');
                $sheet->mergeCells("J$row:K$row");
                $sheet->setCellValue("J$row",'SALDO AKHIR');
                $row+=1;                
                $sheet->setCellValue("F$row",'KUANTITAS');    
                $sheet->setCellValue("G$row",'JUMLAH');    
                $sheet->setCellValue("H$row",'KUANTITAS');    
                $sheet->setCellValue("I$row",'JUMLAH');    
                $sheet->setCellValue("J$row",'KUANTITAS');    
                $sheet->setCellValue("K$row",'JUMLAH');                  
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:K$row")->getAlignment()->setWrapText(true);
                                
                $str = "SELECT lksp.idobat_puskesmas,lksp.tanggal_puskesmas,lksp.qty_puskesmas,dsb.idsatuan_obat AS pembelian_idsatuan,dsb.harga AS pembelian_harga,dsb.harga*lksp.qty_puskesmas AS pembelian_jumlah,dsk.idsatuan_obat AS pengeluaran_idsatuan,dsk.harga AS pengeluaran_harga,dsk.harga*lksp.qty_puskesmas AS pengeluaran_jumlah,lksp.sisa_stock_puskesmas,lksp.mode_puskesmas,lksp.keterangan_puskesmas FROM log_ks_puskesmas lksp LEFT JOIN detail_sbbm_puskesmas dsb ON (lksp.iddetail_sbbm_puskesmas=dsb.iddetail_sbbm_puskesmas) LEFT JOIN detail_sbbk_puskesmas dsk ON (lksp.iddetail_sbbk_puskesmas=dsk.iddetail_sbbk_puskesmas) WHERE lksp.idobat_puskesmas=$idobat_puskesmas AND lksp.tahun_puskesmas=$tahun ORDER BY lksp.date_added ASC";
                $this->db->setFieldTable(array('idobat_puskesmas','tanggal_puskesmas','qty_puskesmas','pembelian_idsatuan','pembelian_harga','pembelian_jumlah','pengeluaran_idsatuan','pengeluaran_harga','pengeluaran_jumlah','sisa_stock_puskesmas','mode_puskesmas','keterangan_puskesmas'));
                $r=$this->db->getRecord($str);          
                $data=array();
                $row+=1;
                $row_awal=$row;
                while (list($k,$v)=each($r)) {                    
                    $sheet->setCellValue("A$row",$v['no']);    
                    $sheet->setCellValue("B$row",$this->tgl->tanggal('d/m/Y',$v['tanggal_puskesmas']));                        
                    $sheet->setCellValue("C$row",$v['keterangan_puskesmas']);                                  
                    if ($v['mode_puskesmas']=='masuk') {                              
                        $sheet->setCellValueExplicit("D$row",$this->toRupiah($v['pembelian_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $nama_satuan=$this->DMaster->getNamaSatuanObat($v['pembelian_idsatuan']);
                        $sheet->setCellValue("E$row",$nama_satuan);                                    
                        $sheet->setCellValue("F$row",$v['qty_puskesmas']);                         
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($v['pembelian_jumlah']),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $sheet->setCellValue("H$row",'-');    
                        $sheet->setCellValue("I$row",'-');                            
                        $sheet->setCellValue("J$row",$v['sisa_stock_puskesmas']);    
                        $jumlah=$v['pembelian_harga']*$v['sisa_stock_puskesmas'];
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($jumlah),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        
                    }else {
                        $sheet->setCellValueExplicit("D$row",$this->toRupiah($v['pengeluaran_harga']),PHPExcel_Cell_DataType::TYPE_STRING);
                        $nama_satuan=$this->DMaster->getNamaSatuanObat($v['pengeluaran_idsatuan']);
                        $sheet->setCellValue("E$row",$nama_satuan);            
                        $sheet->setCellValue("F$row",'-');    
                        $sheet->setCellValue("G$row",'-');                            
                        $sheet->setCellValue("H$row",$v['qty_puskesmas']);                         
                        $sheet->setCellValueExplicit("I$row",$this->toRupiah($v['pengeluaran_jumlah']),PHPExcel_Cell_DataType::TYPE_STRING);                                                
                        $sheet->setCellValue("J$row",$v['sisa_stock_puskesmas']);    
                        $jumlah=$v['pengeluaran_harga']*$v['sisa_stock_puskesmas'];
                        $sheet->setCellValueExplicit("K$row",$this->toRupiah($jumlah),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    }    
                    $sheet->getRowDimension($row)->setRowHeight(21);                
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:K$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("C$row_awal:C$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("G$row_awal:G$row")->applyFromArray($styleArray);                
                $sheet->getStyle("I$row_awal:I$row")->applyFromArray($styleArray);                
                $sheet->getStyle("K$row_awal:K$row")->applyFromArray($styleArray);                
                
                
                $this->printOut('laporanperpetualobat');                
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],'Laporan Perpetual Stock');
    }
    /**
     * digunakan untuk dinamika logistik obat
     */
    public function printDinamikaLogistikObat () {        
        $tahun=$this->dataReport['tahun'];        
        $tahun_sebelumnya=$this->dataReport['tahunsebelumnya'];        
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('I');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $sheet->mergeCells("A$row:I$row");                
                $sheet->setCellValue("A$row","LAPORAN DINAMIKA LOGISTIK OBAT TAHUN $tahun");                                
                $sheet->getRowDimension($row)->setRowHeight(20);                                                 
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(12);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(18);   
                $sheet->getColumnDimension('H')->setWidth(18);
                $sheet->getColumnDimension('I')->setWidth(18);                                 
                $sheet->getRowDimension($row)->setRowHeight(23);                                
                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'NAMA OBAT');                                                                
                $sheet->setCellValue("D$row",'SATUAN');    
                $sheet->setCellValue("E$row",'KEMASAN');    
                $sheet->setCellValue("F$row",'STOCK AWAL TAHUN');    
                $sheet->setCellValue("G$row",'PENERIMAAN');    
                $sheet->setCellValue("H$row",'PENGELUARAN');    
                $sheet->setCellValue("I$row",'SISA STOCK');                  
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:I$row")->getAlignment()->setWrapText(true);
                
                $str = "SELECT mo.idobat,mo.nama_obat,mo.idsatuan_obat,mo.kemasan,COALESCE(penerimaan_jumlah_tahun_lalu-pengeluaran_jumlah_tahun_lalu,0) AS penerimaan_jumlah_tahun_lalu,COALESCE(penerimaan_jumlah,0) penerimaan_jumlah,COALESCE(pengeluaran_jumlah,0) pengeluaran_jumlah FROM master_obat mo LEFT JOIN (SELECT idobat,SUM(qty) AS penerimaan_jumlah_tahun_lalu FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND msb.tahun=$tahun_sebelumnya GROUP BY dsb.idobat) penerimaan_tahun_lalu ON (penerimaan_tahun_lalu.idobat=mo.idobat) LEFT JOIN (SELECT idobat,SUM(pemberian) AS pengeluaran_jumlah_tahun_lalu FROM master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND msk.tahun=$tahun_sebelumnya GROUP BY idobat) pengeluaran_tahun_lalu ON (pengeluaran_tahun_lalu.idobat=mo.idobat) LEFT JOIN (SELECT idobat,SUM(qty) AS penerimaan_jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE msb.idsbbm=dsb.idsbbm AND msb.tahun=$tahun GROUP BY dsb.idobat) penerimaan ON (penerimaan.idobat=mo.idobat) LEFT JOIN (SELECT idobat,SUM(pemberian) AS pengeluaran_jumlah FROM master_sbbk msk,detail_sbbk dsk WHERE msk.idsbbk=dsk.idsbbk AND msk.tahun=$tahun GROUP BY idobat) pengeluaran ON (pengeluaran.idobat=mo.idobat) ORDER BY mo.nama_obat ASC";
                $this->db->setFieldTable(array('idobat','nama_obat','idsatuan_obat','kemasan','penerimaan_jumlah_tahun_lalu','penerimaan_jumlah','pengeluaran_jumlah'));
                $r=$this->db->getRecord($str);                
                $row_awal=$row;
                $row+=1;                
                while (list($k,$v)=each($r)) {
                    $sheet->setCellValue("A$row",$v['no']);                                                                
                    $sheet->mergeCells("B$row:C$row");
                    $sheet->setCellValue("B$row",$v['nama_obat']);                                                                
                    $sheet->setCellValue("D$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));    
                    $sheet->setCellValue("E$row",$v['kemasan']);    
                    $sheet->setCellValue("F$row",$v['penerimaan_jumlah_tahun_lalu']);    
                    $sheet->setCellValue("G$row",$v['penerimaan_jumlah']);    
                    $sheet->setCellValue("H$row",$v['pengeluaran_jumlah']); 
                    $sisa_stock=($v['penerimaan_jumlah_tahun_lalu']+$v['penerimaan_jumlah'])-$v['pengeluaran_jumlah'];
                    $sheet->setCellValue("I$row",$sisa_stock);                                                     
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:I$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:I$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                
                $row+=2;
                $row_awal=$row;
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",'Kepala UPTD Instalasi Farmasi');                                                                                 
                $row+=1;
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",'Kepala Kabupaten Bintan');                                                                                 
                $row+=4;
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",$this->dataReport['nama_ka_gudang']);                
                $row+=1;
                $sheet->mergeCells("E$row:G$row");
                $sheet->setCellValue("E$row",$this->dataReport['nip_ka_gudang']);
                
                $styleArray=array(								
                                    'font' => array('bold' => true),
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER));																					 
                $sheet->getStyle("E$row_awal:E$row")->applyFromArray($styleArray);
                
                $this->printOut('laporandinamikalogistikobat');
                
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Dinamika Logistik Obat Tahun $tahun");
    }    
    /**
     * digunakan untuk cetak obat expire
     */
    public function printExpireObat () {        
        $idprogram=$this->dataReport['idprogram'];        
        $nama_program=$this->dataReport['nama_program'];        
        $waktuexpires=$this->dataReport['waktuexpires'];
        $modeexpires=$this->dataReport['modeexpires'];        
        switch($modeexpires) {
            case 'harikedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires DAY)";
                $keterangan="$waktuexpires Hari Ke Depan";
            break;
            case 'minggukedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires WEEK)";
                $keterangan="$waktuexpires Minggu Ke Depan";
            break;
            case 'bulankedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires MONTH)";
                $keterangan="$waktuexpires Bulan Ke Depan";
            break;   
            case 'harikebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires DAY)";
                $keterangan="$waktuexpires Hari Ke Belakang";
            break;
            case 'minggukebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires WEEK)";
                $keterangan="$waktuexpires Minggu Ke Belakang";
            break;
            case 'bulankebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires MONTH)";
                $keterangan="$waktuexpires Bulan Ke Belakang";
            break;   
        }
        $str = "SELECT iddetail_sbbm,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete'$str_mode_expires GROUP BY idobat,harga,tanggal_expire ORDER BY tanggal_expire ASC,nama_obat ASC";        
        $this->db->setFieldTable(array('iddetail_sbbm','nama_obat','harga','idsatuan_obat','kemasan','tanggal_expire','idprogram'));
        $str = $idprogram == 'none' ?$str:" $str AND dsb.idprogram=$idprogram";            
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('J');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $sheet->mergeCells("A$row:J$row");                
                $sheet->setCellValue("A$row","LAPORAN KADALUWARSA OBAT$nama_program");                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:J$row")->applyFromArray($styleArray);
                $row+=2;
                $sheet->setCellValue("A$row",'BATAS KADALUWARSA');
                $sheet->setCellValue("C$row",": $keterangan");
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(12);                
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(18);   
                $sheet->getColumnDimension('H')->setWidth(18);
                $sheet->getColumnDimension('I')->setWidth(18);                                 
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getRowDimension($row)->setRowHeight(23);                                
                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'NAMA OBAT');                                                                
                $sheet->setCellValue("D$row",'HARGA');    
                $sheet->setCellValue("E$row",'SATUAN');    
                $sheet->setCellValue("F$row",'KEMASAN');    
                $sheet->setCellValue("G$row",'PROGRAM');    
                $sheet->setCellValue("H$row",'TANGGAL KADALUWARSA');    
                $sheet->setCellValue("I$row",'VOLUME');    
                $sheet->setCellValue("J$row",'SUB TOTAL');                  
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:J$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:J$row")->getAlignment()->setWrapText(true);
                
                $r=$this->db->getRecord($str);                
                $row_awal=$row;
                $row+=1;                
                while (list($k,$v)=each($r)) {
                    $sheet->setCellValue("A$row",$v['no']);                                                                
                    $sheet->mergeCells("B$row:C$row");
                    $sheet->setCellValue("B$row",$v['nama_obat']);                                                                
                    $harga=$v['harga'];                                                                                             
                    $sheet->setCellValueExplicit("D$row",$this->toRupiah($harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    $sheet->setCellValue("E$row",'SATUAN');    
                    $sheet->setCellValue("F$row",'KEMASAN');                        
                    $sheet->setCellValue("G$row",$this->DMaster->getNamaProgramByID($v['idprogram']));    
                    $sheet->setCellValue("H$row",$this->tgl->tanggal('d/m/Y',$v['tanggal_expire']));    
                    $iddetail_sbbm=$v['iddetail_sbbm'];
                    $volume=$this->db->getCountRowsOfTable ("kartu_stock WHERE iddetail_sbbm=$iddetail_sbbm AND mode='masuk' AND isdestroyed=0",'iddetail_sbbm');		
                    $sheet->setCellValue("I$row",$volume);    
                    $subtotal=$volume*$harga;
                    $sheet->setCellValueExplicit("J$row",$this->toRupiah($subtotal),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:J$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:J$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                
                $sheet->getStyle("J$row_awal:J$row")->applyFromArray($styleArray);                
                $this->printOut('laporanexpireobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Expire Obat$nama_program");
    }
    /**
     * digunakan untuk cetak obat expire puskesmas
     */
    public function printExpireObatPuskesmas ($idpuskesmas) {        
        $idprogram=$this->dataReport['idprogram'];        
        $nama_program=$this->dataReport['nama_program'];        
        $waktuexpires=$this->dataReport['waktuexpires'];
        $modeexpires=$this->dataReport['modeexpires'];
        switch($modeexpires) {
            case 'harikedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires DAY)";
                $keterangan="$waktuexpires Hari Ke Depan";
            break;
            case 'minggukedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires WEEK)";
                $keterangan="$waktuexpires Minggu Ke Depan";
            break;
            case 'bulankedepan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires MONTH)";
                $keterangan="$waktuexpires Bulan Ke Depan";
            break;   
            case 'harikebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires DAY)";
                $keterangan="$waktuexpires Hari Ke Belakang";
            break;
            case 'minggukebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires WEEK)";
                $keterangan="$waktuexpires Minggu Ke Belakang";
            break;
            case 'bulankebelakang' :                
                $str_mode_expires = " AND YEAR(tanggal_expire) = YEAR(CURRENT_DATE - INTERVAL $waktuexpires MONTH) AND MONTH(tanggal_expire) = MONTH(CURRENT_DATE - INTERVAL $waktuexpires MONTH)";
                $keterangan="$waktuexpires Bulan Ke Belakang";
            break;   
        }
        switch($modeexpires) {
            case 'hari' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires DAY)";
            break;
            case 'minggu' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires WEEK)";
            break;
            case 'bulan' :                
                $str_mode_expires = " AND tanggal_expire>=DATE(NOW()) AND tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL $waktuexpires MONTH)";
            break;            
        }
        $str = "SELECT iddetail_sbbm_puskesmas,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram_gudang FROM master_sbbm_puskesmas msb,detail_sbbm_puskesmas dsb WHERE dsb.idsbbm_puskesmas=msb.idsbbm_puskesmas AND msb.idpuskesmas=$idpuskesmas AND status_puskesmas='complete'$str_mode_expires GROUP BY idobat_puskesmas,harga,tanggal_expire ORDER BY tanggal_expire ASC,nama_obat ASC";        
        $this->db->setFieldTable(array('iddetail_sbbm_puskesmas','nama_obat','harga','idsatuan_obat','kemasan','tanggal_expire','idprogram_gudang'));
        $str = $idprogram == 'none' ?$str:" $str AND dsb.idprogram_gudang=$idprogram";            
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('J');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $sheet->mergeCells("A$row:J$row");                
                $sheet->setCellValue("A$row","LAPORAN KADALUWARSA OBAT$nama_program");                
                $sheet->getRowDimension($row)->setRowHeight(20);                                
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:J$row")->applyFromArray($styleArray);
                $row+=2;
                $sheet->setCellValue("A$row",'BATAS KADALUWARSA');
                $sheet->setCellValue("C$row",": $keterangan");
                $row+=1;
                $sheet->setCellValue("A$row",'PUSKESMAS');
                $sheet->setCellValue("C$row",": ".$this->dataReport['nama_puskesmas']);
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(12);                
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(18);   
                $sheet->getColumnDimension('H')->setWidth(18);
                $sheet->getColumnDimension('I')->setWidth(18);                                 
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getRowDimension($row)->setRowHeight(23);                                
                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'NAMA OBAT');                                                                
                $sheet->setCellValue("D$row",'HARGA');    
                $sheet->setCellValue("E$row",'SATUAN');    
                $sheet->setCellValue("F$row",'KEMASAN');    
                $sheet->setCellValue("G$row",'PROGRAM');    
                $sheet->setCellValue("H$row",'TANGGAL KADALUWARSA');    
                $sheet->setCellValue("I$row",'VOLUME');    
                $sheet->setCellValue("J$row",'SUB TOTAL');                  
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:J$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:J$row")->getAlignment()->setWrapText(true);
                
                $r=$this->db->getRecord($str);                
                $row_awal=$row;
                $row+=1;                
                while (list($k,$v)=each($r)) {
                    $sheet->setCellValue("A$row",$v['no']);                                                                
                    $sheet->mergeCells("B$row:C$row");
                    $sheet->setCellValue("B$row",$v['nama_obat']);                                                                
                    $harga=$v['harga'];                                                                                             
                    $sheet->setCellValueExplicit("D$row",$this->toRupiah($harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    $sheet->setCellValue("E$row",'SATUAN');    
                    $sheet->setCellValue("F$row",'KEMASAN');                        
                    $sheet->setCellValue("G$row",$this->DMaster->getNamaProgramByID($v['idprogram']));    
                    $sheet->setCellValue("H$row",$this->tgl->tanggal('d/m/Y',$v['tanggal_expire']));    
                    $iddetail_sbbm_puskesmas=$v['iddetail_sbbm_puskesmas'];
                    $volume=$this->db->getCountRowsOfTable ("kartu_stock_puskesmas WHERE iddetail_sbbm_puskesmas=$iddetail_sbbm_puskesmas AND mode_puskesmas='masuk' AND isdestroyed=0",'iddetail_sbbm_puskesmas');		
                    $sheet->setCellValue("I$row",$volume);    
                    $subtotal=$volume*$harga;
                    $sheet->setCellValueExplicit("J$row",$this->toRupiah($subtotal),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    $sheet->getRowDimension($row)->setRowHeight(21);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:J$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:J$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("B$row_awal:B$row")->applyFromArray($styleArray);                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                
                $sheet->getStyle("J$row_awal:J$row")->applyFromArray($styleArray);                
                $this->printOut('laporanexpireobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Expire Obat$nama_program");
    }
    /**
     * digunakan untuk cetak rekapitulasi distribusi obat
     */
    public function printRekapitulasiDistribusiObat () {        
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('I');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $sheet->mergeCells("A$row:J$row");                
                $sheet->setCellValue("A$row","LAPORAN REKAPITULASI DISTRIBUSI OBAT KE PUSKESMAS");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
                $row+=2;
                $sheet->setCellValue("A$row",'BULAN');
                $bulantahun=$this->tgl->tanggal('F Y',$this->dataReport['bulantahun']);
                $sheet->setCellValue("C$row",": $bulantahun");
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(7);                
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);                
                $sheet->getRowDimension($row)->setRowHeight(23);                                
                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'KODE');                                                                
                $sheet->setCellValue("D$row",'NAMA');                    
                $sheet->setCellValue("E$row",'JUMLAH KELUAR');    
                $sheet->setCellValue("F$row",'JUMLAH RUPIAH');                    
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:F$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:F$row")->getAlignment()->setWrapText(true);
                
                $listpuskesmas=$this->DMaster->getListPuskesmas ();   
                $bulan_sekarang=$this->tgl->tanggal('Y-m',$this->dataReport['bulantahun']);                          
                $i=1;
                $row+=1;
                $row_awal=$row;
                $total_qty=0;
                $total_harga=0;
                foreach($listpuskesmas as $k=>$v) {
                    if ($k!='none') {
                        $sheet->setCellValue("A$row",$i);                                                                
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$k);                                                                
                        $sheet->setCellValue("D$row",$v);    
                        
                        $str = "SELECT COUNT(ks.idkartu_stock) AS jumlah_qty,SUM(dsb.harga) AS jumlah_rupiah FROM kartu_stock ks, master_sbbk msk,detail_sbbk dsb WHERE ks.idsbbk=msk.idsbbk AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND msk.idpuskesmas=$k AND msk.status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')='$bulan_sekarang' AND ks.isdestroyed=0";                
                        $this->db->setFieldTable(array('jumlah_qty','jumlah_rupiah'));
                        $r=$this->db->getRecord($str);
                        $pengeluaran=$r[1]['jumlah_qty']>0 ? $r[1]['jumlah_qty']:0;                
                        $total_qty+=$pengeluaran;
                        $sheet->setCellValue("E$row",$pengeluaran); 
                        $jumlah_rupiah=$r[1]['jumlah_rupiah']>0 ? $r[1]['jumlah_rupiah']:0;
                        $total_harga+=$jumlah_rupiah;                        
                        $sheet->setCellValueExplicit("F$row",$this->toRupiah($jumlah_rupiah),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $i+=1;
                        $row+=1;
                    }
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:F$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:F$row")->getAlignment()->setWrapText(true);
                $row+=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                                
                $sheet->setCellValue("D$row",'TOTAL');    
                $sheet->setCellValue("E$row",$total_qty);                    
                $sheet->setCellValueExplicit("F$row",$this->toRupiah($total_harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("F$row_awal:F$row")->applyFromArray($styleArray);                
                $this->printOut('laporanrekapitulasidistribusiobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Rekapitulasi Distribusi Obat");
    }
    /**
     * digunakan untuk cetak detail distribusi obat
     */
    public function printDetailDistribusiObat () {   
        $bulan_sekarang=$this->tgl->tanggal('Y-m',$this->dataReport['bulantahun']);                
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('I');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $sheet->mergeCells("A$row:J$row");                
                $sheet->setCellValue("A$row","LAPORAN DETAIL DISTRIBUSI OBAT KE PUSKESMAS");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
                $row+=2;
                $sheet->setCellValue("A$row",'BULAN');
                $bulantahun=$this->tgl->tanggal('F Y',$this->dataReport['bulantahun']);
                $sheet->setCellValue("C$row",": $bulantahun");
                $row+=1;
                $sheet->setCellValue("A$row",'PUSKESMAS');                
                $sheet->setCellValue("C$row",': '.$this->dataReport['nama_puskesmas']);
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(7);                
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);                
                $sheet->getColumnDimension('G')->setWidth(15);                
                $sheet->getColumnDimension('H')->setWidth(15);                
                $sheet->getRowDimension($row)->setRowHeight(23);                                
                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'KODE');                                                                
                $sheet->setCellValue("D$row",'NAMA');                    
                $sheet->setCellValue("E$row",'SATUAN');
                $sheet->setCellValue("F$row",'HARGA');
                $sheet->setCellValue("G$row",'JUMLAH KELUAR');    
                $sheet->setCellValue("H$row",'JUMLAH RUPIAH');                    
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:H$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:H$row")->getAlignment()->setWrapText(true);                
                
                $row+=1;
                $row_awal=$row;
                $total_qty=0;
                $total_harga=0;
                $idpuskesmas=$this->dataReport['idpuskesmas'];                                
                $str = "SELECT dsb.iddetail_sbbk,mop.idobat_puskesmas,dsb.kode_obat,mop.kode_obat AS mst_kode_obat,dsb.nama_obat,mop.nama_obat AS mst_nama_obat,mop.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mop.harga AS mst_harga,dsb.harga FROM master_obat_puskesmas mop LEFT JOIN (SELECT iddetail_sbbk,kode_obat,nama_obat,idobat_puskesmas,idsatuan_obat,harga FROM master_sbbk msk,detail_sbbk dsb1 WHERE msk.idsbbk=dsb1.idsbbk AND msk.status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')='$bulan_sekarang') AS dsb ON (mop.idobat_puskesmas=dsb.idobat_puskesmas) WHERE mop.idpuskesmas=$idpuskesmas GROUP BY mop.idobat_puskesmas,dsb.harga ORDER BY mop.nama_obat ASC";
                $this->db->setFieldTable(array('iddetail_sbbk','idobat_puskesmas','kode_obat','mst_kode_obat','nama_obat','mst_nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_harga','harga'));
                $r=$this->db->getRecord($str);                                 
                while (list($k,$v)=each($r)) {                             
                    $iddetail_sbbk=$v['iddetail_sbbk'];
                    $sheet->setCellValue("A$row",$v['no']);                                                                
                    if ($iddetail_sbbk=='') {                        
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['mst_kode_obat']);                                                                
                        $sheet->setCellValue("D$row",$v['mst_nama_obat']);
                        $sheet->setCellValue("E$row",$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']));                        
                        $sheet->setCellValueExplicit("F$row",$this->toRupiah($v['mst_harga']),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $sheet->setCellValue("G$row",0);    
                        $sheet->setCellValue("H$row",0);                                                                    
                    }else {                        
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['kode_obat']);
                        $sheet->setCellValue("D$row",$v['nama_obat']);
                        $sheet->setCellValue("E$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));                                                
                        $sheet->setCellValueExplicit("F$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $str = "SELECT COUNT(ks.idkartu_stock) AS jumlah_qty,SUM(dsb.harga) AS jumlah_rupiah FROM detail_sbbk dsb,kartu_stock ks WHERE ks.iddetail_sbbk=dsb.iddetail_sbbk AND ks.iddetail_sbbk=$iddetail_sbbk AND ks.isdestroyed=0";
                        $this->db->setFieldTable(array('jumlah_qty','jumlah_rupiah'));
                        $r1=$this->db->getRecord($str);
                        $pengeluaran=$r1[1]['jumlah_qty']>0 ? $r1[1]['jumlah_qty']:0;                
                        $jumlah_rupiah=$r1[1]['jumlah_rupiah']>0 ? $r1[1]['jumlah_rupiah']:0;
                        $sheet->setCellValue("G$row",$pengeluaran);                            
                        $sheet->setCellValueExplicit("H$row",$this->toRupiah($jumlah_rupiah),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    }
                    $sheet->getRowDimension($row)->setRowHeight(23);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:H$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:H$row")->getAlignment()->setWrapText(true);
                $row+=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                                
//                $sheet->setCellValue("D$row",'TOTAL');    
//                $sheet->setCellValue("E$row",$total_qty);                    
//                $sheet->setCellValueExplicit("F$row",$this->toRupiah($total_harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("F$row_awal:F$row")->applyFromArray($styleArray);                
                $sheet->getStyle("H$row_awal:H$row")->applyFromArray($styleArray);
                $this->printOut('laporandetaildistribusiobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Detail Distribusi Obat");
    }
    /**
     * digunakan untuk cetak rekapitulasi distribusi obat
     */
    public function printRekapitulasiStockObatPuskesmas () {        
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('I');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $sheet->mergeCells("A$row:J$row");    
                $bulantahun=$this->tgl->tanggal('F Y',$this->dataReport['bulantahun']);
                $sheet->setCellValue("A$row","LAPORAN REKAPITULASI STOCK OBAT PUSKESMAS s.d BULAN $bulantahun");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);                
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(7);                
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(25);   
                $sheet->getColumnDimension('F')->setWidth(20);                
                $sheet->getColumnDimension('G')->setWidth(20);                
                $sheet->getColumnDimension('H')->setWidth(20);                
                $sheet->getColumnDimension('I')->setWidth(20);                
                $sheet->getRowDimension($row)->setRowHeight(23);                                
                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'KODE');                                                                
                $sheet->setCellValue("D$row",'NAMA');                    
                $sheet->setCellValue("E$row",'DISTRIBUSI OBAT BERDASARKAN SBBK');    
                $sheet->setCellValue("F$row",'TOTAL PENERIMAAN');                    
                $sheet->setCellValue("G$row",'PEMAKAIAN');                    
                $sheet->setCellValue("H$row",'SISA OBAT');                    
                $sheet->setCellValue("I$row",'JUMLAH RUPIAH');                    
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:I$row")->getAlignment()->setWrapText(true);
                
                $listpuskesmas=$this->DMaster->getListPuskesmas ();   
                $bulan_sekarang=$this->tgl->tanggal('Y-m',$this->dataReport['bulantahun']);                          
                $i=1;
                $row+=1;
                $row_awal=$row;
                $total_qty=0;
                $total_harga=0;
                foreach($listpuskesmas as $k=>$v) {
                    if ($k!='none') {
                        $sheet->setCellValue("A$row",$i);                                                                
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$k);                                                                
                        $sheet->setCellValue("D$row",$v);                            
                        
                        $str = "SELECT COUNT(ks.idkartu_stock) AS jumlah_qty FROM kartu_stock ks, master_sbbk msk,detail_sbbk dsb WHERE ks.idsbbk=msk.idsbbk AND ks.iddetail_sbbk=dsb.iddetail_sbbk AND msk.idpuskesmas=$k AND msk.status='complete' AND DATE_FORMAT(msk.tanggal_sbbk,'%Y-%m')<='$bulan_sekarang'";                
                        $this->db->setFieldTable(array('jumlah_qty'));
                        $r=$this->db->getRecord($str);                
                        $distribusi_obat=$r[1]['jumlah_qty']>0 ? $r[1]['jumlah_qty']:0;                
                
                        $sheet->setCellValue("E$row",$distribusi_obat);    
                        
                        $str = "SELECT mode_puskesmas,COUNT(idkartu_stock_puskesmas) AS jumlah_qty FROM kartu_stock_puskesmas_dinas WHERE idpuskesmas=$k AND DATE_FORMAT(date_added,'%Y-%m')<='$bulan_sekarang' GROUP BY mode_puskesmas ORDER BY mode_puskesmas ASC";
                        $this->db->setFieldTable(array('mode_puskesmas','jumlah_qty'));
                        $r=$this->db->getRecord($str);            
                        $total_penerimaan=0;
                        $total_pemakaian=0;
                        foreach ($r as $m) {
                            if ($m['mode_puskesmas'] == 'keluar') {
                                $total_pemakaian+=$m['jumlah_qty'];
                            }
                            $total_penerimaan+=$m['jumlah_qty'];
                        }
                        $sisa_obat = $total_penerimaan - $total_pemakaian;                                             
                        
                        $sheet->setCellValue("F$row",$total_penerimaan);                    
                        $sheet->setCellValue("G$row",$total_pemakaian);                    
                        $sheet->setCellValue("H$row",$sisa_obat);   
                        $total_qty+=$sisa_obat;
                        $jumlah_rupiah=$this->db->getSumRowsOfTable('harga',"kartu_stock_puskesmas_dinas kspd,detail_sbbm_puskesmas dspp WHERE kspd.iddetail_sbbm_puskesmas=dspp.iddetail_sbbm_puskesmas AND kspd.mode_puskesmas='masuk' AND kspd.idpuskesmas=$k AND DATE_FORMAT(kspd.date_added,'%Y-%m')<='$bulan_sekarang'");                                                
                        $total_harga+=$jumlah_rupiah;
                        $sheet->setCellValueExplicit("I$row",$this->toRupiah($jumlah_rupiah),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $i+=1;
                        $row+=1;
                    }
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:I$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:I$row")->getAlignment()->setWrapText(true);
                $row+=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                                
                $sheet->setCellValue("G$row",'TOTAL');    
                $sheet->setCellValue("H$row",$total_qty);                    
                $sheet->setCellValueExplicit("I$row",$this->toRupiah($total_harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("I$row_awal:I$row")->applyFromArray($styleArray);                
                $this->printOut('laporanrekapitulasistockobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Rekapitulasi Stock Obat Puskesmas");
    }
    /**
     * digunakan untuk cetak usulan kebutuhan obat
     */
    public function printUsulanKebutuhanObat () { 
        $tahun=$this->dataReport['tahun'];
        $tahun_lalu=$tahun-1;
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('I');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $row_awal=$row;
                $sheet->mergeCells("A$row:K$row");                
                $sheet->setCellValue("A$row","USULAN KEBUTUHAN OBAT");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                $row+=1;
                $sheet->mergeCells("A$row:K$row");                
                $sheet->setCellValue("A$row","TAHUN ANGGARAN $tahun");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);                
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(7);                
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);                
                $sheet->getColumnDimension('G')->setWidth(18);                
                $sheet->getColumnDimension('H')->setWidth(20);                
                $sheet->getColumnDimension('I')->setWidth(18);                                
                $sheet->getColumnDimension('J')->setWidth(15);                
                $sheet->getRowDimension($row)->setRowHeight(33);                                
                
                                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'KODE');                                                                
                $sheet->setCellValue("D$row",'NAMA');                    
                $sheet->setCellValue("E$row",'SATUAN');
                $sheet->setCellValue("F$row",'HARGA');
                $sheet->setCellValue("G$row","SISA STOCK PER 31 DES $tahun_lalu");    
                $sheet->setCellValue("H$row","PEMAKAIAN RATA-RATA PER BULAN TAHUN $tahun_lalu");                    
                $sheet->setCellValue("I$row","USULAN KEBUTUHAN TAHUN $tahun");                    
                $sheet->setCellValue("J$row",'SUB TOTAL');                   
                $sheet->setCellValue("K$row",'KET');                   
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:K$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:K$row")->getAlignment()->setWrapText(true);                
                
                $row+=1;
                $row_awal=$row;
                $total_qty=0;
                $total_harga=0;
                $idpuskesmas=$this->dataReport['idpuskesmas'];                
                $str = 'SELECT idobat,kode_obat,nama_obat,idsatuan_obat,harga FROM master_obat ORDER BY nama_obat ASC';
                $this->db->setFieldTable(array('idobat','kode_obat','nama_obat','idsatuan_obat','harga'));
                $r=$this->db->getRecord($str);                 
                while (list($k,$v)=each($r)) {                    
                    $idobat=$v['idobat'];                    
                    $sheet->setCellValue("A$row",$v['no']);                                                                
                    $sheet->mergeCells("B$row:C$row");
                    $sheet->setCellValue("B$row",$v['kode_obat']);                                                                
                    $sheet->setCellValue("D$row",$v['nama_obat']);                    
                    $sheet->setCellValue("E$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));                    
                    $sheet->setCellValueExplicit("F$row",$this->toRupiah($v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    $str = "SELECT sisa_stock FROM log_ks ksp,(SELECT MAX(idlog) AS idlog FROM log_ks WHERE tahun=$tahun_lalu AND idobat=$idobat) AS ksp2 WHERE ksp.idlog=ksp2.idlog";            
                    $this->db->setFieldTable(array('sisa_stock'));
                    $r1=$this->db->getRecord($str);        
                    $sisa_stock=isset($r1[1]) ? $r1[1]['sisa_stock'] : 0;
                    $sheet->setCellValue("G$row",$sisa_stock);    
                    $str = "SELECT SUM(pemberian)/12 AS rata2 FROM master_sbbk msb, detail_sbbk dsb WHERE msb.idsbbk=dsb.idsbbk AND msb.status='complete' AND  msb.tahun=$tahun_lalu AND dsb.idobat=$idobat";
                    $this->db->setFieldTable(array('rata2'));
                    $r2=$this->db->getRecord($str);                    
                    $rata2=$r2[1]['rata2'] > 0 ? number_format($r2[1]['rata2'],2) : 0;
                    $sheet->setCellValue("H$row",$rata2);                    
                    $usulan = ($rata2*18)-$sisa_stock;
                    $sheet->setCellValue("I$row",$usulan);                                                                                       
                    $sheet->setCellValueExplicit("J$row",$this->toRupiah($usulan*$v['harga']),PHPExcel_Cell_DataType::TYPE_STRING);                        
                    $sheet->getRowDimension($row)->setRowHeight(23);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:K$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:K$row")->getAlignment()->setWrapText(true);
                $row+=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                                
//                $sheet->setCellValue("D$row",'TOTAL');    
//                $sheet->setCellValue("E$row",$total_qty);                    
//                $sheet->setCellValueExplicit("F$row",$this->toRupiah($total_harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
                                );																					 
                $sheet->getStyle("F$row_awal:F$row")->applyFromArray($styleArray);                
                $sheet->getStyle("J$row_awal:J$row")->applyFromArray($styleArray);
                $this->printOut('laporanusulankebutuhanobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Usulan Kebutuhan Obat");
    }
    
    /**
     * digunakan untuk cetak analisa ketersediaan obat
     */
    public function printAnalisaKetersediaanObat () { 
        $tahun=$this->dataReport['tahun'];
        $bulansekarang=$this->tgl->tanggal('Y-m',$this->dataReport['bulantahun']);
        $tahun_lalu=$tahun-1;
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('T');                
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $row_awal=$row;
                $sheet->mergeCells("A$row:T$row");                
                $sheet->setCellValue("A$row","ANALISA KETERSEDIAAN OBAT DAN PERBEKALAN KESEHATAN");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                $row+=1;
                $sheet->mergeCells("A$row:T$row");                
                $sheet->setCellValue("A$row","TAHUN $tahun");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row_awal:T$row")->applyFromArray($styleArray);                
                
                $row+=1;
                $sheet->mergeCells("A$row:B$row");
                $sheet->setCellValue("A$row",'BULAN');                                                                
                $sheet->setCellValue("C$row",$this->tgl->tanggal('F',$this->dataReport['bulantahun']));                                                                
                
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(7);                
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);                
                $sheet->getColumnDimension('G')->setWidth(18);                
                $sheet->getColumnDimension('H')->setWidth(20);                
                $sheet->getColumnDimension('I')->setWidth(18);                                
                $sheet->getColumnDimension('J')->setWidth(15);                
                $sheet->getColumnDimension('K')->setWidth(13);
                $sheet->getColumnDimension('L')->setWidth(13);
                $sheet->getColumnDimension('M')->setWidth(13);
                $sheet->getColumnDimension('N')->setWidth(13);
                $sheet->getColumnDimension('O')->setWidth(16);
                $sheet->getColumnDimension('P')->setWidth(15);
                $sheet->getColumnDimension('Q')->setWidth(18);
                $sheet->getColumnDimension('R')->setWidth(18);                
                $sheet->getColumnDimension('S')->setWidth(15);                
                $sheet->getColumnDimension('T')->setWidth(18);                
                $sheet->getRowDimension($row)->setRowHeight(36);                                
                
                $row_akhir=$row+1;
                $sheet->mergeCells("A$row:A$row_akhir");
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row_akhir");
                $sheet->setCellValue("B$row",'KODE');                                                                
                $sheet->mergeCells("D$row:D$row_akhir");
                $sheet->setCellValue("D$row",'NAMA');     
                $sheet->mergeCells("E$row:E$row_akhir");
                $sheet->setCellValue("E$row",'SATUAN');
                $sheet->mergeCells("F$row:F$row_akhir");
                $sheet->setCellValue("F$row",'KEMASAN');
                
                $sheet->mergeCells("G$row:G$row_akhir");
                $sheet->setCellValue("G$row","SISA STOCK (GF+PKM) PER 31 DES $tahun_lalu");    
                $sheet->mergeCells("H$row:H$row_akhir");
                $sheet->setCellValue("H$row","PEMAKAIAN RATA-RATA (GF+PKM) PERBULAN TH $tahun_lalu");                    
                $sheet->mergeCells("I$row:I$row_akhir");
                $sheet->setCellValue("I$row","KETERSEDIAAN PER 31 DES $tahun_lalu (BULAN)");                    
                
                $sheet->mergeCells("J$row:J$row_akhir");
                $tahunselanjutnya=$tahun+1;
                $sheet->setCellValue("J$row","KEBUTUHAN TH $tahunselanjutnya");                   
                $sheet->mergeCells("K$row:O$row");                
                $sheet->setCellValue("K$row",'PENERIMAAN (BLN YBS)');                   
                $sheet->mergeCells("P$row:P$row_akhir");
                $sheet->setCellValue("P$row",'PERSEDIAAN (BLN YBS)');                   
                $sheet->mergeCells("Q$row:Q$row_akhir");
                $sheet->setCellValue("Q$row",'KETERSEDIAAN (BLN YBS)');                   
                $sheet->mergeCells("R$row:R$row_akhir");
                $sheet->setCellValue("R$row",'PENGELUARAN (BLN YBS)');                   
                $sheet->mergeCells("S$row:S$row_akhir");
                $sheet->setCellValue("S$row",'SISA STOCK (BLN YBS)');                   
                $sheet->mergeCells("T$row:T$row_akhir");
                $sheet->setCellValue("T$row",'KETERSEDIAAN (BLN YBS)');                   
                
                $sheet->setCellValue("K$row_akhir",'APBD I');
                $sheet->setCellValue("L$row_akhir",'APBD II');
                $sheet->setCellValue("M$row_akhir",'APBN');
                $sheet->setCellValue("N$row_akhir",'HIBAH');
                $sheet->setCellValue("O$row_akhir",'TOTAL');
                $row_akhir+=1;
                $sheet->setCellValue("A$row_akhir",1);
                $sheet->mergeCells("B$row_akhir:C$row_akhir");
                $sheet->setCellValue("B$row_akhir",2);
                $sheet->setCellValue("D$row_akhir",3);
                $sheet->setCellValue("E$row_akhir",4);
                $sheet->setCellValue("F$row_akhir",5);
                $sheet->setCellValue("G$row_akhir",6);
                $sheet->setCellValue("H$row_akhir",7);
                $sheet->setCellValue("I$row_akhir",'8=6/7');
                $sheet->setCellValue("J$row_akhir",'9=8*18 BLN');
                $sheet->setCellValue("K$row_akhir",10);
                $sheet->setCellValue("L$row_akhir",11);
                $sheet->setCellValue("M$row_akhir",12);
                $sheet->setCellValue("N$row_akhir",13);
                $sheet->setCellValue("O$row_akhir",'14=10+11+12+13');
                $sheet->setCellValue("P$row_akhir",'15=6+14');
                $sheet->setCellValue("Q$row_akhir",'16=14/8');
                $sheet->setCellValue("R$row_akhir",17);
                $sheet->setCellValue("S$row_akhir",'18=14-17');
                $sheet->setCellValue("T$row_akhir",'19=18/7');
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:T$row_akhir")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:T$row_akhir")->getAlignment()->setWrapText(true);                
                
                $row+=3;
                $row_awal=$row;
                $total_qty=0;
                $total_harga=0;               
                $str = 'SELECT mo.idobat,mo.kode_obat,mo.nama_obat,mo.idsatuan_obat,mo.kemasan FROM master_obat mo ORDER BY mo.nama_obat ASC';        
                $this->db->setFieldTable(array('idobat','kode_obat','nama_obat','idsatuan_obat','kemasan'));
                $r=$this->db->getRecord($str);                  
                while (list($k,$v)=each($r)) {                    
                    $idobat=$v['idobat'];                    
                    $sheet->setCellValue("A$row",$v['no']);                                                                
                    $sheet->mergeCells("B$row:C$row");
                    $sheet->setCellValue("B$row",$v['kode_obat']);                                                                
                    $sheet->setCellValue("D$row",$v['nama_obat']);                    
                    $sheet->setCellValue("E$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));                    
                    $sheet->setCellValue("F$row",$v['kemasan']);                    
                    
                    $str = "SELECT sisa_stock FROM log_ks ksp,(SELECT MAX(idlog) AS idlog FROM log_ks WHERE tahun=$tahun_lalu AND idobat=$idobat) AS ksp2 WHERE ksp.idlog=ksp2.idlog";            
                    $this->db->setFieldTable(array('sisa_stock'));
                    $r1=$this->db->getRecord($str);        
                    $sisa_stock_gf=isset($r1[1]) ? $r1[1]['sisa_stock'] : 0;

                    $str = "SELECT sisa_stock_puskesmas FROM log_ks_puskesmas ksp,(SELECT MAX(idlog_puskesmas) AS idlog_puskesmas FROM log_ks_puskesmas WHERE tahun_puskesmas=$tahun_lalu AND idobat=$idobat) AS ksp2 WHERE ksp.idlog_puskesmas=ksp2.idlog_puskesmas";                                    
                    $this->db->setFieldTable(array('sisa_stock_puskesmas'));
                    $r1=$this->db->getRecord($str);                    
                    $sisa_stock_pm=isset($r1[1]) ? $r1[1]['sisa_stock'] : 0;            
            
                    $sisa_stock_tahun_lalu=$sisa_stock_gf+$sisa_stock_pm;
                    $sheet->setCellValue("G$row",$sisa_stock_tahun_lalu);
            
                    $str = "SELECT SUM(pemberian)/12 AS rata2 FROM master_sbbk msb, detail_sbbk dsb WHERE msb.idsbbk=dsb.idsbbk AND msb.status='complete' AND  msb.tahun=$tahun_lalu AND dsb.idobat=$idobat";
                    $this->db->setFieldTable(array('rata2'));
                    $r2=$this->db->getRecord($str);                    
                    $rata2_gf=$r2[1]['rata2'] > 0 ? number_format($r2[1]['rata2'],2) : 0;

                    $str = "SELECT SUM(pemberian_puskesmas)/12 AS rata2 FROM master_sbbk_puskesmas msb, detail_sbbk_puskesmas dsb WHERE msb.idsbbk_puskesmas=dsb.idsbbk_puskesmas AND msb.status_puskesmas='complete' AND  msb.tahun_puskesmas=$tahun_lalu AND dsb.idobat=$idobat";            
                    $r2=$this->db->getRecord($str);                    
                    $rata2_pm=$r2[1]['rata2'] > 0 ? number_format($r2[1]['rata2'],2) : 0;
                    $rata2_tahun_lalu=$rata2_gf+$rata2_pm;
                    
                    $sheet->setCellValue("H$row",$rata2_tahun_lalu);
                    
                    $ketersediaan_tahun_lalu=number_format($sisa_stock_tahun_lalu/$rata2_tahun_lalu,2);                    
                    $sheet->setCellValue("I$row",$ketersediaan_tahun_lalu);
                    
                    $kebutuhan_tahun_selanjutnya=$rata2_tahun_lalu*18;                    
                    $sheet->setCellValue("J$row",$kebutuhan_tahun_selanjutnya);
                    
                    $str = "SELECT msb.idsumber_dana,SUM(dsb.qty) AS jumlah FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND msb.status='complete' AND msb.tahun='$tahun' AND DATE_FORMAT(msb.tanggal_sbbm,'%Y-%m')<='$bulansekarang' AND dsb.idobat=$idobat GROUP BY msb.idsumber_dana";                    
                    $this->db->setFieldTable(array('idsumber_dana','jumlah'));
                    $r2=$this->db->getRecord($str);
                    
                    $apbd1=0;
                    $apbd2=0;
                    $apbn=0;
                    $hibah=0;
                    $totalpenerimaan_bulan_ini=0;
                    foreach ($r2 as $p) {
                        switch ($p['idsumber_dana']) {
                            case 1 :
                                $apbd1+=$p['jumlah'];
                            break;
                            case 2 :
                                $apbd2+=$p['jumlah'];
                            break;
                            case 3 :
                                $apbn+=$p['jumlah'];
                            break;
                            case 4 :
                                $hibah+=$p['jumlah'];
                            break;                        
                        }
                        $totalpenerimaan_bulan_ini+=$p['jumlah'];
                    }
                    $sheet->setCellValue("K$row",$apbd1);
                    $sheet->setCellValue("L$row",$apbd2);
                    $sheet->setCellValue("M$row",$apbn);
                    $sheet->setCellValue("N$row",$hibah);
                    $sheet->setCellValue("O$row",$totalpenerimaan_bulan_ini);
                    
                    $persedian_bulan_ini = $sisa_stock_tahun_lalu+$totalpenerimaan_bulan_ini;
                    $sheet->setCellValue("P$row",$persedian_bulan_ini);
                    
                    $ketersediaan_bulan_ini = number_format($totalpenerimaan_bulan_ini/$ketersediaan_tahun_lalu,2);
                    $sheet->setCellValue("Q$row",$ketersediaan_bulan_ini);
                                        
                    $pengeluaran_bulan_ini=$this->db->getSumRowsOfTable('pemberian',"master_sbbk msb,detail_sbbk dsb WHERE dsb.idsbbk=msb.idsbbk AND msb.status='complete' AND DATE_FORMAT(msb.tanggal_sbbk,'%Y-%m')='$bulansekarang' AND dsb.idobat=$idobat");
                    $sheet->setCellValue("R$row",$pengeluaran_bulan_ini);
                    
                    $sisa_stock_bulan_ini = $persedian_bulan_ini-$pengeluaran_bulan_ini;
                    $sheet->setCellValue("S$row",$sisa_stock_bulan_ini);
                    
                    $ketersediaan_bulan_ini2 = number_format($sisa_stock_bulan_ini/$ketersediaan_tahun_lalu,2);
                    $sheet->setCellValue("T$row",$ketersediaan_bulan_ini2);
                    
                    $sheet->getRowDimension($row)->setRowHeight(23);
                    $row+=1;
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:T$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:T$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                                
                
                $this->printOut('laporananalisaketersediaanobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Analisa Ketersediaan Obat");
    }
    
    /**
     * digunakan untuk cetak detail distribusi obat
     */
    public function printKeuanganPersediaan () {   
        $tahun=$this->dataReport['tahun'];                
        switch ($this->getDriver()) {
            case 'excel2003' :               
            case 'excel2007' :   
                $this->setHeader('I');
                $sheet=$this->rpt->getActiveSheet();
                $row=$this->currentRow+2;
                $this->rpt->getDefaultStyle()->getFont()->setName('Arial');                
                $this->rpt->getDefaultStyle()->getFont()->setSize('9');                                                                                    
                
                $sheet->mergeCells("A$row:I$row");                
                $sheet->setCellValue("A$row","LAPORAN KEUANGAN PERSEDIAAN T.A $tahun");                
                $sheet->getRowDimension($row)->setRowHeight(20);     
                
                $styleArray=array(
								'font' => array('bold' => true,
                                                'size' => 12),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
							);
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
                               
                $row+=2;
                $sheet->getColumnDimension('A')->setWidth(10);                
                $sheet->getColumnDimension('B')->setWidth(7);                
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(50);
                $sheet->getColumnDimension('E')->setWidth(15);   
                $sheet->getColumnDimension('F')->setWidth(15);                
                $sheet->getColumnDimension('G')->setWidth(15);                
                $sheet->getColumnDimension('H')->setWidth(15);                
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getRowDimension($row)->setRowHeight(23);                                
                                
                $sheet->setCellValue("A$row",'NO');                                                                
                $sheet->mergeCells("B$row:C$row");
                $sheet->setCellValue("B$row",'KODE');                                                                
                $sheet->setCellValue("D$row",'NAMA');                    
                $sheet->setCellValue("E$row",'SATUAN');
                $sheet->setCellValue("F$row",'KEMASAN');
                $sheet->setCellValue("G$row",'HARGA');
                $sheet->setCellValue("H$row","SISA STOCK $tahun");    
                $sheet->setCellValue("I$row",'SUB TOTAL');                    
                
                $styleArray=array(
								'font' => array('bold' => true),
								'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
												   'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
								'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
							);
                
                $sheet->getStyle("A$row:I$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row:I$row")->getAlignment()->setWrapText(true);                
                
                $str = "SELECT mo.idobat,dsb.iddetail_sbbm,mo.kode_obat AS mst_kode_obat,dsb.kode_obat,mo.nama_obat AS mst_nama_obat,dsb.nama_obat,mo.idsatuan_obat AS mst_idsatuan_obat,dsb.idsatuan_obat,mo.kemasan AS mst_kemasan,dsb.kemasan,mo.harga AS mst_harga,dsb.harga FROM master_obat mo LEFT JOIN (SELECT iddetail_sbbm,idobat,kode_obat,nama_obat,harga,idsatuan_obat,kemasan,tanggal_expire,dsb.idprogram FROM master_sbbm msb,detail_sbbm dsb WHERE dsb.idsbbm=msb.idsbbm AND status='complete' AND msb.tahun=$tahun GROUP BY dsb.idobat,dsb.harga) dsb ON (mo.idobat=dsb.idobat) ORDER BY mo.nama_obat ASC";
                $this->db->setFieldTable(array('idobat','iddetail_sbbm','mst_kode_obat','kode_obat','mst_nama_obat','nama_obat','mst_idsatuan_obat','idsatuan_obat','mst_kemasan','kemasan','mst_harga','harga'));
                $r=$this->db->getRecord($str);
                $row+=1;
                $row_awal=$row;
                while (list($k,$v)=each($r)) {                    
                    $sheet->setCellValue("A$row",$v['no']);                                                                
                    $iddetail_sbbm=$v['iddetail_sbbm'];
                    $keluar=0;
                    $total=0;
                    if ($iddetail_sbbm=='') {
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['mst_kode_obat']);                                                                
                        $sheet->setCellValue("D$row",$v['mst_nama_obat']);                    
                        $sheet->setCellValue("E$row",$this->DMaster->getNamaSatuanObat($v['mst_idsatuan_obat']));
                        $sheet->setCellValue("F$row",$v['mst_kemasan']);
                        $harga=$v['mst_harga'];                        
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $sheet->setCellValue("H$row",0);    
                        $sheet->setCellValue("I$row",0);                                             
                    }else {                
                        $sheet->mergeCells("B$row:C$row");
                        $sheet->setCellValue("B$row",$v['kode_obat']);                                                                
                        $sheet->setCellValue("D$row",$v['nama_obat']);                    
                        $sheet->setCellValue("E$row",$this->DMaster->getNamaSatuanObat($v['idsatuan_obat']));
                        $sheet->setCellValue("F$row",$v['kemasan']);
                        $harga=$v['harga'];                        
                        $sheet->setCellValueExplicit("G$row",$this->toRupiah($harga),PHPExcel_Cell_DataType::TYPE_STRING);                        
                        $idobat=$v['idobat'];
                        $str = "SELECT mode,COUNT(idkartu_stock) AS jumlah_qty FROM kartu_stock ks,detail_sbbm dsb WHERE ks.iddetail_sbbm=dsb.iddetail_sbbm AND dsb.idobat=$idobat AND dsb.harga=$harga AND ks.isdestroyed=0 GROUP BY mode";                
                        $this->db->setFieldTable(array('mode','jumlah_qty'));
                        $m=$this->db->getRecord($str);                
                        foreach ($m as $y=>$z) {                    
                            if ($z['mode'] == 'keluar') {                        
                                $keluar+=$z['jumlah_qty'];
                            }
                            $total+=$z['jumlah_qty'];
                        }           
                        $sisa_stock=$total-$keluar;
                        $sheet->setCellValue("H$row",$sisa_stock);    
                        $sheet->setCellValue("I$row",($sisa_stock*$harga));
                        
                    }   
                    $sheet->getRowDimension($row)->setRowHeight(20);
                    $row+=1;                   
                }
                $row-=1;
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                       'vertical'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                                    'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
                                );																					 
                $sheet->getStyle("A$row_awal:I$row")->applyFromArray($styleArray);
                $sheet->getStyle("A$row_awal:I$row")->getAlignment()->setWrapText(true);
                
                $styleArray=array(								
                                    'alignment' => array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                                );																					 
                $sheet->getStyle("D$row_awal:D$row")->applyFromArray($styleArray);                                
                $this->printOut('laporankeuanganpersediaanobat');
            break;
        }
        $this->setLink($this->dataReport['linkoutput'],"Laporan Keuangan Persediaan Obat");
    }
}
?>

