<?php

namespace Modules\Integration\Events;

use stdClass;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Modules\Integration\Contracts\IEventData;

abstract class EventData implements IEventData
{
    /**
     * @var object
     */
    protected $attributes;

    public function __construct(object $attributes) 
    {
        $this->attributes = $attributes;
    }

    /**
     * @param array $keys
     * @param object $object
     * @return object
     */
    protected function normalize(array $keys, object $object): object
    {
        $metadata = new stdClass();
        if ($object instanceof EloquentModel) {
            $object = $object->getAttributes();
        }

        foreach ($object as $property => $value) {
            if (array_key_exists($property, $keys)) {
                $metadata->{$keys[$property]} = $value;
            }
        }

        return $metadata;
    }

    /**
     * @return object
     */ 
    public function getAttributes()
    {
        return $this->attributes;
    }
}
