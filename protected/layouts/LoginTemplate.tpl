<!DOCTYPE html>
<html lang="id">
<com:THead>    
    <meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />        
    <!-- bootstrap -->
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/bootstrap/bootstrap.min.css" />		
	<!-- libraries -->
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/libs/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/libs/nanoscroller.css" />
	<!-- global styles -->
	<link rel="stylesheet" type="text/css" href="<%=$this->Page->Theme->baseUrl%>/css/compiled/theme_styles.css" />
	<link type="image/x-icon" href="resources/favicon.png" rel="shortcut icon"/>
	<!-- google font libraries -->
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300|Titillium+Web:200,300,400' rel='stylesheet' type='text/css'>
	<!--[if lt IE 9]>
		<script src="<%=$this->Page->Theme->baseUrl%>/js/html5shiv.js"></script>
		<script src="<%=$this->Page->Theme->baseUrl%>/js/respond.min.js"></script>
	<![endif]-->
</com:THead>
<body id="login-page-full">    
    <div id="login-full-wrapper">
		<div class="container">
			<div class="row">
                <div id="loading" style="display:none">
                    Please wait while process your request !!!
                </div>
				<div class="col-xs-12">                    
					<div id="login-box">
						<div id="login-box-holder">
							<div class="row">
								<div class="col-xs-12">
									<header id="login-header">
										<div id="login-logo">
                                            <img src="<%=$this->Page->setup->getAddress()%>/resources/logosilo.png" alt=""/>
										</div>
									</header>
									<div id="login-box-inner">
                                        <com:TForm Attributes.role="form">   
                                            <com:TContentPlaceHolder ID="content" />
                                        </com:TForm>										
									</div>
								</div>
							</div>
						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- global scripts -->
<script src="<%=$this->Page->Theme->baseUrl%>/js/demo-skin-changer.js"></script> <!-- only for demo -->
<script src="<%=$this->Page->Theme->baseUrl%>/js/jquery.js"></script>
<script type="text/javascript">
    jQuery.noConflict();
</script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/bootstrap.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/jquery.nanoscroller.min.js"></script>
<script src="<%=$this->Page->Theme->baseUrl%>/js/demo.js"></script> <!-- only for demo -->
<!-- theme scripts -->
<script src="<%=$this->Page->Theme->baseUrl%>/js/scripts.js"></script>
<!-- this page specific inline scripts -->
</body>
</html>
