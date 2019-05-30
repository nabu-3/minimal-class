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

use PHPUnit\Framework\Error\Error;

use PHPUnit\Framework\TestCase;

use nabu\data\CNabuAbstractDataList;
use nabu\data\CNabuAbstractDataObject;

use nabu\data\interfaces\INabuDataList;
use nabu\data\interfaces\INabuDataReadable;

use nabu\infrastructure\reader\interfaces\INabuDataListReader;
use nabu\infrastructure\reader\interfaces\INabuDataListFileReader;

/**
 * PHPUnit tests to verify functionality of class @see { CNabuAbstractDataListReader }
 * and @see { CNabuAbstractDataListFileReader }.
 * @author Rafael Gutierrez <rgutierrez@nabu-3.com>
 * @since 3.0.4
 * @version 3.0.4
 * @package nabu\infrastructure\reader
 */
class CNabuAbstractDataListFileReaderTest extends TestCase
{
    /**
     * @test __construct
     * @test __destruct
     * @test validateFile
     * @test loadFromFile
     */
    public function testConstruct()
    {
        $reader = new CNbuAbstractDataListFileReaderTesting(
            __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'sample-01.txt'
        );
        $this->assertInstanceOf(INabuDataListFileReader::class, $reader);
        $this->assertInstanceOf(INabuDataListReader::class, $reader);

        $reader->setConvertFieldsMatrix([
            'key_1' => 'field_1',
            'key_2' => 'field_2',
            'key_3' => 'field_3'
        ]);
        $reader->setRequiredFields(['field_1', 'field_2']);
        $reader->setUseStrictSourceNames(true);
        $reader->setHeaderNamesOffset(0);
        $reader->setFirstRowOffset(1);

        $list = $reader->parse();
        $this->assertInstanceOf(INabuDataList::class, $list);
        $this->assertSame(3, count($list));
    }

    /**
     * @test validateFile
     * @test loadFromfile
     */
    public function testValidateFileFailsWithEmptyFilename()
    {
        $reader = new CNbuAbstractDataListFileReaderTesting();

        $this->expectException(Error::class);
        $this->expectExceptionMessage(sprintf(TRIGGER_ERROR_INVALID_FILE_READER_FILENAME, ''));

        $reader->loadFromFile('');
    }

    /**
     * @test validateFile
     * @test loadFromfile
     */
    public function testValidateFileFailsWithNotFoundFilename()
    {
        $reader = new CNbuAbstractDataListFileReaderTesting();

        $filename = __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'no-readable-file.txt';
        $this->expectException(Error::class);
        $this->expectExceptionMessage(sprintf(TRIGGER_ERROR_INVALID_FILE_READER_FILENAME, $filename));

        $reader->loadFromFile($filename);
    }
}

class CNbuAbstractDataListFileReaderTesting extends CNabuAbstractDataListFileReader
{
    protected function getValidMIMETypes(): array
    {
        return [ 'text/plain' ];
    }

    protected function customFileValidation(string $filename): bool
    {
        return true;
    }

    protected function openSourceFile(string $filename): bool
    {
        return true;
    }

    protected function closeSourceFile(): void
    {
    }

    protected function createDataListInstance(): INabuDataList
    {
        return new CNabuAbstractDataListFileReaderDataListTesting('field_1');
    }

    protected function getSourceDataAsArray(): ?array
    {
        return [
            [ 'key_1', 'key_2', 'key_3'],
            [ 'value 1', 'value 2', 'value 3'],
            [ 'value 4', 'value 5', 'value 6'],
            [ 'value 7', 'value 8', 'value 9']
        ];
    }

    protected function checkBeforeParse(): bool
    {
        return true;
    }

    protected function checkAfterParse(INabuDataList $resultset): bool
    {
        return true;
    }
}

class CNabuAbstractDataListFileReaderDataListTesting extends CNabuAbstractDataList
{
    protected function acquireItem($key): ?INabuDataReadable
    {
        return null;
    }

    protected function createDataInstance(array $data): ?INabuDataReadable
    {
        return new CNabuAbstractDataListFileReaderDataTesting($data);
    }
}

class CNabuAbstractDataListFileReaderDataTesting extends CNabuAbstractDataObject
{

}
