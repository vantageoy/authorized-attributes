<?php

namespace Vantage;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

trait AuthorizedAttributes
{
    /**
     * Get the hidden attributes for the model.
     *
     * @return array
     */
    public function getHidden()
    {
        $policy = Gate::getPolicyFor(static::class);

        // If no policy found, check does this extend another model
        // and try get the policy from that one
        if (! $policy && static::class !== self::class) {
            $policy = Gate::getPolicyFor(self::class);
        }

        if (! $policy) {
            return $this->hidden;
        }

        return AttributeGate::getHidden($this, $this->hidden, $policy);
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        $policy = Gate::getPolicyFor(static::class);

        // If no policy found, check does this extend another model
        // and try get the policy from that one
        if (! $policy && static::class !== self::class) {
            $policy = Gate::getPolicyFor(self::class);
        }

        if (! $policy) {
            return $this->fillable;
        }

        return AttributeGate::getFillable($this, $this->fillable, $policy);
    }

    /**
     * Get the method name for the attribute visibility ability in the model policy.
     *
     * @param  string  $attribute
     * @return string
     */
    public function getAttributeViewAbilityMethod($attribute)
    {
        return 'see'.Str::studly($attribute);
    }

    /**
     * Get the model policy ability method name to update an model attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    public function getAttributeUpdateAbilityMethod($attribute)
    {
        return 'edit'.Str::studly($attribute);
    }
}
