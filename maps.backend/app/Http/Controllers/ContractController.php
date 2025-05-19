<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Contract;
use App\Models\BusStop;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ContractController extends Controller
{
    // 1. Загрузить файл
    public function upload(Request $request, $stopId)
    {
        $request->validate([
            'contract' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $stop = BusStop::findOrFail($stopId);

        $file = $request->file('contract');
        $uuidName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('contracts', $uuidName);

        $contract = Contract::create([
            'busstation_id'    => $stop->ogc_fid,
            'file_path'      => $filePath,
            'original_name'  => $file->getClientOriginalName(),
            'mime_type'      => $file->getClientMimeType(),
            'size'           => $file->getSize(),
        ]);

        return response()->json($contract, 201);
    }

    // 2. Получить список всех договоров для остановки
    public function index($stopId)
    {
        $stop = BusStop::findOrFail($stopId);
        return response()->json($stop->contracts);
    }

    // 3. Скачать файл
    public function download($id)
    {
        $contract = Contract::findOrFail($id);
        if (!Storage::exists($contract->file_path)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        return Storage::download($contract->file_path, $contract->original_name);
    }

    // 4. Удалить файл и запись
    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        Storage::delete($contract->file_path);
        $contract->delete();

        return response()->json(['message' => 'Contract deleted successfully.']);
    }
}
