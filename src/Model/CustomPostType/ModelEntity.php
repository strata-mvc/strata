<?php

namespace Strata\Model\CustomPostType;

use Strata\Utility\Hash;
use Strata\Model\Validator\Validator;
use Strata\Core\StrataObjectTrait;
use Strata\Controller\Request;
use Strata\Logger\Debugger;
use stdClass;
use Exception;
use WP_Post;
use WP_Term;

/**
 * A ModelEntity can vaguely be seen as a table row. It is a class
 * that wraps around a Model instance.
 */
class ModelEntity
{
    use StrataObjectTrait;

    /**
     * {@inheritdoc}
     */
    public static function getNamespaceStringInStrata()
    {
        return "Model\\Entity";
    }

    /**
     * {@inheritdoc}
     */
    public static function getClassNameSuffix()
    {
        return "Entity";
    }

   /**
     * Factories a model entity based on the Wordpress key
     * @param  string $str
     * @return mixed An instanciated model
     * @throws Exception
     */
    public static function factoryFromString($str, $associatedObject = null)
    {
        $obj = null;

        if (preg_match('/_?(cpt|tax)_(\w+)/', $str, $matches)) {
            $obj = self::factory($matches[2]);
        } elseif (preg_match('/_?(post|page)/', $str, $matches)) {
            $obj = self::factory($matches[1]);
        }

        if (!is_null($obj)) {
            if (!is_null($associatedObject)) {
                $obj->bindToObject($associatedObject);
            }

            return $obj;
        }

        throw new Exception("Unknown pattern sent to ModelEntity::factoryFromString: " . $str);
    }

    public static function factoryFromPost(WP_Post $post)
    {
        return self::factoryFromString($post->post_type, $post);
    }

    public static function factoryFromTerm(WP_Term $term)
    {
        return self::factoryFromString($term->taxonomy, $term);
    }

    public static function factoryFromWpQuery()
    {
        global $wp_query;
        $obj = $wp_query->queried_object;

        if (is_a($obj, "WP_Term")) {
            return self::factoryFromTerm($obj);
        }

        if (is_a($obj, "WP_Post")) {
            return self::factoryFromPost($obj);
        }

        throw new Exception("Unknown pattern obtained from ModelEntity::factoryFromWpQuery: " . get_class($obj));
    }

    /**
     * A list of model attributes used for automated form
     * generation and validation.
     * @var array
     */
    public $attributes  = array();

    /**
     * The object being wrapped around by this class
     * @var mixed Usually a WP_Post object, but can also be anything inheriting stdClass
     */
    private $associatedObject;

    /**
     * Current validation errors on the entity.
     * @var array
     */
    private $validationErrors = array();

    /**
     * Upon construction, an entity is associated to an object
     * if one is passed as parameter, the attributes are normalized
     * and the class triggers the init() function.
     * @var mixed (Optional) Usually a WP_Post object, but can also be anything inheriting stdClass
     */
    public function __construct($associatedObj = null)
    {
        $this->bindToObject(!is_null($associatedObj) ? $associatedObj : new stdClass());

        $this->normalizeAttributes();
        $this->init();
    }

    /**
     * Called each time a new object is declared.
     */
    public function init()
    {

    }

    /**
     * Automated getter. It bridges properties between this object
     * and the associated object.
     * @param  string $var The property name
     * @return mixed
     */
    public function __get($var)
    {
        if (is_null($this->associatedObject)) {
            throw new Exception( get_class($this) . ' was not linked to a Wordpress object.');
        }

        if (property_exists($this->associatedObject, $var)) {
            return $this->associatedObject->{$var};
        }
    }


    /**
     * Automated setter. It bridges properties between this object
     * and the associated object.
     * @param  string $var The property name
     * @return mixed
     */
    public function __set($var, $value)
    {
        if (is_null($this->associatedObject)) {
            return $this->{$var} = $value;
        }

        return $this->associatedObject->{$var} = $value;
    }

    /**
     * Automated validator. It bridges properties between this object
     * and the associated object.
     * @param  string $var The property name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->associatedObject->{$name});
    }

    /**
     * Used to express the contents of the model entity more clearly
     * when debugged.
     * @return array
     */
    public function __debugInfo()
    {
        $objectVars = array();

        if (!is_null($this)) {
            foreach (get_object_vars($this) as $key => $value) {
                $objectVars[$key] = Debugger::export($value);
            }
        }

        return array_merge($objectVars, $this->toArray());
    }

    /**
     * Associates the entity to another object.
     * We pass-through rather than directly assign the properties of the associated object
     * because we don't want this object to inherit WP_Post.
     * @var mixed Usually a WP_Post object, but can also be anything inheriting stdClass
     */
    public function bindToObject($obj)
    {
        if (is_array($obj)) {
            $obj = (object)$obj;
        }

        $this->associatedObject = $obj;
    }

    /**
     * Specifies whether this class has been associated to another object.
     * @return boolean
     */
    public function isBound()
    {
        return !is_null($this->associatedObject);
    }

    /**
     * Returns a list of properties defined on the bound object.
     * @return array
     */
    public function toArray()
    {
        return (array)get_object_vars($this->associatedObject);
    }

    /**
     * Returns a list of properties defined on the bound object as json.
     * @param array config (optional)
     * @return string
     */
    public function toJson($config = null)
    {
        return json_encode($this->toArray(), $config);
    }

    /**
     * Assigns the entity data that may be found in the request
     * to this entity.
     * @param  Request $request
     * @return bool True when entity data was found.
     */
    public function assignRequest(Request $request)
    {
        $extracted = $this->extractData($request);

        if (count($extracted)) {
            foreach ($this->getAttributeNames() as $key) {
                if (array_key_exists($key, $extracted)) {
                    $this->{$key} = $extracted[$key];
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Extracts the request values related to the entity
     * from a supplied request object.
     * @param  Request $request
     * @return array
     */
    public function extractData(Request $request)
    {
        $data = $request->data();
        $entityData = array();
        $inputName = $this->getInputName();

        if (!array_key_exists($inputName, $data)) {
            return array();
        }

        $entityData = $data[$inputName];
        $filteredData = array();

        foreach ($this->getAttributeNames() as $key) {
            if (array_key_exists($key, $entityData)) {
                $filteredData[$key] = $entityData[$key];
            }
        }

        return $filteredData;
    }

    /**
     * Runs validation on the current entity values as declared
     * by the entity's attributes.
     * @return boolean True if validation passed
     */
    public function validates()
    {
        $currentAttributeValues = array();

        if ($this->isBound()) {
            foreach ($this->getAttributeNames() as $name) {
                $currentAttributeValues[$name] = $this->{$name};
            }
        }

        return $this->validate(array(
            $this->getInputName() => $currentAttributeValues
        ));
    }

    /**
     * Runs validation in a hash object that may or may
     * not be the result of request->data(), but has the same
     * format.
     * @return boolean True if validation passed
     */
    public function validate(array $requestData)
    {
        $ourData = Hash::extract($requestData, $this->getInputName());

        foreach ($this->getAttributeNames() as $name) {

            $validations = $this->extractNormalizedValidations($name);
            foreach ($validations as $validationKey => $validatorConfig) {

                $validator = Validator::factory($validationKey);
                $validator->configure($validatorConfig);
                $validator->init();

                if (!array_key_exists($name, $ourData) || !$validator->test($ourData[$name], $this)) {
                    $this->setValidationError($name, $validationKey, $validator->getMessage());
                }
            }
        }

        return !$this->hasValidationErrors();
    }

    /**
     * Returns the current validation error list
     * @return array
     */
    public function getValidationErrors()
    {
        return (array)$this->validationErrors;
    }

    /**
     * Specifies whether there are validation errors currently
     * declared on the entity.
     * @return boolean
     */
    public function hasValidationErrors()
    {
        return count($this->getValidationErrors()) > 0;
    }

    /**
     * Allows the declaration of a validation error on the current entity.
     * @param string $attributeName      The name of a model attribute
     * @param string $validationName A type of validation (ex: 'required')
     * @param string $errorMessage   The error message
     */
    public function setValidationError($attributeName, $validationName, $errorMessage)
    {
        $this->validationErrors = Hash::merge($this->getValidationErrors(), array(
            $attributeName => array(
                $validationName => $errorMessage
            )
        ));
    }

    /**
     * Lists the errors on a precise field.
     * @param  string $name An attribute name
     * @return array
     */
    public function getErrors($name)
    {
        $errors = $this->getValidationErrors();

        if ($this->hasErrors($name)) {
            return $errors[$name];
        }

        return array();
    }

    /**
     * Checks for errors on a precise field.
     * @param  string $name An attribute name
     * @return bool
     */
    public function hasErrors($name)
    {
        return array_key_exists($name, $this->getValidationErrors());
    }



    /**
     * Saves the model entity the current post type based on the
     * associated object's values.
     * @return boolean Whether something was updated
     */
    public function save()
    {
        if ($this->isBound()) {

            if (!isset($this->post_type)) {
                $this->post_type = $this->getWordpressKey();
            }

            if (!isset($this->ping_status)) {
                $this->ping_status = false;
            }

            if (!isset($this->comment_status)) {
                $this->comment_status = false;
            }

            if (!isset($this->ID)) {
                $this->ID = '';
            }

            if ((int)$this->ID > 0) {
                return wp_update_post($this->associatedObject);
            }

            $id = wp_insert_post($this->associatedObject);

            if ((int)$id > 0) {
                $this->ID = $id;
                return true;
            }

            return false;
        }
    }

    /**
     * Deletes the current entity
     * @param  boolean $force  (optional)
     * @return boolean Whether something was updated
     */
    public function delete($force = false)
    {
        return wp_delete_post($this->ID, $force);
    }

    /**
     * Returns the model entity's attributes list.
     * @return array
     */
    public function getAttributes()
    {
        return (array)$this->attributes;
    }

    /**
     * Lists the attributes name without their configuration.
     * @return array
     */
    protected function getAttributeNames()
    {
        return array_keys($this->getAttributes());
    }

    /**
     * Checks whether the attribute is an attribute that has
     * been declared in the entity's attribute configuration.
     * @param  string  $attr
     * @return boolean
     */
    public function isSupportedAttribute($attr)
    {
        $simpleAttr = preg_replace("/\[\d+\]$/", "", $attr);
        return in_array($simpleAttr, $this->getAttributeNames());
    }

    /**
     * Checks whether the specified attribute has declared any validations.
     * @param  string  $attr
     * @return boolean
     */
    protected function hasAttributeValidation($attr)
    {
        return Hash::check($this->getAttributes(), "$attr.validations");
    }

    /**
     * Returns the input field suffix when the entity is being
     * used to generate HTML forms.
     * @return string
     */
    public function getInputName()
    {
        return strtolower($this->getShortName());
    }

    /**
     * Returns the accompanying Model of this entity.
     * @return Strata\Model
     */
    public function getModel()
    {
        $name = $this->getShortName();
        $name = str_replace("Entity", "", $name);

        return CustomPostType::factory($name);
    }

    /**
     * Returns the entity's unique wordpress key
     * @return string
     */
    public function getWordpressKey()
    {
        $model = $this->getModel();
        return $model->getWordpressKey();
    }

    /**
     * Normalizes the validations for a specified attribute.
     * @param  string $attr
     * @return array
     */
    private function extractNormalizedValidations($attr)
    {
        return Hash::normalize(Hash::extract($this->getAttributes(), "$attr.validations"));
    }

    /**
     * Normalized the full list of attribute. This is not performed 'deep'.
     */
    private function normalizeAttributes()
    {
        $this->attributes = Hash::normalize($this->attributes);
    }
}
