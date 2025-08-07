<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cat extends Model
{
    use HasFactory;

    protected $fillable = [
        'img',
        'status',
        'follow',
        'ord',
        'user_id',
        'show_in_media',
        'show_in_article',
        'type',
        'parent_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeHasTitleTranslation($query)
    {
        return $query->has('titleTranslation');
    }

    public function article()
    {
        return $this->hasMany(Article::class,'cat_id');
    }
    
    public function specialVillages()
    {
        return $this->hasMany(Article::class,'special_id');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_cat');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CatTranslation::class);
    }

    public function titleTranslation(): HasOne
    {
        return $this->translations()
            ->one()
            ->select('cat_id', 'title')
            ->where('locale', app()->getLocale());
    }

    public function allTranslation(): HasOne
    {
        return $this->translations()
            ->one()
            ->select('cat_id', 'title','description', 'seo_keywords', 'seo_description')
            ->where('locale', app()->getLocale());
    }

    public function parent() {
        return $this->belongsTo(Cat::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Cat::class, 'parent_id');
    }

    public static function getFollowOptions()
    {
        return [
            1 => 'التراث',
            2 => 'الوسائط',
            4 => 'المدن',
            5 => 'المؤلفين',
            6 => 'الوسوم',
        ];
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function childrenRecursive()
    {
        return $this->hasMany(Cat::class, 'parent_id')->with([
            'childrenRecursive',
            'user:id,name',
            'titleTranslation'
        ]);
    }

    /**
     * Get all descendants of this category
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors of this category
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Get the root category (top-level parent)
     */
    public function root()
    {
        return $this->parent ? $this->parent->root() : $this;
    }

    /**
     * Get the depth level of this category (0 for root)
     */
    public function getDepthAttribute()
    {
        $depth = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }
        
        return $depth;
    }

    /**
     * Get the full path of this category
     */
    public function getPathAttribute()
    {
        $path = collect([$this]);
        $parent = $this->parent;
        
        while ($parent) {
            $path->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $path;
    }

    /**
     * Get the full path as string
     */
    public function getPathStringAttribute()
    {
        return $this->path->pluck('titleTranslation.title')->filter()->implode(' → ');
    }

    /**
     * Count total descendants (children + grandchildren + etc.)
     */
    public function getTotalDescendantsCountAttribute()
    {
        return $this->children()->count() + $this->children()->get()->sum('total_descendants_count');
    }

    /**
     * Check if this category is a leaf (has no children)
     */
    public function getIsLeafAttribute()
    {
        return $this->children()->count() === 0;
    }

    /**
     * Check if this category is a root (has no parent)
     */
    public function getIsRootAttribute()
    {
        return is_null($this->parent_id);
    }

    /**
     * Get siblings (categories with same parent)
     */
    public function siblings()
    {
        if ($this->is_root) {
            return static::whereNull('parent_id')->where('id', '!=', $this->id);
        }
        
        return static::where('parent_id', $this->parent_id)->where('id', '!=', $this->id);
    }

    /**
     * Check if moving to a new parent would create circular reference
     */
    public function wouldCreateCircularReference($newParentId)
    {
        if ($this->id == $newParentId) {
            return true;
        }

        $newParent = static::find($newParentId);
        if (!$newParent) {
            return false;
        }

        // Check if new parent is a descendant of this category
        $descendants = $this->getAllDescendantIds();
        return in_array($newParentId, $descendants);
    }

    /**
     * Get all descendant IDs recursively
     */
    public function getAllDescendantIds()
    {
        $ids = [];
        $children = $this->children()->get();
        
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        
        return $ids;
    }

    /**
     * Get all ancestor IDs recursively
     */
    public function getAllAncestorIds()
    {
        $ids = [];
        $parent = $this->parent;
        
        while ($parent) {
            $ids[] = $parent->id;
            $parent = $parent->parent;
        }
        
        return $ids;
    }

    /**
     * Scope to get only root categories
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to get only inactive categories
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope to get categories by depth level
     */
    public function scopeByDepth($query, $depth)
    {
        if ($depth === 0) {
            return $query->whereNull('parent_id');
        }
        
        return $query->whereHas('parent', function ($q) use ($depth) {
            $q->byDepth($depth - 1);
        });
    }

    /**
     * Get the next order number for siblings
     */
    public function getNextOrderAttribute()
    {
        if ($this->is_root) {
            return static::whereNull('parent_id')->max('ord') + 1;
        }
        
        return static::where('parent_id', $this->parent_id)->max('ord') + 1;
    }

    /**
     * Move category to new parent and reorder
     */
    public function moveToParent($newParentId = null, $newOrder = null)
    {
        $oldParentId = $this->parent_id;
        $oldOrder = $this->ord;
        
        // Update parent
        $this->parent_id = $newParentId;
        
        // Update order
        if ($newOrder) {
            $this->ord = $newOrder;
        } else {
            $this->ord = $this->next_order;
        }
        
        $this->save();
        
        // Reorder old siblings
        if ($oldParentId) {
            static::where('parent_id', $oldParentId)
                ->where('ord', '>', $oldOrder)
                ->decrement('ord');
        }
        
        // Reorder new siblings
        if ($newParentId) {
            static::where('parent_id', $newParentId)
                ->where('ord', '>=', $this->ord)
                ->where('id', '!=', $this->id)
                ->increment('ord');
        }
    }
}
