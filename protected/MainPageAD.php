<?php

class MainPageAD extends MainPage {  
    /**
     * idpuskesmas
     * @var type integer
     */
    public $idpuskesmas;
    /**
     * nama puskesmas
     * @var type string
     */
    public $nama_puskesmas;
    /**     
     * show page unit puskesmas [dmaster]
     */
    public $showUnitPuskesmas=false;
    /**     
     * show page obat [dmaster]
     */
    public $showObatPuskesmas=false;    
    /**     
     * show page lembar permintaan obat baru [permintaan]
     */
    public $showLPOBaru=false;
    /**     
     * show page daftar LPO [permintaan]
     */
    public $showDaftarLPO=false;
    /**     
     * show sub menu mutasi barang masuk[mutasi barang]
     */
    public $showSubMenuMutasiBarangMasuk=false;
    /**     
     * show sub menu mutasi barang keluar[mutasi barang]
     */
    public $showSubMenuMutasiBarangKeluar=false;
    /**     
     * show page SBBM Baru[mutasi barang masuk]
     */
    public $showSBBMBaru=false;
    /**     
     * show page Daftar Obat Masuk[mutasi barang masuk]
     */
    public $showDetailSBBM=false;
    /**     
     * show page Daftar SBBM Baru[mutasi barang masuk]
     */
    public $showDaftarSBBM=false;
    /**     
     * show page SBBK Baru[mutasi barang keluar]
     */
    public $showSBBKBaru=false;
    /**     
     * show page Daftar Obat Masuk[mutasi barang keluar]
     */
    public $showDetailSBBK=false;
    /**     
     * show page Daftar SBBK Baru[mutasi barang keluar]
     */
    public $showDaftarSBBK=false;
    /**     
     * show page Pemakaian Obat Unit [mutasibarang]
     */
    public $showPemakaianObatUnit=false;
    /**     
     * show sub menu report [report]
     */
    public $showSubMenuReport=false;
    /**     
     * show sub menu mutasi obat[report]
     */
    public $showSubMenuReportMutasiObat=false;
    /**     
     * show page Laporan Mutasi Bulanan [report]
     */
    public $showReportMutasiObatBulanan=false;
    /**     
     * show page Laporan Mutasi Tahunan [report]
     */
    public $showReportMutasiObatTahunan=false;
    /**     
     * show sub menu stock [report]
     */
    public $showSubMenuReportStock=false;
    /**     
     * show page Laporan Perpetual Stock [report]
     */
    public $showReportPerpetualStock=false;  
    /**     
     * show page Laporan Expire Obat [report]
     */
    public $showReportExpireObat=false;
    /**     
     * show sub menu setting sistem[setting]
     */
    public $showSubMenuSettingSistem=false;    
    /**     
     * show page user puskesmas [setting sistem]
     */
    public $showUserPuskesmas=false;    
	public function onLoad ($param) {		
		parent::onLoad($param);		
        $this->idpuskesmas = $this->Pengguna->getDataUser('idpuskesmas');
        $this->nama_puskesmas = $this->Pengguna->getDataUser('nama_puskesmas');
        if (!$this->IsPostBack&&!$this->IsCallBack) {	
                                                              
        }
	}   
}
?>