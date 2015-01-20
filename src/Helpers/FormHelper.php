<?php

namespace MVC\Helpers;

use MVC\Utility\Hash;

class FormHelper {

    const POST_KEY_CURRENT      = "mvc-current-step";
    const POST_KEY_NEXT_PAGE    = "mvc-next-step";
    const POST_KEY_PREVIOUS_PAGE = "mvc-previous-step";
    const POST_KEY_GO_TO_STEP   = "mvc-goto-step";
    const POST_KEY_SUBMIT       = "mvc-submit";
    const POST_WRAP             = "data";
    const FIELD_PREFIX          = "input_";

    // @todo: consider adding getters/setters and making this private
    public $currentStep = 1;
    public $stepsQty = 1;
    public $errors = array();
    public $assigned = array();

    private $_wpReserved = array(
        'attachment',
        'attachment_id',
        'author',
        'author_name',
        'calendar',
        'cat',
        'category',
        'category__and',
        'category__in',
        'category__not_in',
        'category_name',
        'comments_per_page',
        'comments_popup',
        'customize_messenger_channel',
        'customized',
        'cpage',
        'day',
        'debug',
        'error',
        'exact',
        'feed',
        'hour',
        'link_category',
        'm',
        'minute',
        'monthnum',
        'more',
        'name',
        'nav_menu',
        'nonce',
        'nopaging',
        'offset',
        'order',
        'orderby',
        'p',
        'page',
        'page_id',
        'paged',
        'pagename',
        'pb',
        'perm',
        'post',
        'post__in',
        'post__not_in',
        'post_format',
        'post_mime_type',
        'post_status',
        'post_tag',
        'post_type',
        'posts',
        'posts_per_archive_page',
        'posts_per_page',
        'preview',
        'robots',
        's',
        'search',
        'second',
        'sentence',
        'showposts',
        'static',
        'subpost',
        'subpost_id',
        'tag',
        'tag__and',
        'tag__in',
        'tag__not_in',
        'tag_id',
        'tag_slug__and',
        'tag_slug__in',
        'taxonomy',
        'tb',
        'term',
        'theme',
        'type',
        'w',
        'withcomments',
        'withoutcomments',
        'year',
    );

    public function __construct()
    {
        $postedStep = (int)$this->getPostedValue(self::POST_KEY_CURRENT);
        if($postedStep > 0) {
            $this->currentStep = $postedStep;
        }
    }

    public function getCurrentStep()
    {
        return $this->currentStep;
    }

    public function hasSteps()
    {
        return (int)$this->stepsQty > 1;
    }

    public function contextWantsToSubmit()
    {
        return $this->hasPostedValue(self::POST_KEY_SUBMIT);
    }

    public function contextWantsToGoForward()
    {
        return $this->hasPostedValue(self::POST_KEY_NEXT_PAGE);
    }

    public function contextWantsToGoBackwards()
    {
        return $this->hasPostedValue(self::POST_KEY_PREVIOUS_PAGE);
    }

    public function contextWantsToGoToStep()
    {
        return $this->hasPostedValue(self::POST_KEY_GO_TO_STEP);
    }

    public function getPostedValue($key)
    {
        $key = str_replace('[]', '', $key); // posted arrays (ex: 'users[]') didn't match.
        return Hash::get($_POST, $this->_removeBrackets($this->name($key)));
    }

    public function hasPostedValue($key)
    {
        $key = str_replace('[]', '', $key); // posted arrays (ex: 'users[]') didn't match.
        return Hash::check($_POST, $this->_removeBrackets($this->name($key)));
    }

    public function hasErrors($key = null)
    {
        if (is_null($key)) {
            return count($this->errors) > 0;
        }

        $key = str_replace('[]', '', $key); // posted arrays (ex: 'users[]') didn't match.
        return count(Hash::get($this->errors, $key)) > 0;
    }

    public function getError($key)
    {
        $key = str_replace('[]', '', $key); // posted arrays (ex: 'users[]') didn't match.
        return Hash::get($this->errors, $key);
    }

    public function error($key, $options = array())
    {
        $errorTag = "";
        $errorObj = $this->getError($key);

        if (!is_null($errorObj)) {
            $errorTag .= '<ul class="inline-errors">';
            foreach ($errorObj as $key => $message) {
                $errorTag .= sprintf('<li class="%s">%s</li>', $key, $message);
            }
            $errorTag .= '</ul>';
        }

        return $errorTag;
    }

    public function create($options = array())
    {
        $options += array(
            "type" => "POST",
            "action" => $_SERVER['REQUEST_URI']
        );

        if ($options['type'] === "file") {
            $options["method"] = "POST";
            $options["enctype"] = "multipart/form-data";
        } else {
            $options["method"] = strtoupper($options['type']);
        }
        // Remove the unsupported attributes once were have mapped them to real html ones.
        unset($options['type']);

        $additional = wp_nonce_field($this->_formKey, "mvc-nonce", true, false);

        if ($this->hasSteps()) {
            // Keep a backlog of previously set values.
            $additional .= $this->_createBacklog();
            // And the current step
            $additional .= $this->input(self::POST_KEY_CURRENT, array("type" => "hidden", "value" => $this->currentStep));
        }

        return sprintf('<form %s>', $this->_arrayToAttributes($options)) . $additional;
    }

    public function end()
    {
        return "</form>";
    }

    public function id($name)
    {
        $keepIdx = preg_replace('/\[(\d+)\]/', "_$1", $name);
        $unbracketted = $this->_removeBrackets($keepIdx, '_');
        $nothingWeird = preg_replace('/[^\w\d\_]+?/', "", $unbracketted);
        $noTrail = preg_replace('/_$/', "", $nothingWeird);

        return self::FIELD_PREFIX . $noTrail;
    }

    public function name($name)
    {
        // To ensure our parameters don't bump in wordpress' get parameters (notably the custom
        // post types slugs), wrap the inputs in a data[] wrapper

        // Transforms user[name] to data[user][name];
        if (preg_match('/^(.+?)\[(.+)?/', $name, $matches)) {
            return self::POST_WRAP . '[' . $matches[1].'][' . $matches[2];
        }

        // Transforms user to data[user]
        return self::POST_WRAP . '[' . $name . ']';
    }

    public function input($name, $options = array())
    {
        $this->_validatePostName($name);

        $options += array(
            "type"  => "text",
            "id"    => $this->id($name),
            "name"  => $this->name($name),
            "inline-errors" => array(),
            "class" => ""
        );

        $prefixing = "";
        $suffixing = "";
        $value = $this->getPostedValue($name);

        // Display errors if there are any
        if ($options["inline-errors"] !== false) {
            $suffixing .= $this->error($name, $options["inline-errors"]);
        }
        unset($options["inline-errors"]);

        if($this->hasErrors($name)) {
            $options["class"] .= " error ";
        }

        // Display the input control wrapped in predefined html should
        // it have been necessary.
        switch ($options['type']) {
            case "textarea" :
                unset($options["type"]);
                return $prefixing . sprintf('<textarea %s>%s</textarea>', $this->_arrayToAttributes($options), stripslashes($value)) . $suffixing;

            case "select" :
                $choices = "";
                if (array_key_exists("choices", $options) && is_array($options["choices"])) {
                    foreach ($options["choices"] as $key => $val) {
                        $choices .= sprintf('<option%s value="%s">%s</option>', $key.'' === $value.'' ? ' selected="selected"' : '', $key, $val);
                    }
                    unset($options["choices"]);
                }
                unset($options["type"]);
                return $prefixing . sprintf('<select %s>%s</select>', $this->_arrayToAttributes($options), $choices) . $suffixing;

            case "radio" :
                 $choices .= sprintf('<input %s%s>', $this->_arrayToAttributes($options), "".$options['value'] === $value ? ' checked="checked"' : '' );
                return $prefixing . $choices . $suffixing;

            case "checkbox" :
                $hidden = sprintf('<input type="hidden" name="%s" value="0">', $options['name']);
                $chk = sprintf('<input %s %s>', $this->_arrayToAttributes($options),  $options['value'] == $value ? ' checked="checked"' : '');
                return $prefixing . $hidden . $chk . $suffixing;

            case 'hidden' :
                unset($options["id"]);
                if (is_array($value)) {
                    $return = "";
                    foreach ($value as $key => $val) {
                        $return .= sprintf('<input %s value="%s">', $this->_arrayToAttributes($options), $val);
                    }
                    return $prefixing . $return . $suffixing;
                } else {
                    return $prefixing . sprintf('<input %s value="%s">', $this->_arrayToAttributes($options), $value) . $suffixing;
                }


            default :
                return $prefixing . sprintf('<input %s value="%s">', $this->_arrayToAttributes($options), $value) . $suffixing;
        }
    }

    public function submit($options = array())
    {
        $options["type"] = "submit";
        return $this->button(self::POST_KEY_SUBMIT, $options);
    }

    public function previous($options = array())
    {
        $options["type"] = "submit";
        return $this->button(self::POST_KEY_PREVIOUS_PAGE, $options);
    }

    public function next($options = array())
    {
        $options["type"] = "submit";
        return $this->button(self::POST_KEY_NEXT_PAGE, $options);
    }

    public function gotostep($stepIdx = 1, $options = array())
    {
        $options["type"] = "submit";
        $options["value"] = $stepIdx;
        return $this->button(self::POST_KEY_GO_TO_STEP, $options);
    }

    public function button($name, $options = array())
    {
        $this->_validatePostName($name);

        $options += array(
            "type" => "button",
            "label" => "Button",
            "id"    => $this->id($name),
            "name"  => $this->name($name),
        );

        $label = $options["label"];
        unset($options["label"]);

        return sprintf('<button %s>%s</button>', $this->_arrayToAttributes($options), $label);
    }

    public function getStepsHtml($options = array())
    {
        if (!$this->hasSteps()) {
            return "";
        }

        $currentStep = $this->getCurrentStep();
        $options += array(
            'wrapperTpl'    => '<ul class="steps">%s</ul>',
            'stepTpl'       => '<li class="%s"><span class="step-number">%s</span>%s</li>',
            'stepTextTpl'   => '<span class="step-text">%s</span>',
            'titles'        => null,
            'allow-step-nav'  => true
        );

        $stepsHtml = "";
        $idx = 0;
        while ($idx++ < $this->stepsQty) {
            $classnames = array();
            $label = $idx;

            if ($currentStep === $idx) {
                $classnames[] = "active";
            } elseif ($idx < $currentStep) {
                $classnames[] = "completed";
                if ($options['allow-step-nav']) {
                    $label = $this->gotostep($idx, array("label" => $idx));
                }

            } elseif ($idx > $currentStep) {
                $classnames[] = "future";
            }

            $stepDetails = "";
            if (is_array($options['titles'])) {
                $stepDetails = sprintf($options['stepTextTpl'], $options['titles'][$idx-1]);
            }

            $stepsHtml .= sprintf($options['stepTpl'], implode(" ", $classnames), $label, $stepDetails);
        }

        return sprintf($options['wrapperTpl'], $stepsHtml);
    }

    protected function _arrayToAttributes($attributes)
    {
        $output = "";
        foreach($attributes as $key => $value){
            $output .=  sprintf('%s="%s" ', $key, $value);
        }
        return $output;
    }

    protected function _createBacklog()
    {
        $backlogHtml = "";
        foreach ($this->assigned as $key => $value) {
            if (!is_array($value)) {
                $backlogHtml .= $this->input($key, array("type" => "hidden", "value" => $value));
            } else {
                foreach($value as $idx => $singleValue) {
                    $backlogHtml .= $this->input($key . "[".$idx."]", array("type" => "hidden", "value" => $singleValue));
                }
            }
        }
        return $backlogHtml;
    }

    protected function _validatePostName($name)
    {
        if (in_array($name, $this->_wpReserved)) {
            throw new \Exception(sprintf("Using Wordpress reserved POST/GET name %s in form.", $name));
        }
    }

    protected function _removeBrackets($key, $replacement = '.')
    {
        return str_replace(array('[', ']'), array($replacement, ''), $key);
    }

}
