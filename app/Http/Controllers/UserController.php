<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get user companies
     */
    public function getCompanies()
    {
        $user = Auth::user();
        return $user->companies;
    }

    /**
     * Add company to user
     */
    public function addCompany(Request $request)
    {
        $rules = [
            'title' => 'required|max:255',
            'phone' => 'required|max:255',
            'description' => 'required|max:255',
        ];
        $this->validate($request, $rules);
        $data = $request->only(array_keys($rules));
        $user = Auth::user();
        $company = new Company($data);
        $user->companies()->save($company);
        return $company;
    }
}
