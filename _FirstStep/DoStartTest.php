<?php 
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);
ini_set("session.use_cookies", 1);
//ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid", 1);

//require_once('../lib/dbSession.inc.php');
require_once('../lib/sessiondb.inc.php');

if ( isset($_REQUEST[htmlentities(session_name())]) )
{
	session_id($_REQUEST[htmlentities(session_name())]);
}

session_start();

if (isset($_SESSION['TestStart']) && $_SESSION['TestStart'] == 0 )
{
	//session_regenerate_id(true);
	$_SESSION['TestStart'] = 1;
}

?>
<script
	type="text/javascript" src="_js/jquery.syntaxhighlighter.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
    	// Smart Wizard    	
  		$('#wizard').smartWizard({contentURL:'_services/DoTestSteps.php?<?php echo htmlentities(session_name()) ."=". session_id(); ?>',transitionEffect:'slideleft',onFinish:onFinishCallback,contentCache:false});
      
		  function onFinishCallback(){
			alert('Finish Called');
		  }     
	});

</script>

<!-- Smart Wizard -->
<div id="wizard" class="swMain">
	<ul>
		<li><a href="#step-1"> <label class="stepNumber">1</label> <span
				class="stepDesc"> PHP Basic<br /> <small>Conhecimentos PHP 
				<span id='QuestionStepNumber1'>1</span>/<span id='QuestionStepTotal1'>10</span>
				</small>
			</span>
		</a></li>
		<li><a href="#step-2"> <label class="stepNumber">2</label> <span
				class="stepDesc"> PHP OO<br /> 
				<small>Orientação Objetos 
				<span id='QuestionStepNumber2'>1</span>/<span id='QuestionStepTotal2'>10</span>
				</small>
			</span>
		</a></li>
		<li><a href="#step-3"> <label class="stepNumber">3</label> <span
				class="stepDesc"> Algorítmos<br /> 
				<small>Lógica programação 
				<span id='QuestionStepNumber3'>1</span>/<span id='QuestionStepTotal3'>5</span>
				</small>
			</span>
		</a></li>
		<li><a href="#step-4"> <label class="stepNumber">4</label> <span
				class="stepDesc"> Padrões<br /> 
				<small>Desenv. Estruturado 
				<span id='QuestionStepNumber4'>1</span>/<span id='QuestionStepTotal4'>5</span>
				</small>
			</span>
		</a></li>

	</ul>
	<div id="step-1"></div>
	<div id="step-2"></div>
	<div id="step-3"></div>
	<div id="step-4"></div>

</div>
<!-- End SmartWizard Content -->

