<?php

namespace App\Http\Controllers;

use App\Models\HoaDatabase;
use App\Models\RemDatabase;

class QrScanController extends Controller
{
    public function index()
    {
        return view('qr.scan');
    }

    public function info(string $type, string $docketNo)
    {
        return $this->open($type, $docketNo);
    }

    public function open(string $type, string $docketNo)
    {
        $normalizedType = strtolower($type);

        if (!in_array($normalizedType, ['hoa', 'rem'], true)) {
            abort(404);
        }

        $target = $normalizedType === 'hoa' ? route('hoa_records') : route('rem_records');
        $params = [
            'qr' => $docketNo,
            'type' => $normalizedType,
        ];

        if (request()->filled('record_id')) {
            $params['record_id'] = request()->query('record_id');
        }
        if (request()->filled('province_id')) {
            $params['province_id'] = request()->query('province_id');
        }
        if (request()->filled('municipality_id')) {
            $params['municipality_id'] = request()->query('municipality_id');
        }

        return redirect()->to($target . '?' . http_build_query($params));
    }

    public function record(string $type, string $docketNo)
    {
        $normalizedType = strtolower($type);
        $recordId = request()->query('record_id');
        $provinceId = request()->query('province_id');
        $municipalityId = request()->query('municipality_id');

        if (!in_array($normalizedType, ['hoa', 'rem'], true)) {
            abort(404);
        }

        $query = $normalizedType === 'hoa'
            ? HoaDatabase::with(['province', 'municipality'])
            : RemDatabase::with(['province', 'municipality']);

        if (!empty($recordId)) {
            $record = $query->where('id', $recordId)->first();
        } else {
            if (!empty($provinceId)) {
                $query->where('province_id', $provinceId);
            }
            if (!empty($municipalityId)) {
                $query->where('municipality_id', $municipalityId);
            }
            $record = $query->where('docket_no', $docketNo)->first();
        }

        if (!$record) {
            return response()->json(['message' => 'Docket not found'], 404);
        }

        return response()->json($record);
    }
}