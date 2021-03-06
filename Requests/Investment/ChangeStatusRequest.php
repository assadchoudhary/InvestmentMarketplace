<?php

namespace Requests\Investment;

use Requests\AbstractRequest;
use Helpers\Validator;
use Models\Constant\ProjectStatus;
use Models\Table\Project;

/**
 * @property int $project
 * @property int $status
 */
class ChangeStatusRequest extends AbstractRequest {

    protected static array
        $properties = [
            'status'    => [self::TYPE_CONSTANTS,  ProjectStatus::class],
            'project'   => [self::TYPE_INT,        [Validator::MODEL => Project::class]],
        ];
}