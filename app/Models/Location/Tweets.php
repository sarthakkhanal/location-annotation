<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Model;
use App\Models\Location\LocationLabels;
use App\Models\Location\Relevance;
use Auth;
class Tweets extends Model
{
  protected $table="location_tweets";
  protected $primaryKey = 'id';
  public $incrementing = false;
  protected $with =['locationLabels','locationRelevance'];
  public $timestamps=false;
  public function locationLabels(){
    return $this->hasMany(LocationLabels::class,'tweet_id','id')->where('user_id',Auth::user()->id);
  }
  public function locationRelevance(){
    return $this->hasOne(Relevance::class,'tweet_id','id')->where('user_id',Auth::user()->id);
  }
  public function syncLocationLabels($data){
    $this->locationLabels()->delete();
    $relevance=0;
    if(count($data["labels"])>0){
      $relevance=1;
    }
    $relevance=Relevance::updateOrCreate([
      "tweet_id"=>$this->id,
      "user_id"=>Auth::user()->id
    ],[
      "relevance"=>$relevance
    ]);

    foreach($data["labels"] as $datum){
      $this->locationLabels()->save(new LocationLabels($datum));
    }
  }
  public function prepareForForm(){
    $data=[];
    foreach($this->locationLabels as $labels){
      $data[]=[
        "location"=>$labels->location,
        "location_type"=>$labels->type
      ];
    }
    return $data;
  }
}
