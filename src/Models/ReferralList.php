<?php

namespace Devi\MultiReferral\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralList extends Model
{
    protected $table = "referral_lists";
    protected $fillable = [
        'user_id','ref_id','level'
    ];

    public function user(){
        return $this->hasOne(config('multi_referral.user_model', 'App\User'),'id','ref_id');
    }
}
