<?php

namespace Modules\Authorization\Exceptions;

use App\Exceptions\BaseException;

class UserAlreadyHasRoleException extends BaseException
{
    public function __construct(array $parameters = [])
    {
        parent::__construct('role.user_already_has_role', $parameters, 'authorization');
    }
}