<?php

namespace Models\Table;

use Core\AbstractEntity;
use Helpers\Validator;
use Interfaces\ModelInterface;
use Traits\Model;

/**
 * @property int    $id
 * @property string $url
 * @property float  $rating
 */
class Hyiplog extends AbstractEntity implements ModelInterface {
    use Model;

    private static string $table = 'hyiplogs';

    protected static array
        $properties = [
            'id'     => [self::TYPE_INT,    [Validator::MIN  => 1]],
            'url'    => [self::TYPE_STRING, [Validator::MIN  => 1, Validator::MAX => 64]],
            'rating' => [self::TYPE_FLOAT,  [Validator::MIN  => 0, Validator::MAX => 10]],
        ];
}
