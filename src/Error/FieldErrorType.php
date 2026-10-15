<?php

namespace Proceda\Error;

enum FieldErrorType: string
{
    case MISSING_REQUIRED_VALUE = 'missing_required_value';
    case TYPE_NOT_ALLOWED = 'type_not_allowed';
    case VALUE_EXCEEDS_MAXIMUM_LENGTH = 'value_exceeds_maximum_length';
    case INVALID_TIME = 'invalid_time';
}