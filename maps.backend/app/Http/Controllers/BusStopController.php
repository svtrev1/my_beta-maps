<?php

namespace App\Http\Controllers;
use App\Models\BusStop;
use DB;

use Illuminate\Http\Request;


class BusStopController extends Controller
{
    public function store(Request $request)
    {


        $data = $request->validate([
            'name' => 'required|string|max:74',
            'street' => 'required|string|max:100',
            'type' => 'required|numeric',
            'coordinates' => 'required|array|min:2|max:2',
            'coordinates.0' => 'required|numeric', 
            'coordinates.1' => 'required|numeric', 

            'year'         => 'nullable|integer|min:1900|max:2099',
            'financing'    => 'nullable|string|max:255',
            'numbus'       => 'nullable|string|max:255',
            'numtaxi'      => 'nullable|string|max:255',
            'comments'     => 'nullable|string|max:1000',
        ]);

        DB::table('busstation')->insert([
            'name'         => $data['name'],
            'street'       => $data['street'],
            'type'         => $data['type'],
            'wkb_geometry' => DB::raw(sprintf(
                'ST_SetSRID(ST_Point(%F, %F), 4326)',
                $data['coordinates'][0],
                $data['coordinates'][1]
            )),
            'year'         => $data['year']      ?? null,
            'financing'    => $data['financing'] ?? null,
            'numbus'       => $data['numbus']    ?? null,
            'numtaxi'      => $data['numtaxi']   ?? null,
            'comments'     => $data['comments']  ?? null,
        ]);
        
        return response()->json(['message' => 'Bus stop added successfully']);
    }

    public function destroy($id)
    {
        try {
            \Log::info('Удаляем остановку с ogc_fid: ' . $id);
            $busStop = BusStop::findOrFail($id);
            $busStop->delete();

            return response()->json(['message' => 'Bus stop deleted successfully']);
        } catch (\Throwable $e) {
            \Log::error('Ошибка при удалении: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка сервера при удалении'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:74',
                'street' => 'required|string|max:100',
                'type' => 'required|numeric',
                'year' => 'nullable|integer|min:1900|max:2099',
                'financing' => 'nullable|string|max:255',
                'numbus' => 'nullable|string|max:255',
                'numtaxi' => 'nullable|string|max:255',
                'comments' => 'nullable|string|max:1000',
            ]);

            $busStop = BusStop::findOrFail($id);
            $busStop->update($data);
            return response()->json(['message' => 'Остановка успешно обновлена']);
        } catch (\Throwable $e) {
            \Log::error('Ошибка обновления: '.$e->getMessage());
            return response()->json(['error' => 'Ошибка сервера'], 500);
        }
    }
}

   
    