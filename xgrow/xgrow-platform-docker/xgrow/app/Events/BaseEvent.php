<?php

namespace App\Events;

use stdClass;
use App\Platform;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * @deprecated v0.23
 */
class BaseEvent
{
    use Dispatchable, SerializesModels;

    public $platform;
    public $metadata;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Platform $platform, object $metadata) {
        $this->platform = $platform;
        $this->metadata = $metadata;
    }

    public function normalize($keys, $object) {
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
}
