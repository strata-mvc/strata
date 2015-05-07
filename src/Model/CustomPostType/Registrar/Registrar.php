<?php
namespace Strata\Model\CustomPostType\Registrar;

use Strata\Model\CustomPostType\LabelParser;

class Registrar
{
    protected $_entity;
    protected $_wordpressKey;
    protected $_labelParser;

    function __construct(\Strata\Model\CustomPostType\Entity $entity)
    {
        $this->_entity = $entity;

        $class = get_class($entity);
        $this->_wordpressKey = $class::wordpressKey();

        $this->_labelParser = new LabelParser($this->_entity);
        $this->_labelParser->parse();
    }
}
