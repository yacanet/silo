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
	<!-- google font libraries -->
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>
	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->
</com:THead>
<body class="theme-white pace-done">
<com:TForm id="mainform">
<div id="theme-wrapper">    
    <header class="navbar" id="header-navbar">
        <div class="container">            			
            <div class="clearfix">                
                <div class="nav-no-collapse navbar-left pull-left hidden-sm hidden-xs">
                    <ul class="nav navbar-nav pull-left">                                                
                        <li>                            
                            <div id="loading" style="display: none">
                                Please wait while process your request !!!
                            </div>
                        </li>
                    </ul>
                </div>				                
            </div>
        </div>
    </header>
    <div id="page-wrapper" class="container">
        <div class="row">
            <div class="col-lg-12">							        
                <com:TContentPlaceHolder ID="maincontent" />                                                    
            </div>
        </div>	
    </div>
</div>	
<com:TJavascriptLogger />
</com:TForm>
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