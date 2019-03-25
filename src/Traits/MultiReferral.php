<?php

namespace Devi\MultiReferral\Traits;

use App\User;
use Devi\MultiReferral\Models\ReferralList;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;

trait MultiReferral
{
    public $parentRefList = [];

    public function getReferralLink()
    {
        return url('/').'/?ref='.$this->login;
    }

    public static function scopeReferralExists(Builder $query, $referral)
    {
        return $query->whereLogin($referral)->exists();
    }

    public function getParentReferral()
    {
        return $this->hasOne(config('multi_referral.user_model', 'App\User'),'id','ref_id');
    }

    public function findAndSaveAllParents($user=false,$level = 1)
    {
        if(!$user){$user = $this;}

        if($user->ref_id && $level <= config("multi_referral.referral_levels",3)){

            $this->parentRefList[] = [
                "user_id" => $user->ref_id,
                "ref_id" => $this->id,
                "level" => $level
            ];

            self::findAndSaveAllParents($user->getParentReferral,$level+1);
        }else{
            if(!empty($this->parentRefList)) {
                ReferralList::insert($this->parentRefList);
            }
        }
        return $this->parentRefList;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if ($ref = Cookie::get('referral')) {
                if($user = User::whereLogin($ref)->first()) {
                    $model->ref_id = $user->id;
                }
            }
        });
    }

}