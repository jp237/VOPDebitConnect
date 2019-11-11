<?php
/**
 * RequestSepaMoneyTransferParams
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
 * RequestSepaMoneyTransferParams Class Doc Comment
 *
 * @category Class
 * @description Parameters for a single or collective SEPA money transfer order request
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class RequestSepaMoneyTransferParams implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'RequestSepaMoneyTransferParams';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'recipient_name' => 'string',
        'recipient_iban' => 'string',
        'recipient_bic' => 'string',
        'clearing_account_id' => 'string',
        'amount' => 'float',
        'purpose' => 'string',
        'sepa_purpose_code' => 'string',
        'account_id' => 'int',
        'banking_pin' => 'string',
        'store_secrets' => 'bool',
        'two_step_procedure_id' => 'string',
        'execution_date' => 'string',
        'single_booking' => 'bool',
        'additional_money_transfers' => '\Swagger\Client\Model\SingleMoneyTransferRecipientData[]',
        'challenge_response' => 'string',
        'multi_step_authentication' => '\Swagger\Client\Model\MultiStepAuthenticationCallback',
        'hide_transaction_details_in_web_form' => 'bool',
        'store_pin' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'recipient_name' => null,
        'recipient_iban' => null,
        'recipient_bic' => null,
        'clearing_account_id' => null,
        'amount' => null,
        'purpose' => null,
        'sepa_purpose_code' => null,
        'account_id' => 'int64',
        'banking_pin' => null,
        'store_secrets' => null,
        'two_step_procedure_id' => null,
        'execution_date' => null,
        'single_booking' => null,
        'additional_money_transfers' => null,
        'challenge_response' => null,
        'multi_step_authentication' => null,
        'hide_transaction_details_in_web_form' => null,
        'store_pin' => null
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
        'recipient_name' => 'recipientName',
        'recipient_iban' => 'recipientIban',
        'recipient_bic' => 'recipientBic',
        'clearing_account_id' => 'clearingAccountId',
        'amount' => 'amount',
        'purpose' => 'purpose',
        'sepa_purpose_code' => 'sepaPurposeCode',
        'account_id' => 'accountId',
        'banking_pin' => 'bankingPin',
        'store_secrets' => 'storeSecrets',
        'two_step_procedure_id' => 'twoStepProcedureId',
        'execution_date' => 'executionDate',
        'single_booking' => 'singleBooking',
        'additional_money_transfers' => 'additionalMoneyTransfers',
        'challenge_response' => 'challengeResponse',
        'multi_step_authentication' => 'multiStepAuthentication',
        'hide_transaction_details_in_web_form' => 'hideTransactionDetailsInWebForm',
        'store_pin' => 'storePin'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'recipient_name' => 'setRecipientName',
        'recipient_iban' => 'setRecipientIban',
        'recipient_bic' => 'setRecipientBic',
        'clearing_account_id' => 'setClearingAccountId',
        'amount' => 'setAmount',
        'purpose' => 'setPurpose',
        'sepa_purpose_code' => 'setSepaPurposeCode',
        'account_id' => 'setAccountId',
        'banking_pin' => 'setBankingPin',
        'store_secrets' => 'setStoreSecrets',
        'two_step_procedure_id' => 'setTwoStepProcedureId',
        'execution_date' => 'setExecutionDate',
        'single_booking' => 'setSingleBooking',
        'additional_money_transfers' => 'setAdditionalMoneyTransfers',
        'challenge_response' => 'setChallengeResponse',
        'multi_step_authentication' => 'setMultiStepAuthentication',
        'hide_transaction_details_in_web_form' => 'setHideTransactionDetailsInWebForm',
        'store_pin' => 'setStorePin'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'recipient_name' => 'getRecipientName',
        'recipient_iban' => 'getRecipientIban',
        'recipient_bic' => 'getRecipientBic',
        'clearing_account_id' => 'getClearingAccountId',
        'amount' => 'getAmount',
        'purpose' => 'getPurpose',
        'sepa_purpose_code' => 'getSepaPurposeCode',
        'account_id' => 'getAccountId',
        'banking_pin' => 'getBankingPin',
        'store_secrets' => 'getStoreSecrets',
        'two_step_procedure_id' => 'getTwoStepProcedureId',
        'execution_date' => 'getExecutionDate',
        'single_booking' => 'getSingleBooking',
        'additional_money_transfers' => 'getAdditionalMoneyTransfers',
        'challenge_response' => 'getChallengeResponse',
        'multi_step_authentication' => 'getMultiStepAuthentication',
        'hide_transaction_details_in_web_form' => 'getHideTransactionDetailsInWebForm',
        'store_pin' => 'getStorePin'
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
        $this->container['recipient_name'] = isset($data['recipient_name']) ? $data['recipient_name'] : null;
        $this->container['recipient_iban'] = isset($data['recipient_iban']) ? $data['recipient_iban'] : null;
        $this->container['recipient_bic'] = isset($data['recipient_bic']) ? $data['recipient_bic'] : null;
        $this->container['clearing_account_id'] = isset($data['clearing_account_id']) ? $data['clearing_account_id'] : null;
        $this->container['amount'] = isset($data['amount']) ? $data['amount'] : null;
        $this->container['purpose'] = isset($data['purpose']) ? $data['purpose'] : null;
        $this->container['sepa_purpose_code'] = isset($data['sepa_purpose_code']) ? $data['sepa_purpose_code'] : null;
        $this->container['account_id'] = isset($data['account_id']) ? $data['account_id'] : null;
        $this->container['banking_pin'] = isset($data['banking_pin']) ? $data['banking_pin'] : null;
        $this->container['store_secrets'] = isset($data['store_secrets']) ? $data['store_secrets'] : false;
        $this->container['two_step_procedure_id'] = isset($data['two_step_procedure_id']) ? $data['two_step_procedure_id'] : null;
        $this->container['execution_date'] = isset($data['execution_date']) ? $data['execution_date'] : null;
        $this->container['single_booking'] = isset($data['single_booking']) ? $data['single_booking'] : false;
        $this->container['additional_money_transfers'] = isset($data['additional_money_transfers']) ? $data['additional_money_transfers'] : null;
        $this->container['challenge_response'] = isset($data['challenge_response']) ? $data['challenge_response'] : null;
        $this->container['multi_step_authentication'] = isset($data['multi_step_authentication']) ? $data['multi_step_authentication'] : null;
        $this->container['hide_transaction_details_in_web_form'] = isset($data['hide_transaction_details_in_web_form']) ? $data['hide_transaction_details_in_web_form'] : false;
        $this->container['store_pin'] = isset($data['store_pin']) ? $data['store_pin'] : false;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['amount'] === null) {
            $invalidProperties[] = "'amount' can't be null";
        }
        if ($this->container['account_id'] === null) {
            $invalidProperties[] = "'account_id' can't be null";
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
     * Gets recipient_name
     *
     * @return string
     */
    public function getRecipientName()
    {
        return $this->container['recipient_name'];
    }

    /**
     * Sets recipient_name
     *
     * @param string $recipient_name Name of the recipient. Note: Neither finAPI nor the involved bank servers are guaranteed to validate the recipient name. Even if the recipient name does not depict the actual registered account holder of the specified recipient account, the money transfer request might still be successful. This field is optional only when you pass a clearing account as the recipient. Otherwise, this field is required.
     *
     * @return $this
     */
    public function setRecipientName($recipient_name)
    {
        $this->container['recipient_name'] = $recipient_name;

        return $this;
    }

    /**
     * Gets recipient_iban
     *
     * @return string
     */
    public function getRecipientIban()
    {
        return $this->container['recipient_iban'];
    }

    /**
     * Sets recipient_iban
     *
     * @param string $recipient_iban IBAN of the recipient's account. This field is optional only when you pass a clearing account as the recipient. Otherwise, this field is required.
     *
     * @return $this
     */
    public function setRecipientIban($recipient_iban)
    {
        $this->container['recipient_iban'] = $recipient_iban;

        return $this;
    }

    /**
     * Gets recipient_bic
     *
     * @return string
     */
    public function getRecipientBic()
    {
        return $this->container['recipient_bic'];
    }

    /**
     * Sets recipient_bic
     *
     * @param string $recipient_bic BIC of the recipient's account. Note: This field is optional when you pass a clearing account as the recipient or if the bank connection of the account that you want to transfer money from supports the IBAN-Only money transfer. You can find this out via GET /bankConnections/<id>. Also note that when a BIC is given, then this BIC will be used for the money transfer request independent of whether it is required or not (unless you pass a clearing account, in which case this field will always be ignored).
     *
     * @return $this
     */
    public function setRecipientBic($recipient_bic)
    {
        $this->container['recipient_bic'] = $recipient_bic;

        return $this;
    }

    /**
     * Gets clearing_account_id
     *
     * @return string
     */
    public function getClearingAccountId()
    {
        return $this->container['clearing_account_id'];
    }

    /**
     * Sets clearing_account_id
     *
     * @param string $clearing_account_id Identifier of a clearing account. If this field is set, then the fields 'recipientName', 'recipientIban' and 'recipientBic' will be ignored and the recipient account will be the specified clearing account.
     *
     * @return $this
     */
    public function setClearingAccountId($clearing_account_id)
    {
        $this->container['clearing_account_id'] = $clearing_account_id;

        return $this;
    }

    /**
     * Gets amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->container['amount'];
    }

    /**
     * Sets amount
     *
     * @param float $amount The amount to transfer. Must be a positive decimal number with at most two decimal places (e.g. 99.99)
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->container['amount'] = $amount;

        return $this;
    }

    /**
     * Gets purpose
     *
     * @return string
     */
    public function getPurpose()
    {
        return $this->container['purpose'];
    }

    /**
     * Sets purpose
     *
     * @param string $purpose The purpose of the transfer transaction
     *
     * @return $this
     */
    public function setPurpose($purpose)
    {
        $this->container['purpose'] = $purpose;

        return $this;
    }

    /**
     * Gets sepa_purpose_code
     *
     * @return string
     */
    public function getSepaPurposeCode()
    {
        return $this->container['sepa_purpose_code'];
    }

    /**
     * Sets sepa_purpose_code
     *
     * @param string $sepa_purpose_code SEPA purpose code, according to ISO 20022, external codes set.
     *
     * @return $this
     */
    public function setSepaPurposeCode($sepa_purpose_code)
    {
        $this->container['sepa_purpose_code'] = $sepa_purpose_code;

        return $this;
    }

    /**
     * Gets account_id
     *
     * @return int
     */
    public function getAccountId()
    {
        return $this->container['account_id'];
    }

    /**
     * Sets account_id
     *
     * @param int $account_id Identifier of the bank account that you want to transfer money from
     *
     * @return $this
     */
    public function setAccountId($account_id)
    {
        $this->container['account_id'] = $account_id;

        return $this;
    }

    /**
     * Gets banking_pin
     *
     * @return string
     */
    public function getBankingPin()
    {
        return $this->container['banking_pin'];
    }

    /**
     * Sets banking_pin
     *
     * @param string $banking_pin Online banking PIN. Any symbols are allowed. Max length: 170. If a PIN is stored in the bank connection, then this field may remain unset. If finAPI's web form is not required and the field is set though then it will always be used (even if there is some other PIN stored in the bank connection). If you want the user to enter a PIN in finAPI's web form even when a PIN is stored, then just set the field to any value, so that the service recognizes that you wish to use the web form flow.
     *
     * @return $this
     */
    public function setBankingPin($banking_pin)
    {
        $this->container['banking_pin'] = $banking_pin;

        return $this;
    }

    /**
     * Gets store_secrets
     *
     * @return bool
     */
    public function getStoreSecrets()
    {
        return $this->container['store_secrets'];
    }

    /**
     * Sets store_secrets
     *
     * @param bool $store_secrets Whether to store the PIN. If the PIN is stored, it is not required to pass the PIN again when executing SEPA orders. Default value is 'false'. <br/><br/>NOTES:<br/> - before you set this field to true, please regard the 'pinsAreVolatile' flag of the bank connection that the account belongs to;<br/> - this field is ignored in case when the user will need to use finAPI's web form. The user will be able to decide whether to store the PIN or not in the web form, depending on the 'storeSecretsAvailableInWebForm' setting (see Client Configuration).
     *
     * @return $this
     */
    public function setStoreSecrets($store_secrets)
    {
        $this->container['store_secrets'] = $store_secrets;

        return $this;
    }

    /**
     * Gets two_step_procedure_id
     *
     * @return string
     */
    public function getTwoStepProcedureId()
    {
        return $this->container['two_step_procedure_id'];
    }

    /**
     * Sets two_step_procedure_id
     *
     * @param string $two_step_procedure_id The bank-given ID of the two-step-procedure that should be used for the order. For a list of available two-step-procedures, see the corresponding bank connection (GET /bankConnections). If this field is not set, then the bank connection's default two-step-procedure will be used. Note that in this case, when the bank connection has no default two-step-procedure set, then the response of the service depends on whether you need to use finAPI's web form or not. If you need to use the web form, the user will be prompted to select the two-step-procedure within the web form. If you don't need to use the web form, then the service will return an error (passing a value for this field is required in this case).
     *
     * @return $this
     */
    public function setTwoStepProcedureId($two_step_procedure_id)
    {
        $this->container['two_step_procedure_id'] = $two_step_procedure_id;

        return $this;
    }

    /**
     * Gets execution_date
     *
     * @return string
     */
    public function getExecutionDate()
    {
        return $this->container['execution_date'];
    }

    /**
     * Sets execution_date
     *
     * @param string $execution_date Execution date for the money transfer(s), in the format 'YYYY-MM-DD'. If not specified, then the current date will be used.
     *
     * @return $this
     */
    public function setExecutionDate($execution_date)
    {
        $this->container['execution_date'] = $execution_date;

        return $this;
    }

    /**
     * Gets single_booking
     *
     * @return bool
     */
    public function getSingleBooking()
    {
        return $this->container['single_booking'];
    }

    /**
     * Sets single_booking
     *
     * @param bool $single_booking This field is only regarded when you pass multiple orders. It determines whether the orders should be processed by the bank as one collective booking (in case of 'false'), or as single bookings (in case of 'true'). Default value is 'false'.
     *
     * @return $this
     */
    public function setSingleBooking($single_booking)
    {
        $this->container['single_booking'] = $single_booking;

        return $this;
    }

    /**
     * Gets additional_money_transfers
     *
     * @return \Swagger\Client\Model\SingleMoneyTransferRecipientData[]
     */
    public function getAdditionalMoneyTransfers()
    {
        return $this->container['additional_money_transfers'];
    }

    /**
     * Sets additional_money_transfers
     *
     * @param \Swagger\Client\Model\SingleMoneyTransferRecipientData[] $additional_money_transfers In case that you want to submit not just a single money transfer, but do a collective money transfer, use this field to pass a list of additional money transfer orders. The service will then pass a collective money transfer request to the bank, including both the money transfer specified on the top-level, as well as all money transfers specified in this list. The maximum count of money transfers that you can pass (in total) is 15000. Note that you should check the account's 'supportedOrders' field to find out whether or not it is supporting collective money transfers.
     *
     * @return $this
     */
    public function setAdditionalMoneyTransfers($additional_money_transfers)
    {
        $this->container['additional_money_transfers'] = $additional_money_transfers;

        return $this;
    }

    /**
     * Gets challenge_response
     *
     * @return string
     */
    public function getChallengeResponse()
    {
        return $this->container['challenge_response'];
    }

    /**
     * Sets challenge_response
     *
     * @param string $challenge_response NOTE: This field is DEPRECATED and will get removed at some point. Please refer to the 'multiStepAuthentication' field instead.<br/><br/>Challenge response. This field should be set only when the previous attempt to request a SEPA money transfer failed with HTTP code 510, i.e. the bank sent a challenge for the user for an additional authentication. In this case, this field must contain the response to the bank's challenge. Please note that in case of using finAPI's web form you have to leave this field unset and the application will automatically recognize that the user has to input challenge response and then a web form will be shown to the user.
     *
     * @return $this
     */
    public function setChallengeResponse($challenge_response)
    {
        $this->container['challenge_response'] = $challenge_response;

        return $this;
    }

    /**
     * Gets multi_step_authentication
     *
     * @return \Swagger\Client\Model\MultiStepAuthenticationCallback
     */
    public function getMultiStepAuthentication()
    {
        return $this->container['multi_step_authentication'];
    }

    /**
     * Sets multi_step_authentication
     *
     * @param \Swagger\Client\Model\MultiStepAuthenticationCallback $multi_step_authentication Container for multi-step authentication data. Required when a previous service call initiated a multi-step authentication.
     *
     * @return $this
     */
    public function setMultiStepAuthentication($multi_step_authentication)
    {
        $this->container['multi_step_authentication'] = $multi_step_authentication;

        return $this;
    }

    /**
     * Gets hide_transaction_details_in_web_form
     *
     * @return bool
     */
    public function getHideTransactionDetailsInWebForm()
    {
        return $this->container['hide_transaction_details_in_web_form'];
    }

    /**
     * Sets hide_transaction_details_in_web_form
     *
     * @param bool $hide_transaction_details_in_web_form Whether the finAPI web form should hide transaction details when prompting the caller for the second factor. Default value is false.
     *
     * @return $this
     */
    public function setHideTransactionDetailsInWebForm($hide_transaction_details_in_web_form)
    {
        $this->container['hide_transaction_details_in_web_form'] = $hide_transaction_details_in_web_form;

        return $this;
    }

    /**
     * Gets store_pin
     *
     * @return bool
     */
    public function getStorePin()
    {
        return $this->container['store_pin'];
    }

    /**
     * Sets store_pin
     *
     * @param bool $store_pin Whether to store the PIN. If the PIN is stored, it is not required to pass the PIN again when executing SEPA orders. Default value is 'false'. <br/><br/>NOTES:<br/> - before you set this field to true, please regard the 'pinsAreVolatile' flag of the bank connection that the account belongs to;<br/> - this field is ignored in case when the user will need to use finAPI's web form. The user will be able to decide whether to store the PIN or not in the web form, depending on the 'storeSecretsAvailableInWebForm' setting (see Client Configuration).<br><br>NOTE: This field is deprecated and will be removed at some point. Use 'storeSecrets' instead.
     *
     * @return $this
     */
    public function setStorePin($store_pin)
    {
        $this->container['store_pin'] = $store_pin;

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

