<?php
namespace App\Traits;
/**
 * Code adapted from
 * https://github.com/mojoblanco/NUBAN/blob/master/PHP/NUBAN.php
 */
trait NubanValidator{

  public function validateNuban($accountNumber, $bankCode) {
		if (strlen($bankCode) == 3 && strlen($accountNumber) == 10) {
		
			$nubanAccSerialNum = substr($accountNumber, 0, -1);
			$checkDigit = substr($accountNumber, -1);
			$nubanFormat = $bankCode . $nubanAccSerialNum;

			$nubanArray = str_split($nubanFormat);
			$algoDictionary = [3, 7, 3, 3, 7, 3, 3, 7, 3, 3, 7, 3];

			$checkSum = 0;

			foreach ($nubanArray as $key => $value) {
				$checkSum += ($value * $algoDictionary[$key]);
			}

			$validatedCheckDigit = 10 - ($checkSum % 10);
			$validatedCheckDigit = $validatedCheckDigit == 10 ? 0 : $validatedCheckDigit;

			return ($checkDigit == $validatedCheckDigit);	
			
		}

    return false;
	}
}