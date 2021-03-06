<?php

namespace App;


/**
 * App\IcecatFeatureGroup
 *
 * @property integer $id
 * @property integer $icecat_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\IcecatCategoryFeatureGroup[] $categoriesFeatureGroups
 * @method static \Illuminate\Database\Query\Builder|\App\IcecatFeatureGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\IcecatFeatureGroup whereIcecatId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\IcecatFeatureGroup whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\IcecatFeatureGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\IcecatFeatureGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\IcecatFeatureGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\LGGModel last()
 */
class IcecatFeatureGroup extends LGGModel {

    protected $table = 'icecat_feature_groups';
    public $timestamps = true;
    protected $fillable = [
        'icecat_id', 'type', 'name', 'description', 'measure'
    ];

    public static $rules = [
        'icecat_id' => 'integer|required|unique:icecat_feature_groups',
        'name'      => 'string|required|max:70',
    ];

    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot() {
        parent::boot();
        IcecatFeatureGroup::creating(function ($icecat_feature_group) {
            return $icecat_feature_group->isValid();
        });
        IcecatFeatureGroup::updating(function ($icecat_feature_group) {
            $icecat_feature_group->updateRules = self::$rules;
            $icecat_feature_group->updateRules['icecat_id'] = "integer|required|unique:icecat_feature_groups,icecat_id,{$icecat_feature_group->id}";

            return $icecat_feature_group->isValid('update');
        });
    }

    /**
     * Obtiene las relaciones con category_feature_group a la que pertenece este feature_group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function categoriesFeatureGroups() {
        return $this->hasMany('App\IcecatCategoryFeatureGroup', 'icecat_feature_group_id', 'icecat_id');
    }
}
