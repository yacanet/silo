<?php
prado::using ('Application.MainPageSA');
class Home extends MainPageSA {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;
        $this->createObj('dmaster');
        $this->createObj('obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHome'])||$_SESSION['currentPageHome']['page_name']!='sa.Home') {                                
                $str = "SELECT dsb.idobat,dsb.nama_obat,dsb.harga,dsb.idsatuan_obat,dsb.kemasan,dsb.tanggal_expire,dsb.idprogram,COUNT(kartu_stock.idobat) AS volume FROM master_sbbm msb JOIN detail_sbbm dsb ON (dsb.idsbbm=msb.idsbbm) LEFT JOIN (SELECT ks.idobat,dsb.harga FROM kartu_stock ks,detail_sbbm dsb WHERE dsb.iddetail_sbbm=ks.iddetail_sbbm AND ks.mode='masuk') AS kartu_stock ON (kartu_stock.idobat=dsb.idobat AND kartu_stock.harga=dsb.harga) WHERE msb.status='complete' AND dsb.tanggal_expire>=DATE(NOW()) AND dsb.tanggal_expire <= DATE_ADD(DATE(NOW()),INTERVAL 5 MONTH) GROUP BY dsb.idobat,dsb.harga HAVING COUNT(kartu_stock.idobat) > 0 ORDER BY dsb.tanggal_expire ASC,dsb.nama_obat ASC LIMIT 10";
                $this->DB->setFieldTable(array('idobat','nama_obat','harga','idsatuan_obat','kemasan','tanggal_expire','idprogram','volume'));
                $r=$this->DB->getRecord($str);
                $dataobatexpires=array();        
                while (list($k,$v)=each($r)) {
                    $harga=$v['harga'];
                    $v['harga']=$this->Obat->toRupiah($harga);
                    $v['nama_program']=$this->DMaster->getNamaProgramByID($v['idprogram']);
                    $v['tanggal_expire']=$this->TGL->tanggal('d/m/Y',$v['tanggal_expire']);                    
                    $subtotal=$v['volume']*$harga;
                    $v['subtotal']=$this->Obat->toRupiah($subtotal);
                    $dataobatexpires[$k]=$v;
                }                
                $_SESSION['currentPageHome']=array('page_name'=>'sa.Home',
                                                   'stockobat'=>$this->Obat->getJumlahStockObat (),
                                                   'obatexpires'=>$this->Obat->getJumlahObatExpire(),
                                                   'lembarpo'=>$this->Obat->getJumlahLPO ('allwithoutcomplete'),
                                                   'distribusiobat'=>$this->Obat->getJumlahDistribusiObat($_SESSION['ta']),
                                                   'dataobatexpires'=>$dataobatexpires);
            }                        
                
            $this->RepeaterObatKadaluarsa->DataSource=$_SESSION['currentPageHome']['dataobatexpires'];
            $this->RepeaterObatKadaluarsa->DataBind();
		}        
	}
    public function refreshPage ($sender,$param) {
        unset($_SESSION['currentPageHome']);
        $this->redirect('sa.Home');
    }
    public function clickDetailObatExpire  ($sender,$param) {
        $_SESSION['currentPageExpireObat']=array('page_name'=>'sa.report.ExpireObat','page_num'=>0,'idprogram'=>'none','waktuexpires'=>5,'modeexpires'=>'bulan');												        
        $this->redirect('report.ExpireObat',true);
    }
    
}
?>