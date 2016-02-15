<?php
namespace Strata\Model\Validator;

use Strata\Strata;

class SameValidator extends Validator
{

    protected $_config = array(
        "as" => null,
    );

    function __construct()
    {
        $this->setMessage(__("The two values do not match.", "strata"));
    }

    public function test($value, $context)
    {
        $request =  Strata::app()->getCurrentController()->request;

        if ($request->isPost($this->_config['as'])) {
            $comparedWith = $request->post($this->_config['as']);
        } elseif ($request->isGet($this->_config['as'])) {
            $comparedWith = $request->get($this->_config['as']);
        } else {
            return false;
        }

        // When the value compared is null (instead of empty string), it means
        // it was not posted. Imply that if the post value is null, then we do not have to compare
        // values.
        return is_null($comparedWith) || $value === $comparedWith;
    }
}
