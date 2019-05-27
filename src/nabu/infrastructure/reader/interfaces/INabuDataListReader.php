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

namespace nabu\infrastructure\reader\interfaces;

use nabu\data\interfaces\INabuDataList;

/**
 * Interface to implement a Data List Reader that fills a data list implementing @see { INabuDataList } interface in nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.4
 * @version 3.0.4
 * @package \nabu\infrastructure\reader\interfaces
 */
interface INabuDataListReader
{
    /**
     * Get the convert fields matrix as an associative array.
     * @return array|null Returns an associative array if matrix is set or null otherwise.
     */
    public function getConvertFieldsMatrix(): ?array;
    /**
     * Set the convert fields matrix, an associative array where the index is the name of the field in the source,
     * and the value is the name of the field in the target (internal stored data).
     * @param array $matrix_fields Array of fields to apply as matrix.
     * @return INabuDataListReader Returns the self pointer to grant Fluent Interface.
     */
    public function setConvertFieldsMatrix(array $matrix_fields): INabuDataListReader;
    /**
     * Get the minimum require fields list as a single array.
     * @return array|null Returns an array with required fields if exists or null otherwise.
     */
    public function getRequiredFields(): ?array;
    /**
     * Set the minimum required fields when parse the source. Field names are based in data field names
     * and not in source field names.
     * @param array $required_fields The required field list as a simple array where each value
     * is the name of a source field.
     * @return INabuDataListReader Returns the self pointer to grant Fluent Interface.
     */
    public function setRequiredFields(array $required_fields): INabuDataListReader;
    /**
     * Check if Use of Strict Source Names policy is enabled or not.
     * @return bool Returns true if Use of Strict Source Names policy is enabled.
     */
    public function isUseStrictSourceNames(): bool;
    /**
     * Stablishes the policy that allows the Reader to apply strict source names passed as Matrix fields
     * or to apply canonicalized source names.
     * @param bool $strict_names If true strict names policy is applied.
     * @return INabuDataListReader Returns the self pointer to grant Fluent Interface.
     */
    public function setUseStrictSourceNames(bool $strict_names): INabuDataListReader;
    /**
     * Parse source to create a INabuDataList collection with all records found.
     * This process discards columns not listed in conversion matrix and check for required fields.
     * @return INabuDataList Returns the INabuDataList containing valid records found.
     */
    public function parse(): INabuDataList;
}
