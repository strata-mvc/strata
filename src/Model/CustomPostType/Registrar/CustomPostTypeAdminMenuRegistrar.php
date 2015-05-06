<?php
namespace Strata\Model\CustomPostType\Registrar;

use Strata\Router\Router;
use Strata\Model\CustomPostType\Registrar\Registrar;
use Strata\Utility\Inflector;

class CustomPostTypeAdminMenuRegistrar extends Registrar
{
    private $_config = array();

    public function configure($config)
    {
        $this->_config = $config;
    }

    public function register()
    {
        if (count($this->_config)) {
            add_action('admin_menu', array($this, 'action_addAdminMenus'));
        }
    }

    public function action_addAdminMenus()
    {
        // Default to the model's likely controller.
        $defaultController = Inflector::classify($this->_entity->getShortName() . 'Controller');
        $parentSlug = 'edit.php?post_type=' . $this->_wordpressKey;

        foreach ($this->_config as $func => $config) {
            $config += array(
                'title'         => ucfirst($func),
                'menu-title'    => ucfirst($func),
                'capability'    => "manage_options",
                'icon'          => null,
                'route'         => array($defaultController, $func),
                'position'      => null,
            );

            // This is to circumvent that wordpress doesn't let you pass arguments to
            // callbacks so we can send the controller and function to the router.
            // We dont want people to have to specify that odd function name.
            // Allow them to send the controller string name and take care of the rest.
            if (is_string($config['route'])) {
                $route = Router::callback($config['route'], $func);
            }  else {
                $route = Router::callback($config['route'][0], $config['route'][1]);
            }

            add_submenu_page($parentSlug, $config['title'], $config['menu-title'], $config['capability'], $func, $route, $config['icon'], $config['position']);
        }
    }
}
