
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

if (isset($_SESSION['TestStart']) != 1 )
{
	//session_regenerate_id(true);
	include_once '../tlp/Main.Auth.Error.php';
	die();
}

$step_number = $_REQUEST["step_number"];

include('../lib/edb/edb.class.php');

/* Connect to an ODBC database using driver invocation
 $dsn = 'mysql:dbname=wellclinic_ic;host=mysql.hitk.com.br';
*/
$host = 'localhost';
$user = 'hitkprocess';
$password = '@hitkprocess312';
$database = 'HITKProcess';
$date = new DateTime();
$date->setTimezone(new DateTimeZone('America/Sao_Paulo'));

$db = new edb($host,$user,$password,$database);
@$db->q("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

//$sql = "SELECT email, name, token FROM HITKProcess.Candidato as c WHERE c.token = AES_ENCRYPT('" .$_REQUEST['password']. "','@Hitk.....3321') AND email = '" .$_REQUEST['email']. "'";
$sql = "SELECT StartTime FROM HITKProcess.Candidato as c WHERE c.token ='" . $_SESSION['token'] . "' AND c.email = '" .$_SESSION['Email']. "'";
$result = $db->line($sql);

if ( is_null($result['StartTime']) )
{
	$query = "UPDATE HITKPRocess.Candidato SET ";
	$query .= "	StartTest = '" .$date->format('Y-m-d H:i:s'). "'";
	$query .= " WHERE email = '" .$_SESSION['Email']. "' AND";
	$query .= " WHERE Token = '" .$_SESSION['Token']. "'";
}

$result = $db->line($query);

if ( !$result )
{
	echo "Date: " .$date->format('Y-m-d H:i:s'). "<br>";
	echo "Result: " .$result. "<br>";
	echo "Problemas atualizar dados usu�rio<br>";
	die();
}


if ( $step_number == '1' )
{
	?>
<script type="text/javascript">$.SyntaxHighlighter.init();</script>
<script type="text/javascript">
	function NextQuestion()
	{	

		var TestValue = $('input:radio[name=test-101]:checked').val();	
		$('#wizard').smartWizard('showMessage',"Resposta: " + TestValue);
		$('#QuestionStep1').css({ opacity: 0.5 });
		
		var values = "QuestionID=101&Answer=" + TestValue;
		
			$.ajax({
			  url: "_services/DoNextQuestion.php?<?php echo htmlentities(session_name()) .'='. session_id(); ?>",
			  type: "post",
			  data: values,
			  success: function(HCPresult){
				  var HCPdata = jQuery.parseJSON(HCPresult);
				  //alert( HCPdata.toSource() );
				  $('#QuestionDescription').html(HCPdata.Question);
				  $('#QuestionCode').html(HCPdata.QuestionCode);
				  $('#FormQuestion').html(HCPdata.FormQuestion);
				  $('#QuestionStepNumber').html('2');
				  $('#QuestionCode').syntaxHighlight();
				  $('#QuestionStep1').animate({scrollTop:0}, 'slow');
				  $('#QuestionStep1').css({ opacity: 1 });				  
			  },
			  error:function(HCPresult){
				  var HCPdata = jQuery.parseJSON(HCPresult);			  
				  alert("Failure:" + HCPdata.Response);
			  }   
			}); 	
	}
	</script>
<h2 class="StepTitle">PHP Basic</h2>
<div id="QuestionStep1" class="WizardContent">
	<p id="QuestionDescription">What is the output of the following PHP
		code?</p>

	<div id="QuestionCode">
		<pre class="highlight">
					$a = null;
					$b = null;
					$c = null;
					$d = null;
				
					if($a && !$b) {
					  if(!!$c && !$d) {
						if($d && ($a || $c)) {
						  if(!$d && $b) {
							$number = 1;
						  } else {
							$number = 2;
						  }
						} else {
						  $number = 3;
						}
					  } else {
						$number = 4;
					  }
					} else {
					  $number = 5;
					}
				</pre>
	</div>
	<form id="FormQuestion">
		<input type="radio" name="test-101" value="FOO"
			onclick="NextQuestion()">FOO<br> <input type="radio" name="test-101"
			value="100" onclick="NextQuestion()">100<br> <input type="radio"
			name="test-101" value="200" onclick="NextQuestion()">200<br> <input
			type="radio" name="test-101" value="20" onclick="NextQuestion()">20<br>
		<input type="radio" name="test-101" value="10"
			onclick="NextQuestion()">10
	</form>
	<br> <br> <br>


</div>
<?php 
} else if ( $step_number == '3' )
{
	echo "<h1>Teste 3</h1>";
	echo "Passo três, aqui entra o passo um.<br>";
	echo "Descrição<br>";
}

?>
