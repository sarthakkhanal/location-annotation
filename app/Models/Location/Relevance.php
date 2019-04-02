<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class Relevance extends Model
{
  protected $table="location_tweet_relevance";
  protected $fillable = ['user_id','tweet_id','relevance'];
  public $timestamps=false;
  public function tweet(){
    return $this->belongsTo("App\Models\Location\Tweets","tweet_id","id");
  }
}
