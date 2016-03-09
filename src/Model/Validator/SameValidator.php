<?php

namespace Strata\Model\Validator;

use Strata\Strata;
use Exception;

class SameValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setMessage(__("The two values do not match.", $this->getTextdomain()));
    }

    /**
     * {@inheritdoc}
     */
    public function test($value, $context)
    {
        $confiiguration = $this->getconfiguration();
        if (!isset($confiiguration['as'])) {
            throw new Exception("SameValidator is missing the required 'as' configuration key.");
        }

        $request =  Strata::app()->router->getCurrentController()->request;
        $as = $this->getConfig('as');

        if ($request->isPost($as)) {
            $comparedWith = $request->post($as);
        } elseif ($request->isGet($as)) {
            $comparedWith = $request->get($as);
        } else {
            return false;
        }

        // When the value compared is null (instead of empty string), it means
        // it was not posted. Imply that if the post value is null, then we do not have to compare
        // values.
        return is_null($comparedWith) || $value === $comparedWith;
    }
}
