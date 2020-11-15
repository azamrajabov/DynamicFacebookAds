<?php
/**
 * Created by IntelliJ IDEA.
 * User: azam
 * Date: 12/06/2018
 * Time: 09:52
 */

namespace DynamicFbAds\Models;

use DynamicFbAds\Library\Pocket;
use Webmozart\Assert\Assert;
use Exception;

/**
 * Class DynamicFbAdsModel
 *
 * @package DynamicFbAds\Entity
 */
class DynamicFbAdsModel extends ModelAbstract implements ModelInterface
{
    // table name
    private $table = 'dynamic_fb_ads';
    private $request;

    // table fields
    public $tableFields = [
        'id',
        'website_id',
        'website_name',
        'feed',
        'item_title',
        'item_description',
        'new_price_field',
        'used_price_field',
        'vehicle_details_url',
        'is_certified',
        'precise_price',
        'created_at',
        'params',
        'fb_page_id',
        'marketplace'
    ];
    public $serializedFields = ['fb_page_id', 'marketplace'];

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

    public function __construct()
    {
        $this->request = Pocket::getDep()->make('HttpRequest');
        foreach ($this->tableFields as $field) {
            $value = $this->request->get($field);
            if ($field == 'feed') {
                $value = is_array($this->request->get($field))
                        ?implode(",", $this->request->get($field))
                        :'';
            }
            $this->{$field} = $value;
        }
    }

    /**
     * Validate rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['nullOrNumeric'],
            'website_id' => ['numeric|Select Website'],
            'website_name' => ['stringNotEmpty', 'maxLength|256'],
            //'feed' => ['nullOrisArray'],
            'item_title' => ['stringNotEmpty', 'maxLength|100'],
            'item_sub_title' => ['stringNotEmpty', 'maxLength|100'],
            'item_description' => ['stringNotEmpty', 'maxLength|100'],
            'new_price_field' => ['stringNotEmpty', 'maxLength|45'],
            'used_price_field' => ['stringNotEmpty', 'maxLength|45'],
            'vehicle_details_url' => ['stringNotEmpty', 'maxLength|45'],
            'is_certified' => ['numeric'],
            'precise_price' => ['nullOrNumeric'],
            'params' => ['nullOrString', 'maxLength|100'],
        ];
    }

    /**
     * do validation based on rules
     *
     * @return array
     */
    public function validate()
    {
        $errors = [];
        foreach (get_object_vars($this) as $field => $value) {
            if (!in_array($field, $this->tableFields) || !isset($this->rules()[$field])) {
                continue;
            }
            foreach ($this->rules()[$field] as $assertRules) {
                try {
                    $splitRule = explode('|', $assertRules);
                    isset($splitRule[1])
                        ? Assert::{$splitRule[0]}($value, $splitRule[1])
                        : Assert::{$assertRules}($value);
                } catch (Exception $e) {
                    $errors[$field][] = $e->getMessage();
                }
            }
        }

        return $errors;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function loadFields($row)
    {
        $params = $row->params
                ?unserialize($row->params)
                :[];
        foreach ($this->tableFields as $field) {
            if (in_array($field, $this->serializedFields)) {
                $this->{$field} = isset($params[$field])
                                ?$params[$field]
                                :null;
                continue;
            }
            $this->{$field} = $row->{$field};
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getNormilizedArray()
    {
        $fieldValues = [];
        $serializedFields = [];
        foreach ($this->tableFields as $field) {
            if (in_array($field, $this->serializedFields)) {
                $serializedFields[$field] = $this->{$field};
                continue;
            }
            $fieldValues[$field] = $this->{$field};
        }
        $fieldValues['params'] = serialize($serializedFields);

        $feed = '';
        if (!empty($fieldValues['feed'])) {
            $feed = is_array($fieldValues['feed'])
                    ?implode(",", $this->feed)
                    :$fieldValues['feed'];
        }
        $fieldValues['feed'] = $feed;
        unset($fieldValues['id']);
        return $fieldValues;
    }
    /**
     * @param $field
     *
     * @return mixed
     */
    public function get($field)
    {
        return $this->{$field};
    }
}