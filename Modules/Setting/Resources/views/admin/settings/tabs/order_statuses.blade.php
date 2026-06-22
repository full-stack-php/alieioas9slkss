<div class="row">
    <div class="col-md-12">
        {{ Form::select('default_order_status', trans('setting::attributes.default_order_status'), $errors, $order_statuses, $settings, ['required' => true]) }}
    </div>
    <div class="col-md-12">
        {{ Form::select('pending_order_status', trans('setting::attributes.pending_order_status'), $errors, $order_statuses, $settings, ['required' => true]) }}
    </div>
    <div class="col-md-12">
        {{ Form::select('pending_payment_order_status', trans('setting::attributes.pending_payment_order_status'), $errors, $order_statuses, $settings, ['required' => true]) }}
    </div>
    <div class="col-md-12">
        {{ Form::select('complete_payment_order_status', trans('setting::attributes.complete_payment_order_status'), $errors, $order_statuses, $settings, ['required' => true]) }}
    </div>
    <div class="col-md-12">
        {{ Form::select('completed_order_status', trans('setting::attributes.completed_order_status'), $errors, $order_statuses, $settings, ['required' => true]) }}
    </div>
    <div class="col-md-12">
        {{ Form::select('canceled_order_status', trans('setting::attributes.canceled_order_status'), $errors, $order_statuses, $settings, ['required' => true]) }}
    </div>
    <div class="col-md-12">
        {{ Form::select('refunded_order_status', trans('setting::attributes.refunded_order_status'), $errors, $order_statuses, $settings, ['required' => true]) }}
    </div>
</div>
