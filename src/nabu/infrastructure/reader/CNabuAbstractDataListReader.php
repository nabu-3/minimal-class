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

namespace nabu\infrastructure\reader;

use \Error;

use nabu\data\interfaces\INabuDataList;

use nabu\infrastructure\reader\interfaces\INabuDataListReader;

use nabu\min\CNabuObject;

/**
 * Abstract base class to implement a Data List Reader implementing @see { INabuDataListReader } interface in nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.4
 * @version 3.0.4
 * @package \nabu\infrastructure\reader
 */
abstract class CNabuAbstractDataListReader extends CNabuObject implements INabuDataListReader
{
    /** @var array|null Matrix to convert fields from source to data. */
    protected $matrix_fields = null;
    /** @var array|null Required fields of data. */
    protected $required_fields = null;
    /** @var bool Use strict names for source field names. */
    protected $strict_source_names = true;
    /** @var int Header names offset. */
    protected $header_names_offset = 0;
    /** @var int First row offset. */
    protected $first_row_offset = $this->header_names_offset + 1;

    /**
     * Called internally. Creates an empty Data List Instance implementing @see { INabuDataList } interface.
     * @return INabuDataList Returns created instance. */
    protected function createDataListInstance(): INabuDataList;
    /**
     * Called internally. Returns the source as a two level associative array, where the first level are the rows,
     * and the second the columns for each row. All cells in the same column will have the same index name in each row.
     * @return array|null Returns the array if data exists or null otherwise.
     */
    protected function getSourceDataAsArray(): ?array;

    /**
     * Creates the instance passing optionally the Matrix and Required fields, and Use Strict Source Names policy.
     * @param array|null $matrix_fields Matrix of fields.
     * @param array|null $required_fields Required fields in data.
     * @param bool $strict_source_names If true, forces to apply Strict Source Names to source data.
     * @param int $header_names_offset Offset of the row containing column names.
     * @param int $first_row_offset Offset of the first row of valid records to parse.
     */
    public function __construct(
        ?array $matrix_fields = null,
        ?array $required_fields = null,
        bool $strict_source_names = true,
        int $header_names_offset = 0,
        int $first_row_offset = $header_names_offset + 1
    ) {
        parent::__construct();

        $this->matrix_fields = $matrix_fields;
        $this->required_fields = $required_fields;
        $this->strict_source_names = $strict_source_names;
        $this->header_names_offset = $header_names_offset;
        $this->first_row_offset = $first_row_offset;
    }

    public function getConvertFieldsMatrix(): ?array
    {
        return $this->matrix_fields;
    }

    public function setConvertFieldsMatrix(array $matrix_fields): INabuDataListReader
    {
        $this->matrix_fields = $matrix_fields;

        return $this;
    }

    public function getRequiredFields(): ?array
    {
        return $this->required_fields;
    }

    public function setRequiredFields(array $required_fields): INabuDataListReader
    {
        $this->required_fields = $required_fields;

        return $this;
    }

    public function isUseStrictSourceNames(): bool
    {
        return $this->strict_source_names;
    }

    public function setUseStrictSourceNames(bool $strict_names): INabuDataListReader
    {
        $this->strict_source_names = $strict_names;

        return $this;
    }

    public function parse(): INabuDataList
    {
        $sourcedata = $this->getSourceAsArray();
        $resultset = $this->createDataInstance();

        if (!is_null($sourcedata) && count($sourcedata) > 0) {
            $index_field = $resultset->getMainIndexFieldName();
            $copy_required_fields = $this->required_fields;
            if (is_string($index_field) && !in_array($index_field, $copy_required_fields)) {
                $copy_required_fields[] = $index_field;
            }
            $translated_fields = $this->calculateColumnNameTranslations($translation_fields, $resultset[1], $canonize);
            $this->checkMandatoryFields($translated_fields, $required_fields);
            $this->mapData($resultset, $sourcedata, $translated_fields, $required_fields, $index_field, 2);
        }

        return $resultset;
    }

    /**
     * Calculates the matrix to translate from initial column naming (0, 1, 2... or A, B, C...)
     * to final translation fields.
     * This method grants that columns could be unordered and extra columns interlaced between required columns.
     * @param array $column_names Original column names found in the first line of the datasheet.
     * @return array|null Returns an array with translation of columns.
     */
    private function calculateColumnNameTranslations(array $column_names): ?array
    {
        $keys = array_values($column_names);
        $values = array_keys($column_names);

        if (!$this->strict_source_names) {
            array_walk($keys, function(&$value) {
                $value = mb_strtolower(preg_replace('/_+$/', '', preg_replace('/^_+/', '', preg_replace('/[\s\.\(\)]+/', '_', $value))));
                $value = str_replace(NABU_ARRAY_CHARACTER_WITH_TILDE, NABU_ARRAY_CHARACTER_CANONICAL, $value);
            });
        }

        $new_keys = array_intersect_key($this->matrix_fields, array_combine($keys, $values));
        $new_values = array_intersect_key(array_combine($keys, $values), $this->matrix_fields);

        $translated_columns = array();
        foreach ($new_keys as $key => $value) {
            $translated_columns[$value] = $new_values[$key];
        }

        return $translated_columns;
    }

    /**
     * Check if all mandatory fields are present in columns.
     * @param array $fields Fields found in columns.
     * @throws Error Throws and error if a mandatory field is missed.
     */
    private function checkMandatoryFields(array $fields): void
    {
        if (!(count($this->required_fields) === count(array_intersect(array_keys($fields), $this->required_fields)))) {
            $missed_columns = array_diff($this->required_fields, array_keys($fields));
            trigger_error(sprintf(TRIGGER_ERROR_REQUIRED_FIELDS_NOT_FOUND, implode(', ', $missed_columns)));
        }
    }


}
