<?php
 
namespace App\Http\Controllers;

use Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RacesController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Races;
use App\Models\Activities;
use App\Models\Race_registrations;
use App\Models\Activity_races;
use Illuminate\Support\Facades\DB;

class ActivitiesController extends Controller
{
    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
     
     public function createActivities(Request $request)
     {
        $validator = Validator::make($request->all(), [
             'activity_picture' => 'image|mimes:svg,jpeg,png,jpg,gif|max:2048',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'message' => 'Validation fail',
                 'errors' => $validator->errors(),
             ], 400);
         }

         $user = Auth::guard('api')->user();
 
         $activities = new Activities;
         $activities->activity_name = $request->input('activity_name');
         $activities->activity_type = $request->input('activity_type');
         $activities->activity_kilometers = $request->input('activity_kilometers');
         $activities->activity_hours = $request->input('activity_hours');
         $activities->activity_minutes = $request->input('activity_minutes');
         $activities->activity_seconds = $request->input('activity_seconds');
         $activities->activity_datetime = $request->input('activity_datetime');
        //  $activities->race_ids = $request->race_ids;
         $activities->user_id = $user->id;
 
         if ($request->hasFile('activity_picture')) {
             $image = $request->file('activity_picture');
             $imageName = time().'.'.$image->getClientOriginalExtension();
             $image->move($this->getPublicPath('images'), $imageName);
             $activities->activity_picture = $this->getPublicPath('images/'.$imageName);
         }
 
        $activities->save();

        $userRaces = DB::table('races')
            ->join('race_registrations', 'races.id', '=', 'race_registrations.race_id')
            ->join('users', 'race_registrations.user_id', '=', 'users.id')
            ->select('races.id')
            ->where('users.id', $user->id)
            ->get();
        
        $userRacesIds = [];
        
        foreach ($userRaces as $userRace) {
            if ($activities->activity_datetime > $userRace->race_activitystartdatetime &&
                $activities->activity_datetime < $userRace->race_activityenddatetime) {
                array_push($userRacesIds, $userRace->id);
            }
        }
        
        if (!empty($userRacesIds)) {
            foreach ($userRacesIds as $userRaceId) {
                $activityRaces = $this->addActivtyRaces($activities->id, $userRaceId);
            }
        }
          
         return response()->json([
             'success' => true,
             'message' => 'New activities created',
             'data' => [
                 'activities' => $activities,
                //  'activityraces' => $activityRaces
             ]
         ]);
     }

     private function getPublicPath($path = '')
     {
         return rtrim(app()->basePath('public/' . $path), '/');
     }

     public function addActivtyRaces($idactivity, $idraces)
    {
        $activityraces = Activity_races::create([
            'activity_id' => $idactivity,
            'race_id' => $idraces
        ]);

        return response()->json($activityraces);
    }

    public function getAllUserActivities()
    {
        $user = Auth::guard('api')->user();

        $activities =   DB::table('activities')
                        ->leftjoin('activity_races', 'activity_races.activity_id', '=', 'activities.id')
                        ->leftjoin('races', 'races.id', '=', 'activity_races.race_id')
                        ->select('activities.*', 'races.race_name')
                        ->where('user_id', $user->id)
                        ->get();

        foreach ($activities as $activity) {
            $activity->activity_picture = str_replace(
                'C:\\Users\\ASUS\\Desktop\\tes-magang\\causeid-api\\public/images/',
                'http://127.0.0.1:8000/images/',
                $activity->activity_picture
            );
        }
 
         return response()->json([
             'status' => 'success',
             'data' => [
                'activities' => $activities,
                'user' => $user,
             ]
         ]);
    }

    public function delUserActivities($id)
    {
        $activities = Activities::find($id);

        if (!$activities) {
            return response()->json([
                'success' => false,
                'message' => 'activities not found'
            ], 404);
        }

        $activities->delete();

        return response()->json([
            'success' => true,
            'message' => 'activities was deleted'
        ], 200);
    }

}