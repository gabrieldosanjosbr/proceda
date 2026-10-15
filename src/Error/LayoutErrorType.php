<?php

namespace Proceda\Error;

enum LayoutErrorType: string
{
    case MISSING_REQUIRED_REGISTRO = 'missing_required_registro';
    case INLINE_LIMIT_EXCEEDED = 'inline_limit_exceeded';
    case OCCURS_LIMIT_EXCEEDED = 'occurs_limit_exceeded';
}