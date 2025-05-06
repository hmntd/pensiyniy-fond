<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        $paths = [];

        foreach ($request->file('files', []) as $file) {
            $paths[] = $file->store('documents', 'public');
        }

        Document::create([
            'client_id' => $request->client_id,
            'document_path' => json_encode($paths),
            'document_type_id' => $request->type === 'passport' ? 1 : 2,
        ]);

        return response()->json([
            'message' => 'Files uploaded successfully.',
            'paths' => $paths,
        ]);
    }
}
