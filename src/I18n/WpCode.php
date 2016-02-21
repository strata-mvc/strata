<?php

namespace Strata\I18n;

use Strata\I18n\PhpFunctionsScanner;
use Gettext\Extractors\PhpCode;
use Gettext\Translations;

/**
 * Class to lookup Gettext strings from PHP files that are coded
 * using the slightly different Wordpress translation scheme.
 */
class WpCode extends PhpCode
{
    /**
     * These functions behave like the general Gettext implementation. We let them
     * fall back to the default behavior.
     * @var array
     */
    public static $functions = array(
        '_' => '__',
        '__' => '__',
        '_e' => '__',
        '_c' => '__',
        '_n' => 'n__',
        '_n_noop' => 'n__',
        '_nc' => 'n__',
        '__ngettext' => 'n__',
        '__ngettext_noop' => 'n__',
        '_n_js' => 'n__',
        'esc_attr__' => '__',
        'esc_html__' => '__',
        'esc_attr_e' => '__',
        'esc_html_e' => '__',
    );

    /**
     * These functions have been "Wordpressed" -- meaning parameters
     * may not be in the same order as the default Gettext functions.
     * They imply custom manipulation on our part.
     * @var array
     */
    public static $wp_functions = array(
        '_x' => 'wp_p__',
        '_ex' => 'wp_p__',
        'esc_attr_x' => 'wp_p__',
        'esc_html_x' => 'wp_p__',
        '_nx' => 'wp_n__x_n',
        '_nx_js' => 'wp_n__x',
        '_nx_noop' => 'wp_n__x',
        'comments_number_link' => 'wp_n__x',
    );

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $functions = new WpPhpFunctionsScanner($string);
        $functions->saveGettextFunctions(self::$functions, $translations, $file);
        $functions->saveWPGettextFunctions(self::$wp_functions, $translations, $file);
    }
}
