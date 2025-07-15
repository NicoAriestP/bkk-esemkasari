<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Partner;
use App\Models\Vacancy;
use App\Models\VacancyApplication;

class DashboardPartnerController extends Controller
{
    public function index(Request $request)
    {
        $dashboardData = Partner::getDashboardData();

        return Inertia::render('dashboard/DashboardPartner', [
            'dashboardData' => $dashboardData
        ]);
    }
}
