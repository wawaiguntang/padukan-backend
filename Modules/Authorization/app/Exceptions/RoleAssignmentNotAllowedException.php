<?php

namespace Modules\Authorization\Exceptions;

use App\Exceptions\BaseException;

class RoleAssignmentNotAllowedException extends BaseException
{
    public function __construct(array $parameters = [])
    {
        parent::__construct('role.assignment_not_allowed', $parameters, 'authorization');
    }
}