<?php

class Fraction {

	private $numerator;
	private $denominator;
	private $mnumerator;
	private $mdenominator;

	public function __construct($numerator = 0, $denominator = 1, $mnumerator = 0, $mdenominator = 1) {
		$numerator = $this->realToFraction($numerator);
		$denominator = $this->realToFraction($denominator);
		$this->numerator = (int) ( $numerator[0] * $denominator[1] );
		$this->denominator = (int) ( $denominator[0] * $numerator[1]);

		$mnumerator = $this->realToFraction($mnumerator);
		$mdenominator = $this->realToFraction($mdenominator);
		$this->mnumerator = (int) ( $mnumerator[0] * $mdenominator[1] );
		$this->mdenominator = (int) ( $mdenominator[0] * $mnumerator[1] );
		if ($this->denominator == 0 || $this->mdenominator == 0) {
			throw new Exception('Denominator can\'t be 0!');
		}
		$this->reduction();
	}

	public function getNumerator() {
		return $this->numerator;
	}

	public function getDenominator() {
		return $this->denominator;
	}

	public function getMNumerator() {
		return $this->mnumerator;
	}

	public function getMDenominator() {
		return $this->mdenominator;
	}

	private function reduction() {
		if ($this->denominator < 0) {
			$this->expansion(-1);
			$this->reduction();
		} elseif ($this->numerator == 0) {
			$this->denominator = 1;
		} elseif (abs($this->numerator) == 1 || abs($this->denominator) == 1) {
			//do nothing - cannot reduce fraction with 1
		} elseif ($this->numerator < 0) {
			$this->numerator = abs($this->numerator);
			$hcd = $this->highestCommonDivisor($this->numerator, $this->denominator);
			$this->numerator /= $hcd;
			$this->denominator /= $hcd;
			$this->numerator*=-1;
		} else {
			$hcd = $this->highestCommonDivisor($this->numerator, $this->denominator);
			$this->numerator /= $hcd;
			$this->denominator /= $hcd;
		}
	}

	public function expansion($num) {
		$this->numerator *= $num;
		$this->denominator *= $num;
	}

	public function mexpansion($num) {
		$this->mnumerator *= $num;
		$this->mdenominator *= $num;
	}

	public function contraction($num) {
		$this->numerator /= $num;
		$this->denominator /= $num;
	}

	public function compare($param) {
		if ($param instanceof Fraction) {
			$a = $this->numerator * $param->getDenominator();
			$b = $this->denominator * $param->getNumerator();
			if ($a > $b) {
				return true;
				//this fraction is bigger
			} else {
				return false;
				//param fraction is bigger
			}
		} elseif (is_numeric($param)) {
			$p = $this->realToFraction($param);
			$a = $this->numerator * $p[1];
			$b = $this->denominator * $p[0];
			if ($a > $b) {
				return true;
			} else {
				return false;
			}
		}
	}

	public static function isPositive($param) {
		if ($param instanceof Fraction) {
			if ($param->numerator > 0) {
				if ($param->mnumerator >= 0) {
					return true;
				} else {
					return false;
				}
			} else {
				if ($param->mnumerator > 0) {
					return true;
				} else {
					return false;
				}
			}
		} elseif (is_numeric($param)) {
			return $param > 0 ? true : false;
		}
	}

	public static function isNegative($param) {
		if ($param instanceof Fraction) {
			if ($param->numerator < 0) {
				if ($param->mnumerator <= 0) {
					return true;
				} else {
					return false;
				}
			} else {
				if ($param->mnumerator < 0) {
					return true;
				} else {
					return false;
				}
			}
		} elseif (is_numeric($param)) {
			return $param < 0 ? true : false;
		}
	}

	public function reverse() {
		$sign = 1;
		$numerator = $this->numerator;
		$denominator = $this->denominator;
		if ($this->numerator < 0) {
			$sign = -1;
			$this->numerator*=$sign;
		}
		$this->numerator = $denominator;
		$this->denominator = $numerator;
		if ($sign == -1) {
			$this->numerator*=$sign;
		}
		$this->reduction();
	}

	public function realToFraction($number) {
		$endOfNumber = $number - (int) $number;
		if ($endOfNumber != 0) {
			$mul = bcpow(10, strlen($endOfNumber) - 2);
			return array($number * $mul, $mul);
		} else {
			return array($number, 1);
		}
	}

	public function toString() {
		$string = '';
		if ($this->denominator == 1) {
			$string.=$this->numerator;
		} else {
			$string.=$this->numerator . '/' . $this->denominator;
		}
		if ($this->mnumerator == 0) {
			return $string;
		} else {
			$string.=($this->mnumerator >= 0 ? '+' : '');
			$string.=($this->mdenominator == 1 ? $this->mnumerator . 'M' : $this->mnumerator . '/' . $this->mdenominator . 'M');
		}
		return $string;
	}

	public static function highestCommonDivisor($a, $b) {
		//echo 'hcd('.$a.','.$b.')';
		$a = abs($a);
		while ($a != $b) {
			if ($a > $b) {
				$a = $a - $b;
			} else {
				$b = $b - $a;
			}
		}
		return $a;
	}

	public function isEqual($param) {
		if ($param instanceof Fraction) {
			return ($param->getNumerator() == $this->numerator && $param->getDenominator() == $this->denominator) ? true : false;
		} elseif (is_numeric($param)) {
			return ($this->getRealValue() == $param) ? true : false;
		}
	}

	public function getImproperPart() {
		if (Fraction::isPositive(new Fraction($this->getNumerator(), $this->getDenominator()))) {
			while ($this->numerator >= $this->denominator) {
				$this->numerator-=$this->denominator;
			}
			$this->minusFraction();
		} elseif (Fraction::isNegative(new Fraction($this->getNumerator(), $this->getDenominator()))) {
			while ($this->numerator < -$this->denominator) {
				$this->numerator+=$this->denominator;
			}
			$this->add(1);
			$this->minusFraction();
		}
	}

	public function commonDenominator(&$fraction) {
		$lcm = $this->leastCommonMultiple($this->denominator, $fraction->denominator);
		$this->numerator = $this->numerator * ( $lcm / $this->denominator );
		$fraction->numerator = $fraction->numerator * ( $lcm / $fraction->denominator );
		$this->denominator = $fraction->denominator = $lcm;
	}

	private function leastCommonMultiple($a, $b) {
		return ( $a * $b ) / $this->highestCommonDivisor($a, $b);
	}

	public function getRealValue() {
		return ($this->mnumerator != 0 ? ($this->mnumerator > 0 ? PHP_INT_MAX : ~PHP_INT_MAX) : $this->numerator / $this->denominator);
	}

	public static function errormessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	public function minusFraction() {
		$this->numerator = -$this->numerator;
		$this->mnumerator = -$this->mnumerator;
	}

	public function add($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->add($s);
		} elseif ($param instanceof Fraction) {
			$denominator = $this->denominator * $param->denominator;
			$numerator = $this->numerator * $param->denominator + $this->denominator * $param->numerator;
			$this->numerator = $numerator;
			$this->denominator = $denominator;
			$mdenominator = $this->mdenominator * $param->mdenominator;
			$mnumerator = $this->mnumerator * $param->mdenominator + $this->mdenominator * $param->mnumerator;
			$this->mnumerator = $mnumerator;
			$this->mdenominator = $mdenominator;
			$this->reduction();
		}
	}

	public function substract($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->substract($s);
		} elseif ($param instanceof Fraction) {
			$denominator = $this->denominator * $param->denominator;
			$numerator = $this->numerator * $param->denominator - $this->denominator * $param->numerator;
			$this->numerator = $numerator;
			$this->denominator = $denominator;
			$mdenominator = $this->mdenominator * $param->mdenominator;
			$mnumerator = $this->mnumerator * $param->mdenominator - $this->mdenominator * $param->mnumerator;
			$this->mnumerator = $mnumerator;
			$this->mdenominator = $mdenominator;
			$this->reduction();
		}
	}

	public function multiply($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->multiply($s);
		} elseif ($param instanceof Fraction) {
			$this->numerator*=$param->numerator;
			$this->denominator*=$param->denominator;
			$this->mnumerator*=$param->mnumerator;
			$this->mdenominator*=$param->mdenominator;
			$this->reduction();
		}
	}

	public function divide($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->divide($s);
		} elseif ($param instanceof Fraction) {
			$this->numerator*=$param->denominator;
			$this->denominator*=$param->numerator;
			$this->mnumerator*=$param->mdenominator;
			$this->mdenominator*=$param->mnumerator;
			$this->reduction();
		} else {
			$this->errormessage('Must be a fraction or number!');
		}
	}

	public function _increment() {
		$this->add(new Fraction(1));
	}

	public function hasM() {
		return $this->mnumerator == 0 ? false : true;
	}

	public function isInteger() {
		return is_integer($this->numerator / $this->denominator);
	}

}

?>
