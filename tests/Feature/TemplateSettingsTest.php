<?php

namespace Tests\Feature;

use App\Models\{ApprovalWorkflow, Department, Memo, MemoTemplate, Role, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function template(): MemoTemplate
    {
        $role = Role::create(['role_name' => 'Admin', 'status' => true]);
        $this->actingAs(User::factory()->create(['username' => 'template-admin', 'role_id' => $role->id]));
        $department = Department::create(['department_name' => 'Finance', 'department_code' => 'FIN', 'status' => true]);
        return MemoTemplate::create(['department_id' => $department->id, 'template_name' => 'Payment', 'template_code' => 'PAY']);
    }

    public function test_admin_can_list_rename_and_delete_an_unused_template(): void
    {
        $template = $this->template();
        $workflow = ApprovalWorkflow::create(['template_id' => $template->id, 'workflow_name' => 'Payment approval', 'approval_type' => 'sequential', 'approval_rule' => 'all']);
        $this->get(route('admin.roles.index', ['tab' => 'templates']))->assertOk()->assertSee('Payment')->assertSee('Save name');
        $this->from(route('admin.roles.index', ['tab' => 'templates']))
            ->put(route('admin.templates.update', $template), ['template_name' => ''])->assertSessionHasErrors('template_name');
        $this->put(route('admin.templates.update', $template), ['template_name' => 'Supplier payment'])
            ->assertRedirect(route('admin.roles.index', ['tab' => 'templates']));
        $this->assertSame('Supplier payment', $template->fresh()->template_name);
        $this->delete(route('admin.templates.destroy', $template))->assertSessionHasNoErrors();
        $this->assertModelMissing($template);
        $this->assertModelMissing($workflow);
    }

    public function test_used_templates_and_their_memos_are_preserved(): void
    {
        $template = $this->template();
        $memo = Memo::create(['template_id' => $template->id, 'department_id' => $template->department_id,
            'created_by' => auth()->id(), 'memo_number' => 'TEST-001', 'subject' => 'Payment request', 'status' => 'draft']);
        $this->delete(route('admin.templates.destroy', $template))->assertSessionHasErrors('template');
        $this->assertModelExists($template);
        $this->assertSame($template->id, $memo->fresh()->template_id);
    }

    public function test_non_admins_cannot_rename_or_delete_templates(): void
    {
        $template = $this->template();
        $role = Role::create(['role_name' => 'Staff', 'status' => true]);
        $this->actingAs(User::factory()->create(['username' => 'template-staff', 'role_id' => $role->id]));
        $this->put(route('admin.templates.update', $template), ['template_name' => 'Changed'])->assertForbidden();
        $this->delete(route('admin.templates.destroy', $template))->assertForbidden();
        $this->assertSame('Payment', $template->fresh()->template_name);
    }

    public function test_templates_are_filtered_and_paginated_eight_per_page(): void
    {
        $template = $this->template();
        for ($index = 1; $index <= 9; $index++) {
            MemoTemplate::create(['department_id' => $template->department_id,
                'template_name' => 'Invoice '.$index, 'template_code' => 'INV-'.$index]);
        }
        $otherDepartment = Department::create(['department_name' => 'IT', 'department_code' => 'IT', 'status' => true]);
        MemoTemplate::create(['department_id' => $otherDepartment->id, 'template_name' => 'Invoice IT', 'template_code' => 'IT-INV']);

        $filters = ['tab' => 'templates', 'template_search' => 'Invoice', 'department_id' => $template->department_id];
        $response = $this->get(route('admin.roles.index', $filters))->assertOk()
            ->assertSee('js/memo-live-search.js?v=', false);
        $page = $response->viewData('templates');
        $this->assertSame(9, $page->total());
        $this->assertCount(8, $page->items());
        $this->assertStringContainsString('template_search=Invoice', $page->nextPageUrl());
        $this->assertStringContainsString('department_id='.$template->department_id, $page->nextPageUrl());
        $this->assertStringContainsString('tab=templates', $page->nextPageUrl());

        $secondPage = $this->get($page->nextPageUrl())->assertOk()->viewData('templates');
        $this->assertCount(1, $secondPage->items());
        $this->assertSame('Invoice 9', $secondPage->first()->template_name);

        $codeSearch = $this->get(route('admin.roles.index', ['tab' => 'templates', 'template_search' => 'IT-INV']))
            ->assertOk()->viewData('templates');
        $this->assertSame(1, $codeSearch->total());
        $this->assertSame('Invoice IT', $codeSearch->first()->template_name);
        $this->get(route('admin.roles.index', array_merge($filters, ['template_search' => 'Missing'])))
            ->assertOk()->assertSee('No templates found.');
    }
}
