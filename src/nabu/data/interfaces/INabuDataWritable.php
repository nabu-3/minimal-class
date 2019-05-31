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

namespace nabu\data\interfaces;

/**
 * Interface to implement readable data objects of nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.2
 * @version 3.0.4
 * @package \nabu\data\interfaces
 */
interface INabuDataWritable
{
    /**
     * Check if the instance is editable.
     * @return bool Returns true if instance is editable.
     */
    public function isEditable(): bool;
    /**
     * Entry instance in edit mode.
     * @return INabuDataWritable Returns the self pointer for convenience to use in cascade setters call.
     */
    public function setAsEditable(): INabuDataWritable;
    /**
     * Check if the instance is read only.
     * @return bool Returns true if instance is read only.
     */
    public function isReadOnly(): bool;
    /**
     * Entry instance in read only mode.
     * @return INabuDataWritable Returns the self pointer for convenience to use in cascade setters call.
     */
    public function setAsReadOnly(): INabuDataWritable;
    /**
     * Sets a Value associated to a name.
     * @param string $name Name of the value to set.
     * @param mixed $value Value to be setted.
     * @return INabuDataWritable Returns the self pointer for convenience to call cascade setters.
     */
    public function setValue(string $name, $value): INabuDataWritable;
    /**
     * Sets data array from another source.
     * If $object is an object of class CNabuAbstractDataObject or any inherited class then copy his $data array.
     * If $object is an array then apply directly this array
     * @param INabuDataReadable $object
     */
    public function copyData(INabuDataReadable $object);
}
