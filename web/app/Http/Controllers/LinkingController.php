<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LinkingController extends Controller
{
    public function __invoke(Request $request, string $linkingToken)
    {
        $request->validate([
            'pushToken' => 'required|string',
        ]);

        try {
            $decrypted = Crypt::decrypt($linkingToken);
        } catch (DecryptException $e) {
            return response()->json([
                'message' => 'Érvénytelen összekapcsolási token.',
            ], 400);
        }

        $team = Team::findOrFail($decrypted['team_id']);

        $team->push_tokens = array_unique(array_merge($team->push_tokens, [$request->input('pushToken')]));

        $team->save();

        return response()->json([
            'message' => 'Alkalmazás sikeresen összekapcsolva!',
        ]);
    }
}
