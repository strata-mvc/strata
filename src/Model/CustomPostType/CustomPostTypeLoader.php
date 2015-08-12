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

            $obj = Model::factory($cpt);

            $this->addWordpressRegisteringAction($obj);

            if ($this->shouldAddAdminMenus($obj)) {
                $this->addWordpressMenusRegisteringAction($obj);
            }

            if ($this->shouldAddRoutes($obj)) {
                $this->addResourceRoute($obj);
            }
        }
    }

    private function logAutoloadedEntities()
    {
        $app = Strata::app();
        $cpts = array_keys($this->config);
        $app->log(sprintf("Found %s custom post types : %s", count($cpts), implode(", ", $cpts)), "[Strata::CustomPostTypeLoader]");
    }

    private function shouldAddAdminMenus(Entity $customPostType)
    {
        return is_admin() && count($customPostType->admin_menus) > 0;
    }

    private function addWordpressMenusRegisteringAction(Entity $customPostType)
    {
        $customPostType->registerAdminMenus();
    }

    private function shouldAddRoutes(Entity $customPostType)
    {
        return !is_admin() && $customPostType->routed === true;
    }

    private function addResourceRoute(Entity $customPostType)
    {
        $app = Strata::app();
        $app->router->route->addResource($customPostType);
    }

    private function addWordpressRegisteringAction(Entity $customPostType)
    {
        add_action('init', array($customPostType, "register"));
    }
}
