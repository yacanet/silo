<?php
/**
*
* digunakan untuk memproses setup aplikasi
*
*/
prado::using ('Application.logic.Logic_Global');
class Logic_DMaster extends Logic_Global {     
    /**
     * golongan obat
     * @var type 
     */
    private $golonganObat = array('none'=>'Daftar Golongan Obat',1=>'BEBAS',2=>'BEBAS TERBATAS',3=>'KERAS',4=>'PSIKOTROPIKA',5=>'NARKOTIKA');
    /**
     * tipe response
     * @var type 
     */
    private $response = array('none'=>'Daftar Response',1=>'PENDING',2=>'PROCESSED',3=>'SHIPPED',4=>'CANCELED',5=>'COMPLETE');
    /**
     * sumber dana
     * @var type 
     */
    private $sumberdana= array('none'=>'Daftar Sumber Dana',1=>'APBD I',2=>'APBD II',3=>'APBN',4=>'HIBAH');
    /**
	* object setup;	
	*/
	public $setup;
	public function __construct ($db) {
		parent::__construct ($db);	  
        $this->setup = $this->getLogic ('Setup');
	}
    /**
     * digunakan untuk mendapatkan golongan obat
     * @param type $id
     * @return type tinyin
     */
    public function getGolonganObat ($id=null) {
        if ($id===null) {
            return $this->golonganObat;
        }else{
            return $this->golonganObat[$id];
        }
    }
    /**
     * digunakan untuk mendapatkan jenis response LPO
     * @param type $id
     * @return type tinyin
     */
    public function getJenisResponseLPO ($id=null) {
        if ($id===null) {
            return $this->response;
        }else{
            return $this->response[$id];
        }
    }
    /**
     * digunakan untuk mendapatkan jenis response SBBK
     * @param type $id
     * @return type tinyin
     */
    public function getJenisResponseSBBK ($id=null) {
        if ($id===null) {
            return $this->response;
        }elseif($id==0) {
            return 'N.A';
        }else{
            return $this->response[$id];
        }
    }
    /**
     * digunakan untuk mendapatkan daftar kecamatan
     */
    public function getListKecamatan () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listkecamatan');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('kecamatan WHERE enabled=1',array('idkecamatan','nama_kecamatan'),'nama_kecamatan',null,1);
                $dataitem['none']='Daftar Kecamatan';    
                $this->Application->Cache->set('listkecamatan',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('kecamatan WHERE enabled=1',array('idkecamatan','nama_kecamatan'),'nama_kecamatan',null,1);
            $dataitem['none']='Daftar Kecamatan';
        }
        return $dataitem;        
    }
    /**
     * digunakan untuk mendapatkan nama kecamatan
     * @param type $idkecamatan
     */
    public function getNamaKecamatanByID ($idkecamatan) {
        if ($this->Application->Cache) {            
            $dataitem=$this->getListKecamatan();
            $nama_item=$dataitem[$idkecamatan];
        }else {
            $dataitem=$this->getList("kecamatan WHERE idkecamatan=$idkecamatan",array('nama_kecamatan'),'nama_kecamatan');
            $nama_item=$dataitem[1]['nama_kecamatan'];                    
        }
        return $nama_item;
    }
    /**
     * digunakan untuk mendapatkan sumber dana
     */
    public function getListSumberDana () {
        return $this->sumberdana;        
    }
    /**
     * digunakan untuk mendapatkan nama Sumber Dana 
     * @param type $idsumber_dana
     */
    public function getNamaSumberDanaByID ($idsumber_dana) {        
        return $this->sumberdana[$idsumber_dana];        
    }    
    /**
     * digunakan untuk mendapatkan program
     */
    public function getListProgram () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listprogram');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('program WHERE enabled=1',array('idprogram','nama_program'),'nama_program',null,1);
                $dataitem['none']='Daftar Program';    
                $this->Application->Cache->set('listprogram',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('program WHERE enabled=1',array('idprogram','nama_program'),'nama_program',null,1);
            $dataitem['none']='Daftar Program';
        }
        return $dataitem;        
    }
    /**
     * digunakan untuk mendapatkan nama program
     * @param type $idprogram
     */
    public function getNamaProgramByID ($idprogram) {
        if ($this->Application->Cache) {            
            $dataitem=$this->getListProgram();
            $nama_item=$dataitem[$idprogram];
        }else {
            $dataitem=$this->getList("program WHERE idprogram=$idprogram",array('nama_program'),'nama_program');
            $nama_item=$dataitem[1]['nama_program'];                    
        }
        return $nama_item;
    }
    /**
     * digunakan untuk mendapatkan daftar puskesmas
     */
    public function getListPuskesmas () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listpuskesmas');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nama_puskesmas'),'nama_puskesmas',null,1);
                $dataitem['none']='Daftar Puskesmas';    
                $this->Application->Cache->set('listpuskesmas',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('puskesmas WHERE enabled=1',array('idpuskesmas','nama_puskesmas'),'nama_puskesmas',null,1);
            $dataitem['none']='Daftar Puskesmas';
        }
        return $dataitem;        
    }      
    
    /**
     * digunakan untuk mendapatkan nama puskesmas
     * @param type $idpuskesmas
     */
    public function getNamaPuskesmasByID ($idpuskesmas) {        
        if ($this->Application->Cache) {            
            $dataitem=$this->getListPuskesmas();
            $unitname=$dataitem[$idpuskesmas];
        }else {
            $dataitem=$this->getList("puskesmas WHERE idpuskesmas=$idpuskesmas",array('nama_puskesmas'),'nama_puskesmas');
            $unitname=$dataitem[1]['nama_puskesmas'];                    
        }
        return $unitname;
    }   
    /**
     * digunakan untuk mendapatkan daftar unit puskesmas
     */
    public function getListUnitPuskesmas () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listunitpuskesmas');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('unitpuskesmas WHERE enabled=1',array('idunitpuskesmas','nama_unit'),'nama_unit',null,1);
                $dataitem['none']='Daftar Unit Puskesmas';    
                $this->Application->Cache->set('listpolipuskesmas',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('unitpuskesmas WHERE enabled=1',array('idunitpuskesmas','nama_unit'),'nama_unit',null,1);
            $dataitem['none']='Daftar Unit Puskesmas';
        }
        return $dataitem;        
    }      
    
    /**
     * digunakan untuk mendapatkan nama unit puskesmas
     * @param type $idunitpuskesmas
     */
    public function getNamaUnitPuskesmasByID ($idunitpuskesmas) {        
        if ($this->Application->Cache) {            
            $dataitem=$this->getListUnitPuskesmas();
            $unitname=$dataitem[$idunitpuskesmas];
        }else {
            $dataitem=$this->getList("unitpuskesmas WHERE idunitpuskesmas=$idunitpuskesmas",array('nama_unit'),'nama_unit');
            $unitname=$dataitem[1]['nama_unit'];                    
        }
        return $unitname;
    } 
    /**
     * digunakan untuk mendapatkan ka puskesmas
     */
    public function getListKAPuskesmas () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listkapuskesmas');            
            if (!isset($dataitem['none'])) {    
                $str = "SELECT idpuskesmas,nip_ka,nama_ka FROM puskesmas WHERE enabled=1 ORDER BY nama_puskesmas ASC";
                $this->db->setFieldTable(array('idpuskesmas','nip_ka','nama_ka'));
                $r=$this->db->getRecord($str);
                $dataitem=array();
                $dataitem['none']='Daftar KA Puskesmas';    
                while (list($k,$v)=each($r)) {
                    $dataitem[$v['idpuskesmas']]=$v;
                }
                $this->Application->Cache->set('listkapuskesmas',$dataitem);
            }
        }else {                        
            $str = "SELECT idpuskesmas,nip_ka,nama_ka FROM puskesmas WHERE enabled=1 ORDER BY nama_puskesmas ASC";
            $this->db->setFieldTable(array('idpuskesmas','nip_ka','nama_ka'));
            $r=$this->db->getRecord($str);
            $dataitem=array();
            $dataitem['none']='Daftar KA Puskesmas';
            while (list($k,$v)=each($r)) {
                $dataitem[$v['idpuskesmas']]=$v;
            }                            
        }
        return $dataitem;        
    }     
    /**
     * digunakan untuk mendapatkan nama KA puskesmas
     * @param type $idpuskesmas
     */
    public function getNamaKAPuskesmasByID ($idpuskesmas) {        
        if ($this->Application->Cache) {            
            $dataitem=$this->getListKAPuskesmas();              
            $unitname=$dataitem[$idpuskesmas];
        }else {
            $dataitem=$this->getList("puskesmas WHERE idpuskesmas=$idpuskesmas",array('nip_ka','nama_ka'),'nama_puskesmas');
            $unitname=$dataitem[1];                    
        }
        return $unitname;
    }   
    /**
     * digunakan untuk mengetahui jumlah puskesmas
     * 
     */
    public function getJumlahPuskesmas () {        
        if ($this->Application->Cache) {
            $dataitem=count($this->getListPuskesmas());
            $jumlah=$dataitem > 0 ? $dataitem - 1 : 0;
        }else {
            $jumlah=$this->DB->getCountRowsOfTable ('puskesmas','idpuskesmas');        
        }
        return $jumlah;
    }
    /**
     * digunakan untuk mendapatkan daftar bentuk sediaan
     */
    public function getListBentukSediaan () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listbentuksediaan');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('bentuksediaan WHERE enabled=1',array('idbentuk_sediaan','nama_bentuk'),'nama_bentuk',null,1);
                $dataitem['none']='Daftar Bentuk Sediaan';    
                $this->Application->Cache->set('listbentuksediaan',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('bentuksediaan WHERE enabled=1',array('idbentuk_sediaan','nama_bentuk'),'nama_bentuk',null,1);
            $dataitem['none']='Daftar Bentuk Sediaan';
        }
        return $dataitem;        
    }
    /**
     * digunakan untuk mendapatkan nama bentuk sediaan
     */
    public function getNamaBentukSediaan ($idbentuk_sediaan) {
        if ($this->Application->Cache) {            
            $dataitem=$this->getListBentukSediaan();
            $name=$dataitem[$idbentuk_sediaan];
        }else {
            $dataitem=$this->getList("bentuksediaan WHERE idbentuk_sediaan=$idbentuk_sediaan",array('nama_bentuk'),'nama_bentuk');
            $name=$dataitem[1]['nama_bentuk'];                    
        }
        return $name;
    }
    /**
     * digunakan untuk mendapatkan daftar satuan obat
     */
    public function getListSatuanObat () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listsatuanobat');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('satuanobat WHERE enabled=1',array('idsatuan_obat','nama_satuan'),'nama_satuan',null,1);
                $dataitem['none']='Daftar Satuan Obat';    
                $this->Application->Cache->set('listsatuanobat',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('satuanobat WHERE enabled=1',array('idsatuan_obat','nama_satuan'),'nama_satuan',null,1);
            $dataitem['none']='Daftar Satuan Obat';
        }
        return $dataitem;        
    }
    /**
     * digunakan untuk mendapatkan nama satuan obat
     */
    public function getNamaSatuanObat ($idsatuan_obat) {
        if ($this->Application->Cache) {            
            $dataitem=$this->getListSatuanObat();
            $name=$dataitem[$idsatuan_obat];
        }else {
            $dataitem=$this->getList("satuanobat WHERE idsatuan_obat=$idsatuan_obat",array('nama_satuan'),'nama_satuan');
            $name=$dataitem[1]['nama_satuan'];                    
        }
        return $name;
    }
    /**
     * digunakan untuk mendapatkan daftar produsen
     */
    public function getListProdusen () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listprodusen');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('produsen WHERE enabled=1',array('idprodusen','nama_produsen'),'nama_produsen',null,1);
                $dataitem['none']='Daftar Produsen';    
                $this->Application->Cache->set('listprodusen',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('produsen WHERE enabled=1',array('idprodusen','nama_produsen'),'nama_produsen',null,1);
            $dataitem['none']='Daftar Produsen';
        }
        return $dataitem;        
    }
    /**
     * digunakan untuk mendapatkan nama produsen
     * @param type $idprodusen
     */
    public function getNamaProdusenByID ($idprodusen) {
        if ($this->Application->Cache) {            
            $dataitem=$this->getListProdusen();
            $name=$dataitem[$idprodusen];
        }else {
            $dataitem=$this->getList("produsen WHERE idprodusen=$idprodusen",array('nama_produsen'),'nama_produsen');
            $name=$dataitem[1]['nama_produsen'];                    
        }
        return $name;
    }   
    /**
     * digunakan untuk mendapatkan daftar farmakologi
     */
    public function getListFarmakologi () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listfarmakologi');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('farmakologi WHERE enabled=1',array('idfarmakologi','nama_farmakologi'),'nama_farmakologi',null,1);
                $dataitem['none']='Daftar Farmakologi';    
                $this->Application->Cache->set('listfarmakologi',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('farmakologi WHERE enabled=1',array('idfarmakologi','nama_farmakologi'),'nama_farmakologi',null,1);
            $dataitem['none']='Daftar Farmakologi';
        }
        return $dataitem;        
    }  
    /**
     * digunakan untuk mendapatkan daftar penyalur
     */
    public function getListPenyalur () {
        if ($this->Application->Cache) {            
            $dataitem=$this->Application->Cache->get('listpenyalur');            
            if (!isset($dataitem['none'])) {
                $dataitem=$this->getList('penyalur WHERE enabled=1',array('idpenyalur','nama_penyalur'),'nama_penyalur',null,1);
                $dataitem['none']='Daftar Penyalur';    
                $this->Application->Cache->set('listpenyalur',$dataitem);
            }
        }else {                        
            $dataitem=$this->getList('penyalur WHERE enabled=1',array('idpenyalur','nama_penyalur'),'nama_penyalur',null,1);
            $dataitem['none']='Daftar Penyalur';
        }
        return $dataitem;        
    }    
    /**
     * digunakan untuk mendapatkan nama penyalur
     * @param type $idpenyalur
     */
    public function getNamaPenyalurByID ($idpenyalur) {
        if ($this->Application->Cache) {            
            $dataitem=$this->getListPenyalur();
            $name=$dataitem[$idpenyalur];
        }else {
            $dataitem=$this->getList("penyalur WHERE idpenyalur=$idpenyalur",array('nama_penyalur'),'nama_penyalur');
            $name=$dataitem[1]['nama_produsen'];                    
        }
        return $name;
    }  
}
?>