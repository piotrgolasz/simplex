<?php

namespace pbaczek\simplex\tests\Integration;

use Exception;
use pbaczek\simplex\Equation;
use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\FractionsCollection;
use pbaczek\simplex\Simplex\Solver\Dantzig;
use PHPUnit\Framework\TestCase;
use Simplex;
use TextareaProcesser;

/**
 * Class SimplexTest
 * @package pbaczek\simplex\tests\Integration
 */
class SimplexTest extends TestCase
{
//    /**
//     * Test simple functionality
//     * @return void
//     * @throws Exception
//     */
//    public function testBasicFunctionality(): void
//    {
//        // max 2x1+6x2
//        //2x1+5x2<=30
//        //2x1+3x2<=26
//        //0x1+3x2<=15
//
//        $equationsCollection = new EquationsCollection();
//
//        $firstEquationVariables = new FractionsCollection();
//        $firstEquationVariables->add(new Fraction(2));
//        $firstEquationVariables->add(new Fraction(5));
//        $firstEquation = new Equation($firstEquationVariables, new Sign\LessOrEqual(), new Fraction(30));
//
//        $secondEquationVariables = new FractionsCollection();
//        $secondEquationVariables->add(new Fraction(2));
//        $secondEquationVariables->add(new Fraction(3));
//        $secondEquation = new Equation($secondEquationVariables, new Sign\LessOrEqual(), new Fraction(26));
//
//        $thirdEquationVariables = new FractionsCollection();
//        $thirdEquationVariables->add(new Fraction(0));
//        $thirdEquationVariables->add(new Fraction(3));
//        $thirdEquation = new Equation($thirdEquationVariables, new Sign\LessOrEqual(), new Fraction(15));
//
//        $equationsCollection->add($firstEquation);
//        $equationsCollection->add($secondEquation);
//        $equationsCollection->add($thirdEquation);
//        $solver = new Dantzig($equationsCollection, new FractionsCollection([new Fraction(2), new Fraction(6)]), true);
////
//        $simplex = new \pbaczek\simplex\Simplex($solver);
//        $simplex->run();
//    }

    public function testOldSimplex()
    {
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src\Signs.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src\Point.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src\Fraction.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src\Simplex.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src\TextareaProcesser.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src\SimplexTableau.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src\DivisionCoefficient.class.php';

        $css = '<style>table.result td.mainelement {
	color:white;
	background-color:red;
	text-align:center;
	width:45px;
}</style>';

        $textarea = '2x1+5x2<=30
2x1+3x2<=26
0x1+3x2<=15';
        $targetfunction = '2x1+6x2';
        $function = 'true';
        $gomorry = 'false';

        $tp = new TextareaProcesser(
            $textarea, $targetfunction, $function, $gomorry
        );
        try {
            if ($tp->isCorrect()) {
                $simplex = new Simplex($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());

                file_put_contents('result.html', $css . $simplex->printSolution() . $simplex->printValuePair() . $simplex->printResult());
            } else {
                $json[0] = -2;
                $json[3] = TextareaProcesser::errormessage('Puste dane lub złe dane. Proszę poprawić treść wpisanego zadania.');
            }
        } catch (Exception $e) {
            $json[0] = -2;
            $json[3] = TextareaProcesser::errormessage($e->getMessage());
        }
    }
}