<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HoaDatabase;
use App\Models\RemDatabase;

class ArchiveController extends Controller
{
    // 🔹 Archive Record
    public function archiveRecord($type, $id)
    {
        if ($type === 'hoa') {
            $record = HoaDatabase::find($id);
            if ($record) {
                $record->update([
                    'previous_status' => $record->status,
                    'status' => 'ARCHIVED'
                ]);
            }
        } elseif ($type === 'rem') {
            $record = RemDatabase::find($id);
            if ($record) {
                $record->update([
                    'previous_status' => $record->status,
                    'status' => 'ARCHIVED'
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // 🔹 Unarchive Record
    public function unarchiveRecord($type, $id)
    {
        if ($type === 'hoa') {
            HoaDatabase::where('id', $id)->update(['status' => 'ON-SHELF']);
        } elseif ($type === 'rem') {
            RemDatabase::where('id', $id)->update(['status' => 'ON-SHELF']);
        }

        return response()->json(['success' => true]);
    }
}
