<?php

namespace App\Http\Controllers\Location;

use Auth;
use DB;
use Validator;
use App\Models\Location\Tweets;
use App\Models\Location\Relevance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
  public function index(Request $request)
  {

      if($request->has("tweet_id") && $request->has("search")){
        $validation = Validator::make($request->all(),[
          "tweet_id"=>"exists:location_tweets,id"
        ]);
        if ($validation->fails())
        {
          return redirect()->back()->withError($validation->errors()->first());
        }
        $tweets = Tweets::where("id",$request->get('tweet_id'))->paginate(10);
        return view('location.index')
          ->with("tweets",$tweets)
          ->with("categories",config("location.categories"));
      }
      if($request->has("relevant") && $request->has("tweet_id")){
        $validation = Validator::make($request->all(),[
          "tweet_id"=>"exists:location_tweets,id",
          "relevant"=>"required"
    		]);
        if ($validation->fails())
        {
          return redirect()->back()->withError($validation->errors()->first());
        }
        $tweet=Tweets::find($request->get('tweet_id'));
        $relevance=Relevance::updateOrCreate([
          "tweet_id"=>$tweet->id,
          "user_id"=>Auth::user()->id
        ],[
          "relevance"=>0

        ]);


        return redirect()->back()->withSuccess("Updated.");
      }
      $tweets = Tweets::doesntHave("locationLabels")->doesntHave("locationRelevance")->orderBy("id")->paginate(1);
      return view('location.index')
        ->with("tweets",$tweets)
        ->with("categories",config("location.categories"))
        ->with("humanitarian",config("location.humanitarian"));
  }
  public function save(Request $request)
  {
      Validator::extend('parallel_array', function($attribute, $value, $parameters,$validator)
  		{
  				$data=$validator->getData();
          return (count($value) == count($data[$parameters[0]]));
  		});
      $validation = Validator::make($request->all(),[
        "tweet_id"=>"exists:location_tweets,id",
        "location"=>"array|parallel_array:location_type",
        "location_type"=>"array",
        "humanitarian"=>"in:".implode(",",config('location.humanitarian'))."",
        "eye_witness"=>"in:1,0"
  		],[
  			"parallel_array"=>"Invalid input: locations and location type counts are different."
  		]);
      if ($validation->fails())
  		{
  			return redirect()->back()->withError($validation->errors()->first());
  		}
      $user_id=Auth::user()->id;
      $tweet=Tweets::find($request->get("tweet_id"));
      $labels=[];
      foreach($request->get('location',[]) as $idx=>$value){
        $labels[]=[
          "user_id"=>$user_id,
          "type"=>trim($request->get('location_type')[$idx]),
          "location"=>trim($value)
        ];
      }
      $data=[
        "humanitarian"=>$request->get("humanitarian",null),
        "eye_witness"=>$request->get("eye_witness",0),
        "labels"=>$labels
      ];
      DB::transaction(function () use($tweet,$data) {
          $tweet->syncLocationLabels($data);
      });
      return redirect()->back()->withSuccess("Updated");
  }
}
