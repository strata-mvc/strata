
<section>
    <header>
        <h1><?php echo $error['type']; ?></h1>
        <h3><?php echo $error['description']; ?></h3>
        <h4>in <?php echo basename($error['file']); ?> on line <?php echo $error['line']; ?></h4>
    </header>

    <?php $filename = $error['file']; ?>
    <?php if ($filename) : ?>
        <?php $lines = file($filename); ?>
        <?php if (count($lines)) : ?>
        <div class="source">~ <?php echo $error['file']; ?></div>
        <table>
            <?php $specificLine = $error['line']; ?>
            <?php $start = $specificLine - 4; ?>
            <?php $end = $specificLine + 4; ?>
            <?php for ($i = $start; $i < $end; $i++) : ?>
            <tr>
                <td class="lines"><?php echo ($i + 1); ?></td>
                <td class="code <?php echo $specificLine === ($i + 1) ? "focus" : ""; ?>"><?php echo htmlentities($lines[$i]); ?></td>
            </tr>
            <?php endfor; ?>
        </table>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($error['trace'])) : ?>
        <pre><?php echo $error['trace']; ?></pre>
    <?php endif ;?>

</section>


