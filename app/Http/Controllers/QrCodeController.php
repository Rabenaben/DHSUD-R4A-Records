<?php

namespace App\Http\Controllers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\HoaDatabase;
use App\Models\RemDatabase;

class QrCodeController extends Controller
{
    private function buildProvinceCode(string $name): string
    {
        $clean = strtoupper($name);
        $clean = preg_replace('/\b(PROVINCE OF|PROVINCE)\b/', '', $clean);
        $clean = preg_replace('/[^A-Z0-9\s]/', '', $clean);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));
        if ($clean === '') {
            return '';
        }
        return substr($clean, 0, 3);
    }

    private function buildMunicipalityCode(string $name): string
    {
        $clean = strtoupper($name);
        $clean = preg_replace('/\b(CITY OF|MUNICIPALITY OF|CITY|MUNICIPALITY)\b/', '', $clean);
        $clean = preg_replace('/[^A-Z0-9\s]/', '', $clean);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));
        if ($clean === '') {
            return '';
        }

        $words = preg_split('/\s+/', $clean);
        $initials = '';
        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }
            $initials .= substr($word, 0, 1);
        }

        if (strlen($initials) >= 2) {
            return $initials;
        }

        return substr($clean, 0, 2);
    }

    public function show(string $type, string $docketNo): Response
    {
        $normalizedType = strtolower($type);
        if (!in_array($normalizedType, ['hoa', 'rem'], true)) {
            abort(404);
        }

        $recordId = request()->query('record_id');
        $provinceId = request()->query('province_id');
        $municipalityId = request()->query('municipality_id');

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

        $provinceName = $record?->province?->province_name ?? '';
        $municipalityName = $record?->municipality?->municipality_name ?? '';
        $classification = $normalizedType === 'hoa' ? ($record?->classification ?? '') : '';
        $resolvedRecordId = $record?->id ?? $recordId;
        $labelProvince = $provinceName !== '' ? $provinceName : 'N/A';
        $labelMunicipality = $municipalityName !== '' ? $municipalityName : 'N/A';
        if ($normalizedType === 'hoa' && $classification !== '') {
            $labelText = $classification . ' - ' . $labelProvince . ' - ' . $labelMunicipality . ' - ' . $docketNo;
        } else {
            $labelText = strtoupper($normalizedType) . ' - ' . $labelProvince . ' - ' . $labelMunicipality . ' - ' . $docketNo;
        }

        $provinceCode = $this->buildProvinceCode($provinceName);
        $municipalityCode = $this->buildMunicipalityCode($municipalityName);
        $normalizedDocket = strtoupper(trim($docketNo));
        $prefix = $provinceCode && $municipalityCode ? $provinceCode . '-' . $municipalityCode : '';
        $typePrefix = strtoupper($normalizedType);
        $classificationSlug = '';
        if ($classification !== '') {
            $classificationSlug = strtoupper(trim($classification));
            $classificationSlug = preg_replace('/\s+/', '-', $classificationSlug);
            $classificationSlug = preg_replace('/[^A-Z0-9-]+/', '', $classificationSlug);
            $classificationSlug = trim(preg_replace('/-+/', '-', $classificationSlug), '-');
        }
        if ($prefix !== '' && str_starts_with($normalizedDocket, $prefix)) {
            $downloadBase = $normalizedDocket;
        } elseif ($prefix !== '') {
            $downloadBase = $prefix . $normalizedDocket;
        } else {
            $downloadBase = $normalizedDocket;
        }

        if ($normalizedType === 'hoa') {
            if ($classificationSlug !== '') {
                $downloadBase = $classificationSlug . '-' . ltrim($downloadBase, '-');
            }
        } else {
            $downloadBase = $typePrefix . '-' . ltrim($downloadBase, '-');
        }

        $query = [];
        if ($resolvedRecordId) {
            $query['record_id'] = $resolvedRecordId;
        }
        if ($record?->province_id ?? $provinceId) {
            $query['province_id'] = $record?->province_id ?? $provinceId;
        }
        if ($record?->municipality_id ?? $municipalityId) {
            $query['municipality_id'] = $record?->municipality_id ?? $municipalityId;
        }
        if ($provinceName !== '') {
            $query['province'] = $provinceName;
        }
        if ($municipalityName !== '') {
            $query['municipality'] = $municipalityName;
        }

        $payload = url('/qr/open/' . $normalizedType . '/' . rawurlencode($docketNo));
        if (!empty($query)) {
            $payload .= '?' . http_build_query($query);
        }

        $safeDocket = preg_replace('/[^A-Za-z0-9_-]+/', '_', $docketNo);
        if (!$safeDocket) {
            $safeDocket = sha1($docketNo);
        }
        $safeRecordId = $resolvedRecordId ? preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) $resolvedRecordId) : '';
        $safeProvince = $provinceName !== '' ? preg_replace('/[^A-Za-z0-9_-]+/', '_', $provinceName) : '';
        $safeMunicipality = $municipalityName !== '' ? preg_replace('/[^A-Za-z0-9_-]+/', '_', $municipalityName) : '';
        $suffixParts = array_filter([$safeRecordId ? 'id' . $safeRecordId : '', $safeProvince, $safeMunicipality]);
        $suffix = $suffixParts ? '_' . implode('_', $suffixParts) : '';
        $safeDownloadBase = preg_replace('/[^A-Za-z0-9_-]+/', '_', $downloadBase);

        $disk = Storage::disk('public');
        $dir = 'qr/' . $normalizedType;
        $relativePath = $dir . '/' . $safeDocket . $suffix . '.png';

        if (!$disk->exists($relativePath)) {
            $disk->makeDirectory($dir);

            $result = new Builder(
                writer: new PngWriter(),
                data: $payload,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,

                margin: 10,

                roundBlockSizeMode: RoundBlockSizeMode::None,

                labelText: $labelText,
                labelFont: new OpenSans(10),
                labelAlignment: LabelAlignment::Center,

                labelMargin: new Margin(0, 1, 5, 1)
            );

            $built = $result->build();
            $disk->put($relativePath, $built->getString());
        }

        $fileContents = $disk->get($relativePath);

        return response($fileContents, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . ($safeDownloadBase ?: ('qr-' . $normalizedType . '-' . $safeDocket)) . '.png"',
        ]);
    }
}
