<?php

namespace App\Http\Controllers;

use App\Models\StudentSession;
use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Ambil StudentSession dari browser session
     */
    protected function getStudent(Request $request): ?StudentSession
    {
        $id = $request->session()->get('student_session_id');
        if (!$id) return null;

        return StudentSession::find($id);
    }
}
