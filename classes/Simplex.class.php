<?php

/**
 * Description of Simplex2
 *
 * @author PETTER
 */
class Simplex {

	private $index = 0;
	private $matrixes = Array();
	private $gomory;
	private $extreme;
	private $variables;
	private $boundaries;
	private $signs;
	private $targetfunction;
	private $wrongsigns = 0;
	private $M, $N, $O;
	private $cCoefficient;
	private $basis;
	private $basisVariable;
	private $nonBasisVariable;

	public function __construct(Array $variables, Array $boundaries, Array $signs, Array $targetfunction, $max = true, $gomory = false) {
		$this->gomory = (boolean) $gomory;
		$this->extreme = (boolean) $max;
		$this->variables = $variables;
		$this->targetfunction = $targetfunction;
		$this->boundaries = $boundaries;
		$this->signs = $signs;
		$this->M = count($variables[0]) + 1; //3
		$this->N = count($boundaries) + 1; //4
		$this->O = count($targetfunction);
		$this->cCoefficient[$this->index] = Array();
		$this->basisVariable = Array();
		$this->nonBasisVariable = Array();

		if (empty($variables) || empty($boundaries) || empty($signs) || empty($targetfunction)) {
			throw new Exception('Input array is empty!.');
		}

		if (count($boundaries) != count($signs) || count($signs) == 0) {
			throw new Exception('Sizes of arrays Boundaries and Signs have to be the same.');
		}
		if ($this->extreme) {
			foreach ($this->signs as $key => $value) {
				if ($value != enumSigns::_LEQ) {
					$this->cCoefficient[$this->index][$key] = new Fraction(0, 1, -1, 1);
					$this->wrongsigns++;
				} else {
					$this->cCoefficient[$this->index][$key] = new Fraction(0);
				}
			}
		} else {
			foreach ($this->signs as $key => $value) {
				$this->cCoefficient[$this->index][$key] = new Fraction(0, 1, 1, 1);
				if ($value != enumSigns::_LEQ) {
					$this->wrongsigns++;
				}
			}
		}

		$this->basis = new SplFixedArray($this->O + $this->N + $this->wrongsigns - 1);

		for ($i = 1; $i < $this->N; $i++) {
			$this->basisVariable[$this->index][$i] = 'S<sub>' . $i . '</sub>';
		}
		for ($i = 1; $i < $this->O + $this->N + $this->wrongsigns; $i++) {
			$this->nonBasisVariable[$this->index][$i] = 'x<sub>' . $i . '</sub>';
		}

		$this->matrixes[$this->index] = new SimplexTableu($this->N, $this->N + $this->M - 1 + $this->wrongsigns);

		for ($i = 0; $i < $this->N - 1; $i++) {
			for ($j = 0; $j < $this->M - 1; $j++) {
				$this->matrixes[$this->index]->setValue($j, $i, clone $this->variables[$i][$j]);
			}
		}

		for ($i = 0; $i < $this->N - 1; $i++) {
			$this->matrixes[$this->index]->setValue($this->N + $this->wrongsigns + $this->M - 2, $i, clone $boundaries[$i]);
		}

		$ax = 0;
		foreach ($this->signs as $key => $value) {
			switch ($value) {
				case enumSigns::_GEQ:
					$this->matrixes[$this->index]->setValue($this->M - 1 + $key, $key, new Fraction(-1));
					$this->matrixes[$this->index]->setValue($this->M - 1 + $this->N - 1 + $ax, $key, new Fraction(1));
					$ax++;
					break;
				default:
					for ($j = $this->M - 1; $j < $this->N + $this->M - 2; $j++) {
						if (($j - ($this->M - 1)) == $key) {
							$this->matrixes[$this->index]->setValue($j, $key, new Fraction(1));
						}
					}
					break;
			}
		}
		unset($ax);

		for ($i = 0; $i < $this->O; $i++) {
			$targetfunction[$i]->minusFraction();
			$this->matrixes[$this->index]->setValue($i, $this->N - 1, clone $targetfunction[$i]);
		}
		$this->partialAdding();


		//--------------------------------------------
		$this->Solve();
	}

	private function partialAdding() {
		for ($i = 0; $i < $this->matrixes[$this->index]->getRows() - 1; $i++) {
			$temp = new Fraction(0);
			for ($j = 0; $j < $this->matrixes[$this->index]->getCols() - 1; $j++) {
				if ($this->signs[$j] != enumSigns::_LEQ) {
					$temp->add($this->matrixes[$this->index]->getElement($i, $j));
				}
			}
			$this->matrixes[$this->index]->getElement($i, $this->N - 1)->substract(new Fraction(0, 1, $temp->getNumerator(), $temp->getDenominator()));
		}
		//for boundaries
		$temp = new Fraction(0);
		for ($j = 0; $j < $this->matrixes[$this->index]->getCols() - 1; $j++) {
			if ($this->signs[$j] != enumSigns::_LEQ) {
				$temp->add($this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $j));
			}
		}
		$this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $j)->substract(new Fraction(0, 1, $temp->getNumerator(), $temp->getDenominator()));
	}

	private function Solve() {
		while (true) {
			$this->index++;
			$this->matrixes[$this->index] = clone $this->matrixes[$this->index - 1];
			$this->matrixes[$this->index]->setIndex($this->index);
			$this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
			$this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
			$this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
			$this->partialAdding();
			$p = $this->matrixes[$this->index]->findBaseCol();
			if ($p == -1) {
				break;
			} else {
				$this->matrixes[$this->index - 1]->setMainCol($p);
				$this->matrixes[$this->index]->setMainCol($p);
			}
			$q = $this->matrixes[$this->index]->findBaseRow($p);
			if ($q == -1) {
				$this->errorMessage("Linear problem is unbounded");
				unset($this->matrixes[$this->index]);
				$this->index--;
				break;
			} else {
				$this->matrixes[$this->index - 1]->setMainRow($q);
				$this->matrixes[$this->index]->setMainRow($q);
			}

			if (isset($this->targetfunction[$p])) {
				$this->cCoefficient[$this->index][$q] = clone $this->targetfunction[$p];
			} else {
				$this->cCoefficient[$this->index][$q] = new Fraction(0);
			}
			$this->cCoefficient[$this->index][$q]->minusFraction();
			$this->swapBase();
			$this->simplexIteration();
			if (!isset($this->basis[$q])) {
				$this->basis[$p] = $q;
			}
			//-------------------------------
			//break;
			if ($this->checkTargetFunction()) {
				$this->matrixes[$this->index]->setMainCol(-1);
				$this->matrixes[$this->index]->setMainRow(-1);
				break;
			}
		}

		if ($this->gomory && $this->index != 0) {
			//GOMORY'S CUTTING PLANE METHOD
			//TODO Implement!
			while (true) {
				$q = $this->gomoryRow();
				if ($q == -1) {
					break;
				}
				$this->index++;
				$this->matrixes[$this->index] = new SimplexTableu($this->matrixes[$this->index - 1]->getCols() + 1, $this->matrixes[$this->index - 1]->getRows());
				$this->matrixes[$this->index]->swapGomory();
				$this->matrixes[$this->index]->setIndex($this->index);
				$this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
				$this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
				$this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
				$this->gomoryNewTableau($q);
				$this->matrixes[$this->index]->setMainRow($this->matrixes[$this->index]->getCols() - 2);
				$this->matrixes[$this->index]->setMainCol($this->matrixes[$this->index]->getRows() - 2);
				$this->signs[count($this->signs)] = '>=';
				$this->basisVariable[$this->index][count($this->basisVariable)] = 'S<sub>' . (count($this->boundaries) + 1) . '</sub>';
				$this->cCoefficient[$this->index][count($this->cCoefficient[$this->index])] = 0;
				//-------------------------------------------
				$this->index++;
				$this->matrixes[$this->index] = clone $this->matrixes[$this->index - 1];
				$this->matrixes[$this->index]->swapGomory();
				$this->matrixes[$this->index]->setIndex($this->index);
				$this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
				$this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
				$this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
				$this->swapBase();
				$this->simplexIteration();
				//-------------------------------------------
				if ($this->checkTargetIntegerFunction()) {
					$this->matrixes[$this->index]->setMainCol(-1);
					$this->matrixes[$this->index]->setMainRow(-1);
					break;
				}
			}
		}
	}

	public function printSolution() {
		foreach ($this->matrixes as $key => $value) {
			echo '<table class="result">';
			echo '<tbody>';
			echo '<tr>';
			echo '<th class="ui-state-default">(' . $value->getIndex() . ')</th>';
			echo '<th class="ui-state-default"></th>';
			for ($j = 0; $j < $this->N + $this->M - 2 + $this->wrongsigns; $j++) {
				if (isset($this->targetfunction[$j])) {
					echo '<th class="ui-state-default">' . $this->targetfunction[$j] . '</th>';
				} else {
					echo '<th class="ui-state-default">0</th>';
				}
			}
			echo '<th class="ui-state-default" rowspan="2">Warto&#347;&#263;</th>';
			echo '</tr>';
			echo '<tr><th class="ui-state-default">Baza</th>';
			echo '<th class="ui-state-default">c</th>';
			for ($j = 0; $j < $this->N + $this->wrongsigns + $this->M - 2; $j++) {
				if (isset($this->nonBasisVariable[$key][$j + 1])) {
					echo '<th class="ui-state-default">' . $this->nonBasisVariable[$key][$j + 1] . '</th>';
				}
			}
			echo '</tr>';
			for ($i = 0; $i < $value->getCols(); $i++) {
				echo '<tr>';
				if (isset($this->basisVariable[$key][($i + 1)])) {
					echo '<th class="ui-state-default">' . $this->basisVariable[$key][($i + 1)] . '</th>';
					echo '<td class="center">' . $this->cCoefficient[$key][$i] . '</td>';
				} else {
					echo '<th class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></th>';
					echo '<th></th>';
				}
				for ($j = 0; $j < $value->getRows(); $j++) {
					if ($key != 0 && !$value->isGomory()) {
						//ALL PICTURES NEEDED
						if ($j == $this->matrixes[$key]->getMainCol() && $i == $this->matrixes[$key]->getMainRow()) {
							if ($j == $this->matrixes[$key - 1]->getMainCol() && $i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td class="mainelement" data-dane="m,1,' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							} elseif ($j == $this->matrixes[$key - 1]->getMainCol()) {
								echo '<td class="mainelement" data-dane="c,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							} elseif ($i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td class="mainelement" data-dane="r,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							} else {
								echo '<td class="mainelement" data-dane="g,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $i) . ',' . $this->matrixes[$key - 1]->getElement($j, $this->matrixes[$key - 1]->getMainRow()) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							}
						} else {
							if ($j == $this->matrixes[$key - 1]->getMainCol() && $i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td data-dane="m,1,' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							} elseif ($j == $this->matrixes[$key - 1]->getMainCol()) {
								echo '<td data-dane="c,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							} elseif ($i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td data-dane="r,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							} else {
								echo '<td data-dane="g,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $i) . ',' . $this->matrixes[$key - 1]->getElement($j, $this->matrixes[$key - 1]->getMainRow()) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
							}
						}
					} else {
						//NO PICTURES
						if ($j == $this->matrixes[$key]->getMainCol() && $i == $this->matrixes[$key]->getMainRow()) {
							echo '<td class="mainelement">' . $value->getElement($j, $i) . '</td>';
						} elseif ($j == $this->matrixes[$key]->getMainCol()) {
							echo '<td>' . $value->getElement($j, $i) . '</td>';
						} elseif ($i == $this->matrixes[$key]->getMainRow()) {
							echo '<td>' . $value->getElement($j, $i) . '</td>';
						} else {
							echo '<td>' . $value->getElement($j, $i) . '</td>';
						}
					}
				}
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
			echo '<br/>';
		}
	}

	public function testPrint() {
		foreach ($this->matrixes as $value) {
			echo 'Index:' . $value->getIndex() . '<br/>';
			echo 'Col: ' . $value->getMainCol() . '<br/>';
			echo 'Row: ' . $value->getMainRow() . '<br/>';
			echo 'Gomory: ' . $value->isGomory() . '<br/>';
			echo '<table border="1">';
			for ($i = 0; $i < $value->getCols(); $i++) {
				echo '<tr>';
				for ($j = 0; $j < $value->getRows(); $j++) {
					echo '<td>' . $value->getElement($j, $i) . '</td>';
				}
				echo '</tr>';
			}
			echo '</table><br/>';
		}
	}

	private function gomoryRow() {
		foreach ($this->getValuePair() as $key => $value) {
			if (!$value->isInteger()) {
				return $key;
			}
		}
		return -1;
	}

	private function gomoryNewTableau($k) {
		for ($i = 0; $i < $this->matrixes[$this->index - 1]->getCols() - 1; $i++) {
			for ($j = 0; $j < $this->matrixes[$this->index - 1]->getRows(); $j++) {
				$this->matrixes[$this->index]->setValue($j, $i, $this->matrixes[$this->index - 1]->getElement($j, $i));
			}
		}
		for ($j = 0; $j < $this->matrixes[$this->index - 1]->getRows(); $j++) {
			$this->matrixes[$this->index]->setValue($j, $this->matrixes[$this->index]->getCols() - 1, $this->matrixes[$this->index - 1]->getElement($j, $this->matrixes[$this->index - 1]->getCols() - 1));
			$s = clone $this->matrixes[$this->index - 1]->getElement($j, $k);
			$s->getImproperPart();
			$this->matrixes[$this->index]->setValue($j, $this->matrixes[$this->index]->getCols() - 2, $s);
		}
	}

	private function checkTargetFunction() {
		for ($i = 0; $i < $this->matrixes[$this->index]->getRows() - 1; $i++) {
			if (Fraction::isNegative($this->matrixes[$this->index]->getElement($i, $this->matrixes[$this->index]->getCols() - 1))) {
				return false;
			}
		}
		return true;
	}

	private function checkTargetIntegerFunction() {
		foreach ($this->getValuePair() as $value) {
			if ($value->isInteger()) {
				continue;
			} else {
				return false;
			}
		}
		return true;
	}

	public function getResult() {
		return $this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $this->matrixes[$this->index]->getCols() - 1);
	}

	public function printResult() {
		echo 'W=' . $this->getResult();
	}

	public static function errorMessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	private function swapBase() {
		$buffer = $this->basisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainRow() + 1];
		$this->basisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainRow() + 1] = $this->nonBasisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainCol() + 1];
		$this->nonBasisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainCol() + 1] = $buffer;
		unset($buffer);
	}

	private function simplexIteration() {
		$previousBaseRow = $this->matrixes[$this->index - 1]->getMainRow();
		$previousBaseCol = $this->matrixes[$this->index - 1]->getMainCol();
		$previousMainElement = $this->matrixes[$this->index - 1]->getElement($this->matrixes[$this->index - 1]->getMainCol(), $this->matrixes[$this->index - 1]->getMainRow());
		for ($i = 0; $i < $this->matrixes[$this->index]->getCols(); $i++) {
			for ($j = 0; $j < $this->matrixes[$this->index]->getRows(); $j++) {
				if ($i == $previousBaseRow && $j == $previousBaseCol) {
					//Main element
					$this->matrixes[$this->index]->setValue($j, $i, new Fraction(1));
				} elseif ($i == $previousBaseRow) {
					//Main row
					$s = clone $this->matrixes[$this->index]->getElement($j, $i);
					$n = clone $previousMainElement;
					$s->divide($n);
					$this->matrixes[$this->index]->setValue($j, $i, clone $s);
				} elseif ($j == $previousBaseCol) {
					//Main column
					$this->matrixes[$this->index]->setValue($j, $i, new Fraction(0));
				} else {
					//Other elements
					$s = clone $this->matrixes[$this->index - 1]->getElement($j, $previousBaseRow);
					$m = clone $this->matrixes[$this->index - 1]->getElement($previousBaseCol, $i);
					$n = clone $this->matrixes[$this->index - 1]->getElement($previousBaseCol, $previousBaseRow);
					$l = clone $this->matrixes[$this->index]->getElement($j, $i);
					$s->multiply($m);
					$s->divide($n);
					$l->substract($s);
					$this->matrixes[$this->index]->setValue($j, $i, $l);
				}
			}
		}
	}

	public function printProblem() {
		$index = 1;
		echo $this->extreme == true ? 'max ' : 'min ';
		foreach ($this->targetfunction as $key => $value) {
			$a = clone $value;
			$a->minusFraction();
			if ($key == 0 && (Fraction::isPositive($a) || Fraction::equalsZero($a))) {
				echo $a . 'x<sub>' . $index . '</sub>';
			} else {
				echo '+' . $a . 'x<sub>' . $index . '</sub>';
			}
			$index++;
		}
		echo '<br/>';
		$index = 1;
		for ($i = 0; $i < $this->matrixes[0]->getCols() - 1; $i++) {
			for ($j = 0; $j < $this->matrixes[0]->getRows() - 1; $j++) {
				if (Fraction::isPositive($this->matrixes[0]->getElement($j, $i)) || Fraction::equalsZero($this->matrixes[0]->getElement($j, $i))) {
					echo $j != 0 ? '+' : '';
					echo $this->matrixes[0]->getElement($j, $i) . 'x<sub>' . $index . '</sub>';
				} else {
					echo $this->matrixes[0]->getElement($j, $i) . 'x<sub>' . $index . '</sub>';
				}
				$index++;
			}
			echo $this->signs[$i];
			echo $this->boundaries[$i];
			echo '<br/>';
			$index = 1;
		}
		$index = 1;
		for ($i = 0; $i < $this->matrixes[0]->getRows() - 1; $i++) {
			echo 'x<sub>' . $index . '</sub>' . enumSigns::_GEQ . '0<br/>';
			$index++;
		}
		if ($this->gomory) {
			echo '<u>in integers</u>';
		}
		echo '<br/>';
		unset($index);
	}

	public function printValuePair() {
		foreach ($this->getValuePair() as $key => $value) {
			echo 'x<sub>' . ($key + 1) . '</sub>=' . $value . (Fraction::isFraction($value) ? ' (' . $value->getRealValue() . ')' : '') . '<br/>';
		}
	}

	public function getValuePair() {
		if ($this->index == 0) {
			return Array("NaN");
		} else {
			$x = Array();
			foreach ($this->basis as $key => $value) {
				if (!isset($value)) {
					$x[$key] = new Fraction(0, 1);
				} else {
					$x[$key] = $this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $value);
				}
			}
			return $x;
		}
	}

	public function getJSON() {
		$a = count($this->targetfunction);
		$b = count($this->boundaries);
		$json = Array();
		switch ($a) {
			case 2:
				$maxx = new Fraction(0);
				$maxy = new Fraction(0);
				for ($i = 0; $i < $b; $i++) {
					if (Fraction::equalsZero($this->variables[$i][1])) {
						continue;
					}
					$s = clone $this->boundaries[$i];
					$s->divide($this->variables[$i][1]);
					if ($s->compare($maxy)) {
						$maxy = $s;
					}
					if (Fraction::equalsZero($this->variables[$i][0])) {
						continue;
					}
					$s = clone $this->boundaries[$i];
					$s->divide($this->variables[$i][0]);
					if ($s->compare($maxx)) {
						$maxx = $s;
					}
				}
				for ($i = 0; $i < $b; $i++) {
					$json[$i] = Array('label' => 'S' . ($i + 1), 'data' => '');
					if (Fraction::equalsZero($this->variables[$i][1])) {
						$s = clone $this->boundaries[$i];
						$s->divide($this->variables[$i][0]);
						$json[$i]['data'][] = Array($s->getRealValue(), $maxy->getRealValue());
					} else {
						$j = clone $this->boundaries[$i];
						$j->divide($this->variables[$i][1]);
						$json[$i]['data'][] = Array(0, $j->getRealValue());
					}
					if (Fraction::equalsZero($this->variables[$i][0])) {
						$s = clone $this->boundaries[$i];
						$s->divide($this->variables[$i][1]);
						$json[$i]['data'][] = Array($maxx->getRealValue(), $s->getRealValue());
					} else {
						$j = clone $this->boundaries[$i];
						$j->divide($this->variables[$i][0]);
						$json[$i]['data'][] = Array($j->getRealValue(), 0);
					}
				}
				if (!Fraction::equalsZero($this->targetfunction[0]) && !Fraction::equalsZero($this->targetfunction[1])) {
					$t = clone $this->targetfunction[1];
					$t->multiply($maxx);
					$t->divide($this->targetfunction[0]);
					$json[] = Array('label' => 'gradient', 'data' => Array(Array(0, 0), Array($maxx->getRealValue() / 4, $t->getRealValue() / 4)));
				}
				echo '<script>';
				echo '$(document).ready(function(){';
				echo '$.plot($("#placeholder1"),' . json_encode($json) . ');';
				echo '});';
				echo '</script>';
				echo '<div style="width:480px;float:right;">';
				echo '<div id="placeholder1" style="width: 480px; height: 360px;"></div>';
				echo '</div>';
				break;
			default:
				$maxx = new Fraction(0);
				$maxy = new Fraction(0);
				$maxz = new Fraction(0);
				for ($i = 0; $i < $b; $i++) {
					if (Fraction::equalsZero($this->variables[$i][1])) {
						continue;
					}
					$s = clone $this->boundaries[$i];
					$s->divide($this->variables[$i][1]);
					if ($s->compare($maxy)) {
						$maxy = $s;
					}

					if (Fraction::equalsZero($this->variables[$i][0])) {
						continue;
					}
					$s = clone $this->boundaries[$i];
					$s->divide($this->variables[$i][0]);
					if ($s->compare($maxx)) {
						$maxx = $s;
					}
					if (Fraction::equalsZero($this->variables[$i][2])) {
						continue;
					}

					$s = clone $this->boundaries;
					$s->divide($this->variables[$i][2]);
					if ($s->compare($maxz)) {
						$maxz = $s;
					}
				}
				for ($i = 0; $i < $maxx->getRealValue(); $i = $i + ($maxx->getRealValue() / 25)) {
					for ($j = 0; $j < $maxy->getRealValue(); $j = $j + ($maxy->getRealValue() / 25)) {
						for ($k = 0; $k < $maxz->getRealValue(); $k = $k + ($maxz->getRealValue() / 25)) {
							if ($this->isValidPoint($i, $j, $k)) {
								$json[] = Array($i, $j, $k);
							}
						}
					}
				}
				echo '<canvas id="canvas1" width="613" height="500"></canvas>';
				echo '<script>';
				echo '$(document).ready(function() {';
				echo 'var vars = [];';
				echo 'a =' . json_encode($json) . ';';
				echo 'for (var i = 0; i < a.length; i++) {';
				echo 'vars.push("Punkt" + (i + 1));';
				echo '}';
				echo 'var x = {
                            "y": {
                                "vars": vars,
                                "smps": [
                                    "X",
                                    "Y",
                                    "Z"
                                ],
                                "desc": [
                                    "Simplex method"
                                ],
                                "data": a
                            }
                        };';
				echo 'new CanvasXpress("canvas1", x, {';
				echo '"graphType": "Scatter3D",';
				echo '"useFlashIE": true,';
				echo '"xAxis": [';
				echo '"X"';
				echo '],';
				echo '"yAxis": [';
				echo '"Y"';
				echo '],';
				echo '"zAxis": [';
				echo '"Z"';
				echo '],';
				echo '"scatterType": false,';
				echo '"setMinX": 0,';
				echo '"setMinY": 0';
				echo '});';
				echo '});';
				echo '</script>';
				break;
		}
	}

	private function isValidPoint($x, $y, $z) {
		$b = count($this->boundaries);
		$str = false;
		for ($i = 0; $i < $b; $i++) {
			eval("\$str = ((\$this->variables[$i][0]->getRealValue()*$x+\$this->variables[$i][1]->getRealValue()*$y+\$this->variables[$i][2]->getRealValue()*$z)$this->signs[$i](\$this->boundaries[$i]->getRealValue())) ? true : false;");
			if (!$str) {
				return false;
			}
		}
		return true;
	}

}

?>
