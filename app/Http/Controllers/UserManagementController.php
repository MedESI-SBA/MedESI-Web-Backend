<?php

namespace App\Http\Controllers;

use App\Imports\StudentImport;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Storage;

class UserManagementController extends Controller
{
    public function createStudentAccounts()
    {
        request()->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls|max:2048',
        ]);
        $path = request()->file('file')->store('temp_uploads'); // Store the file in a temporary location
        $filename = basename($path);
        try {
            $path = 'temp_uploads/' . $filename;
            Excel::import(new StudentImport ,$path);
    
            // Delete the temporary file after import
            Storage::delete('temp_uploads/' . $filename);
    
            return redirect()->back()->with('success', 'File imported successfully!');
        } catch (\Exception $e) {
            // Handle import errors
            Storage::delete('temp_uploads/' . $filename); //Delete file on error
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }

    }
}
