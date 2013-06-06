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
/*
 $_SESSION['Name'] = $result['Nome'];
$_SESSION['Email'] = $result['email'];
$_SESSION['reToken'] = $result['reToken'];
$_SESSION['TestStart'] = 0;
*/

if ( isset ($_SESSION['Token']) &&
	 isset($_SESSION['reToken']) &&
	 isset($_REQUEST['QuestionID']) &&
	 isset($_REQUEST['Answer']) &&
	 isset($_REQUEST['Time']) &&
	 $_REQUEST['Candidato'] === $_SESSION['reToken'] 
)
{

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
		$query = $db->prepare("SELECT c.idCandidato FROM HITKProcess.Candidato as c WHERE c.reToken = :reToken AND c.email = :Email");
		$query->bindParam(':reToken', $_SESSION['reToken'], PDO::PARAM_STR, 41);
		$query->bindParam(':Email', $_SESSION['Email'], PDO::PARAM_STR, 60);
	
		$query->execute();
	
		$result = $query->fetch();
		
		// Inserir resposta na entidade Resposta.
		$InsertAnswer = "INSERT INTO HITKProcess.Resposta ";
		if ( $_REQUEST['QuestionTipo'] === 'alternativa')
			$InsertAnswer .= "(Candidato, Pergunta, Time, RespostaID ) ";
		else
			$InsertAnswer .= "(Candidato, Pergunta, Time, RespostaDissertativa ) ";
		$InsertAnswer .= " VALUES (" .$result['idCandidato']. ", " .$_REQUEST['QuestionID']. ",'" .$_REQUEST['Time']. "','" .$_REQUEST['Answer']. "')";
		$DoInsert = $db->exec($InsertAnswer);
		
		//Atualizar entidade com ID's das perguntas
		
		$UpdateLastAnswer = "UPDATE HITKProcess.GenProvas as g SET ";
		$UpdateLastAnswer .= " Respondeu = 1 ";
		$UpdateLastAnswer .= " WHERE g.idCandidato = " .$result['idCandidato']. " AND";
		$UpdateLastAnswer .= " g.idPergunta = " .$_REQUEST['QuestionID'];
		
		$DoUpdate = $db->exec($UpdateLastAnswer);
		
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
		$Question->bindParam(':nCandidato', $result['idCandidato'], PDO::PARAM_INT);
		$Question->bindParam(':sAssunto', $_REQUEST['Assunto'], PDO::PARAM_STR, 15);
		$Question->execute();
		$QuestionResult = $Question->fetch();
		
		}
	catch(PDOException $e) {
		echo $e->getMessage();
		die();
	}
	
	$QuestionCode = "<pre class='highlight'>\n";
	$QuestionCode .= htmlentities($QuestionResult['Code']);
	$QuestionCode .= "</pre>\n";

	$QuestionDescription = htmlentities($QuestionResult['Questao']);

	if ($QuestionResult['Tipo'] === 'alternativa')
	{
		$query = $db->prepare("SELECT a.Alternativa, a.idAlternativa FROM HITKProcess.Alternativas as a WHERE a.idPergunta = :idPergunta");
		$query->bindParam(':idPergunta', $QuestionResult['ID'], PDO::PARAM_INT);
	
		$query->execute();
		$query->setFetchMode(PDO::FETCH_ASSOC);
		$result = $query->fetchAll();

		$FormQuestion = "";
		foreach ($result as $Qt){
			$FormQuestion .= "<input type='radio' name='test-" .$QuestionResult['ID']. " value='" .$Qt["idAlternativa"]. " onclick='NextQuestion()'>" .htmlentities($Qt["Alternativa"]);
			$FormQuestion .= "<br>"; 
		}
	} else {
			$FormQuestion = "<textarea id='test-" .$QuestionResult['ID']. " rows='6' cols='60'></textarea>";
	}
	
	echo json_encode(array('Token' => $_SESSION['Token'],
			'reToken' => $_SESSION['reToken'],
			'QuestionID' => $QuestionResult['ID'],
			'Question' => $QuestionResult['Questao'],
			'QuestionCode' => $QuestionCode,
			'FormQuestion' => $FormQuestion
			)
		);
} else {

	echo json_encode(array('Token' => $_SESSION['Token'],
			'reToken' => $_SESSION['reToken'],
			'QuestionID' => $_REQUEST['QuestionID'],
			'Answer' => $_REQUEST['Answer']
	)
	);
}
?>