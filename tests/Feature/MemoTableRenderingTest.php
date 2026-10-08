<?php

namespace Tests\Feature;

use App\Models\{Memo, MemoFieldValue, User};
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class MemoTableRenderingTest extends TestCase
{
    public function test_memo_rows_render_recipients_and_each_status(): void
    {
        $template = file_get_contents(resource_path('views/memos/my.blade.php'));
        $start = strpos($template, '@foreach($memos as $memo)');
        $end = strpos($template, '@endforeach', $start) + strlen('@endforeach');
        $rowTemplate = substr($template, $start, $end - $start);

        foreach (['draft', 'pending', 'approved', 'rejected'] as $status) {
            $memo = new Memo(['memo_number' => 'TEST-001', 'subject' => 'Example', 'status' => $status]);
            $memo->id = 1;
            $memo->created_at = now();
            $memo->setRelation('template', null);
            $memo->setRelation('creator', new User(['name' => 'Memo Creator']));
            $memo->setRelation('fieldValues', collect([
                new MemoFieldValue(['field_name' => 'to', 'field_value' => "Recipient One\nRecipient Two"]),
            ]));

            $html = Blade::render($rowTemplate, ['memos' => collect([$memo]), 'isAdmin' => false]);

            $this->assertStringContainsString("<div class=\"recipient-cell\">Recipient One\nRecipient Two</div>", $html);
            $this->assertStringContainsString('Memo Creator', $html);
            $this->assertStringContainsString('status-badge '.$status, $html);
        }
    }
}
