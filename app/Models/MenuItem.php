<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\MenuItem\CategoryEnum;
use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property numeric $price
 * @property CategoryEnum|null $category
 * @property bool $is_available
 * @property int $restaurant_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Restaurant $restaurant
 *
 * @method static Builder<static>|MenuItem byCategory(?string $category)
 * @method static \Database\Factories\MenuItemFactory factory($count = null, $state = [])
 * @method static Builder<static>|MenuItem newModelQuery()
 * @method static Builder<static>|MenuItem newQuery()
 * @method static Builder<static>|MenuItem query()
 * @method static Builder<static>|MenuItem search(?string $search)
 * @method static Builder<static>|MenuItem whereCategory($value)
 * @method static Builder<static>|MenuItem whereCreatedAt($value)
 * @method static Builder<static>|MenuItem whereDescription($value)
 * @method static Builder<static>|MenuItem whereId($value)
 * @method static Builder<static>|MenuItem whereIsAvailable($value)
 * @method static Builder<static>|MenuItem whereName($value)
 * @method static Builder<static>|MenuItem wherePrice($value)
 * @method static Builder<static>|MenuItem whereRestaurantId($value)
 * @method static Builder<static>|MenuItem whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'category' => CategoryEnum::class,
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        return $query->when(
            $category,
            fn (Builder $q) => $q->whereRaw('LOWER(category) = ?', [strtolower($category)])
        );
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when(
            $search,
            fn (Builder $q) => $q->where('name', 'like', "%{$search}%")
        );
    }
}
