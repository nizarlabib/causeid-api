<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use PDF;
use Auth;
use Illuminate\Support\Facades\DB;
  
class PDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePDF(Request $request)
    {
        $user = Auth::guard('api')->user();

        $userRaceData = DB::table('races')
            ->join('race_registrations', 'races.id', '=', 'race_registrations.race_id')
            ->join('users', 'race_registrations.user_id', '=', 'users.id')
            ->select('races.race_name', 'users.username')
            ->where('users.id', $user->id)
            ->where('races.id', $request->id)
            ->first();
          
        $data = [
            'username' => 'nizar',
            'race_name' => $userRaceData->race_name,
        ]; 

        $pdf = PDF::loadView('myPDF', $data);

        return $pdf->stream('myPDF');
    }
}