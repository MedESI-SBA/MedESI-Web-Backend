<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            "year" => "required|integer|min:2020|max:" . date("Y"),
        ]);
        $year = $request->input("year");

        $totalPatients = [
            "male" => Patient::where('gender', 'male')->count(),
            "female" => Patient::where('gender', 'male')->count()
        ];
        $contagious = Consultation::query()
            ->whereYear('consultations.created_at', $year)
            ->where('contagious', true)
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->select(
                'consultations.condition',
    
                DB::raw("COUNT(CASE WHEN patients.gender = 'male' THEN 1 ELSE NULL END) as male_count"),
                DB::raw("COUNT(CASE WHEN patients.gender = 'female' THEN 1 ELSE NULL END) as female_count"),
                DB::raw("COUNT(*) as total_count"), 
        
                DB::raw("COUNT(CASE WHEN patients.gender = 'male' AND (consultations.reorientation IS NULL OR consultations.reorientation = '') THEN 1 ELSE NULL END) as male_reorientation_null_count"),
                DB::raw("COUNT(CASE WHEN patients.gender = 'female' AND (consultations.reorientation IS NULL OR consultations.reorientation = '') THEN 1 ELSE NULL END) as female_reorientation_null_count"),
        
                DB::raw("COUNT(CASE WHEN patients.gender = 'male' AND consultations.reorientation IS NOT NULL AND consultations.reorientation != '' THEN 1 ELSE NULL END) as male_reorientation_not_null_count"),
                DB::raw("COUNT(CASE WHEN patients.gender = 'female' AND consultations.reorientation IS NOT NULL AND consultations.reorientation != '' THEN 1 ELSE NULL END) as female_reorientation_not_null_count"),
            )
            ->groupBy('consultations.condition')
            ->get();

            $chronic = Consultation::query()
            ->whereYear('consultations.created_at', $year)
            ->where('chronic', true)
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->select(
                'consultations.condition',
    
                DB::raw("COUNT(CASE WHEN patients.gender = 'male' THEN 1 ELSE NULL END) as male_count"),
                DB::raw("COUNT(CASE WHEN patients.gender = 'female' THEN 1 ELSE NULL END) as female_count"),
                DB::raw("COUNT(*) as total_count"), 
        
                DB::raw("COUNT(CASE WHEN patients.gender = 'male' AND (consultations.reorientation IS NULL OR consultations.reorientation = '') THEN 1 ELSE NULL END) as male_reorientation_null_count"),
                DB::raw("COUNT(CASE WHEN patients.gender = 'female' AND (consultations.reorientation IS NULL OR consultations.reorientation = '') THEN 1 ELSE NULL END) as female_reorientation_null_count"),
        
                DB::raw("COUNT(CASE WHEN patients.gender = 'male' AND consultations.reorientation IS NOT NULL AND consultations.reorientation != '' THEN 1 ELSE NULL END) as male_reorientation_not_null_count"),
                DB::raw("COUNT(CASE WHEN patients.gender = 'female' AND consultations.reorientation IS NOT NULL AND consultations.reorientation != '' THEN 1 ELSE NULL END) as female_reorientation_not_null_count"),
            )
            ->groupBy('consultations.condition')
            ->get();
        
        return response()->json([
            "year" => $year,
            "totalPatients" => $totalPatients,
            "contagious" => $contagious,
            "chronic" => $chronic
        ]);

    }
}
