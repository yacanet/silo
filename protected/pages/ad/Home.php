<?php
prado::using ('Application.MainPageAD');
class Home extends MainPageAD {
	public function onLoad($param) {		
		parent::onLoad($param);		            
        $this->showDashboard=true;
        $this->createObj('dmaster');
        $this->createObj('obat');
		if (!$this->IsPostBack&&!$this->IsCallBack) {              
            if (!isset($_SESSION['currentPageHome'])||$_SESSION['currentPageHome']['page_name']!='ad.Home') {                                
                $idpuskesmas=$this->Pengguna->getDataUser('idpuskesmas');
                $_SESSION['currentPageHome']=array('page_name'=>'ad.Home',
                                                   'stockobat'=>$this->Obat->getJumlahStockObatPuskesmas ($idpuskesmas),
                                                   'obatexpires'=>$this->Obat->getJumlahObatExpirePuskesmas($idpuskesmas),
                                                   'lembarpo'=>$this->Obat->getJumlahLPO ('allwithoutcomplete',$idpuskesmas),
                                                   'distribusiobat'=>$this->Obat->getJumlahDistribusiObat($_SESSION['ta'],$idpuskesmas));
            }                
            $this->populateDataLPO();
		}        
	} 
    public function refreshPage ($sender,$param) {
        unset($_SESSION['currentPageHome']);
        $this->redirect('sa.Home');
    }    
    public function populateDataLPO ($search=false) {        
        $status='complete';                
        $ta=$_SESSION['ta'];
        $idpuskesmas=$this->idpuskesmas;        
        
        $str = "SELECT ml.idlpo,ml.no_lpo,msk.no_sbbk,msk.tanggal_sbbk,ml.tanggal_lpo,ml.nama_ka,ml.jumlah_kunjungan_gratis,ml.jumlah_kunjungan_bayar,ml.jumlah_kunjungan_bpjs,ml.status,ml.response_lpo,ml.date_modified FROM master_lpo ml LEFT JOIN master_sbbk msk ON(ml.idlpo=msk.idlpo) WHERE ml.status='complete' AND ml.idpuskesmas='$idpuskesmas' AND ml.tahun=$ta AND ml.response_lpo=3 OR ml.response_lpo!=5 ORDER BY ml.date_modified DESC,ml.no_lpo ASC LIMIT 10";                                
		$this->DB->setFieldTable(array('idlpo','no_lpo','tanggal_lpo','no_sbbk','tanggal_sbbk','nama_ka','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','status','response_lpo','date_modified'));
		$r=$this->DB->getRecord($str,$offset+1);          
        $data=array();        
        while (list($k,$v)=each($r)) {
            $idlpo=$v['idlpo'];                        
            $v['total_permintaan']=$this->DB->getSumRowsOfTable ('permintaan',"detail_lpo WHERE idlpo=$idlpo");		  
            $data[$k]=$v;
        }
        $this->RepeaterS->DataSource=$data;
		$this->RepeaterS->dataBind();             
	}
    public function viewRecordLPO ($sender,$param) {
        $this->createObj('obat');
        $idlpo=$this->getDataKeyField($sender,$this->RepeaterS);           
        $str = "SELECT ml.idlpo,ml.no_lpo,ml.idpuskesmas,ml.tanggal_lpo,jumlah_kunjungan_gratis,jumlah_kunjungan_bayar,jumlah_kunjungan_bpjs,ml.nip_pengelola_obat AS nip_pengelola,ml.nama_pengelola_obat AS nama_pengelola,ml.nip_kadis,ml.nama_kadis,ml.nip_ka_gudang,ml.nama_ka_gudang,ml.nip_ka,ml.nama_ka,response_lpo FROM master_lpo ml WHERE ml.idlpo=$idlpo";            
        $this->DB->setFieldTable(array('idlpo','no_lpo','idpuskesmas','tanggal_lpo','jumlah_kunjungan_gratis','jumlah_kunjungan_bayar','jumlah_kunjungan_bpjs','nip_pengelola','nama_pengelola','nip_kadis','nama_kadis','nip_ka_gudang','nama_ka_gudang','nip_ka','nama_ka','response_lpo'));
        $r=$this->DB->getRecord($str);
        $_SESSION['currentPageDaftarLPO']['tanggal']=$r[1]['tanggal_lpo'];
        $_SESSION['currentPageDaftarLPO']['datalpo']=$r[1];
        $_SESSION['currentPageDaftarLPO']['datalpo']['issaved']=true;
        $_SESSION['currentPageDaftarLPO']['datalpo']['mode']='buat';
        $this->redirect('permintaan.DaftarLPO',true);
    }
}
?>