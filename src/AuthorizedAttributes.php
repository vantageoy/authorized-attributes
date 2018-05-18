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
            $view_ability = $this->getAttributeViewAbilityMethod($attribute);

            if (is_callable([$policy, $view_ability])) {
                return Gate::denies($view_ability, $this);
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
            $update_ability = $this->getAttributeUpdateAbilityMethod($attribute);

            if (is_callable([$policy, $update_ability])) {
                return ! Gate::denies($update_ability, $this);
            }

            return true;
        });
    }

    /**
     * Backward-compatibility
     *
     * @param $attribute
     * @return string
     */
    protected function getAttributeAbilityMethod($attribute)
    {
        return $this->getAttributeViewAbilityMethod($attribute);
    }

    /**
     * Get the method name for the attribute visibility ability in the model policy.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttributeViewAbilityMethod($attribute)
    {
        return 'see'.Str::studly($attribute);
    }

    /**
     * Get the method name for the ability to update attribute in the model policy.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getAttributeUpdateAbilityMethod($attribute)
    {
        return 'change'.Str::studly($attribute);
    }
}
