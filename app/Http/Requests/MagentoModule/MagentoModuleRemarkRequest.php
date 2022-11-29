<?php

namespace App\Http\Requests\MagentoModule;

use Illuminate\Foundation\Http\FormRequest;

class fMagentoModuleRemarkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'remark' => 'required',
            // 'send_to' => 'required',
            'magento_module_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'remark.required' => __('validation.required', ['attribute' => 'remark']),
            // 'send_to.required' => __('validation.required', ['attribute' => 'Status']),
            'magento_module_id.required' => __('validation.required', ['attribute' => 'module']),
        ];
    }
}
