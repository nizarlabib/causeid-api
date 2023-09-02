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
        // $user = Auth::guard('api')->user();

        $userRaceData = DB::table('races')
            ->join('race_registrations', 'races.id', '=', 'race_registrations.race_id')
            ->join('users', 'race_registrations.user_id', '=', 'users.id')
            ->select('races.race_name', 'users.username')
            ->where('users.id', '1')
            ->where('races.id', '1')
            ->first();
          
        $data = [
            'username' => 'nizar',
            'race_name' => $userRaceData->race_name,
        ]; 

        $pdf = PDF::loadView('myPDF', $data);

        $pdfPath = tempnam(sys_get_temp_dir(), 'pdf');
        $pdf->save($pdfPath);

        return response()->file($pdfPath);
    }
}