<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private const BD_PHONE = '/^01[3-9]\d{8}$/';

    public function showLogin() { return view('auth.login'); }

    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);

        if (!Auth::attempt($cred, $r->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email or password is incorrect.'])->onlyInput('email');
        }
        if (Auth::user()->status === 'suspended') {
            Auth::logout();
            return back()->withErrors(['email' => 'This account is suspended. Contact the BugShield admin.']);
        }
        $r->session()->regenerate();
        return redirect($this->home(Auth::user()));
    }

    public function showResearcherRegister() { return view('auth.register-researcher'); }

    public function registerResearcher(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'regex:' . self::BD_PHONE],
            'password' => 'required|min:8|confirmed',
        ], ['phone.regex' => 'Enter a valid Bangladeshi mobile number, like 017XXXXXXXX.']);

        $user = User::create($d + ['role' => 'researcher', 'status' => 'active']);
        Auth::login($user);
        return redirect($this->home($user))->with('ok', 'Welcome to BugShield BD. Pick a program and submit your first report.');
    }

    public function showOrgRegister() { return view('auth.register-org'); }

    public function registerOrg(Request $r)
    {
        $d = $r->validate([
            'org_name' => 'required|max:120',
            'website' => 'required|url|max:200',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'regex:' . self::BD_PHONE],
            'password' => 'required|min:8|confirmed',
            'sender_number' => ['required', 'regex:' . self::BD_PHONE],
            'trx_id' => ['required', 'regex:/^[A-Za-z0-9]{8,12}$/', 'unique:organizations,trx_id'],
        ], [
            'phone.regex' => 'Enter a valid Bangladeshi mobile number, like 017XXXXXXXX.',
            'sender_number.regex' => 'Enter the bKash number you sent the fee from, like 017XXXXXXXX.',
            'trx_id.regex' => 'The bKash TrxID has 8 to 12 letters and numbers.',
            'trx_id.unique' => 'This TrxID has already been used on another registration.',
        ]);

        $user = DB::transaction(function () use ($d) {
            $user = User::create([
                'name' => $d['name'], 'email' => $d['email'], 'phone' => $d['phone'],
                'password' => $d['password'], 'role' => 'organization', 'status' => 'pending',
            ]);
            Organization::create([
                'user_id' => $user->id, 'name' => $d['org_name'], 'website' => $d['website'],
                'trx_id' => strtoupper($d['trx_id']), 'sender_number' => $d['sender_number'],
                'fee_amount' => config('bugshield.org_fee'), 'status' => 'pending',
            ]);
            return $user;
        });

        Auth::login($user);
        return redirect()->route('org.pending')->with('ok', 'Registration submitted. An admin will verify your payment.');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('home');
    }

    private function home(User $u): string
    {
        return match ($u->role) {
            'admin' => route('admin.dashboard'),
            'organization' => route('org.dashboard'),
            default => route('researcher.dashboard'),
        };
    }
}
