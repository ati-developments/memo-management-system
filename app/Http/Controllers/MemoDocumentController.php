<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\MemoAttachment;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemoDocumentController extends Controller
{
    // The authenticated organization-wide register already exposes all memos.
    public function show(Memo $memo)
    {
        $this->authorizeView($memo);
        $memo->load('attachments');
        return view('memos.show', compact('memo'));
    }

    public function downloadAttachment(Memo $memo, MemoAttachment $attachment)
    {
        $this->authorizeView($memo);
        abort_unless((int) $attachment->memo_id === (int) $memo->id, 404);
        abort_unless(Storage::disk('local')->exists($attachment->storage_path), 404);

        return Storage::disk('local')->download($attachment->storage_path, $attachment->original_name);
    }

    public function pdf(Request $request, Memo $memo)
    {
        $this->authorizeView($memo);
        $memo->load(['department', 'creator', 'fieldValues.templateField', 'tableRows.templateTable.columns', 'approvals.approver', 'approvals.approvalStep', 'attachments']);
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
        $memo->load(['template.fields', 'template.tables.columns', 'fieldValues', 'tableRows', 'attachments']);
        abort_unless($memo->template, 422, 'This memo no longer has a template.');
        $values = $memo->fieldValues->pluck('field_value', 'field_name');

        $approvalWorkflow = \App\Models\ApprovalWorkflow::with('steps.approver')->where('template_id', $memo->template_id)->where('is_active', true)->first();
        $workflowUsers = \App\Models\User::orderBy('name')->get(['id', 'name', 'designation']);
        return view('memos.edit', compact('memo', 'values', 'approvalWorkflow', 'workflowUsers'));
    }

    public function update(Request $request, Memo $memo)
    {
        $this->authorizeEdit($memo);

        return app(MemoController::class)->saveMemo($request, $memo);
    }

    public function destroy(Memo $memo)
    {
        $attachmentPaths = $memo->attachments()->pluck('storage_path');
        $signaturePaths = $memo->approvals()->pluck('signature_path')->filter();

        foreach ($attachmentPaths as $path) {
            Storage::disk('local')->delete($path);
        }
        foreach ($signaturePaths as $path) {
            Storage::disk('public')->delete($path);
        }

        $memo->delete();

        return to_route('memos.my')->with('success', 'Memo ' . $memo->memo_number . ' deleted.');
    }

    private function authorizeEdit(Memo $memo): void
    {
        $isAdmin = in_array(strtolower((string) auth()->user()?->role?->role_name), ['admin', 'administrator'], true);
        if ($isAdmin) {
            return;
        }
        abort_unless((int) $memo->created_by === (int) auth()->id(), 403);
        abort_unless($memo->status === 'draft', 403, 'Only draft memos can be edited.');
    }

    private function authorizeView(Memo $memo): void
    {
        $user = auth()->user();
        $isAdmin = in_array(strtolower((string) $user?->role?->role_name), ['admin', 'administrator'], true);
        $isCreator = (int) $memo->created_by === (int) $user?->id;
        $isApprover = $memo->approvals()->where('approver_id', $user?->id)->exists();

        abort_unless($isAdmin || $isCreator || $isApprover, 403);
    }
}
