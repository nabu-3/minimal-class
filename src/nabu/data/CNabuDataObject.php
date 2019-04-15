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

/**
 * Abstract class to implement editable data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @version 3.0.2
 * @package \nabu\data
 */
abstract class CNabuDataObject extends CNabuRODataObject
{
    /** @var int MODE_READONLY Constant value to determine if the instance is in read only mode. */
    const MODE_READONLY = 0;
    /** @var int MODE_EDITABLE Constant value to determine if the insance is in edit mode. */
    const MODE_EDITABLE = 1;

    /** @var int Current Edit mode. */
    protected $edit_mode = CNabuDataObject::MODE_EDITABLE;

    /**
     * Check if the instance is editable.
     * @return bool Returns true if instance is editable.
     */
    public function isEditable(): bool
    {
        return $this->edit_mode === self::MODE_READONLY;
    }

    /**
     * Entry instance in edit mode.
     * @return CNabuDataObject Returns the self pointer for convenience to use in cascade setters call.
     */
    public function setAsEditable(): CNabuDataObject
    {
        $this->edit_mode = self::MODE_EDITABLE;
    }

    /**
     * Check if the instance is read only.
     * @return bool Returns true if instance is read only.
     */
    public function isReadOnly(): bool
    {
        return $this->edit_mode === self::MODE_EDITABLE;
    }

    /**
     * Entry instance in read only mode.
     * @return CNabuDataObject Returns the self pointer for convenience to use in cascade setters call.
     */
    public function setAsReadOnly(): CNabuDataObject
    {
        $this->edit_mode = self::MODE_READONLY;
    }

    /**
     * Sets a Value associated to a name.
     * @param string $name Name of the value to set.
     * @param mixed $value Value to be setted.
     * @return CNabuDataObject Returns the self pointer for convenience to call cascade setters.
     */
    public function setValue(string $name, $value): CNabuDataObject
    {
        if ($this->isEditable()) {
            if ($this->data === null) {
                $this->data = array($name => $value);
            } else {
                $this->data[$name] = $value;
            }
            if (!is_scalar($value) && !is_null($value)) {
                if (is_array($value)) {
                    trigger_error(
                        "CNabuDataObject::setValue is using an array value. Try to use TNabuJSONData trait in your class.",
                        E_USER_NOTICE
                    );
                } else {
                    trigger_error(
                        "CNabuDataObject::setValue is using a mismatch value. Result will be unpredectible.",
                        E_USER_NOTICE
                    );
                }
            }
        } else {
            trigger_error( "Instance is in Read Only mode and cannot be edited.", E_USER_ERROR);
        }
    }

    /**
     * Load data using an external array of values. Types of each element are not verified. Keep with caution.
     * If previous data exists, then merge arrays at first level. If you manage multilevel arrays, try to use
     * @see { TNabuJSONData } trait.
     * @param array $array Associative array with names and values.
     * @return CNabuDataObject Returns the self pointer for convenience to call cascade setters.
     */
    public function setArrayValues(array $array)
    {
        if ($this->isEditable()) {
            if ($this->data == null) {
                $this->data = $array;
            } elseif ($array != null) {
                $this->data = array_merge($this->data, $array);
            }
        } else {
            trigger_error( "Instance is in Read Only mode and cannot be edited.", E_USER_ERROR);
        }
    }

    /**
     * Transfer a value from $object to this instance. If $target_name is omitted then $source_name is used for both
     * field names.
     * @param CNabuDataObject|null $object Object instance where is stored the value to be transferred.
     * @param string|null $source_name Name of field in $object that contains the value.
     * @param string|null $target_name Name of field in this instance where the value will be stored. If null,
     * then usees $source_name.
     */
    public function transferValue(CNabuDataObject $object = null, string $source_name = null, string $target_name = null)
    {
        if ($this->isEditable()) {
            if ($object instanceof CNabuDataObject && is_string($source_name)) {
                $target_name = (is_string($target_name) ? $target_name : $source_name);
                $this->setValue($target_name, $object->getValue($source_name));
            }
        } else {
            trigger_error( "Instance is in Read Only mode and cannot be edited.", E_USER_ERROR);
        }
    }

    /**
     * Transfer a mixed value from $object to this instance.
     * @param mixed|null $object Object instance where is stored the value to be transferred.
     * @param string|null $source_name Name of field in $object that contains the value.
     * @param string|null $target_name Name of field in this instance where the value will be stored. If null,
     * then usees $source_name.
     * @param string|null $type Type of object to match.
     */
    public function transferMixedValue($object = null, string $source_name = null, string $target_name = null, string $type = null)
    {
        if ($this->isEditable()) {
            if (($object instanceof CNabuDataObject) && ($type === null || ($object instanceof $type))) {
                $target_name = (is_string($target_name) ? $target_name : $source_name);
                $this->setValue($target_name, $object->getValue($source_name));
            } elseif (is_scalar($object) || is_array($object) || is_null($object)) {
                $this->setValue($source_name, $object);
            }
        } else {
            trigger_error( "Instance is in Read Only mode and cannot be edited.", E_USER_ERROR);
        }
    }

    /**
     * Echange values between this instance and another instance derived from CNabuDataObject.
     * @param CNabuDataObject|null $object Object instance to exchange values.
     * @param string|null $source_name Source name of value to exchange.
     * @param string|null $target_name Target name in $object to exchange values. If null, then usees $source_name.
     */
    public function exchangeValue(CNabuDataObject $object = null, string $source_name = null, string $target_name = null)
    {
        if ($this->isEditable()) {
            if ($object instanceof CNabuDataObject && is_string($source_name)) {
                $target_name = (is_string($target_name) ? $target_name : $source_name);
                $aux = $object->getValue($target_name);
                $object->setValue($target_name, $this->getValue($source_name));
                $this->setValue($source_name, $aux);
            }
        } else {
            trigger_error( "Instance is in Read Only mode and cannot be edited.", E_USER_ERROR);
        }
    }

    /**
     * Sets data array from another source.
     * If $object is an object of class CNabuDataObject or any inherited class then copy his $data array.
     * If $object is an array then apply directly this array
     * @param CNabuDataObject $object
     */
    public function copyData(CNabuDataObject $object)
    {
        if ($this->isEditable()) {
            $this->data = $object->data;
        } else {
            trigger_error( "Instance is in Read Only mode and cannot be edited.", E_USER_ERROR);
        }
    }
}
