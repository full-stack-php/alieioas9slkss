<?php

namespace Modules\Contact\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Modules\Contact\Entities\ContactSubmission;
use Modules\Contact\Events\ContactSubmissionCreated;
use Modules\Contact\Http\Requests\StoreCallbackRequest;
use Modules\Contact\Http\Requests\StoreContactSubmissionRequest;
use Modules\Media\Entities\File;
use Modules\Page\Entities\Page;
use Illuminate\Support\Facades\Log;

class ContactController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('storefront::public.contact.create', ['contactData' => $this->getContactData(), 'contactBg' => $this->getMedia(setting('storefront_contact_bg'))->path,  'privacyPageUrl' => $this->getPrivacyPageUrl(),]);
    }


    private function getMedia($fileId)
    {
        return Cache::rememberForever(md5("files.{$fileId}"), function () use ($fileId) {
            return File::findOrNew($fileId);
        });
    }

    private function getPrivacyPageUrl()
    {
        return Cache::tags('settings')->rememberForever('privacy_page_url', function () {
            return Page::urlForPage(setting('storefront_privacy_page'));
        });
    }
    private function getContactData()
    {
        return [
            'phone_1' => setting('store_phone')?? false,
            'phone_2' => setting('store_phone2')?? false,
            'phone_3' => setting('store_phone3')?? false,
            'openTime' => setting('storefront_opentime')?? false,
            'addressHeader' => setting('storefront_address')?? false,
            'showCallBackForm' => setting('storefront_show_callback_btn')?? false,
            'facebook' => setting('storefront_facebook_link')?? false,
            'viber' => setting('storefront_viber_link')?? false,
            'telegram' => setting('storefront_telegram_link')?? false,
            'whatsapp' => setting('storefront_whatsapp_link')?? false,
            'footer_open_time' => setting('storefront_footer_open_time')?? false,
            'footer_address' => setting('storefront_footer_address')?? false,
            'store_email' => setting('store_email')?? false,
        ];

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(StoreContactSubmissionRequest $request)
    {
        $submission = ContactSubmission::create([
            'type' => 'contact',

            'name' => $request->input('name'),
            'phone' => $request->input('phone'),

            'source_url' => $request->input('source_url', url()->previous()),

            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        event(new ContactSubmissionCreated($submission));

        return back()->with('success', trans('contact::messages.your_message_has_been_sent'));
    }

    public function callbackModal()
    {
        if (!setting('storefront_show_callback_btn')) {
            abort(404);
        }

        return view('storefront::public.partials.callback-modal', [
            'privacyPageUrl' => $this->getPrivacyPageUrl(),
        ]);
    }

    public function callback(StoreCallbackRequest $request)
    {
        if (!setting('storefront_show_callback_btn')) {
            abort(404);
        }

        $submission = ContactSubmission::create([
            'type' => 'callback',

            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email_buyer'),

            'message' => $request->input('comment_buyer'),
            'topic' => $request->input('topic_callback_send'),

            'preferred_call_at' => $request->input('time_callback_on'),

            'source_url' => $request->input('url_site'),

            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        event(new ContactSubmissionCreated($submission));

        return response()->json([
            'success' => trans('contact::messages.your_callback_has_been_sent'),
        ]);

    }

}
