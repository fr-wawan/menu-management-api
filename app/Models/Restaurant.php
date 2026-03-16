<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string|null $phone
 * @property string|null $opening_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\RestaurantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant whereOpeningHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Restaurant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Restaurant extends Model
{
    /** @use HasFactory<\Database\Factories\RestaurantFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
