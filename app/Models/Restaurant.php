<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string|null $phone
 * @property string|null $opening_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MenuItem> $menuItems
 * @property-read int|null $menu_items_count
 * @method static \Database\Factories\RestaurantFactory factory($count = null, $state = [])
 * @method static Builder<static>|Restaurant newModelQuery()
 * @method static Builder<static>|Restaurant newQuery()
 * @method static Builder<static>|Restaurant query()
 * @method static Builder<static>|Restaurant search(?string $search)
 * @method static Builder<static>|Restaurant whereAddress($value)
 * @method static Builder<static>|Restaurant whereCreatedAt($value)
 * @method static Builder<static>|Restaurant whereId($value)
 * @method static Builder<static>|Restaurant whereName($value)
 * @method static Builder<static>|Restaurant whereOpeningHours($value)
 * @method static Builder<static>|Restaurant wherePhone($value)
 * @method static Builder<static>|Restaurant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Restaurant extends Model
{
    /** @use HasFactory<\Database\Factories\RestaurantFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when(
            $search,
            fn(Builder $q) => $q->where('name', 'like', "%{$search}%")
        );
    }
}
