<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Financial;
use App\Models\Media;
use App\Models\Message;
use App\Models\Propertie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class DashboardController extends Controller
{
    public function index()
    {
       
        return view('dashboard.index');
    }

    public function account()
    {
        return view('dashboard.user-account.form');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update the user's name and email
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Update the password if provided
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
            $user->save();

            // Log out the user
            Auth::logout();

            // Invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            toast('Password updated. Please log in again.');

            // Redirect to login page
            return redirect('/login');
        }
        // Save the updated user details
        toast('Profile updated successfully!');
        $user->save();

        // Redirect with a success message
        return redirect()->back()->with('status', 'Profile updated successfully!');
    }
    public function qrCodes()
    {
        $totalTables = Configuration::where('configuration_key', 'number_of_qr_codes_per_table')->value('configuration_value');
        $totalTables = $totalTables ? (int) $totalTables : 20;

        $tables = [];
        for ($i = 1; $i <= $totalTables; $i++) {
            $token = Crypt::encryptString((string) $i);
            $tables[$i] = route('menu.table', ['token' => $token]);
        }

        return view('dashboard.qr-codes', compact('tables'));
    }
}
