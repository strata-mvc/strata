<?php

namespace Strata\Model\Validator;

use Strata\Model\Validator\Validator;

class SameValidator extends Validator {

    public $_errorMessage = "The two values do not match.";

    public $_config = array(
        "as" => null,
    );

    public function test($value, $context)
    {
        $comparedWith = $context->getPostedValue($this->_config['as']);

        // When the value compared is null (instead of empty string), it means
        // it was not posted. Imply that if the post value is null, then we do not have to compare
        // values.
        // Ex: email is compared on step1, but not in future steps.
        return is_null($comparedWith) || $value === $comparedWith;
    }
}
