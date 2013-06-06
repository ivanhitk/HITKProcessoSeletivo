<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);
ini_set("session.use_cookies", 1);
//ini_set("session.use_only_cookies", 1);
ini_set("session.use_trans_sid", 1);

function _DoGetProof($nCandidato, $sAssunto, $stepNumber)
{
	global $QuestionResult;
	
	$host = 'localhost';
	$user = 'hitkprocess';
	$password = '@hitkprocess312';
	$dbname = 'HITKProcess';
	
	$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	
	// Com o ID do candidato faça um select no schema resgatando da entidade GenProva:
	// ID Pergunta, Respondeu, Assunto
	// Obs: Tera que fazer um Inner Join na tabela de Perguntas para resgatar o Assunto
	// O WHERE deverá ser filtrando por Candidato.
	//
	// Validar quantas respondeu e quantas falta para terminar, guardar em uma variável de sessão (respondidas, restantes, ultimoID, NextQuestionID-idGenProvas)
	// Selecionar a pergunta em montar a tela.
	
	// Questions to Answer
	$QuestionToAnswer = $db->prepare("SELECT  count(idCandidato) as Total
    					   FROM HITKProcess.GenProvas as g
    					   		LEFT JOIN HITKProcess.Perguntas as p
        						ON g.idPergunta = p.idPerguntas 
    					   WHERE g.idCandidato = :nCandidato AND
          						 p.Assunto = :sAssunto AND
          						 g.Respondeu = 0
						 ");
	$QuestionToAnswer->bindParam(':nCandidato', $nCandidato, PDO::PARAM_INT);
	$QuestionToAnswer->bindParam(':sAssunto', $sAssunto, PDO::PARAM_STR, 15);
	$QuestionToAnswer->execute();
	$QuestionToAnswerResult = $QuestionToAnswer->fetch();
	
	$setSession = "QuestionToAnswerStep" .$stepNumber;
	
	$_SESSION[$setSession] = $QuestionToAnswerResult['Total'];
	
	// Question To do
	$QuestionAnswer = $db->prepare("SELECT  count(idCandidato) as Total
    					   FROM HITKProcess.GenProvas as g
    					   		LEFT JOIN HITKProcess.Perguntas as p
        						ON g.idPergunta = p.idPerguntas
    					   WHERE g.idCandidato = :nCandidato AND
          						 p.Assunto = :sAssunto AND
          						 g.Respondeu = 1
						 ");
	$QuestionAnswer->bindParam(':nCandidato', $nCandidato, PDO::PARAM_INT);
	$QuestionAnswer->bindParam(':sAssunto', $sAssunto, PDO::PARAM_STR, 15);
	$QuestionAnswer->execute();
	$QuestionAnswerResult = $QuestionAnswer->fetch();
	
	$setSession = "QuestionAnsweredStep" .$stepNumber;
	
	$_SESSION[$setSession] = $QuestionAnswerResult['Total'];
	
	// Question to Answer
	$Question = $db->prepare("SELECT p.idPerguntas as ID,
										   p.Questao,
										   p.Tipo,
										   p.Code
									FROM HITKProcess.Perguntas as p
										LEFT JOIN HITKProcess.GenProvas as g
										ON p.idPerguntas = g.idPergunta
									WHERE g.idCandidato = :nCandidato AND
										  g.Respondeu = 0 AND
										  p.Assunto = :sAssunto
									ORDER BY g.idGenProvas LIMIT 1
								  ");	
	$Question->bindParam(':nCandidato', $nCandidato, PDO::PARAM_INT);
	$Question->bindParam(':sAssunto', $sAssunto, PDO::PARAM_STR, 15);
	$Question->execute();
	$QuestionResult = $Question->fetch();
	
	
}

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

switch ($step_number)
{
	case "1":
		$Assunto = "PHP Basic";
		$QtdQuestion = 10;
		break;
	case "2":
		$Assunto = "PHP OO";
		$QtdQuestion = 10;
		break;
	case "3":
		$Assunto = "Algoritmos";
		$QtdQuestion = 5;
		break;
	case "4":
		$Assunto = "Padroes";
		$QtdQuestion = 5;
		break;		
}

$host = 'localhost';
$user = 'hitkprocess';
$password = '@hitkprocess312';
$dbname = 'HITKProcess';
$date = new DateTime();
$date->setTimezone(new DateTimeZone('America/Sao_Paulo'));

try {

	$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	/*** INSERT data ***/
	@$db->exec("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	//$sql = "SELECT StartTest FROM HITKProcess.Candidato as c WHERE c.Token = '" . $_SESSION['Token'] . "' AND c.email = '" .$_SESSION['Email']. "'";
	//$db = beginTransaction();
	$query = $db->prepare("SELECT idCandidato, StartTest, LastTime FROM HITKProcess.Candidato as c WHERE c.Token = :Token AND c.email = :Email");
	$query->bindParam(':Token', $_SESSION['Token'], PDO::PARAM_STR, 41);
	$query->bindParam(':Email', $_SESSION['Email'], PDO::PARAM_STR, 60);

	$query->execute();

	/*
	 $FirstResult = $db->query($sql);
	$FirstResult->setFetchMode(PDO::FETCH_ASSOC);
	*/

	$result = $query->fetch();

	if ( is_null ($result['StartTest']) ||$result['StartTest'] == '0000-00-00 00:00:00')
	{
		$UpdateStartProof = "UPDATE HITKProcess.Candidato as c SET ";
		$UpdateStartProof .= "	StartTest = '" .$date->format('Y-m-d H:i:s'). "'";
		$UpdateStartProof .= " WHERE c.email = '" .$_SESSION['Email']. "' AND";
		$UpdateStartProof .= " c.Token = '" .$_SESSION['Token']. "'";
		
		$DoUpdate = $db->exec($UpdateStartProof);
		
		// Gerar PHP Basic (idPerguntas, Assunto)
		$genProof = "CALL DoGenProof(" .$result['idCandidato']. ",'" .$Assunto. "'," .$QtdQuestion. ")";
		$GenProofResult = $db->exec($genProof);
		
		_DoGetProof($result['idCandidato'], $Assunto, $step_number);
		
		$setTime = '00:00:00';
		
	} else {
		_DoGetProof($result['idCandidato'], $Assunto, $step_number);
		
		$setTime = $result['LastTime']; 
	}	

}
catch(PDOException $e) {
	echo $e->getMessage();
	die();
}

?>
<script type="text/javascript">$.SyntaxHighlighter.init();</script>
<script type="text/javascript">

	function DoRecordTime()
	{
		$('#LogTime').html('<span style="color:red"><b>Saving time</b></span>');
		var Time = "Time=" + $('#TestTime').text();
		$.ajax({
			  url: "_services/DoRecordTime.php?<?php echo htmlentities(session_name()) .'='. session_id(); ?>",
			  type: "post",
			  data: Time,
			  success: function(){
				  $('#LogTime').html('Saved');
				  setTimeout(clearLogForm,3000);			  
			  },
			  error:function(HCPresult){
				  $('#LogTime').html('<span style="color:red"><b>Err Saving</b></span>');
				  setTimeout(clearLogForm,3000);		  	
			  }   
			}); 
	}

	function clearLogForm()
	{
		$('#LogTime').html('');
	}	
	<?php 
		$QuestionNumber = "QuestionAnsweredStep" .$step_number;
	?>	
	$('#QuestionStepNumber<?php echo $step_number;?>').html('<?php echo $_SESSION[$QuestionNumber];?>');
	$('#QuestionStepTotal<?php echo $step_number;?>').html('<?php echo $QtdQuestion;?>');
		

	<?php 
	if ($QuestionResult['Tipo'] === 'alternativa')	
	{
	?>
		var TestValue = $('input:radio[name=test-<?php echo $QuestionResult['ID'];?>]:checked').val();	
		$('#wizard').smartWizard('showMessage',"Resposta: " + TestValue);
			
	<?php } else { ?>

		var TestValue = $('input[id=test-<?php echo $QuestionResult['ID'];?>]').val();
		
	<?php }	?>

	var AnswerTime = $('#TestTime').text();
	var values = "QuestionID=<?php echo $QuestionResult['ID'];?>&QuestionTipo=<?php echo $QuestionResult['Tipo'];?>&Answer=" + TestValue + "&Assunto=<?php echo $Assunto;?>&Time=" + AnswerTime + "&Candidato=<?php echo $_SESSION['reToken'];?>";

	function NextQuestion()
	{	
	
		$('#QuestionStep1').css({ opacity: 0.5 });

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
				  var NextNumber = Number($('#QuestionStepNumber<?php echo $step_number;?>').text()) + 1;
				  $('#QuestionStepNumber<?php echo $step_number;?>').html(NextNumber);
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

	$('#TestTime').html('<?php echo $setTime;?>');
	$('.counter').counter({});

	setInterval('DoRecordTime()', 60000);
	
	</script>
<h2 class="StepTitle"><?php echo $Assunto;?></h2>
<div id="QuestionStep1" class="WizardContent">
	<p id="QuestionDescription"><?php echo $QuestionResult['Questao']; ?></p>

	<div id="QuestionCode">
	<?php
		if (! is_null($QuestionResult['Code']) )	
			echo '<pre class="highlight">' .$QuestionResult['Code']; 
			echo '</pre>';
	?>
	</div>
	<br>
	<form id="FormQuestion">
	<?php 
		if ($QuestionResult['Tipo'] === 'alternativa')
		{
			$query = $db->prepare("SELECT a.Alternativa, a.idAlternativa FROM HITKProcess.Alternativas as a WHERE a.idPergunta = :idPergunta");
			$query->bindParam(':idPergunta', $QuestionResult['ID'], PDO::PARAM_INT);
						
			$query->execute();
			$query->setFetchMode(PDO::FETCH_ASSOC);
			$result = $query->fetchAll();
			
			foreach ($result as $Qt){
	?>				
			<input type="radio" name="test-<?php echo $QuestionResult['ID'];?>" value="<?php echo $Qt["idAlternativa"];?>" onclick="NextQuestion()"><?php echo $Qt["Alternativa"];?>
			<br> 
	<?php 
			}
		} else {
	?>	
			<textarea id="test-<?php echo $QuestionResult['ID'];?>" rows="6" cols="60"></textarea>
	<?php } ?>
		
	</form>
	
	<br> <br> <br>


</div>
<?php 

/*** close the database connection ***/
$db = null;
?>
