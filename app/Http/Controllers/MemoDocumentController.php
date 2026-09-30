<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemoDocumentController extends Controller
{
    // The authenticated organization-wide register already exposes all memos.
    public function show(Memo $memo)
    {
        return view('memos.show', compact('memo'));
    }

    public function pdf(Request $request, Memo $memo)
    {
        $memo->load(['department', 'creator', 'fieldValues.templateField', 'tableRows.templateTable.columns', 'approvals.approver', 'approvals.approvalStep']);
        $values = $memo->fieldValues->pluck('field_value', 'field_name');
        $approvals = $memo->approvals->sortBy('chain_order');
        $signatureImages = [];
        foreach ($approvals->where('action', 'approved') as $approval) {
            $path = $approval->signature_path;
            if (!$path || !Storage::disk('public')->exists($path)) {
                continue;
            }
            // Embed the signature recorded at approval time; no HTTP request or
            // public storage symlink is needed by the PDF renderer.
            $bytes = Storage::disk('public')->get($path);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
            if (in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/webp', 'image/bmp'], true)) {
                $signatureImages[$approval->id] = 'data:' . $mime . ';base64,' . base64_encode($bytes);
            }
        }
        $logoPath = public_path('storage/images/Amana-Takaful-logo.jpeg');
        $brandImage = is_file($logoPath)
            ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
            : null;
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isPhpEnabled', false);
        $options->set('isJavascriptEnabled', false);
        $pdf = new Dompdf($options);
        $pdf->setPaper('A4');
        $pdf->loadHtml(view('memos.pdf', compact('memo', 'values', 'approvals', 'signatureImages', 'brandImage'))->render());
        $pdf->render();
        $filename = preg_replace('/[^A-Za-z0-9_-]/', '-', $memo->memo_number) . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($request->boolean('download') ? 'attachment' : 'inline') . '; filename="' . $filename . '"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function edit(Memo $memo)
    {
        $this->authorizeEdit($memo);
        $memo->load(['template.fields', 'template.tables.columns', 'fieldValues', 'tableRows']);
        abort_unless($memo->template, 422, 'This memo no longer has a template.');
        $values = $memo->fieldValues->pluck('field_value', 'field_name');

        return view('memos.edit', compact('memo', 'values'));
    }

    public function update(Request $request, Memo $memo)
    {
        $this->authorizeEdit($memo);

        return app(MemoController::class)->saveMemo($request, $memo);
    }

    private function authorizeEdit(Memo $memo): void
    {
        abort_unless((int) $memo->created_by === (int) auth()->id(), 403);
        abort_unless($memo->status === 'draft', 403, 'Only draft memos can be edited.');
    }
}
