<?php

namespace Modules\Authorization\Exceptions;

use App\Exceptions\BaseException;

class InvalidRoleTypeException extends BaseException
{
    public function __construct(array $parameters = [])
    {
        parent::__construct('role.invalid_role_type', $parameters, 'authorization');
    }
}