<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $widget = [
            'users' => User::where('user_role', 'users')->count(),
        ];

        // Fetch upcoming events
        $events = Event::where('tanggal', '>=', Carbon::now())->paginate(10);

        // Calculate total revenue across all events
        $totalRevenue = EventRegistration::join('events', 'event_registrations.event_id', '=', 'events.id')
            ->sum('events.harga');

        // Calculate total people registered across all events
        $totalRegistrations = EventRegistration::count();


        // Fetch all events and calculate total revenue for each
        $allEvents = Event::with('registrations')->get()->map(function($event) {
            $event->total_registrations = $event->registrations->count();
            $event->total_revenue = $event->total_registrations * $event->harga;
            return $event;
        });

        $totalEvent = $allEvents->count();



        return view('home', compact('widget', 'events', 'totalRevenue', 'allEvents', 'totalEvent', 'totalRegistrations'));
    }



}
