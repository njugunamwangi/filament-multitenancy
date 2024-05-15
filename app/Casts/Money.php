<?php

namespace App\Casts;

use Brick\Math\RoundingMode;
use Brick\Money\Money as MoneyMoney;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Money implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return MoneyMoney::of($value, $model->currency->abbr)->formatTo($model->currency->locale);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value instanceof MoneyMoney) {
            return $value;
        }

        return $value->getAmount()->toScale(0, RoundingMode::UP)->toInt();
    }
}
