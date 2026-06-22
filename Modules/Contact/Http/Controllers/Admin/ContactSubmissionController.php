<?php

namespace Modules\Contact\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Contact\Entities\ContactSubmission;

class ContactSubmissionController
{
    public function index(Request $request)
    {
        return view('contact::admin.contact_submissions.index', [
            'types' => [
                '' => trans('contact::contact_submissions.filters.all_types'),
                'callback' => trans('contact::contact_submissions.types.callback'),
                'contact' => trans('contact::contact_submissions.types.contact'),
            ],

            'readStatuses' => [
                '' => trans('contact::contact_submissions.filters.all_read_statuses'),
                '0' => trans('contact::contact_submissions.filters.new'),
                '1' => trans('contact::contact_submissions.filters.read'),
            ],

            'processedStatuses' => [
                '' => trans('contact::contact_submissions.filters.all_processed_statuses'),
                '0' => trans('contact::contact_submissions.filters.unprocessed'),
                '1' => trans('contact::contact_submissions.filters.processed'),
            ],

            'selectedFilters' => [
                'type' => $request->query('type'),
                'read' => $request->query('read'),
                'processed' => $request->query('processed'),
            ],
        ]);
    }

    public function table(Request $request)
    {
        return (new ContactSubmission())->table($request);
    }

    public function show($id)
    {
        $contactSubmission = ContactSubmission::with('processor')->findOrFail($id);

        $contactSubmission->markAsRead();

        return view('contact::admin.contact_submissions.show', compact('contactSubmission'));
    }

    public function markAsProcessed(Request $request, $id)
    {
        $contactSubmission = ContactSubmission::findOrFail($id);

        $contactSubmission->markAsProcessed();

        return $this->redirectToIndexWithFilters($request)
            ->withSuccess(trans('contact::contact_submissions.messages.marked_as_processed'));
    }

    public function markAsUnprocessed(Request $request, $id)
    {
        $contactSubmission = ContactSubmission::findOrFail($id);

        $contactSubmission->markAsUnprocessed();

        return $this->redirectToIndexWithFilters($request)
            ->withSuccess(trans('contact::contact_submissions.messages.marked_as_unprocessed'));
    }

    private function redirectToIndexWithFilters(Request $request)
    {
        return redirect()->route('admin.contact_submissions.index', array_filter([
            'type' => $request->input('type'),
            'read' => $request->input('read'),
            'processed' => $request->input('processed'),
        ], function ($value) {
            return $value !== null && $value !== '';
        }));
    }

    public function destroy(string $ids): void
    {
        ContactSubmission::whereIn('id', explode(',', $ids))->delete();
    }
}
