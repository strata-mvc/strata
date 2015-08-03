<?php
namespace Strata\Model\CustomPostType;

use Strata\Strata;
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
        $this->logAutoloadedEntities();

        foreach ($this->config as $cpt => $config) {
            if (is_array($config)) {
                $config = Hash::normalize($config);
            }

            $this->addWordpressRegisteringAction($cpt);

            if ($this->shouldAddAdminMenus($config)) {
                $this->addWordpressMenusRegisteringAction($cpt, $config);
            }

            if ($this->shouldAddRoutes($config)) {
                $this->addResourceRoute($cpt, $config);
            }
        }
    }

    private function logAutoloadedEntities()
    {
        $app = Strata::app();
        $cpts = array_keys($this->config);
        $app->log(sprintf("Found %s custom post types : %s", count($cpts), implode(", ", $cpts)), "[Strata::CustomPostTypeLoader]");
    }

    private function shouldAddAdminMenus($config)
    {
        return is_admin() && is_array($config) && array_key_exists('admin', $config);
    }

    private function addWordpressMenusRegisteringAction($ctpName, $config)
    {
        $obj = Model::factory($ctpName);
        $obj->registerAdminMenus($config['admin']);
    }

    private function shouldAddRoutes($config)
    {
        return !is_admin() && is_array($config) && array_key_exists('routed', $config);
    }

    private function addResourceRoute($ctpName, $config)
    {
        $app = Strata::app();
        $app->router->route->addResource(array($ctpName => $config['routed']));
    }

    private function addWordpressRegisteringAction($ctpName)
    {
        add_action('init', Entity::buildRegisteringCall($ctpName));
    }
}
