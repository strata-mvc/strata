<?php
namespace Strata\Model\CustomPostType\Registrar;

use Strata\Model\CustomPostType\LabelParser;
use Strata\Model\CustomPostType\CustomPostType;

class Registrar
{
    protected $entity;
    protected $wordpressKey;
    protected $labelParser;

    function __construct(CustomPostType $entity)
    {
        $this->entity = $entity;

        $this->labelParser = new LabelParser($entity);
        $this->labelParser->parse();
    }
}
