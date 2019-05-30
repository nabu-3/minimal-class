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

use nabu\infrastructure\reader\interfaces\INabuDataListFileReader;

/**
 * Abstract base class to implement a Data List File Reader implementing @see { INabuDataListReader } interface
 * in nabu-3.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.4
 * @version 3.0.4
 * @package \nabu\infrastructure\reader
 */
abstract class CNabuAbstractDataListFileReader extends CNabuAbstractDataListReader implements INabuDataListFileReader
{
    /** @var string|null $filename Full path file name of Reader Source File.. */
    private $filename = null;

    /**
     * Get a simple array with at least one valid MIME type of valid files.
     * @return array Returns a filled array with valid MIME types.
     */
    protected abstract function getValidMIMETypes(): array;
    /**
     * Performs additional File validation against included in method @see { validateFile }. This method is called
     * after all standard validations of @see { validateFile } are well done to have the last chance to reject a file
     * before open it.
     * @param string $filename Name of the file to perform additional validations.
     * @return bool Returns true if the file is valid and false otherwise.
     */
    protected abstract function customFileValidation(string $filename): bool;
    /**
     * Opens the source data file after to be validated. File name passed as parameter is a valid and safe name
     * and the method does not require to revalidate it.
     * @param string $filename File name to open and read source data.
     * @return bool Returns true if the File is opened and data found.
     */
    protected abstract function openSourceFile(string $filename): bool;
    /**
     * Closes the source data file after parse it or when the instance is released.
     */
    protected abstract function closeSourceFile(): void;

    /**
     * Creates the instance with the possibility to pass the file name of source file to be parsed.
     * @param string|null $filename Filename to read.
     * @param array|null $matrix_fields Matrix of fields.
     * @param array|null $required_fields Required fields in data.
     * @param bool $strict_source_names If true, forces to apply Strict Source Names to source data.
     * @param int $header_names_offset Offset of the row containing column names.
     * @param int $first_row_offset Offset of the first row of valid records to parse.
     */
    public function __construct(
        string $filename = null,
        ?array $matrix_fields = null,
        ?array $required_fields = null,
        bool $strict_source_names = true,
        int $header_names_offset = 0,
        int $first_row_offset = 0
    ) {
        parent::__construct(
            $matrix_fields, $required_fields, $strict_source_names, $header_names_offset, $first_row_offset
        );

        if (is_string($filename)) {
            $this->loadFromFile($filename);
        }
    }

    /**
     * Destructor of the instance. Releases Source File if opened.
     */
    public function __destruct()
    {
        $this->closeSourceFile();
    }

    /**
     * Validates a file before read it.
     * @param string $filename Filename to read.
     * @return bool Returns true if the file is valid or false otherwise.
     */
    protected function validateFile(string $filename): bool
    {
        if (strlen($filename) === 0 ||
            ($this->filename = realpath($filename)) === false ||
            mb_strlen($this->filename) === 0 ||
            !file_exists($this->filename) ||
            !is_file($this->filename) ||
            !is_readable($this->filename) ||
            !in_array(mime_content_type($this->filename), $this->getValidMIMETypes()) ||
            !$this->customFileValidation($this->filename)
        ) {
            $this->filename = null;
            trigger_error(sprintf(TRIGGER_ERROR_INVALID_FILE_READER_FILENAME, $filename));
        }

        return true;
    }

    public function loadFromFile(string $filename): void
    {
        $this->validateFile($filename);
        $this->openSourceFile($this->filename);
    }
}
