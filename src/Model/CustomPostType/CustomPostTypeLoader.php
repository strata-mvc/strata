<?php

namespace Strata\Model\CustomPostType;

use Strata\Strata;
use Strata\Model\CustomPostType\CustomPostType;
use Strata\Core\StrataConfigurableTrait;

/**
 * Loads Custom Post Types objects based on the loader's
 * configuration.
 */
class CustomPostTypeLoader
{
    use StrataConfigurableTrait;

    /**
     * Loads the entities.
     */
    public function load()
    {
        // Even though we can't confirm the entity has
        // been loaded, we should still inform the user.
        $this->logAutoloadedEntities();

        foreach ($this->getConfiguration() as $cpt => $config) {
            $obj = CustomPostType::factory($cpt);

            $this->addWordpressRegisteringAction($obj);

            if ($this->shouldAddAdminMenus($obj)) {
                $this->addWordpressMenusRegisteringAction($obj);
            }

            if ($this->shouldAddRoutes($obj)) {
                $this->addResourceRoute($obj);
            }
        }
    }

    /**
     * Logs the names of the entities the loader is attempting
     * to load.
     */
    private function logAutoloadedEntities()
    {
        $cpts = array_keys($this->getConfiguration());
        $message = sprintf("Found %s custom post types : %s", count($cpts), implode(", ", $cpts));
        Strata::app()->setConfig("runtime.custom_post_types", $cpts);
    }

    /**
     * Returns whether the $customPostType has declared sub-menus
     * that need to be autoloaded.
     * @param  CustomPostType $customPostType
     * @return boolean
     */
    private function shouldAddAdminMenus(CustomPostType $customPostType)
    {
        return is_admin() && count($customPostType->admin_menus) > 0;
    }

    /**
     * Queues the $customPostType's admin menus for registration.
     * @param CustomPostType $customPostType
     */
    private function addWordpressMenusRegisteringAction(CustomPostType $customPostType)
    {
        $customPostType->registerAdminMenus();
    }

    /**
     * Specifies whether the $customPostType should be routed as
     * a resource.
     * @param  CustomPostType $customPostType
     * @return boolean
     */
    private function shouldAddRoutes(CustomPostType $customPostType)
    {
        return !is_admin() && (bool)$customPostType->routed === true;
    }

    /**
     * Adds a new resource route to the router attached to the current
     * app.
     * @param CustomPostType $customPostType
     */
    private function addResourceRoute(CustomPostType $customPostType)
    {
        Strata::app()->router->route->addResource($customPostType);
    }

    /**
     * Adds the Wordpress action to instantiate the custom post type
     * on init.
     * @param CustomPostType $customPostType
     */
    private function addWordpressRegisteringAction(CustomPostType $customPostType)
    {
        add_action('init', array($customPostType, "register"));
    }
}
