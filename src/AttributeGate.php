<?php

namespace Vantage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class AttributeGate
{
    public static function getHidden(Model $model, $fields, $policy)
    {
        return array_values(array_filter($fields, function ($attribute) use ($model, $policy) {
            $ability = $model->getAttributeViewAbilityMethod($attribute);

            if (is_callable([$policy, $ability])) {
                return Gate::denies($ability, $model);
            }

            return true;
        }));
    }

    public static function getFillable(Model $model, $fields, $policy)
    {
        return array_values(array_filter($fields, function ($attribute) use ($model, $policy) {
            $ability = $model->getAttributeUpdateAbilityMethod($attribute);

            if (is_callable([$policy, $ability])) {
                return Gate::allows($ability, $model);
            }

            return true;
        }));
    }
}
