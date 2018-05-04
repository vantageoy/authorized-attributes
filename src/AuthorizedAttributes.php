<?php

namespace Salomoni;

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
        if (! $policy = Gate::getPolicyFor(self::class)) {
            return $this->hidden;
        }

        return array_filter($this->hidden, function ($attribute) use ($policy) {
            $ability = $this->getAttributeAbilityMethod($attribute);

            if (is_callable([$policy, $ability])) {
                return Gate::denies($ability, $this);
            }

            return true;
        });
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable()
    {
        if (! $policy = Gate::getPolicyFor(self::class)) {
            return $this->fillable;
        }

        return array_filter($this->fillable, function ($attribute) use ($policy) {
            $ability = $this->getAttributeAbilityMethod($attribute);

            if (is_callable([$policy, $ability])) {
                return ! Gate::denies($ability, $this);
            }

            return true;
        });
    }

    /**
     * Make the given, typically fillable, attributes non-fillable (but not guarded).
     *
     * @param  array|string  $attributes
     * @return $this
     */
    public function makeNonFillable($attributes)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        $this->fillable = array_diff($this->fillable, (array) $attributes);

        return $this;
    }

    /**
     * Get the method name for the attribute visibility ability in the model policy.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttributeAbilityMethod($attribute)
    {
        return 'see'.Str::studly($attribute);
    }
}
