<?php

/** @license
 *  Copyright 2009-2011 Rafael Gutierrez Martinez
 *  Copyright 2012-2013 Welma WEB MKT LABS, S.L.
 *  Copyright 2014-2016 Where Ideas Simply Come True, S.L.
 *  Copyright 2017 nabu-3 Group
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace nabu\data;

use \nabu\min\CNabuObject;

/**
 * Abstract class to implement all inherited data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.0 Surface
 * @package \nabu\data
 */
abstract class CNabuDataObject extends CNabuObject
{
    /** @var int MODE_READONLY Constant value to determine if the instance is in read only mode. */
    const MODE_READONLY = 0;
    /** @var int MODE_EDITABLE Constant value to determine if the insance is in edit mode. */
    const MODE_EDITABLE = 1;

    /** @var int JSON_GRANT_PATH Flag to determine if the path needs to be granted in a JSON node. */
    const JSON_GRANT_PATH = 0x0001;
    /** @var int JSON_REPLACE_EXISTING Flag to determine if an operation with JSON needs to replace existing path. */
    const JSON_REPLACE_EXISTING = 0x0002;

    /** @var int Current Edit mode. */
    protected $edit_mode = CNabuDataObject::MODE_EDITABLE;

    /** @var array|null Data stored in the instance. Functions that applies to set data in this instance modifies this
     * array. */
    protected $data = null;
    /** @var array|null Data LIFO point of time stack. It can be used to make differential updates of data storages. */
    protected $data_stack = null;

    /**
     * Reset the data content stored in the instance and empty internal storage, lossing all previous stored data.
     */
    public function reset()
    {
        $this->data = null;
        $this->data_stack = null;
    }

    /**
     * Copy current data to a point of time storage. This is a LIFO stack.
     */
    public function push()
    {
        if (is_array($this->data_stack)) {
            array_push($this->data_stack, $this->data);
        } else {
            $this->data_stack = array($this->data);
        }
    }

    /**
     * Sets current data from the LIFO stack.
     * @return bool Returns true if the data is popped, and false if the LIFO is empty.
     */
    public function pop(): bool
    {
        $result = false;

        if (is_array($this->data_stack) && count($this->data_stack)) {
            $this->data = array_pop($this->data_stack);
            $result = true;
        } else {
            $this->data = null;
            trigger_error("Data stack is empty. Current data is emptied.");
        }

        return $result;
    }

    /**
     * Dumps current data to the standard output using var_export.
     */
    public function dump()
    {
        return var_export($this->data, true);
    }

    /**
     * Check if the instance is editable.
     * @return bool Returns true if instance is editable.
     */
    public function isEditable(): bool
    {
        return $this->edit_mode === self::MODE_READONLY;
    }

    /**
     * Check if the instance is read only.
     * @return bool Returns true if instance is read only.
     */
    public function isReadOnly($value): bool
    {
        return $this->edit_mode === self::MODE_EDITABLE;
    }

    /**
     * Get an stoared value in the current data storage of instance.
     * @param string $name Name of the value to get.
     * @return mixed|null Returns the stored value if exists or null otherwise.
     */
    public function getValue(string $name)
    {
        return ($this->data == null || !array_key_exists($name, $this->data) ? null : $this->data[$name]);
    }

    /**
     * Splits the value using a token based in a regular expression (uses function preg_split) and returns the result
     * as an array. If the value cannot be splitted then returns their original value.
     * @param string $name Name of the value to get.
     * @return array|null Returns the array as result of split the value or null if it is empty.
     */
    public function getValueAsList(string $name)
    {
        $value = $this->getValue($name);

        if ($value != null && is_string($value)) {
            $value = preg_split("/(\s*,\s*)/", $value);
        }

        return $value;
    }

    /**
     * Try to parse value as a JSON data descriptor. If success returns an array with parsed structure. If fails,
     * then returns the original value.
     * @param string $name Name of the value to get.
     * @return array|null Returns the array as result of decode the JSON, the original value if cannot be decoded,
     * or null if data value is empty or not exists.
     */
    public function getValueAsJSONDecoded(string $name)
    {
        $value = $this->getValue($name);

        if ($value !== null && is_string($value)) {
            $value = json_decode($value, true);
        }

        return $value;
    }

    public function setValueJSONEncoded($name, $value)
    {
        if ($value === null) {
            $this->setValue($name, null);
        } elseif (is_string($value)) {
            $this->setValue($name, $value);
        } elseif (is_array($value) || is_object($value)) {
            $this->setValue($name, json_encode($value));
        }

        return $this;
    }

    public function hasValue($name)
    {
        return ($this->data !== null && array_key_exists($name, $this->data));
    }

    public function isEmpty()
    {
        return ($this->data === null) || (count($this->data) == 0);
    }

    public function isValueNumeric($name)
    {
        return ($this->data !== null && array_key_exists($name, $this->data) ? is_numeric($this->data[$name]) : false);
    }

    public function isValueFloat($name)
    {
        return $this->data !== null && array_key_exists($name, $this->data)
               ? is_numeric($this->data[$name]) || is_float($this->data[$name])
               : false;
    }

    public function isValueString($name)
    {
        return $this->data !== null && array_key_exists($name, $this->data) ? is_string($this->data[$name]) : false;
    }

    public function isValueNull($name)
    {
        return $this->data !== null && array_key_exists($name, $this->data) ? is_null($this->data[$name]) : false;
    }

    public function isValueEmptyString($name)
    {
        return $this->data !== null && array_key_exists($name, $this->data)
               ? (strlen($this->data[$name]) === 0)
               : false;
    }

    public function isValueEmpty($name)
    {
        return $this->data !== null && array_key_exists($name, $this->data) &&
               ($this->data[$name] === null || $this->data[$name] === false || strlen($this->data[$name]) === 0)
               ? true
               : false;
    }

    public function isValueGUID($name)
    {
        return $this->data !== null && array_key_exists($name, $this->data)
               ? nb_isValidGUID($this->data[$name])
               : false;
    }

    public function isValueEqualThan($name, $test)
    {
        return ($this->contains($name) && $this->data[$name] === $test);
    }

    public function isValueModified($name)
    {
        $retval = false;

        if (is_array($this->data) && array_key_exists($name, $this->data)) {
            if (is_array($this->data_stack) && array_key_exists($name, $this->data_stack)) {
                $retval = ($this->data[$name] !== $this->data_stack[$name]);
            } else {
                $retval = true;
            }
        }

        return $retval;
    }

    public function setValue($name, $value)
    {
        if ($this->data === null) {
            $this->data = array($name => $value);
        } else {
            $this->data[$name] = $value;
        }
    }

    public function setArrayValues($array)
    {
        if ($this->data == null) {
            $this->data = $array;
        } elseif ($array != null) {
            $this->data = array_merge($this->data, $array);
        }
    }

    /**
     * Transfer a value from $object to this instance. If $target_name is omitted then $source_name is used for both
     * field names.
     * @param CNabuDataObject|null $object Object instance where is stored the value to be transferred.
     * @param string $source_name Name of field in $object that contains the value.
     * @param string $target_name Name of field in this instance where the value will be stored.
     */
    public function transferValue($object, $source_name, $target_name = false)
    {
        if ($object instanceof CNabuDataObject) {
            $target_name = ($target_name ? $target_name : $source_name);
            $this->setValue($target_name, $object->getValue($source_name));
        }
    }

    public function transferMixedValue($object, $source_name, $target_name = false, $type = false)
    {
        if (($object instanceof CNabuDataObject) && ($type === false || ($object instanceof $type))) {
            $target_name = ($target_name ? $target_name : $source_name);
            $this->transferValue($object, $source_name, $target_name);
        } elseif (is_scalar($object)) {
            $this->setValue($source_name, $object);
        }
    }

    public function exchangeValue($object, $source_name, $target_name = false)
    {
        if ($object instanceof CNabuDataObject) {
            $target_name = ($target_name ? $target_name : $source_name);
            $aux = $object->getValue($target_name);
            $object->setValue($target_name, $this->getValue($source_name));
            $this->setValue($source_name, $aux);
        }
    }

    public function matchValue($object, $source_name, $target_name = false)
    {
        if ($object instanceof CNabuDataObject) {
            $target_name = ($target_name ? $target_name : $source_name);
            return $this->contains($source_name) &&
                   $object->contains($target_name) &&
                   $this->getValue($source_name) == $object->getValue($target_name);
        } else {
            return false;
        }
    }

    public function contains($field)
    {
        return $this->data != null ? array_key_exists($field, $this->data) : false;
    }

    public function getTreeData($nb_language = null, $dataonly = false)
    {
        if (!$dataonly) {
            $tree = array(
                'is_fetched' => $this->isFetched(),
                'is_new' => $this->isNew(),
                'is_empty' => $this->isEmpty(),
                'is_deleted' => $this->isDeleted()
            );
        } else {
            $tree = array();
        }

        $tree = ($this->data == null ? $tree : array_merge($tree, $this->data));

        return $tree;
    }

    /**
     * @deprecated since version 2.1
     */
    public function getXmlData($parent = null)
    {
        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    public static function buildXmlObject($xmldata)
    {
        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function buildXmlElement($name, $field)
    {
        if ($this->contains($field)) {
            $value = $this->getValue($field);
            if ($value == null) {
                $xmldata = new \nabu\core\CNabuXmlElement("<$name/>");
            } else {
                $xmldata = new \nabu\core\CNabuXmlElement("<$name>$value</$name>");
            }
            return $xmldata;
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function buildXmlElementCDATA($name, $field)
    {
        if ($this->contains($field)) {
            $value = $this->getValue($field);
            if ($value == null) {
                $xmldata = new \nabu\core\CNabuXmlElement("<$name/>");
            } else {
                $xmldata = new \nabu\core\CNabuXmlElement(
                    "<$name><![CDATA[".htmlentities($value, ENT_COMPAT, 'UTF-8')."]]></$name>"
                );
            }
            return $xmldata;
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function addXmlAttribute($xml, $name, $field)
    {
        if ($xml instanceof \nabu\core\CNabuXmlElement && $name != null && $this->contains($field)) {
            $value = $this->getValue($field);
            if ($value !== null) {
                $xml->addAttribute($name, $value);

                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function addXmlChild($xml, $name, $field = null)
    {
        if ($xml instanceof \nabu\core\CNabuXmlElement && $name != null) {
            if ($field == null) {
                return $xml->addChild($name);
            } elseif ($this->contains($field)) {
                $value = $this->getValue($field);
                if ($value !== null) {
                    return $xml->addChild($name, $value);
                } else {
                    return $xml->addChild($name);
                }
            }
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function addXmlChildCDATA($xml, $name, $field = null)
    {
        if ($xml instanceof \nabu\core\CNabuXmlElement && $name != null) {
            if ($field == null) {
                return $xml->addChild($name);
            } elseif ($this->contains($field)) {
                $value = $this->getValue($field);
                return $xml->addChildCDATA($name, $value);
            }
        }

        return null;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function getXmlAttribute($xml, $attribute, $field, $defaultvalue = null, $mapvalue = null)
    {
        $value = $xml->getAttribute($attribute);
        if ($value !== false) {
            if (func_num_args() > 4 && $mapvalue != null) {
                if (isset($mapvalue[$value])) {
                    $value = $mapvalue[$value];
                    $this->setValue($field, $value);
                } else {
                    $value = $defaultvalue;
                    $this->setValue($field, $value);
                }
            } else {
                $this->setValue($field, $value);
            }
        } elseif (func_num_args() > 3) {
            $value = $defaultvalue;
            $this->setValue($field, $value);
        }

        return $value;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function getXmlCDATA($xml, $field, $emptynull)
    {
        $value = $xml->getCDATA();
        if (strlen($value) == 0 && $emptynull === true) {
            $value = null;
        }
        $this->setValue($field, $value);

        return $value;
    }

    /**
     * @deprecated since version 2.1
     */
    protected function getXmlText($xml, $field, $emptynull)
    {
        $value = $xml->getText();
        if (strlen($value) == 0 && $emptynull === true) {
            $value = null;
        }
        $this->setValue($field, $value);

        return $value;
    }

    /**
     * Sets data array from another source.
     * If $object is an object of class CNabuDataObject or any inherited class then copy his $data array.
     * If $object is an array then apply directly this array
     * @param array|CNabuDataObject $object
     */
    public function copyData($object)
    {
        if ($object instanceof CNabuDataObject) {
            $this->data = $object->data;
        } elseif (is_array($object)) {
            $this->data = $object;
        }
    }

    public function checkJSONPath($path)
    {
        if ($this->isEmpty() || strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) > 0) {
            $l = count($route);
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (!is_array($p) || !array_key_exists($route[$i], $p)) {
                    return false;
                }
                $p = &$p[$route[$i]];
            }
            return true;
        } else {
            return false;
        }
    }

    public function getJSONValue($path)
    {
        if ($this->isEmpty() || strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) > 0) {
            $l = count($route);
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if ($p ===null || !array_key_exists($route[$i], $p)) {
                    return false;
                }
                $p = &$p[$route[$i]];
            }
            return $p;
        } else {
            return false;
        }
    }

    public function isJSONValueEqualThan($path, $value)
    {
        if ($this->isEmpty() || strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) > 0) {
            $l = count($route);
            $p = &$this->data;
            for ($i = 0; $i < $l; $i++) {
                if (!array_key_exists($route[$i], $p)) {
                    return false;
                }
                $p = &$p[$route[$i]];
            }
            return ($p === $value);
        } else {
            return false;
        }
    }

    public function setJSONValue($path, $value, $flags = 0)
    {
        if (strlen($path) === 0) {
            return false;
        }

        $route = preg_split('/\./', $path);

        if (count($route) === 0) {
            return;
        }

        $l = count($route);
        $p = &$this->data;
        for ($i = 0; $i < $l; $i++) {
            if (is_array($p)) {
                if (!array_key_exists($route[$i], $p)) {
                    if ($flags & CNabuDataObject::JSON_GRANT_PATH) {
                        $p[$route[$i]] = array();
                    } else {
                        return;
                    }
                }
            } elseif ($p === null && ($flags & CNabuDataObject::JSON_GRANT_PATH)) {
                $p = array($route[$i] => null);
            } elseif ($flags & CNabuDataObject::JSON_REPLACE_EXISTING) {
                $p = array($route[$i] => null);
            } else {
                return;
            }
            $p = &$p[$route[$i]];
        }
        $p = $value;
    }
}
