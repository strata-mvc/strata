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
        $this->_wordpressKey = $entity->getWordpressKey();

        $this->_labelParser = new LabelParser($entity);
        $this->_labelParser->parse();
    }
}
