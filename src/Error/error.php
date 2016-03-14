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
            <h3><?php echo "Trace"; ?></h3>
            <?php echo $error['trace']; ?>
        <?php endif ;?>
    </div>

    <div class="context">
        <?php if (method_exists("\Strata\Strata", "app")) : ?>
            <?php $app = Strata::app(); ?>
            <h3>Context</h3>
            <?php
                $controller = $app->router->getCurrentController();
                $action = $app->router->getCurrentAction();
                $method = strtoupper($_SERVER['REQUEST_METHOD']);
            ?>
            <p>[<?php echo $method; ?>] <?php echo WP_HOME . $_SERVER['REQUEST_URI']; ?></p>
            <p>
                <?php if (!is_null($controller)) : ?>
                    Routed to <?php echo get_class($controller); ?>#<?php echo $action; ?>.
                <?php else : ?>
                    Strata did not route to a controller.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

</section>


