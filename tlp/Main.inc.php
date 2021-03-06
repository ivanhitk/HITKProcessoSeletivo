
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HITK Centro Processo Seletivo</title>
<meta name="keywords" content="HITK, Processo, Seletivo, hitk.com.br" />
<meta name="description"
	content="HITK Centro de processo seletivo � um sistema simples para aplicar testes para profissionais da �rea de TI." />
<link href="_css/HITKProcess_core.css" rel="stylesheet" type="text/css" />
<link href="_FirstStep/styles/smart_wizard.css" rel="stylesheet"
	type="text/css">
<link href="_css/jquery.counter-analog.css" media="screen"
	rel="stylesheet" type="text/css" />
<script src="http://code.jquery.com/jquery-1.4.2.js"></script>
<script src="http://code.jquery.com/ui/1.8.24/jquery-ui.js"></script>
<script type="text/javascript" src="_js/jquery.smartWizard-2.0.min.js"></script>
<script src="_js/jquery.counter.js" type="text/javascript"></script>
<script>
  
  var mainDIV;
  
    // run the currently selected effect
    function runEffect() {
      var options = {};
      // run the effect
      $( "#content_wrapper" ).effect( 'explode', options, 500, LoadTest );
    };
 
    // callback function to bring a hidden box back
    function callback() {
      setTimeout(function() {
        $( "#content_wrapper" ).removeAttr( "style" ).hide().fadeIn();
      }, 1000 );
    };
 
 	function LoadTest() {
		// Save content of main div.
		mainDIV = $('#content_wrapper').html();
		$('html, body').animate({scrollTop:$('#content_wrapper').position().top}, 'slow');
		$('#content_wrapper').html('<img src="_images/loading.gif">');
		//loadjscssfile("_FirstStep/styles/smart_wizard.css", "css");
		$('#content_wrapper').load('_FirstStep/DoStartTest.php?<?php echo htmlentities(session_name()) .'='. $AuthReply['SID']; ?>').unbind('click');
		$('#button').unbind('click');

		
		$( "#content_wrapper" ).removeAttr( "style" ).hide().fadeIn();
		$( "#content_wrapper" ).css('height', '500px');
		//$('#content_wrapper').show().fadeIn();	
		
		var MontaBarraStatus = '<ul>';
            	MontaBarraStatus += '<li><span onclick="BackMain()"><strong class="MouseHand">Voltar</strong></span></li>';
           		MontaBarraStatus += '<li>Candidato: <?php print $result['Nome'];?></li>';
           		MontaBarraStatus += '<li></li>';
            	MontaBarraStatus += '<li>Tempo: <span id="TestTime" class="counter " data-direction="up" data-format="23:59:59" >00:00:00</span></li>';
            	MontaBarraStatus += '<li><div id="LogTime"></div></li>';
        		MontaBarraStatus += '<li><span onclick="PauseTest()"><strong class="MouseHand">Pause</strong></span></li>';
        		MontaBarraStatus += '</ul>';
		$('#templatemo_menu').html(MontaBarraStatus);

		//$('.counter').counter({});
	}
 
	function loadjscssfile(filename, filetype)
	{
		if (filetype=="css") 
		{
			var fileref = document.createElement("link");
			fileref.rel = "stylesheet";
			fileref.type = "text/css";
			fileref.href = filename;
			document.getElementsByTagName("head")[0].appendChild(fileref)
		}
	}
	 
	function BackMain()
	{
     $( "#content_wrapper" ).removeAttr( "style" );
	  $('#content_wrapper').html(mainDIV);
	}

  </script>

</head>
<body>
	<div id="templatemo_header_wrapper">
		<div id="templatemo_header">
			<div id="site_title">
				<h1>
					<a href="http://hitk.com.br" target="_parent"> <img
						src="_images/HITK_logo_full.png" alt="HITK" /> <span>Centro
							processo seletivo</span>
					</a>
				</h1>
			</div>
			<p>Sistema simples para atestar de forma b�sica os conhecimentos do
				candidato na forma de perguntas espec�ficas da vaga.</p>

		</div>
		<!-- end of templatemo_header -->

	</div>
	<!-- end of templatemo_menu_wrapper -->

	<div id="templatemo_menu_wrapper">
		<div id="templatemo_menu"></div>
	</div>

	<div id="content_wrapper">

		<div id="templatemo_sidebar">

			<div class="sidebar_box">

				<h2>Candidato(a)</h2>

				<div class="news_box">
					<p class="post_info">
						Nome:
						<dfn>
							<?php print $result['Nome'];?>
						</dfn>
					</p>
					<p class="post_info">
						E-mail: <em><?php print $result['email'];?> </em>
					</p>
				</div>

				<div class="news_box">
					<p class="post_info">
						Preten��o:
						<dfn>
							R$
							<?php print $result['Pretensao'];?>
						</dfn>
					</p>
					<p class="post_info">
						Escolaridade: <em>Superior Completo</em>
					</p>
				</div>

				<div class="news_box">
					<p class="post_info">
						N�vel:
						<dfn>Desenvolvedor PHP S�nior</dfn>
					</p>
					<p class="post_info">
						Curriculum: <a
							href="curriuclum/239029384572347598237457234057.pdf"
							target="_parent">Candidato X</a>
					</p>
				</div>

			</div>
			<div class="sidebar_box_bottom"></div>

			<div class="sidebar_box">

				<h2>Quest�es</h2>
				<p>
					Algor�tmos: <strong>3</strong>
				</p>
				<p>
					Conhecimentos b�sicos PHP: <strong>10</strong>
				</p>
				<p>
					Orienta��o Objetos: <strong>10</strong>
				</p>
				<p>
					Conhecimentos MVC: <strong>5</strong>
				</p>
				<p>
					Protocolo HTTP: <strong>5</strong>
				</p>
				<p>
					Sess�es: <strong>5</strong>
				</p>
				<p>
					Seguran�a: <strong>5</strong>
				</p>
				<p>
					Desenv. Estruturado: <strong>10</strong>
				</p>

				<div class="cleaner"></div>

			</div>
			<div class="sidebar_box_bottom"></div>

			<div class="sidebar_box">

				<h2>Iniciar Prova!</h2>
				<p>Leia a informa��es na p�gina e se tudo estiver corretamente
					inserido e entendido clique no bot�o abaixo para iniciar o
					processo.</p>
				<br /> <input type="button" id='button' name="Iniciar Prova"
					value="Iniciar Prova" onclick="runEffect()" />

				<div class="cleaner"></div>

			</div>
			<div class="sidebar_box_bottom"></div>

		</div>
		<!-- end of sidebar -->

		<div id="templatemo_content">

			<div class="content_box">

				<h2>Desenvolvedor PHP</h2>

				<p>A vaga � para um perfil autodidata, que saiba correr atr�s de
					solu��es, que se esforce para entender o c�digo sem ficar
					perguntando a todo minuto. Claro que d�vidas sempre aparecem, mas
					esperamos que d�vidas simples da linguagem o candidato seja capaz
					de pesquisar e aprender por si s�, seguindo o padr�o do projeto. Os
					itens exigidos para a prova s�o:</p>

				<div class="cleaner_h20"></div>

				<div class="image_fl">
					<img src="_images/templatemo_images01.jpg" alt="image" />
				</div>

				<div class="section_w250 float_r">

					<ul class="list_01">
						<li>L�gica programa��o/Algor�tmos</li>
						<li>Conhecimentos linguagem PHP</li>
						<li>Orienta��o Objetos PHP</li>
						<li>MVC: Model-View-Controller</li>
						<li>Protocolo HTTP</li>
						<li>Seguran�a PHP</li>
						<li>Desenv. Estruturado</li>
					</ul>

				</div>

				<div class="cleaner"></div>
			</div>
			<div class="content_box_bottom"></div>

			<div class="content_box">

				<h2>Sobre o processo seletivo</h2>
				<p>O processo seletivo da HITK Consultoria � composto de 3 etapas</p>
				<ul>
					<li>Prova te�rica online</li>
					<li>Prova pr�tica online</li>
					<li>Entrevista com o gerente do projeto por confer�ncia</li>
				</ul>
				<div class="section_w250 float_l">
					<h3>Prova te�rica</h3>
					<p>A prova te�rica ter� o equivalente a 60 quest�es em m�dia, ser�
						permitido a busca de solu��es em mecanismos de pesquisa, assim
						como parar a prova e iniciar em outro momento (todo esse processo
						ser� contabilizado).</p>

				</div>

				<div class="section_w250 float_r">
					<h3>Prova pr�tica</h3>
					<p>A prova pr�tica ter� o equivalente a 5 atividades em um servidor
						GNU/Linux com Apache ou NGINX, ser� permitido a busca de solu��es
						em mecanismos de pesquisa, o candidato ter� atividades na
						linguagem PHP, estrutura de diret�rios e acesso subversion.</p>

				</div>


				<div class="cleaner"></div>
			</div>
			<div class="content_box_bottom"></div>

		</div>
		<!-- end of content -->

		<div class="cleaner"></div>

	</div>

	<div id="templatemo_footer_wrapper">

		<div id="templatemo_footer">
			Copyright � 2013 <a href="hitk.com.br">HITK Consultoria</a> |
			Designed by <a href="http://hitk.com.br" target="_parent">HITK
				Consultoria</a> | Validate <a
				href="http://validator.w3.org/check?uri=referer">XHTML</a> &amp; <a
				href="http://jigsaw.w3.org/css-validator/check/referer">CSS</a>
		</div>

	</div>
</body>
</html>
<?php 