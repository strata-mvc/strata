<?php

namespace Strata\View\Helper;

use Strata\Model\ModelEntity;
use Strata\Controller\Request;
use Exception;

class FormHelper extends Helper {

    private $request = null;
    protected $configuration = array();
    protected $associatedEntity = null;
    public $validationErrors = null;

    public $keys = array(
        'POST_KEY_SUBMIT'       => "strata-submit",
        'POST_WRAP'             => "data"
    );

    public function create($mixed = null, $options = array())
    {
        $this->request = new Request();

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

        return sprintf('<form %s>', $this->arrayToHtmlAttributes($formAttributes));
    }

    public function end()
    {
        return "</form>";
    }

    public function id($name)
    {
        $keepIdx = preg_replace('/\[(\d+)\]/', "_$1", $name);
        $unbracketted = $this->removeBrackets($keepIdx, '_');
        $nothingWeird = preg_replace('/[^\w\d\_]+?/', "", $unbracketted);
        $noTrail = preg_replace('/_$/', "", $nothingWeird);

        $clean = $noTrail;

        if (!is_null($this->associatedEntity) && in_array($name, array_keys($this->associatedEntity->attributes))) {
            $clean = $this->associatedEntity->getInputName() . "_" . $clean;
        }

        return $this->keys['POST_WRAP'] . "_" . $clean;
    }

    public function name($name)
    {
        $prefix = !is_null($this->associatedEntity) && in_array($name, array_keys($this->associatedEntity->attributes)) ?
            $this->keys['POST_WRAP'] . '[' . $this->associatedEntity->getInputName() . ']' :
            $this->keys['POST_WRAP'];

        // Transforms user[name] to data[user][name];
        if (preg_match('/^(.+?)\[(.+)?/', $name, $matches)) {
            return $prefix . '[' . $matches[1].'][' . $matches[2];
        }

        // Transforms user to data[user]
        return $prefix . '[' . $name . ']';
    }

    public function submit($options = array())
    {
        $options["type"] = "submit";
        return $this->button($this->keys['POST_KEY_SUBMIT'], $options);
    }

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
        if ((bool)$options['error'] && !is_null($this->associatedEntity) && $this->associatedEntity->hasValidationErrors()) {
            $this->validationErrors = $this->associatedEntity->getValidationErrors();
            $errorHtml = $this->generateInlineErrors($name);
            $options['class'] .= " error ";
        }
        unset($options["error"]);

        $label = "";
        if (!is_null($options['label'])) {
            $label .= $this->generateLabel($options);
        }
        unset($options['label']);

        switch (strtolower($options['type'])) {
            case "textarea" :   return $label . "\n" . $this->generateTextarea($options, $currentValue) . $errorHtml . "\n";
            case "select" :     return $label . "\n" . $this->generateSelect($options, $currentValue) . $errorHtml . "\n";
            case "radio" :      return $label . "\n" . $this->generateRadio($options, $currentValue) . $errorHtml . "\n";
            case "checkbox" :   return $label . "\n" . $this->generateCheckbox($options, $currentValue) . $errorHtml . "\n";
            case "hidden" :     return $label . "\n" . $this->generateHidden($options, $currentValue) . $errorHtml . "\n";
            default :           return $label . "\n" . $this->generateTextinput($options, $currentValue) . $errorHtml . "\n";
        }
    }

    public function generateInlineErrors($postName)
    {
        if (array_key_exists($postName, $this->validationErrors)) {
            $errorTag = '<ul class="inline-errors">';
            foreach ($this->validationErrors[$postName] as $key => $message) {
                $errorTag .= sprintf('<li class="%s">%s</li>', $key, $message);
            }
            return $errorTag . '</ul>';
        }
        return "";
    }

    protected function parseFormConfiguration($options)
    {
        $options += array(
            "type" => "POST",
            "hasSteps" => false,
            "action" => $_SERVER['REQUEST_URI']
        );

        if (strtolower($options['type']) === "file") {
            $options["method"] = "POST";
            $options["enctype"] = "multipart/form-data";
        } else {
            $options["method"] = strtoupper($options['type']);
        }

        return $options;
    }

    protected function generateTextarea($options, $currentValue = null)
    {
        $value = is_null($currentValue) ? $options['value'] : $currentValue;

        unset($options['value']);
        unset($options["type"]);

        return sprintf('<textarea %s>%s</textarea>', $this->arrayToHtmlAttributes($options), stripslashes($value));
    }

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

    protected function generateRadio($options, $currentValue = null)
    {
        return sprintf('<input %s%s>', $this->arrayToHtmlAttributes($options), $options['value'] === $currentValue ? ' checked="checked"' : '' );
    }

    protected function generateCheckbox($options, $currentValue = null)
    {
        $hidden = sprintf('<input type="hidden" name="%s" value="0">', $options['name']);
        $chk = sprintf('<input %s %s>', $this->arrayToHtmlAttributes($options),  $options['value'] == $currentValue ? ' checked="checked"' : '');
        return $hidden . $chk;
    }

    protected function generateHidden($options, $currentValue)
    {
        $value = is_null($currentValue) ? $options['value'] : $currentValue;

        unset($options["id"]);
        unset($options["value"]);

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

    protected function generateTextinput($options, $currentValue = null)
    {
        $value = is_null($currentValue) ? $options['value'] : $currentValue;

        $options["type"] = "text";
        unset($options["value"]);

        return sprintf('<input %s value="%s">', $this->arrayToHtmlAttributes($options), $value);
    }

    protected function generateLabel($options)
    {
        return sprintf('<label for="%s">%s</label>', $options['id'], $options['label']);
    }

    protected function arrayToHtmlAttributes(array $values)
    {
        $output = "";

        foreach($values as $key => $value){
            $output .=  sprintf('%s="%s" ', htmlentities($key), htmlentities($value));
        }

        return $output;
    }

    protected function removeBrackets($key, $replacement = '.')
    {
        return str_replace(array('[', ']'), array($replacement, ''), $key);
    }

    protected function getCurrentValue($key)
    {
        if ($this->configuration['type'] === "GET") {
            return $this->request->get($key);
        }
        return $this->request->post($key);
    }
}
