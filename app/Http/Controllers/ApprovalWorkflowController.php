<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use App\Models\MemoTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ApprovalWorkflowController extends Controller
{
    /**
     * Show the approval workflow configuration page.
     */
    public function edit(MemoTemplate $template)
    {
        $template->load('department');

        $workflow = ApprovalWorkflow::with([
            'steps.approver'
        ])
            ->where('template_id', $template->id)
            ->where('is_active', true)
            ->first();

        $users = User::orderBy('name')
            ->get();

        $usersJson = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'designation' => $user->designation,
            ];
        })->values();

        return view(
            'approval_workflows.edit',
            compact(
                'template',
                'workflow',
                'users',
                'usersJson'
            )
        );
    }

    /**
     * Save or update the approval workflow.
     */
    public function update(
        Request $request,
        MemoTemplate $template
    ) {
        $validated = $request->validate([

            'workflow_name' => [
                'required',
                'string',
                'max:255',
            ],

            'approval_type' => [
                'required',
                Rule::in([
                    'sequential',
                    'open',
                ]),
            ],

            'approval_rule' => [
                'required',
                Rule::in([
                    'all',
                    'any',
                    'minimum',
                ]),
            ],

            'minimum_approvals' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'steps' => [
                'required',
                'array',
                'min:1',
            ],

            'steps.*.approver_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'steps.*.is_required' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
         * Minimum approvals is only meaningful
         * when the approval rule is "minimum".
         */
        if (
            $validated['approval_rule'] === 'minimum'
            && empty($validated['minimum_approvals'])
        ) {
            return back()
                ->withErrors([
                    'minimum_approvals' =>
                        'Please specify the minimum number of approvals.'
                ])
                ->withInput();
        }


        /*
         * If the rule is not minimum,
         * clear the minimum approvals value.
         */
        if ($validated['approval_rule'] !== 'minimum') {
            $validated['minimum_approvals'] = null;
        }


        /*
         * For an "any" workflow, minimum approvals
         * is also unnecessary.
         */
        if ($validated['approval_rule'] === 'any') {
            $validated['minimum_approvals'] = null;
        }


        /*
         * Make sure minimum approvals does not exceed
         * the number of configured approvers.
         */
        if (
            $validated['approval_rule'] === 'minimum'
            && $validated['minimum_approvals'] >
               count($validated['steps'])
        ) {
            return back()
                ->withErrors([
                    'minimum_approvals' =>
                        'Minimum approvals cannot be greater than the number of approvers.'
                ])
                ->withInput();
        }


        DB::transaction(function () use (
            $validated,
            $template
        ) {

            /*
             * Find existing active workflow.
             */
            $workflow = ApprovalWorkflow::where(
                'template_id',
                $template->id
            )
            ->where('is_active', true)
            ->first();


            /*
             * Create workflow if it doesn't exist.
             */
            if (!$workflow) {

                $workflow = ApprovalWorkflow::create([
                    'template_id' => $template->id,
                    'workflow_name' =>
                        $validated['workflow_name'],
                    'approval_type' =>
                        $validated['approval_type'],
                    'approval_rule' =>
                        $validated['approval_rule'],
                    'minimum_approvals' =>
                        $validated['minimum_approvals'] ?? null,
                    'is_active' => true,
                ]);

            } else {

                /*
                 * Update existing workflow.
                 */
                $workflow->update([
                    'workflow_name' =>
                        $validated['workflow_name'],
                    'approval_type' =>
                        $validated['approval_type'],
                    'approval_rule' =>
                        $validated['approval_rule'],
                    'minimum_approvals' =>
                        $validated['minimum_approvals'] ?? null,
                ]);
            }


            /*
             * Remove old approval steps.
             *
             * We recreate them using the current
             * configuration from the form.
             */
            $workflow->steps()->delete();


            /*
             * Create the new approval steps.
             */
            foreach ($validated['steps'] as $index => $step) {

                $workflow->steps()->create([
                    'approver_id' =>
                        $step['approver_id'],

                    'step_order' =>
                        $index + 1,

                    'is_required' =>
                        !empty($step['is_required']),
                ]);
            }
        });


        return redirect()
            ->route(
                'templates.approval-workflow.edit',
                $template->id
            )
            ->with(
                'success',
                'Approval workflow saved successfully.'
            );
    }
}