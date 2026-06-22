<?php

namespace Modules\Account\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Account\Entities\Address;
use Modules\Account\Entities\DefaultAddress;
use Modules\Account\Http\Requests\SaveAddressRequest;

class AccountAddressController extends Controller
{
    private const NP_ADDRESS_BRANCH = 1;
    private const NP_ADDRESS_ADDRESS = 2;
    private const NP_ADDRESS_POSTOMAT = 3;

    public function index()
    {
        $customer = auth()->user();

        $addresses = $customer->addresses()
            ->orderBy('created_at', 'desc')
            ->get();

        $addressesByType = collect([
            self::NP_ADDRESS_BRANCH => $addresses->where('np_address_type', self::NP_ADDRESS_BRANCH)->values(),
            self::NP_ADDRESS_ADDRESS => $addresses->where('np_address_type', self::NP_ADDRESS_ADDRESS)->values(),
            self::NP_ADDRESS_POSTOMAT => $addresses->where('np_address_type', self::NP_ADDRESS_POSTOMAT)->values(),
        ])->filter(fn ($items) => $items->isNotEmpty());

        $defaultAddresses = DefaultAddress::with('address')
            ->where('customer_id', auth()->id())
            ->get();

        $defaultAddressIds = $defaultAddresses
            ->filter(fn ($defaultAddress) => $defaultAddress->np_address_type)
            ->mapWithKeys(fn ($defaultAddress) => [
                (int) $defaultAddress->np_address_type => (int) $defaultAddress->address_id,
            ])
            ->toArray();

        $legacyDefaultAddress = $defaultAddresses->firstWhere('np_address_type', null);

        if (
            $legacyDefaultAddress
            && $legacyDefaultAddress->address
            && $legacyDefaultAddress->address->np_address_type
            && empty($defaultAddressIds[$legacyDefaultAddress->address->np_address_type])
        ) {
            $defaultAddressIds[(int) $legacyDefaultAddress->address->np_address_type] = (int) $legacyDefaultAddress->address_id;
        }

        return view('storefront::public.account.addresses.index', [
            'addressesByType' => $addressesByType,
            'defaultAddressIds' => $defaultAddressIds,
            'addressTypes' => $this->addressTypes(),
            'defaultCountry' => 'UA',
        ]);
    }

    public function store(SaveAddressRequest $request)
    {
        $address = auth()->user()
            ->addresses()
            ->create($this->addressData($request));

        if ($request->expectsJson()) {
            return response()->json([
                'address' => $address,
                'message' => trans('account::messages.address_created'),
            ]);
        }

        return back()->with('success', trans('account::messages.address_created'));
    }

    public function update(SaveAddressRequest $request, $id)
    {
        $address = auth()->user()
            ->addresses()
            ->findOrFail($id);

        $address->update($this->addressData($request));

        if ($request->expectsJson()) {
            return response()->json([
                'address' => $address,
                'message' => trans('account::messages.address_updated'),
            ]);
        }

        return back()->with('success', trans('account::messages.address_updated'));
    }

    public function destroy(Request $request, $id)
    {
        $address = auth()->user()
            ->addresses()
            ->findOrFail($id);

        DefaultAddress::where('customer_id', auth()->id())
            ->where('address_id', $address->id)
            ->delete();

        $address->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => trans('account::messages.address_deleted'),
            ]);
        }

        return back()->with('success', trans('account::messages.address_deleted'));
    }

    public function changeDefault(Request $request)
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'],
        ]);

        $address = auth()->user()
            ->addresses()
            ->findOrFail($data['address_id']);

        DefaultAddress::updateOrCreate(
            [
                'customer_id' => auth()->id(),
                'np_address_type' => $address->np_address_type,
            ],
            [
                'address_id' => $address->id,
            ]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => trans('account::messages.default_address_updated'),
            ]);
        }

        return back()->with('success', trans('account::messages.default_address_updated'));
    }

    private function addressData(SaveAddressRequest $request): array
    {
        return array_merge($request->validated(), [
            'country' => 'UA',
            'zip' => '0',
        ]);
    }

    private function addressTypes(): array
    {
        return [
            self::NP_ADDRESS_BRANCH => [
                'title' => trans('storefront::account.addresses.np_branch_addresses'),
                'description' => trans('storefront::account.addresses.np_branch_description'),
                'badge' => trans('storefront::account.addresses.np_branch'),
            ],

            self::NP_ADDRESS_ADDRESS => [
                'title' => trans('storefront::account.addresses.np_courier_addresses'),
                'description' => trans('storefront::account.addresses.np_courier_description'),
                'badge' => trans('storefront::account.addresses.np_courier'),
            ],

            self::NP_ADDRESS_POSTOMAT => [
                'title' => trans('storefront::account.addresses.np_postomat_addresses'),
                'description' => trans('storefront::account.addresses.np_postomat_description'),
                'badge' => trans('storefront::account.addresses.np_postomat'),
            ],
        ];
    }
}
