<?php
/**
 * PageableTppAuthenticationGroupResources
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * finAPI RESTful Services
 *
 * finAPI RESTful Services
 *
 * OpenAPI spec version: v1.81.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.8
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;
use \Swagger\Client\ObjectSerializer;

/**
 * PageableTppAuthenticationGroupResources Class Doc Comment
 *
 * @category Class
 * @description TPP Authentication groups with paging information
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class PageableTppAuthenticationGroupResources implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'PageableTppAuthenticationGroupResources';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'tpp_authentication_groups' => '\Swagger\Client\Model\TppAuthenticationGroup[]',
        'paging' => '\Swagger\Client\Model\Paging'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'tpp_authentication_groups' => null,
        'paging' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'tpp_authentication_groups' => 'tppAuthenticationGroups',
        'paging' => 'paging'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'tpp_authentication_groups' => 'setTppAuthenticationGroups',
        'paging' => 'setPaging'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'tpp_authentication_groups' => 'getTppAuthenticationGroups',
        'paging' => 'getPaging'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['tpp_authentication_groups'] = isset($data['tpp_authentication_groups']) ? $data['tpp_authentication_groups'] : null;
        $this->container['paging'] = isset($data['paging']) ? $data['paging'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['tpp_authentication_groups'] === null) {
            $invalidProperties[] = "'tpp_authentication_groups' can't be null";
        }
        if ($this->container['paging'] === null) {
            $invalidProperties[] = "'paging' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets tpp_authentication_groups
     *
     * @return \Swagger\Client\Model\TppAuthenticationGroup[]
     */
    public function getTppAuthenticationGroups()
    {
        return $this->container['tpp_authentication_groups'];
    }

    /**
     * Sets tpp_authentication_groups
     *
     * @param \Swagger\Client\Model\TppAuthenticationGroup[] $tpp_authentication_groups List of received TPP authentication groups
     *
     * @return $this
     */
    public function setTppAuthenticationGroups($tpp_authentication_groups)
    {
        $this->container['tpp_authentication_groups'] = $tpp_authentication_groups;

        return $this;
    }

    /**
     * Gets paging
     *
     * @return \Swagger\Client\Model\Paging
     */
    public function getPaging()
    {
        return $this->container['paging'];
    }

    /**
     * Sets paging
     *
     * @param \Swagger\Client\Model\Paging $paging Information for pagination
     *
     * @return $this
     */
    public function setPaging($paging)
    {
        $this->container['paging'] = $paging;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


