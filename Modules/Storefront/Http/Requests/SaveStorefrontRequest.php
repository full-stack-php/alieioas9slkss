<?php

namespace Modules\Storefront\Http\Requests;

use Modules\Core\Http\Requests\Request;

class SaveStorefrontRequest extends Request
{
    /**
     * Array of attributes that should be merged with null
     * if attribute is not found in the current request.
     *
     * @var array
     */
    private $shouldCheck = [
        'storefront_featured_categories_section_category_1_products',
        'storefront_featured_categories_section_category_2_products',
        'storefront_featured_categories_section_category_3_products',
        'storefront_featured_categories_section_category_4_products',
        'storefront_featured_categories_section_category_5_products',
        'storefront_featured_categories_section_category_6_products',
        'storefront_product_tabs_1_section_tab_1_products',
        'storefront_product_tabs_1_section_tab_2_products',
        'storefront_product_tabs_1_section_tab_3_products',
        'storefront_product_tabs_1_section_tab_4_products',
        'storefront_top_brands',
        'storefront_vertical_products_1_products',
        'storefront_vertical_products_2_products',
        'storefront_vertical_products_3_products',
        'storefront_product_grid_section_tab_1_products',
        'storefront_product_grid_section_tab_2_products',
        'storefront_product_grid_section_tab_3_products',
        'storefront_product_grid_section_tab_4_products',
        'storefront_product_tabs_2_section_tab_1_products',
        'storefront_product_tabs_2_section_tab_2_products',
        'storefront_product_tabs_2_section_tab_3_products',
        'storefront_product_tabs_2_section_tab_4_products',
    ];



    protected function prepareForValidation()
    {
        $this->merge([
            'storefront_show_callback_btn' => $this->has('storefront_show_callback_btn') ? $this->get('storefront_show_callback_btn') === 'on' : false,
            'storefront_show_repeat_btn' => $this->has('storefront_show_repeat_btn') ? $this->get('storefront_show_repeat_btn') === 'on' : false,
            'storefront_most_searched_keywords_enabled' => $this->has('storefront_most_searched_keywords_enabled') ? $this->get('storefront_most_searched_keywords_enabled') === 'on' : false,
            'storefront_features_section_enabled' => $this->has('storefront_features_section_enabled') ? $this->get('storefront_features_section_enabled') === 'on' : false,
            'storefront_three_column_banners_enabled' => $this->has('storefront_three_column_banners_enabled') ? $this->get('storefront_three_column_banners_enabled') === 'on' : false,
            'storefront_product_tabs_1_section_enabled' => $this->has('storefront_product_tabs_1_section_enabled') ? $this->get('storefront_product_tabs_1_section_enabled') === 'on' : false,
            'storefront_one_column_banner_enabled' => $this->has('storefront_one_column_banner_enabled') ? $this->get('storefront_one_column_banner_enabled') === 'on' : false,
            'storefront_google_reviews_section_enabled' => $this->has('storefront_google_reviews_section_enabled') ? $this->get('storefront_google_reviews_section_enabled') === 'on' : false,
            'storefront_product_notify_message_status' => $this->has('storefront_product_notify_message_status') ? $this->get('storefront_product_notify_message_status') === 'on' : false,
            'storefront_blogs_section_enabled' => $this->has('storefront_blogs_section_enabled') ? $this->get('storefront_blogs_section_enabled') === 'on' : false,
            'storefront_chatgpt_btn_enabled' => $this->has('storefront_chatgpt_btn_enabled') ? $this->get('storefront_chatgpt_btn_enabled') === 'on' : false,
            'storefront_perplexity_btn_enabled' => $this->has('storefront_perplexity_btn_enabled') ? $this->get('storefront_perplexity_btn_enabled') === 'on' : false,
            'storefront_grok_btn_enabled' => $this->has('storefront_grok_btn_enabled') ? $this->get('storefront_grok_btn_enabled') === 'on' : false,
            'storefront_google_ai_btn_enabled' => $this->has('storefront_google_ai_btn_enabled') ? $this->get('storefront_google_ai_btn_enabled') === 'on' : false,
        ]);
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        foreach ($this->shouldCheck as $attribute) {
            if (!$this->has($attribute)) {
                $this->merge([$attribute => null]);
            }
        }

        return $this->all();
    }
}
