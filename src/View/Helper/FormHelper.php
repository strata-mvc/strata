<?php

namespace Strata\View\Helper;

use Strata\Model\ModelEntity;
use Strata\Strata;
use Exception;

/**
 * The FormHelper is an objects that helps create forms in the view files.
 * It automates the handling of ModelEntities.
 * @todo Complete the integration of the configurable trait
 */
class FormHelper extends Helper
{
    /**
     * @var Strata\Controller\Request The active request
     */
    private $request = null;

    /**
     * @var ModelEntity The model entity being edited by the form
     */
    protected $associatedEntity = null;

    /**
     * @var array Exposes the validation errors in the form
     */
    public $validationErrors = null;

    /**
     * @var array Exposes basic object configuration
     * @todo Move to the configurable array
     */
    public $keys = array(
        'POST_KEY_SUBMIT'       => "strata-submit",
        'POST_WRAP'             => "data"
    );

    /**
     * Opens up a form tag
     * @param  mixed   ModelEntity or null
     * @param  array  $options
     * @return string
     */
    public function create($mixed = null, $options = array())
    {
        $this->request = Strata::app()->router->getCurrentController()->request;

        if (!is_null($mixed) && !in_array('Strata\\Model\\CustomPostType\\ModelEntity', class_parents($mixed))) {
            throw new Exception("A form can only be linked to either an object inheriting ModelEntity or nothing at all.");
        }

        if (is_object($mixed)) {
            $this->associatedEntity = $mixed;
        }

        $this->configuration = $this->parseFormConfiguration($options);

        $formAttributes = $this->configuration;
        unset($formAttributes['hasSteps']);
        unset($formAttributes['type']);
        unset($formAttributes['nonce']);

        if (!is_null($this->associatedEntity) && $this->associatedEntity->hasValidationErrors()) {
            $this->validationErrors = $this->associatedEntity->getValidationErrors();

            if (array_key_exists('class', $formAttributes)) {
                $formAttributes['class'] .= " has-errors ";
            } else {
                $formAttributes['class'] = "has-errors";
            }
        }

        $htmlAttributes = $this->arrayToHtmlAttributes($formAttributes);

        $salt = $this->getNonceSalt();
        $nonceTag = $this->generateNonceTag($salt);
        $nonceHidden = $this->generateHidden(array("name" => "auth_id"), wp_create_nonce($salt));

        return sprintf("<form %s>\n%s\n%s\n", $htmlAttributes, $nonceHidden, $nonceTag);
    }

    /**
     * Closes a form tag
     * @return string
     */
    public function end()
    {
        return "</form>";
    }

    /**
     * Generates a honeypot field named $name.
     * Be wary of the name chosen as browser autocompleters may
     * fill in the form (themselves failing the honeypot test).
     * @param  string $name
     * @return string
     */
    public function honeypot($name)
    {
        $input = $this->input($name, array("name" => $name));
        $wrapperStyles = array(
            "height: 1px",
            "overflow: hidden",
            "padding:1px 0 0 1px",
            "position: absolute",
            "width: 1px",
            "z-index: -1"
        );

        return sprintf('<div class="validation" style="%s">%s</div>', implode("; ", $wrapperStyles), $input);
    }

    /**
     * Generates an id from a field name
     * @param  string $name
     * @return string
     */
    public function id($name)
    {
        $keepIdx = preg_replace('/\[(\d+)\]/', "_$1", $name);
        $unbracketted = $this->removeBrackets($keepIdx, '_');
        $nothingWeird = preg_replace('/[^\w\d\_]+?/', "", $unbracketted);
        $noTrail = preg_replace('/_$/', "", $nothingWeird);

        $clean = $noTrail;

        if (!is_null($this->associatedEntity) && $this->associatedEntity->isSupportedAttribute($name)) {
            $clean = $this->associatedEntity->getInputName() . "_" . $clean;
        }

        return $this->keys['POST_WRAP'] . "_" . $clean;
    }

    /**
     * Generates a valid field name
     * @param  string $name
     * @return string
     */
    public function name($name)
    {
        $prefix = !is_null($this->associatedEntity) && $this->associatedEntity->isSupportedAttribute($name) ?
            $this->keys['POST_WRAP'] . '[' . $this->associatedEntity->getInputName() . ']' :
            $this->keys['POST_WRAP'];

        // Transforms user[name] to data[user][name];
        if (preg_match('/^(.+?)\[(.+)?/', $name, $matches)) {
            return $prefix . '[' . $matches[1].'][' . $matches[2];
        }

        // Transforms user to data[user]
        return $prefix . '[' . $name . ']';
    }

    /**
     * Generates a submit button
     * @param  array  $options
     * @return string
     */
    public function submit($options = array())
    {
        $options["type"] = "submit";
        return $this->button($this->keys['POST_KEY_SUBMIT'], $options);
    }

    /**
     * Generates a button
     * @param  string $name
     * @param  array  $options
     * @return string
     */
    public function button($name, $options = array())
    {
        $options += array(
            "type" => "button",
            "label" => "Button",
            "id"    => $this->id($name),
            "name"  => $this->name($name),
        );

        $label = $options["label"];
        unset($options["label"]);

        return sprintf('<button %s>%s</button>', $this->arrayToHtmlAttributes($options), $label);
    }

    /**
     * Generates an input field
     * @param  string $name
     * @param  array  $options
     * @return string
     */
    public function input($name, $options = array())
    {
        $options += array(
            "type"  => "text",
            "id"    => $this->id($name),
            "name"  => $this->name($name),
            "error" => true,
            "class" => "",
            "value" => "",
            "label" => null
        );

        $currentValue = $this->getCurrentValue($options['name']);

        $errorHtml = "";
        if (array_key_exists($name, (array)$this->validationErrors)) {
            if ((bool)$options['error']) {
                $errorHtml = $this->generateInlineErrors($name);
            }
            $options['class'] .= " error ";
        }
        unset($options["error"]);

        $label = "";
        if (!is_null($options['label'])) {
            $label .= $this->generateLabel($options);
        }
        unset($options['label']);

        switch (strtolower($options['type'])) {
            case "textarea":
                return $label . "\n" . $this->generateTextarea($options, $currentValue) . $errorHtml . "\n";
            case "select":
                return $label . "\n" . $this->generateSelect($options, $currentValue) . $errorHtml . "\n";
            case "radio":
                return $this->generateRadio($options, $currentValue) . $errorHtml . "\n" . $label . "\n";
            case "checkbox":
                return $this->generateCheckbox($options, $currentValue) . $errorHtml . "\n" . $label . "\n";
            case "hidden":
                return $label . "\n" . $this->generateHidden($options, $currentValue) . $errorHtml . "\n";
            default:
                return $label . "\n" . $this->generateTextinput($options, $currentValue) . $errorHtml . "\n";
        }
    }

    /**
     * Generates the inline error messages for a field named $postName
     * @param  string $postName
     * @return string
     */
    public function generateInlineErrors($postName)
    {
        if (array_key_exists($postName, (array)$this->validationErrors)) {
            $errorTag = '<ul class="inline-errors">';
            foreach ($this->validationErrors[$postName] as $key => $message) {
                $errorTag .= sprintf('<li class="%s">%s</li>', $key, $message);
            }
            return $errorTag . '</ul>';
        }

        return "";
    }

    /**
     * Parses the supplied form configuration and populates
     * the missing default values.
     * @param  array $options
     * @return string
     */
    protected function parseFormConfiguration($options)
    {
        $options += array(
            "type" => "POST",
            "action" => $_SERVER['REQUEST_URI'],
            "nonce" => null
        );

        if (strtolower($options['type']) === "file") {
            $options["method"] = "POST";
            $options["enctype"] = "multipart/form-data";
        } else {
            $options["method"] = strtoupper($options['type']);
        }

        return $options;
    }

    /**
     * Generates a textarea
     * @param  array $options
     * @param  string $currentValue (Optional)
     * @return string
     */
    protected function generateTextarea($options, $currentValue = null)
    {
        $value = is_null($currentValue) ? $options['value'] : $currentValue;

        unset($options['value']);
        unset($options["type"]);

        return sprintf('<textarea %s>%s</textarea>', $this->arrayToHtmlAttributes($options), stripslashes($value));
    }

    /**
     * Generates a select field
     * @param  array $options
     * @param  string $currentValue (Optional)
     * @return string
     */
    protected function generateSelect($options, $currentValue = null)
    {
        $value = is_null($currentValue) ? $options['value'] : $currentValue;

        unset($options["type"]);
        unset($options["value"]);

        $optionsHtml = "";
        if (array_key_exists("choices", $options) && is_array($options["choices"])) {
            foreach ($options["choices"] as $key => $val) {
                $optionsHtml .= sprintf('<option%s value="%s">%s</option>', "$key" === "$value" ? ' selected="selected"' : '', $key, $val);
            }
            unset($options["choices"]);
        }

        return sprintf('<select %s>%s</select>', $this->arrayToHtmlAttributes($options), $optionsHtml);
    }

    /**
     * Generates a radio button
     * @param  array $options
     * @param  string $currentValue (Optional)
     * @return string
     */
    protected function generateRadio($options, $currentValue = null)
    {
        return sprintf('<input %s%s>', $this->arrayToHtmlAttributes($options), $options['value'] === $currentValue ? ' checked="checked"' : '');
    }

    /**
     * Generates a checkbox field
     * @param  array $options
     * @param  string $currentValue (Optional)
     * @return string
     */
    protected function generateCheckbox($options, $currentValue = null)
    {
        $hidden = sprintf('<input type="hidden" name="%s" value="0">', $options['name']);
        $chk = sprintf('<input %s %s>', $this->arrayToHtmlAttributes($options), $options['value'] == $currentValue ? ' checked="checked"' : '');
        return $hidden . $chk;
    }

    /**
     * Generates an hidden field
     * @param  array $options
     * @param  string $currentValue (Optional)
     * @return string
     */
    protected function generateHidden($options, $currentValue)
    {
        $value = is_null($currentValue) ? $options['value'] : $currentValue;

        unset($options["id"]);
        unset($options["value"]);

        if (!array_key_exists("type", $options)) {
            $options["type"] = "hidden";
        }

        if (is_array($value)) {
            $returnHtml = "";
            foreach ($value as $key => $val) {
                $returnHtml .= sprintf('<input %s value="%s">', $this->arrayToHtmlAttributes($options), $val);
            }
            return $returnHtml;
        } else {
            return sprintf('<input %s value="%s">', $this->arrayToHtmlAttributes($options), $value);
        }
    }

    /**
     * Generates a basic text field
     * @param  array $options
     * @param  string $currentValue (Optional)
     * @return string
     */
    protected function generateTextinput($options, $currentValue = null)
    {
        $value = is_null($currentValue) ? $options['value'] : $currentValue;

        if (!is_string($value)) {
            $value = "";
        }

        if (!array_key_exists("type", $options)) {
            $options["type"] = "text";
        }

        unset($options["value"]);

        return sprintf('<input %s value="%s">', $this->arrayToHtmlAttributes($options), $value);
    }

    /**
     * Generates a field label
     * @param  array $options
     * @return string
     */
    protected function generateLabel($options)
    {
        return sprintf('<label for="%s">%s</label>', $options['id'], $options['label']);
    }

    /**
     * Generates a html attributes from the $values hash.
     * @param  array $options
     * @return string
     */
    protected function arrayToHtmlAttributes(array $values)
    {
        $output = "";
        ksort($values);

        foreach ($values as $key => $value) {
            $output .=  sprintf('%s="%s" ', htmlentities($key), htmlentities($value));
        }

        return $output;
    }

    /**
     * Goes from HTML post names to dot notation values
     * @param  string $key
     * @param  string $replacement (Optional)
     * @return string
     */
    protected function removeBrackets($key, $replacement = '.')
    {
        return str_replace(array('[', ']'), array($replacement, ''), $key);
    }

    /**
     * Attemps to find the current value of a field named $key based on the
     * current request type.
     * @param  string $key
     * @return string
     */
    protected function getCurrentValue($key)
    {
        $key = $this->removeBrackets($key);

        if ($this->configuration['method'] === "GET" && $this->request->hasGet($key)) {
            return $this->request->get($key);
        }

        if ($this->configuration['method'] === "POST" && $this->request->hasPost($key)) {
            return $this->request->post($key);
        }

        if (!is_null($this->associatedEntity)) {
            $prefix = $this->keys['POST_WRAP'] . '.' . $this->associatedEntity->getInputName() . ".";
            $attributeName = str_replace($prefix, "", $key);
            if (isset($this->associatedEntity->{$attributeName})) {
                return $this->associatedEntity->{$attributeName};
            }
        }
    }

    /**
     * Generates a Wordpress Nonce tag
     * @param  string $salt
     * @return string
     */
    protected function generateNonceTag($salt)
    {
        return wp_nonce_field($salt, "authenticity_token", true, false);
    }

    /**
     * Generates a nonce salt
     * @return string
     */
    protected function getNonceSalt()
    {
        // Allow users to set their own nonce
        if (!is_null($this->configuration['nonce'])) {
            return $this->request->generateNonceKey($this->configuration['nonce']);
        // Use the entity if it is present
        } elseif (!is_null($this->associatedEntity)) {
            return $this->request->generateNonceKey($this->associatedEntity);
        }

        // Fallback to something custom for the Helper
        return $this->request->generateNonceKey();
    }
}
