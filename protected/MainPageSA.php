<?php

class MainPageSA extends MainPage {
    /**     
     * show page puskesmas [dmaster]
     */
    public $showPuskesmas=false;
    /**     
     * show page produsen [dmaster]
     */
    public $showProdusen=false;
    /**     
     * show page supplier [dmaster]
     */
    public $showPenyalur=false;
    /**     
     * show page program [dmaster]
     */
    public $showProgram=false;
    /**     
     * show page obat [dmaster]
     */
    public $showObat=false;
    /**     
     * show page LPO [permintaan]
     */
    public $showDaftarLPO=false;
    /**     
     * show page Usulan kebutuhabn [permintaan]
     */
    public $showUsulanKebutuhan=false;
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
     * show page SBBK Bebas[mutasi barang keluar]
     */
    public $showSBBKBebas=false;
    /**     
     * show page Daftar Obat Masuk[mutasi barang keluar]
     */
    public $showDetailSBBK=false;
    /**     
     * show page Daftar SBBK Baru[mutasi barang keluar]
     */
    public $showDaftarSBBK=false;
     /**     
     * show page Penghapusan Stock[mutasi barang]
     */
    public $showPenghapusanStock=false;
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
     * show page Laporan Mutasi Semester [report]
     */
    public $showReportMutasiObatSemester=false;
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
     * show page Laporan Stock Obat Puskesmas [report]
     */
    public $showReportStockObatPuskesmas=false;
    /**     
     * show page Laporan Dinamika Obat [report]
     */
    public $showReportDinamikaLogistikObat=false;
    /**     
     * show page Laporan Expire Obat [report]
     */
    public $showReportExpireObat=false;
    /**     
     * show page Laporan Keuangan Persediaan [report]
     */
    public $showReportKeuanganPersediaan=false;
    /**     
     * show page Laporan Expire Obat [report]
     */
    public $showReportDistribusiObat=false;    
    /**     
     * show page Laporan Analisa Ketersediaan [report]
     */
    public $showReportAnalisaKetersediaan=false;    
    /**     
     * show sub menu setting obat-obatan[setting]
     */
    public $showSubMenuSettingObatObatan=false;
    /**     
     * show page satuan obat[setting obat-obatan]
     */
    public $showSatuanObat=false;
    /**     
     * show page sumber dana [setting obat-obatan]
     */
    public $showSumberDana=false;
    /**     
     * show page bentuk sediaan[setting obat-obatan]
     */
    public $showBentukSediaan=false;
    /**     
     * show page bentuk farmakologi[setting obat-obatan]
     */
    public $showFarmakologi=false;
    /**     
     * show sub menu setting sistem[setting]
     */
    public $showSubMenuSettingSistem=false;
    /**     
     * show page user dinas [setting sistem]
     */
    public $showUserDinas=false;
    /**     
     * show page user puskesmas [setting sistem]
     */
    public $showUserPuskesmas=false;
    /**     
     * show page cache [setting sistem]
     */
    public $showCache=false;
    /**     
     * show page variable [setting variable]
     */
    public $showVariable=false;
	public function onLoad ($param) {		
		parent::onLoad($param);				
        if (!$this->IsPostBack&&!$this->IsCallBack) {	
                                                              
        }
	}   
}
?>