<?php
 
namespace App\Http\Controllers;
 
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Races;
use App\Models\Activities;
use App\Models\Race_registrations;
use Illuminate\Support\Facades\DB;

 
class RacesController extends Controller
{
    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */

     public function createRaces(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'race_picture' => 'image|mimes:svg,jpeg,png,jpg,gif|max:2048',
         ]);
 
         if ($validator->fails()) {
             return response()->json([
                 'message' => 'Validation fail',
                 'errors' => $validator->errors(),
             ], 400);
         }

         $user = Auth::guard('api')->user();
 
         $races = new Races;

         $races->race_name = $request->input('race_name');
         $races->race_startdatetime = $request->input('race_startdatetime');
         $races->race_enddatetime = $request->input('race_enddatetime');
         $races->race_activitystartdatetime = $request->input('race_activitystartdatetime');
         $races->race_activityenddatetime = $request->input('race_activityenddatetime');
         $races->race_description = $request->input('race_description');
         $races->race_finishkilometer = $request->input('race_finishkilometer');
 
         if ($request->hasFile('race_picture')) {
             $image = $request->file('race_picture');
             $imageName = time().'.'.$image->getClientOriginalExtension();
             $image->move($this->getPublicPath('images'), $imageName);
             $races->race_picture = $this->getPublicPath('images/'.$imageName);
         }
 
         $races->save();
 
         return response()->json([
             'success' => true,
             'message' => 'New races created',
             'data' => [
                 'races' => $races
             ]
         ]);
     }

    public function getAllRaces()
    {
        $races = Races::all();

        if (!$races) {
            return response()->json([
                'status' => false,
                'message' => 'races not found'
            ]);
        }

        foreach ($races as $race) {
            $race->race_picture = str_replace(
                'C:\\Users\\ASUS\\Desktop\\tes-magang\\causeid-api\\public/images/',
                'http://127.0.0.1:8000/images/',
                $race->race_picture
            );
        }

        return response()->json([
            'status' => 'success',
            'data' => $races
        ]);
    }


    public function getRaceById(Request $request)
    {
        $race = Races::where('id', $request->id)->first();

        if (!$race) {
            return response()->json([
                'status' => false,
                'message' => 'race not found'
            ]);
        }

        $race->race_picture = str_replace(
            'C:\\Users\\ASUS\\Desktop\\tes-magang\\causeid-api\\public/images/',
            'http://127.0.0.1:8000/images/',
            $race->race_picture
        );
        
        return response()->json([
            'status' => 'success',
            'data' => $race
        ]);
    }

    public function getUserRaces()
    {
        $user = Auth::guard('api')->user();

        $userRaces = DB::table('races')
            ->join('race_registrations', 'races.id', '=', 'race_registrations.race_id')
            ->join('users', 'race_registrations.user_id', '=', 'users.id')
            ->select('races.*')
            ->where('users.id', $user->id)
            ->get();
        
            foreach ($userRaces as $race) {
                $race->race_picture = str_replace(
                    'C:\\Users\\ASUS\\Desktop\\tes-magang\\causeid-api\\public/images/',
                    'http://127.0.0.1:8000/images/',
                    $race->race_picture
                );
            }

        return response()->json([
            'success' => true,
            'data' => [
                'races' => $userRaces,
                'user' => $user
            ]
        ]);
    }
    
    public function cekStatusUserRaces()
    {
        $user = Auth::guard('api')->user();

        $races = DB::table('races')
            ->leftjoin('race_registrations', 'races.id', '=', 'race_registrations.race_id')
            ->leftjoin('users', 'race_registrations.user_id', '=', 'users.id')
            ->where('users.id', '=', $user->id)
            ->select('races.*', DB::raw('IF(users.id IS NOT NULL, "Terdaftar", "Belum Terdaftar") as status'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'races' => $races,
                'user' => $user
            ]
        ]);
    }

    public function getProgressUserRaces()
    {
        $user = Auth::guard('api')->user();

        $userRaces = DB::table('races')
            ->join('race_registrations', 'races.id', '=', 'race_registrations.race_id')
            ->join('users', 'race_registrations.user_id', '=', 'users.id')
            ->join('activity_races', 'activity_races.race_id', '=', 'races.id')
            ->join('activities', 'activities.id', '=', 'activity_races.activity_id')
            ->select('races.id as race_id', 'races.race_name', 'races.race_finishkilometer', 
                    DB::raw('SUM(activities.activity_kilometers) AS total_kilometers'))
            ->where('users.id', $user->id)
            ->where('activities.user_id', $user->id)
            ->groupBy('races.id', 'races.race_name', 'races.race_finishkilometer')
            ->get();

        foreach ($userRaces as $race) {
            $race->progress_races = ($race->total_kilometers / $race->race_finishkilometer) * 100;
        }

        if (!$userRaces) {
            return response()->json([
                'status' => false,
                'message' => 'race not found'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'races' => $userRaces
            ]
        ]);
    }

    public function joinRace(Request $request)
    {
        $user = Auth::guard('api')->user();

        $raceregistrations = Race_registrations::create([
            'user_id' => $user->id,
            'race_id' => $request->input('race_id'),
            'registration_jerseysize' => $request->input('registration_jerseysize')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'success added user to race',
            'data' => [
                'raceregistrations' => $raceregistrations
            ]
        ]);
    }
    
    private function getPublicPath($path = '')
     {
         return rtrim(app()->basePath('public/' . $path), '/');
     }

}