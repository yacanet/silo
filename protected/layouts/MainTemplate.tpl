<!DOCTYPE html>
<html>
<com:THead>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />	
	<!-- bootstrap --><!-- bootstrap -->
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/bootstrap/bootstrap.min.css" />
	<!-- libraries -->
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/libs/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/libs/nanoscroller.css" />
	<!-- global styles -->
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/compiled/theme_styles.css" />
	<!-- this page specific styles -->
	<com:TContentPlaceHolder ID="csscontent" />   	
    <link type="image/x-icon" href="resources/favicon.png" rel="shortcut icon"/>
	<!-- google font libraries -->
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>
	<!--[if lt IE 9]>
		<script src="<%=$this->Page->Theme->baseUrl%>/js/html5shiv.js"></script>
		<script src="<%=$this->Page->Theme->baseUrl%>/js/respond.min.js"></script>
	<![endif]-->
</com:THead>
<body class="theme-white pace-done fixed-header">
<com:TForm id="mainform">
<div id="theme-wrapper">
    <header class="navbar" id="header-navbar">
        <div class="container">
            <a href="<%=$this->Page->constructUrl('Home',true)%>" id="logo" class="navbar-brand">
                <img src="<%=$this->Page->setup->getAddress()%>/resources/logosilo.png" alt="" class="normal-logo logo-white"/>                
            </a>				
            <div class="clearfix">
                <button class="navbar-toggle" data-target=".navbar-ex1-collapse" data-toggle="collapse" type="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="fa fa-bars"></span>
                </button>
                <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
                    <ul class="nav navbar-nav pull-left">
                        <li>
                            <a class="btn" id="make-small-nav">
                                <i class="fa fa-bars"></i>
                            </a>
                        </li>												
                        <com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='sa'%>">                            
                        <li class="dropdown hidden-xs">                            
                            <a class="btn dropdown-toggle" data-toggle="dropdown">
                                Akses Cepat
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="item">
                                    <a href="<%=$this->Page->constructUrl('mutasibarang.SBBMBaru',true)%>">
                                        <i class="fa fa-file"></i> 
                                        SBBM
                                    </a>
                                </li>								
                                <li class="item">
                                    <a href="<%=$this->Page->constructUrl('mutasibarang.SBBKBaru',true)%>">
                                        <i class="fa fa-file"></i> 
                                        SBBK
                                    </a>
                                </li>
                                <li class="item">
                                    <a href="<%=$this->Page->constructUrl('mutasibarang.DaftarSBBK',true)%>">
                                        <i class="fa fa-file"></i> 
                                        Daftar SBBK
                                    </a>
                                </li>
                            </ul>                            
                        </li>
                        </com:TLiteral>
                        <li class="dropdown hidden-xs">
                            <a class="btn dropdown-toggle" data-toggle="dropdown">
                                Tahun Kegiatan <com:TActiveLabel ID="labelUmumTahunKegiatan" />
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="item">
                                    <span class="pull-left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
                                <com:TActiveCustomDatePicker ID="cmbSettingUmumTahun" DateFormat="yyyy" Culture="id"  FromYear="<%=$_SESSION['awal_tahun_sistem']%>" UpToYear="<%=date('Y')+1%>" InputMode="DropDownList" OnCallBack="changeSettingUmumTahun" ShowCalendar="false">
                                        <prop:ClientSide.OnPreDispatch>                                                                   
                                            $('loading').show(); 
                                            $('<%=$this->cmbSettingUmumTahun->ClientId%>').disabled='disabled';						
                                        </prop:ClientSide.OnPreDispatch>
                                        <prop:ClientSide.OnLoading>
                                            $('<%kc=$this->cmbSettingUmumTahun->ClientId%>').disabled='disabled';						
                                        </prop:ClientSide.OnLoading>
                                        <prop:ClientSide.OnComplete>																	                                    						                                                                            
                                            $('<%=$this->cmbSettingUmumTahun->ClientId%>').disabled='';						
                                            $('loading').hide(); 
                                        </prop:ClientSide.OnComplete>
                                    </com:TActiveCustomDatePicker>
                                </li>								
                            </ul>
                        </li>
                        <li>                            
                            <div id="loading" style="display: none">
                                Please wait while process your request !!!
                            </div>
                        </li>
                    </ul>
                </div>				
                <div class="nav-no-collapse pull-right" id="header-nav">
                    <ul class="nav navbar-nav pull-right">
                        <li style="color:white">
                            <%=$this->Page->TGL->tanggal('d F Y') %>
                        </li>
                        <li class="hidden-xxs">
                            <a class="btn" href="<%=$this->Page->constructUrl('Logout')%>">
                                <i class="fa fa-power-off"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div id="page-wrapper" class="container">
        <div class="row">
            <div id="nav-col">
                <section id="col-left" class="col-left-nano">
                    <div id="col-left-inner" class="col-left-nano-content">
                        <div id="user-left-box" class="clearfix hidden-sm hidden-xs dropdown profile2-dropdown">
                            <img alt="<%=$this->Page->Pengguna->getUsername()%>" src="<%=$this->Page->setup->getAddress()%>/<%=$_SESSION['foto']%>" OnError="no_photo(this,'<%=$this->Page->setup->getAddress()%>/resources/userimages/no_photo.png')" />
                            <div class="user-box">
                                <span class="name">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <%=$this->Page->Pengguna->getUsername()%> 
                                        <i class="fa fa-angle-down"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<%=$this->Page->constructUrl('setting.Profiles',true)%>"><i class="fa fa-user"></i>Profiles</a></li>
                                        <li><a href="<%=$this->Page->constructUrl('Logout')%>"><i class="fa fa-power-off"></i>Logout</a></li>
                                    </ul>
                                </span>									
                            </div>
                        </div>
                        <com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='sa'%>">
                        <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                            <ul class="nav nav-pills nav-stacked">									
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    NAVIGASI
                                </li>
                                <li<%=$this->Page->showDashboard==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('Home',true)%>">
                                        <i class="fa fa-dashboard"></i>
                                        <span>Dashboard</span>											
                                    </a>                                        
                                </li>
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    DATA MASTER
                                </li>
                                <li<%=$this->Page->showPuskesmas==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('dmaster.Puskesmas',true)%>">
                                        <i class="fa fa-hospital-o"></i>
                                        <span>Puskesmas</span>											
                                    </a>                                        
                                </li>
                                <li<%=$this->Page->showProdusen==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('dmaster.Produsen',true)%>">
                                        <i class="fa fa-institution"></i>
                                        <span>Produsen</span>											
                                    </a>                                        
                                </li>
                                <li<%=$this->Page->showPenyalur==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('dmaster.Penyalur',true)%>">
                                        <i class="fa fa-ambulance"></i>
                                        <span>Penyalur</span>											
                                    </a>                                        
                                </li>
                                <li<%=$this->Page->showProgram==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('dmaster.Program',true)%>">
                                        <i class="fa fa-file-code-o"></i>
                                        <span>Program</span>											
                                    </a>                                        
                                </li>
                                <li<%=$this->Page->showObat==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('dmaster.Obat',true)%>">
                                        <i class="fa fa-medkit"></i>
                                        <span>Obat</span>											
                                    </a>                                        
                                </li>
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    PERMINTAAN
                                </li>
                                <li<%=$this->Page->showDaftarLPO==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('permintaan.DaftarLPO',true)%>">
                                        <i class="fa fa-file-text"></i>
                                        <span>Daftar LPLPO</span>											
                                    </a>                                        
                                </li> 
                                <li<%=$this->Page->showUsulanKebutuhan==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('permintaan.UsulanKebutuhan',true)%>">
                                        <i class="fa fa-table"></i>
                                        <span>Usulan Kebutuhan</span>											
                                    </a>                                        
                                </li>
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    MUTASI BARANG
                                </li>
                                <li<%=$this->Page->showSubMenuMutasiBarangMasuk==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-forward"></i>
                                        <span>Barang Masuk</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.SBBMBaru',true)%>"<%=$this->Page->showSBBMBaru==true?' class="active"':''%>>
                                                SBBM
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DaftarSBBM',true)%>"<%=$this->Page->showDaftarSBBM==true?' class="active"':''%>>
                                                Daftar SBBM
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DetailSBBM',true)%>"<%=$this->Page->showDetailSBBM==true?' class="active"':''%>>
                                                Detail Item SBBM
                                            </a>
                                        </li>
                                    </ul>
                                </li>                                                               
                                <li<%=$this->Page->showSubMenuMutasiBarangKeluar==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-mail-reply-all"></i>
                                        <span>Barang Keluar</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.SBBKBaru',true)%>"<%=$this->Page->showSBBKBaru==true?' class="active"':''%>>
                                                SBBK
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.SBBKBebas',true)%>"<%=$this->Page->showSBBKBebas==true?' class="active"':''%>>
                                                SBBK Bebas
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DaftarSBBK',true)%>"<%=$this->Page->showDaftarSBBK==true?' class="active"':''%>>
                                                Daftar SBBK
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DetailSBBK',true)%>"<%=$this->Page->showDetailSBBK==true?' class="active"':''%>>
                                                Detail Item SBBK
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li<%=$this->Page->showPenghapusanStock==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('mutasibarang.PenghapusanStock',true)%>">
                                        <i class="fa fa-times-circle-o"></i>
                                        <span>Penghapusan Stock</span>											
                                    </a>                                        
                                </li>
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    REPORT
                                </li>
                                <li<%=$this->Page->showSubMenuReportMutasiObat==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-exchange"></i>
                                        <span>Mutasi Obat</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.MutasiObatBulanan',true)%>"<%=$this->Page->showReportMutasiObatBulanan==true?' class="active"':''%>>
                                                Mutasi Obat Bulanan
                                            </a>
                                        </li>                                        
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.MutasiObatSemester',true)%>"<%=$this->Page->showReportMutasiObatSemester==true?' class="active"':''%>>
                                                Mutasi Obat Semester
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.MutasiObatTahunan',true)%>"<%=$this->Page->showReportMutasiObatTahunan==true?' class="active"':''%>>
                                                Mutasi Obat Tahunan
                                            </a>
                                        </li>
                                    </ul>
                                </li> 
                                <li<%=$this->Page->showSubMenuReportStock==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-spoon"></i>
                                        <span>Stock Obat</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.StockObatPuskesmas',true)%>"<%=$this->Page->showReportStockObatPuskesmas==true?' class="active"':''%>>
                                                Stock Obat Puskesmas
                                            </a>
                                        </li>                                                                                
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.PerpetualStock',true)%>"<%=$this->Page->showReportPerpetualStock==true?' class="active"':''%>>
                                                Perpetual Stock
                                            </a>
                                        </li>                                                                                
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.DinamikaLogistikObat',true)%>"<%=$this->Page->showReportDinamikaLogistikObat==true?' class="active"':''%>>
                                                Dinamika Logistik
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.ExpireObat',true)%>"<%=$this->Page->showReportExpireObat==true?' class="active"':''%>>
                                                Expire Obat
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.KeuanganPersediaan',true)%>"<%=$this->Page->showReportKeuanganPersediaan==true?' class="active"':''%>>
                                                Keuangan Persediaan
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li<%=$this->Page->showReportDistribusiObat==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('report.DistribusiObat',true)%>">
                                        <i class="fa fa-plane"></i>
                                        <span>Distribusi Obat</span>											
                                    </a>                                        
                                </li>                                
                                <li<%=$this->Page->showReportAnalisaKetersediaan==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('report.AnalisaKetersediaan',true)%>">
                                        <i class="fa fa-cubes"></i>
                                        <span>Analisa Ketersediaan</span>											
                                    </a>                                        
                                </li>                                
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    SETTING
                                </li>
                                <li<%=$this->Page->showVariable==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('setting.Variables',true)%>">
                                        <i class="fa fa-legal"></i>
                                        <span>Variables</span>											
                                    </a>                                        
                                </li> 
                                <li<%=$this->Page->showSubMenuSettingObatObatan==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-medkit"></i>
                                        <span>Obat-Obatan</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('setting.SumberDana',true)%>"<%=$this->Page->showSumberDana==true?' class="active"':''%>>
                                                Sumber Dana
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('setting.SatuanObat',true)%>"<%=$this->Page->showSatuanObat==true?' class="active"':''%>>
                                                Satuan
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('setting.BentukSediaan',true)%>"<%=$this->Page->showBentukSediaan==true?' class="active"':''%>>
                                                Bentuk Sediaan
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('setting.Farmakologi',true)%>"<%=$this->Page->showFarmakologi==true?' class="active"':''%>>
                                                Farmakologi
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li<%=$this->Page->showSubMenuSettingSistem==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-cog"></i>
                                        <span>Sistem</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a<%=$this->Page->showUserDinas==true ? ' class="active" ':''%> href="<%=$this->Page->constructUrl('setting.UserDinas',true)%>">
                                                User Dinas
                                            </a>
                                        </li>
                                        <li>
                                            <a<%=$this->Page->showUserPuskesmas==true ? ' class="active" ':''%> href="<%=$this->Page->constructUrl('setting.UserPuskesmas',true)%>">
                                                User Puskesmas
                                            </a>
                                        </li>
                                        <li>                                                
                                            <a href="<%=$this->Page->constructUrl('setting.Cache',true)%>"<%=$this->Page->showCache==true?' class="active"':''%>>                                                    
                                                Cache											
                                            </a>                                        
                                        </li>
                                    </ul>
                                </li>                                                                    
                            </ul>
                        </div>
                        </com:TLiteral>
                        <com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='ad'%>">
                        <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                            <ul class="nav nav-pills nav-stacked">									
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    NAVIGASI
                                </li>
                                <li<%=$this->Page->showDashboard==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('Home',true)%>">
                                        <i class="fa fa-dashboard"></i>
                                        <span>Dashboard</span>											
                                    </a>                                        
                                </li>  
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    DATA MASTER
                                </li>
                                <li<%=$this->Page->showUnitPuskesmas==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('dmaster.UnitPuskesmas',true)%>">
                                        <i class="fa fa-hospital-o"></i>
                                        <span>Unit Puskesmas</span>											
                                    </a>                                        
                                </li>
                                <li<%=$this->Page->showObatPuskesmas==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('dmaster.ObatPuskesmas',true)%>">
                                        <i class="fa fa-medkit"></i>
                                        <span>Obat Puskesmas</span>											
                                    </a>                                        
                                </li>  
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    MUTASI BARANG
                                </li>
                                <li<%=$this->Page->showSubMenuMutasiBarangMasuk==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-forward"></i>
                                        <span>Barang Masuk</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.SBBMBaru',true)%>"<%=$this->Page->showSBBMBaru==true?' class="active"':''%>>
                                                SBBM
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DaftarSBBM',true)%>"<%=$this->Page->showDaftarSBBM==true?' class="active"':''%>>
                                                Daftar SBBM
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DetailSBBM',true)%>"<%=$this->Page->showDetailSBBM==true?' class="active"':''%>>
                                                Detail Item SBBM
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li<%=$this->Page->showSubMenuMutasiBarangKeluar==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-mail-reply-all"></i>
                                        <span>Barang Keluar</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.SBBKBaru',true)%>"<%=$this->Page->showSBBKBaru==true?' class="active"':''%>>
                                                SBBK
                                            </a>
                                        </li>                                        
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DaftarSBBK',true)%>"<%=$this->Page->showDaftarSBBK==true?' class="active"':''%>>
                                                Daftar SBBK
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('mutasibarang.DetailSBBK',true)%>"<%=$this->Page->showDetailSBBK==true?' class="active"':''%>>
                                                Detail Item SBBK
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li<%=$this->Page->showPemakaianObatUnit==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('mutasibarang.PemakaianObatUnit',true)%>">
                                        <i class="fa fa-slack"></i>
                                        <span>Pemakaian Obat Unit</span>											
                                    </a>                                        
                                </li>
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    PERMINTAAN
                                </li>
                                <li<%=$this->Page->showLPOBaru==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('permintaan.LPOBaru',true)%>">
                                        <i class="fa fa-file-text"></i>
                                        <span>LPLPO</span>											
                                    </a>                                        
                                </li>  
                                <li<%=$this->Page->showDaftarLPO==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('permintaan.DaftarLPO',true)%>">
                                        <i class="fa fa-file-text"></i>
                                        <span>Daftar LPLPO</span>											
                                    </a>                                        
                                </li> 
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    REPORT
                                </li>
                                <li<%=$this->Page->showSubMenuReportMutasiObat==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-exchange"></i>
                                        <span>Mutasi Obat</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.MutasiObatBulanan',true)%>"<%=$this->Page->showReportMutasiObatBulanan==true?' class="active"':''%>>
                                                Mutasi Obat Bulanan
                                            </a>
                                        </li>                                                                                
                                    </ul>
                                </li> 
                                <li<%=$this->Page->showSubMenuReportStock==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-spoon"></i>
                                        <span>Stock Obat</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.PerpetualStock',true)%>"<%=$this->Page->showReportPerpetualStock==true?' class="active"':''%>>
                                                Perpetual Stock
                                            </a>
                                        </li>                                                                                                                        
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.ExpireObat',true)%>"<%=$this->Page->showReportExpireObat==true?' class="active"':''%>>
                                                Expire Obat
                                            </a>
                                        </li>                                        
                                    </ul>
                                </li>
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    SETTING
                                </li>
                                <li<%=$this->Page->showSubMenuSettingSistem==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-cog"></i>
                                        <span>Sistem</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">                                               
                                        <li>
                                            <a<%=$this->Page->showUserPuskesmas==true ? ' class="active" ':''%> href="<%=$this->Page->constructUrl('setting.UserPuskesmas',true)%>">
                                                User Puskesmas
                                            </a>
                                        </li>                                                
                                    </ul>
                                </li>                                    
                            </ul>
                        </div>
                        </com:TLiteral>
                        <com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='sp'%>">
                        <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                            <ul class="nav nav-pills nav-stacked">									
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    NAVIGASI
                                </li>
                                <li<%=$this->Page->showDashboard==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('Home',true)%>">
                                        <i class="fa fa-dashboard"></i>
                                        <span>Dashboard</span>											
                                    </a>                                        
                                </li>                                        
                            </ul>
                        </div>
                        </com:TLiteral>
                        <com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='d'%>">
                        <div class="collapse navbar-collapse navbar-ex1-collapse" id="sidebar-nav">
                            <ul class="nav nav-pills nav-stacked">									
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    NAVIGASI
                                </li>
                                <li<%=$this->Page->showDashboard==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('Home',true)%>">
                                        <i class="fa fa-dashboard"></i>
                                        <span>Dashboard</span>											
                                    </a>                                        
                                </li>                                
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    PERMINTAAN
                                </li>
                                <li<%=$this->Page->showDaftarLPO==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('permintaan.DaftarLPO',true)%>">
                                        <i class="fa fa-file-text"></i>
                                        <span>Daftar LPLPO</span>											
                                    </a>                                        
                                </li> 
                                <li<%=$this->Page->showUsulanKebutuhan==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('permintaan.UsulanKebutuhan',true)%>">
                                        <i class="fa fa-table"></i>
                                        <span>Usulan Kebutuhan</span>											
                                    </a>                                        
                                </li>                                
                                <li class="nav-header nav-header-first hidden-sm hidden-xs">
                                    REPORT
                                </li>
                                <li<%=$this->Page->showSubMenuReportMutasiObat==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-exchange"></i>
                                        <span>Mutasi Obat</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.MutasiObatBulanan',true)%>"<%=$this->Page->showReportMutasiObatBulanan==true?' class="active"':''%>>
                                                Mutasi Obat Bulanan
                                            </a>
                                        </li>                                        
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.MutasiObatTahunan',true)%>"<%=$this->Page->showReportMutasiObatTahunan==true?' class="active"':''%>>
                                                Mutasi Obat Tahunan
                                            </a>
                                        </li>
                                    </ul>
                                </li> 
                                <li<%=$this->Page->showSubMenuReportStock==true?' class="active"':''%>>
                                    <a href="#" class="dropdown-toggle">
                                        <i class="fa fa-spoon"></i>
                                        <span>Stock Obat</span>
                                        <i class="fa fa-angle-right drop-icon"></i>
                                    </a>
                                    <ul class="submenu">
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.PerpetualStock',true)%>"<%=$this->Page->showReportPerpetualStock==true?' class="active"':''%>>
                                                Perpetual Stock
                                            </a>
                                        </li>                                                                                
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.DinamikaLogistikObat',true)%>"<%=$this->Page->showReportDinamikaLogistikObat==true?' class="active"':''%>>
                                                Dinamika Logistik
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.ExpireObat',true)%>"<%=$this->Page->showReportExpireObat==true?' class="active"':''%>>
                                                Expire Obat
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<%=$this->Page->constructUrl('report.KeuanganPersediaan',true)%>"<%=$this->Page->showReportKeuanganPersediaan==true?' class="active"':''%>>
                                                Keuangan Persediaan
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li<%=$this->Page->showReportDistribusiObat==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('report.DistribusiObat',true)%>">
                                        <i class="fa fa-plane"></i>
                                        <span>Distribusi Obat</span>											
                                    </a>                                        
                                </li>                                
                                <li<%=$this->Page->showReportAnalisaKetersediaan==true?' class="active"':''%>>
                                    <a href="<%=$this->Page->constructUrl('report.AnalisaKetersediaan',true)%>">
                                        <i class="fa fa-cubes"></i>
                                        <span>Analisa Ketersediaan</span>											
                                    </a>                                        
                                </li>                                                                                              
                            </ul>
                        </div>
                        </com:TLiteral>
                    </div>
                </section>
                <div id="nav-col-submenu"></div>
            </div>
            <div id="content-wrapper">
                <div class="row">
                    <div class="col-lg-12">							
                        <div class="row">
                            <div class="col-lg-12">
                                <ol class="breadcrumb">
                                    <li><a href="<%=$this->Page->constructUrl('Home',true)%>">Home</a></li>
                                    <com:TContentPlaceHolder ID="modulebreadcrumb" />
                                </ol>
                                <h1><com:TContentPlaceHolder ID="moduleheader" /></h1>
                            </div>
                        </div>							
                        <com:TContentPlaceHolder ID="maincontent" />                                                    
                    </div>
                </div>					
                <footer id="footer-bar" class="row">
                    <p id="footer-copyright" class="col-xs-12">                        
                        Sistem informasi Logistik Obat (SILO) Powered by <a href="http://www.yacanet.com">Yacanet.com</a>
                        <com:TJavascriptLogger />
                    </p>
                </footer>
            </div>
        </div>
    </div>
</div>	
</com:TForm>
<com:TLiteral Visible="<%=$this->Page->Pengguna->getTipeUser()=='ad'%>">
<div id="config-tool" class="closed">
    <a id="config-tool-cog">
        <i class="fa fa-cog"></i>
    </a>
    <div id="config-tool-options">
        <h4>DATA UPT</h4>        
        <br/>
        <h4 style="font-size:12px"><strong>Nama Puskesmas:</strong></h4>
        <ul id="skin-colors" class="clearfix">
            <li style="font-size:10px">
                <%=$this->Page->Pengguna->getDataUser('nama_puskesmas')%>                
            </li>            
        </ul>
    </div>
</div>
</com:TLiteral>
<!-- global scripts -->	
<script src="<%=$this->Page->Theme->baseUrl%>/js/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery.noConflict();
</script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/jquery.nanoscroller.min.js" type="text/javascript"></script>	
<!-- this page specific scripts -->	
<com:TContentPlaceHolder ID="jscontent" />
<!-- theme scripts -->
<script src="<%=$this->Page->Theme->baseUrl%>/js/scripts.js" type="text/javascript"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/pace.min.js" type="text/javascript"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/silo.js" type="text/javascript"></script>
<!-- this page specific inline scripts -->	
<com:TContentPlaceHolder ID="jsinlinecontent" />
</body>
</html>