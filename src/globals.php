<?php

/** @var string TRIGGER_ERROR_READ_ONLY_MODE Constant literal for Error message. */
const TRIGGER_ERROR_READ_ONLY_MODE = "Instance is in Read Only mode and cannot be edited.";
/** @var string TRIGGER_ERROR_INVALID_KEY Constant literal for Error message. */
const TRIGGER_ERROR_INVALID_KEY = "Invalid key supplied [%s].";
/** @var string TRIGGER_ERROR_INVALID_INDEX Constant literal for Error message. */
const TRIGGER_ERROR_INVALID_INDEX = "Invalid index supplied [%s].";
/** @var string TRIGGER_ERROR_INVALID_ARGUMENT Constant literal for Error message. */
const TRIGGER_ERROR_INVALID_ARGUMENT = "Invalid argument [%s].";
/** @var string TRIGGER_ERROR_INVALID_FILE_READER_FILENAME Constant literal for Error message. */
const TRIGGER_ERROR_INVALID_FILE_READER_FILENAME = "Invalid file name [%s] in File Reader.";
/** @var string TRIGGER_ERROR_REQUIRED_FIELDS_NOT_FOUND Constant literal for Error message. */
const TRIGGER_ERROR_REQUIRED_FIELDS_NOT_FOUND = "Required fields [%s] not found.";
/** @var string TRIGGER_ERROR_REQUIRED_FIELDS_NOT_FOUND Constant literal for Error message. */
const TRIGGER_ERROR_REQUIRED_FIELDS_NOT_FOUND_IN_LINE = "Required field(s) [%s] not found in row [%d].";

/** @var array Array list of vowels and consonants with different tildes in UTF-8. */
const NABU_ARRAY_CHARACTER_WITH_TILDE = array(
    'á', 'é', 'í', 'ó', 'ú',
    'à', 'è', 'ì', 'ò', 'ù',
    'ä', 'ë', 'ï', 'ö', 'ü', 'ÿ',
    'â', 'ê', 'î', 'ô', 'û',
    'ã', 'õ', 'ç', 'ñ'
);
/** @var array Equivalent array list of vowels and consonants without tildes in UTF-8. */
const NABU_ARRAY_CHARACTER_CANONICAL = array(
    'a', 'e', 'i', 'o', 'u',
    'a', 'e', 'i', 'o', 'u',
    'a', 'e', 'i', 'o', 'u', 'y',
    'a', 'e', 'i', 'o', 'u',
    'a', 'o', 'c', 'n'
);
