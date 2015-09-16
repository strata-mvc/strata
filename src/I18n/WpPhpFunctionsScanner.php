<?php
namespace Strata\I18n;

use Gettext\Utils\PhpFunctionsScanner;
use Gettext\Translations;

use Exception;

/** The list of these can be found in Wordpress' MakePot file
    http://develop.svn.wordpress.org/trunk/tools/i18n/makepot.php
 */
class WpPhpFunctionsScanner extends PhpFunctionsScanner
{
    public function saveWPGettextFunctions(array $functions, Translations $translations, $file = '')
    {
        foreach ($this->getFunctions() as $function) {
            list($name, $line, $args) = $function;

            if (!isset($functions[$name])) {
                continue;
            }

            $translation = null;

            switch ($functions[$name]) {
                case '__':
                    if (!isset($args[0])) {
                        continue 2;
                    }
                    $original = $args[0];
                    if ($original !== '') {
                        $translation = $translations->insert('', $original);
                    }
                    break;

                case 'wp_n__x_n':
                    if (!isset($args[1])) {
                        continue 2;
                    }
                    $original = $args[0];
                    $plural = $args[1];
                    $context = $args[3];

                    if ($original !== '') {
                        $translation = $translations->insert($context, $original, $plural);
                    }
                    break;


                case 'wp_n__x':
                    if (!isset($args[1])) {
                        continue 2;
                    }
                    $original = $args[0];
                    $plural = $args[1];
                    $context = $args[2];

                    if ($original !== '') {
                        $translation = $translations->insert($context, $original, $plural);
                    }
                    break;


                // Works the same as p__ but the arguments
                // are reversed.
                case 'wp_p__':

                    if (!isset($args[1])) {
                        continue 2;
                    }

                    $original = $args[0];
                    $context = $args[1];

                    if ($original !== '') {
                        $translation = $translations->insert($context, $original);
                    }
                    break;

                default:
                    throw new Exception('Not valid functions ' . $functions[$name]);
            }

            if (isset($translation)) {
                $translation->addReference($file, $line);
            }
        }
    }

}
