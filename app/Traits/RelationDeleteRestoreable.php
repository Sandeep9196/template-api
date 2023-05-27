<?php

namespace App\Traits;

trait RelationDeleteRestoreable
{
    /**
     * @description delete or restoring relationships
     * @param $resource
     * @param $relations_to_cascade
     * @return mixed
     * @date 07 Jan 2023
     * @author Phen
     */
    protected static function boot(){
        parent::boot();
        static::deleting(function ($model) {
            softDeleteRelations($model,$model->relationsToCascade);
        });

        static::restoring(function ($model){
            restoreRelations($model,$model->relationsToCascade);
        });
    }
}
