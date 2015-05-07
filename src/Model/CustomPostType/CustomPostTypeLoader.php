<?php
namespace Strata\Model\CustomPostType;

use Strata\Utility\Hash;
use Strata\Model\CustomPostType\Entity;
use Strata\Model\Model;

class CustomPostTypeLoader
{
    private $config;

    function __construct(array $ctpConfig)
    {
        $this->config = Hash::normalize($ctpConfig);
    }

    public function load()
    {
        foreach ($this->config as $cpt => $config) {

            $this->addWordpressRegisteringAction($cpt);

            if ($this->shouldAddAdminMenus($config)) {
                $this->addWordpressMenusRegisteringAction($cpt, $config);
            }
        }
    }

    private function shouldAddAdminMenus($config)
    {
        return is_admin() && !is_null($config) && Hash::check($config, 'admin');
    }

    private function addWordpressMenusRegisteringAction($ctpName, $config)
    {
        $obj = Model::factory($ctpName);
        $obj->registerAdminMenus(Hash::extract($config, 'admin'));
    }

    private function addWordpressRegisteringAction($ctpName)
    {
        add_action('init', Entity::buildRegisteringCall($ctpName));
    }
}
