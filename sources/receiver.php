<?php

include '../classes/TextareaProcesser.class.php';
include '../classes/Fraction.class.php';
include '../classes/SimplexTableau.class.php';
include '../classes/Signs.class.php';
include '../classes/Simplex.class.php';
include '../classes/activity.class.php';
include '../classes/DivisionCoefficient.class.php';
include '../classes/Point.class.php';

$ss = activity::isactivated2('../activity/active.xml') ? true : false;
$json = Array();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header('Content-Type: application/json');
if ($ss) {
//	$_POST['textarea'] = '1x1+1x2+0x3+0x4<=1
//0x1+1x2+1x3+0x4<=1
//1x1+0x2+0x3+1x4<=1';
//	$_POST['targetfunction'] = '2x1+6x2+1x3+1x4';
//	$_POST['funct'] = 'true';
//	$_POST['gomorryf'] = 'false';
	$tp = new TextareaProcesser(
			!isset($_POST['textarea']) ? Array() : $_POST['textarea'], !isset($_POST['targetfunction']) ? Array() : $_POST['targetfunction'], !isset($_POST['funct']) ? Array() : $_POST['funct'], !isset($_POST['gomorryf']) ? Array() : $_POST['gomorryf']
	);
	try {
		if ($tp->isCorrect()) {
			$simplex = new Simplex($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
			$json[0] = count($simplex->getTargetFunction());
			$json[1] = $simplex->getMaxRangeArray();
			$json[2] = $simplex->getMinRangeArray();
			$json[3] = $simplex->printProblem() . $simplex->printSolution() . $simplex->printValuePair() . $simplex->printResult();
			$json[4] = $simplex->getPrimaryGraphJson();
			$json[5] = $simplex->getSecondaryGraphJson();
			$json[6] = serialize($simplex);
			$json[7] = Array();
		} else {
			$json[0] = -2;
			$json[3] = TextareaProcesser::errormessage('Puste dane lub złe dane. Proszę poprawić treść wpisanego zadania.');
		}
	} catch (Exception $e) {
		$json[0] = -2;
		$json[3] = TextareaProcesser::errormessage($e->getMessage());
	}
} else {
	$json[0] = -1;
	$json[3] = TextareaProcesser::errormessage('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!');
}
echo json_encode($json);
?>

