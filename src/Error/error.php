<?php
    use Strata\Strata;
?>
<section>
    <header>
        <h1><?php echo ucfirst($error['type']); ?></h1>
        <h3><?php echo ucfirst($error['description']); ?></h3>
        <h4>in <?php echo basename($error['file']); ?> on line <?php echo $error['line']; ?></h4>
    </header>

    <?php $filename = $error['file']; ?>
    <?php if ($filename) : ?>
        <?php $lines = file($filename); ?>
        <?php if (count($lines)) : ?>
        <table>
            <?php $specificLine = $error['line']; ?>
            <?php $start = $specificLine - 4; ?>
            <?php $end = $specificLine + 5; ?>
            <?php for ($i = $start; $i < $end; $i++) : ?>
            <tr>
                <td class="lines"><?php echo ($i + 1); ?></td>
                <?php if (isset($lines[$i])) : ?>
                    <td class="code <?php echo $specificLine === ($i + 1) ? "focus" : ""; ?>"><?php echo htmlentities($lines[$i]); ?></td>
                <?php else : ?>
                    <td class="code">&nbsp;</td>
                <?php endif; ?>
            </tr>
            <?php endfor; ?>
        </table>
        <div class="source"><?php echo str_replace(\Strata\Strata::getRootPath(), '~', $error['file']); ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="trace">
        <?php if (isset($error['trace'])) : ?>
            <div style="max-height: 300px;">
                <h3><?php echo "Trace"; ?></h3>
                <?php echo $error['trace']; ?>
            </div>
        <?php endif ;?>
    </div>

    <div class="context">
        <?php if (method_exists("\Strata\Strata", "app")) : ?>
            <?php $app = Strata::app(); ?>
            <?php $router = Strata::router(); ?>
            <h3>Context</h3>
            <?php
                $controller = null;
                $method = strtoupper($_SERVER['REQUEST_METHOD']);
                if (isset($router))  {
                    $controller = $router->getCurrentController();
                    $action = $router->getCurrentAction();
                }
            ?>
            <p>[<?php echo $method; ?>] <?php echo WP_HOME . $_SERVER['REQUEST_URI']; ?></p>
            <p>
                <?php if (!is_null($controller)) : ?>
                    Routed to <?php echo get_class($controller); ?>#<?php echo $action; ?>.
                <?php else : ?>
                    Strata did not route to a controller.
                <?php endif; ?>
            </p>

            <h4>Known routes</h4>
            <table>
                <tr><th>Type</th><th>Match</th><th>Route</th></tr>
                <?php if (isset($router))  : ?>
                    <?php foreach ((array)$router->route->listRegisteredRoutes() as $route) : ?>
                        <tr>
                            <td><?php if (count($route) > 0) : echo $route[0]; endif; ?></td>
                            <td><?php if (count($route) >= 1) : echo $route[1]; endif; ?></td>
                            <td><?php if (count($route) >= 2) : echo $route[2]; endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="3">No routes loaded</td></tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>
    </div>

</section>


