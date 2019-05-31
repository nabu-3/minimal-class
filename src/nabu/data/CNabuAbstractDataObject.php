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

use nabu\data\interfaces\INabuDataIterable;
use nabu\data\interfaces\INabuDataReadable;
use nabu\data\interfaces\INabuDataWritable;

/**
 * Abstract class to implement editable data objects of nabu-3.
 * This class can also be extended by third party classes to inherit his functionality.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.4
 * @package \nabu\data
 */
abstract class CNabuAbstractDataObject extends CNabuAbstractRODataObject implements INabuDataWritable
{
    /** @var int MODE_READONLY Constant value to determine if the instance is in read only mode. */
    const MODE_READONLY = 0;
    /** @var int MODE_EDITABLE Constant value to determine if the insance is in edit mode. */
    const MODE_EDITABLE = 1;

    /** @var string TRIGGER_NOTICE_USING_ARRAY Constant literal for Notice message. */
    private const TRIGGER_NOTICE_USING_ARRAY =
        "CNabuAbstractDataObject::setValue is using an array value. Try to use TNabuJSONData trait in your class.";
    /** @var string TRIGGER_NOTICE_USING_OBJECT Constant literal for Notice message. */
    private const TRIGGER_NOTICE_USING_OBJECT =
        "CNabuAbstractDataObject::setValue is using a mismatch value. Result will be unpredectible.";

    /** @var int Current Edit mode. */
    protected $edit_mode = CNabuAbstractDataObject::MODE_EDITABLE;

    public function isEditable(): bool
    {
        return $this->edit_mode === self::MODE_EDITABLE;
    }

    public function setAsEditable(): INabuDataWritable
    {
        $this->edit_mode = self::MODE_EDITABLE;

        return $this;
    }

    public function isReadOnly(): bool
    {
        return $this->edit_mode === self::MODE_READONLY;
    }

    public function setAsReadOnly(): INabuDataWritable
    {
        $this->edit_mode = self::MODE_READONLY;

        return $this;
    }

    public function clear(): INabuDataIterable
    {
        if ($this->isEditable()) {
            $this->clearInternal();
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }

    public function setValue(string $name, $value): INabuDataWritable
    {
        if ($this->isEditable()) {
            if ($this->data === null) {
                $this->data = array($name => $value);
            } else {
                $this->data[$name] = $value;
            }
            if (!is_scalar($value) && !is_null($value)) {
                if (is_array($value)) {
                    trigger_error(self::TRIGGER_NOTICE_USING_ARRAY, E_USER_NOTICE);
                } else {
                    trigger_error(self::TRIGGER_NOTICE_USING_OBJECT, E_USER_NOTICE);
                }
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }

    public function removeValue(string $name): INabuDataWritable
    {
        if ($this->hasValue($name)) {
            unset($this->data[$name]);
        }

        return $this;
    }

    public function renameValue(string $current_name, string $new_name): INabuDataWritable
    {
        if (strlen($current_name) > 0 &&
            strlen($new_name) > 0 &&
            $current_name !== $new_name &&
            $this->hasValue($current_name)
        ) {
            $value = $this->getValue($current_name);
            $this->removeValue($current_name);
            $this->setValue($new_name, $value);
        }

        return $this;
    }

    public function copyData(INabuDataReadable $object)
    {
        if ($this->isEditable()) {
            $this->data = $object->data;
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }
    }

    /**
     * Load data using an external array of values. Types of each element are not verified. Keep with caution.
     * If previous data exists, then merge arrays at first level. If you manage multilevel arrays, try to use
     * @see { TNabuJSONData } trait.
     * @param array $array Associative array with names and values.
     * @return CNabuAbstractDataObject Returns the self pointer for convenience to call cascade setters.
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
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }

        return $this;
    }

    /**
     * Transfer a value from $object to this instance. If $target_name is omitted then $source_name is used for both
     * field names.
     * @param INabuDataReadable|null $object Object instance where is stored the value to be transferred.
     * @param string|null $source_name Name of field in $object that contains the value.
     * @param string|null $target_name Name of field in this instance where the value will be stored. If null,
     * then usees $source_name.
     */
    public function transferValue(INabuDataReadable $object = null, string $source_name = null, string $target_name = null)
    {
        if ($this->isEditable()) {
            if ($object instanceof INabuDataReadable && is_string($source_name)) {
                $target_name = (is_string($target_name) ? $target_name : $source_name);
                $this->setValue($target_name, $object->getValue($source_name));
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }
    }

    /**
     * Transfer a mixed value from $object to this instance. This method evaluates if $object is an instance of
     * CNabuAbstractDataObject and, if setted $type, an instance of it, and then, transfers the value like a transferValue call.
     * Otherwise, if $object is null, scalar or array, then hi is setted like a setValue call.
     * @param mixed|null $object Mixed value or object instance where is stored the value to be transferred.
     * @param string|null $source_name Name of field in $object that contains the value.
     * @param string|null $target_name Name of field in this instance where the value will be stored. If null,
     * then uses $source_name.
     * @param string|null $type Type of object to match.
     */
    public function transferMixedValue($object = null, string $source_name = null, string $target_name = null, string $type = null)
    {
        if ($this->isEditable()) {
            $target_name = (is_string($target_name) ? $target_name : $source_name);
            if (($object instanceof INabuDataWritable && $object->isEditable()) &&
                ($type === null || ($object instanceof $type))
            ) {
                $this->setValue($target_name, $object->getValue($source_name));
            } elseif (is_scalar($object) || is_array($object) || is_null($object)) {
                $this->setValue($target_name, $object);
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }
    }

    /**
     * Echange values between this instance and another instance derived from CNabuAbstractDataObject.
     * @param INabuDataWritable|null $object Object instance to exchange values.
     * @param string|null $source_name Source name of value to exchange.
     * @param string|null $target_name Target name in $object to exchange values. If null, then usees $source_name.
     */
    public function exchangeValue(INabuDataWritable $object = null, string $source_name = null, string $target_name = null)
    {
        if ($this->isEditable()) {
            if ($object instanceof INabuDataWritable && $object->isEditable() && is_string($source_name)) {
                $target_name = (is_string($target_name) ? $target_name : $source_name);
                $aux = $object->getValue($source_name);
                $object->setValue($source_name, $this->getValue($target_name));
                $this->setValue($target_name, $aux);
            }
        } else {
            trigger_error(TRIGGER_ERROR_READ_ONLY_MODE, E_USER_ERROR);
        }
    }
}
