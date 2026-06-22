<?php

namespace Modules\Contact\Admin;

use Illuminate\Http\JsonResponse;
use Modules\Admin\Ui\AdminTable;
use Modules\Contact\Entities\ContactSubmission;

class ContactSubmissionTable extends AdminTable
{
    protected array $rawColumns = [
        'type',
        'customer',
        'contacts',
        'source_url',
        'read_status',
        'processed_status',
    ];

    public function make()
    {
        return $this->newTable()
            ->editColumn('type', function (ContactSubmission $submission) {
                if ($submission->type === 'callback') {
                    return '<span class="badge badge-soft-primary rounded-pill me-1">'
                        . trans('contact::contact_submissions.short_types.callback') .
                        '</span>';
                }

                if ($submission->type === 'contact') {
                    return '<span class="badge badge-soft-info rounded-pill me-1">'
                        . trans('contact::contact_submissions.short_types.contact') .
                        '</span>';
                }

                return '<span class="badge badge-soft-secondary rounded-pill me-1">'
                    . e($submission->type ?: trans('contact::contact_submissions.types.unknown')) .
                    '</span>';
            })
            ->addColumn('customer', function (ContactSubmission $submission) {
                return e($submission->name ?: trans('contact::contact_submissions.empty'));
            })
            ->addColumn('contacts', function (ContactSubmission $submission) {
                $items = [];

                if ($submission->phone) {
                    $items[] = '<div><strong>'
                        . trans('contact::contact_submissions.fields.phone')
                        . ':</strong> ' . e($submission->phone) . '</div>';
                }

                if ($submission->email) {
                    $items[] = '<div><strong>'
                        . trans('contact::contact_submissions.fields.email')
                        . ':</strong> ' . e($submission->email) . '</div>';
                }

                return $items ? implode('', $items) : trans('contact::contact_submissions.empty');
            })
            ->editColumn('source_url', function (ContactSubmission $submission) {
                if (!$submission->source_url) {
                    return trans('contact::contact_submissions.empty');
                }

                return '<a href="' . e($submission->source_url) . '" target="_blank" rel="noopener noreferrer">'
                    . trans('contact::contact_submissions.buttons.open_page') .
                    '</a>';
            })
            ->addColumn('read_status', function (ContactSubmission $submission) {
                return $submission->is_read
                    ? '<span class="badge badge-soft-success rounded-pill me-1">'
                    . trans('contact::contact_submissions.statuses.read') .
                    '</span>'
                    : '<span class="badge badge-soft-warning rounded-pill me-1">'
                    . trans('contact::contact_submissions.statuses.new') .
                    '</span>';
            })
            ->addColumn('processed_status', function (ContactSubmission $submission) {
                return $submission->is_processed
                    ? '<span class="badge badge-soft-success rounded-pill me-1">'
                    . trans('contact::contact_submissions.statuses.processed') .
                    '</span>'
                    : '<span class="badge badge-soft-danger rounded-pill me-1">'
                    . trans('contact::contact_submissions.statuses.unprocessed') .
                    '</span>';
            });
    }
}
