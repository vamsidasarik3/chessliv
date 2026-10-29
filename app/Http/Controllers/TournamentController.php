<?php

namespace App\Http\Controllers;

use App\Models\Tournament;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::latest()->get();
        $ongoing = $tournaments->where('status', 'ongoing');
        $upcoming = $tournaments->where('status', 'upcoming');
        $completed = $tournaments->where('status', 'completed');

        return view('tournaments.index', compact('ongoing', 'upcoming', 'completed'));
    }
}
