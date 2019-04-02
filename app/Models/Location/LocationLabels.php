<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class LocationLabels extends Model
{
  protected $table="location_tweet_labels";
  protected $primaryKey = 'id';
  protected $fillable = ['user_id','type','location'];
  public function tweet(){
    return $this->belongsTo("App\Models\Location\Tweets","tweet_id","id");
  }
}
